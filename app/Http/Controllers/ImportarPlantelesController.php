<?php

namespace App\Http\Controllers;

use App\Models\Corde; 
use App\Models\Municipio; 
use App\Models\Plantel; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportarPlantelesController extends Controller
{
    private const COLUMNAS = [
        'cct', 'nombre_escuela', 'nivel_educativo', 'turno', 'sostenimiento',
        'municipio', 'localidad', 'clave_corde',
        'domicilio_calle_numero', 'domicilio_colonia', 'domicilio_cp',
        'telefono_plantel', 'correo_institucional',
        'accesibilidad_rampas', 'accesibilidad_banos_adaptados',
        'accesibilidad_senaletica_braille', 'accesibilidad_otros',
        'total_alumnos', 'total_docentes', 'total_administrativos',
        'latitud', 'longitud',
    ]; 

    private const BOOLEANOS =[
        'accesibilidad_rampas', 
        'accesibilidad_banos_adaptados',
        'accesibilidad_senaletica_braille',  
    ]; 

    private const NUMEROS =[
        'total_alumnos', 
        'total_docentes', 
        'total_administrativos', 
    ]; 

    public function plantilla()
    {
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle('Planteles');

        $hoja->fromArray([self::COLUMNAS], null, 'A1');
        $hoja->fromArray([[
            '21DJN0453U', 'JOSE MARIA LUIS MORA', 'Preescolar', 'Matutino', 'Estatal',
            'Tehuacán', 'Santa María Coapan', 'CLAVE_CORDE',
            'Av. Reforma 14', 'Centro', '75700',
            '2381234567', 'escuela@ejemplo.com',
            'si', 'no', 'no', '',
            120, 8, 3,
            18.4616, -97.3930,
        ]], null, 'A2');

        $hoja->getStyle('1:1')->getFont()->setBold(true);
        for ($i = 1; $i <= count(self::COLUMNAS); $i++) {
            $hoja->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
        }, 'plantilla_planteles.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        set_time_limit(300);

        // Lee el archivo según su extensión
        $archivo = $request->file('archivo');
        $tipos = ['xlsx' => 'Xlsx', 'xls' => 'Xls', 'csv' => 'Csv', 'txt' => 'Csv'];
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (! isset($tipos[$extension])) {
            return response()->json(['mensaje' => 'Formato no válido. Usa .xlsx, .xls o .csv'], 422);
        }

        $lector = IOFactory::createReader($tipos[$extension]);
        $filas = $lector->load($archivo->getRealPath())->getActiveSheet()->toArray(null, true, false, false);

        // Fila 1 = encabezados
        $encabezados = array_map(fn ($h) => strtolower(trim((string) $h)), $filas[0] ?? []);

        foreach (['cct', 'nombre_escuela'] as $obligatoria) {
            if (! in_array($obligatoria, $encabezados)) {
                return response()->json(['mensaje' => "Falta la columna obligatoria \"$obligatoria\" en la fila 1"], 422);
            }
        }

        // CORDEs existentes, por clave
        $cordes = Corde::all()->keyBy(fn ($c) => mb_strtoupper(trim($c->clave_corde)));

        $municipios = [];
        $localidades = [];
        $resultado = ['creados' => 0, 'actualizados' => 0, 'errores' => []];

        foreach (array_slice($filas, 1) as $i => $fila) {
            $numFila = $i + 2; // número de fila como se ve en Excel

            // Junta los valores con su encabezado
            $d = [];
            foreach ($encabezados as $col => $nombre) {
                if (in_array($nombre, self::COLUMNAS)) {
                    $d[$nombre] = $this->limpiar($fila[$col] ?? null);
                }
            }

            // Salta filas vacías
            if (! array_filter($d, fn ($v) => $v !== null)) {
                continue;
            }

            try {
                $d = $this->normalizar($d);

                $validador = Validator::make($d, [
                    'cct' => 'required|string|size:10',
                    'nombre_escuela' => 'required|string|max:255',
                    'nivel_educativo' => 'nullable|string|max:100',
                    'turno' => 'nullable|string|max:50',
                    'sostenimiento' => 'nullable|string|max:100',
                    'domicilio_calle_numero' => 'nullable|string|max:255',
                    'domicilio_colonia' => 'nullable|string|max:255',
                    'domicilio_cp' => 'nullable|digits:5',
                    'telefono_plantel' => 'nullable|string|max:50',
                    'correo_institucional' => 'nullable|email|max:255',
                    'accesibilidad_otros' => 'nullable|string|max:2000',
                    'total_alumnos' => 'integer|min:0',
                    'total_docentes' => 'integer|min:0',
                    'total_administrativos' => 'integer|min:0',
                    'latitud' => 'nullable|numeric|between:-90,90',
                    'longitud' => 'nullable|numeric|between:-180,180',
                ]);

                if ($validador->fails()) {
                    $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => $validador->errors()->first()];
                    continue;
                }

                $datos = collect($d)->except(['municipio', 'localidad', 'clave_corde'])->all();

                // CORDE por clave (debe existir)
                if (array_key_exists('clave_corde', $d)) {
                    $datos['corde_id'] = null;
                    if ($d['clave_corde'] !== null) {
                        $clave = mb_strtoupper($d['clave_corde']);
                        if (! isset($cordes[$clave])) {
                            $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => "La CORDE \"{$d['clave_corde']}\" no existe"];
                            continue;
                        }
                        $datos['corde_id'] = $cordes[$clave]->id;
                    }
                }

                // Municipio y localidad por nombre (se crean si no existen)
                if (array_key_exists('localidad', $d) || array_key_exists('municipio', $d)) {
                    $nomMunicipio = $d['municipio'] ?? null;
                    $nomLocalidad = $d['localidad'] ?? null;
                    $datos['localidad_id'] = null;

                    if ($nomLocalidad !== null && $nomMunicipio === null) {
                        $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => 'Tiene localidad pero no municipio'];
                        continue;
                    }

                    if ($nomLocalidad !== null) {
                        $llaveMun = mb_strtolower($nomMunicipio);
                        $municipios[$llaveMun] ??= Municipio::firstOrCreate(['nombre' => $nomMunicipio]);

                        $llaveLoc = $llaveMun . '|' . mb_strtolower($nomLocalidad);
                        $localidades[$llaveLoc] ??= $municipios[$llaveMun]->localidades()->firstOrCreate(['nombre' => $nomLocalidad]);

                        $datos['localidad_id'] = $localidades[$llaveLoc]->id;
                    }
                }

                // Si la CCT ya existe se actualiza; si no, se crea
                $plantel = Plantel::updateOrCreate(['cct' => $d['cct']], $datos);
                $plantel->wasRecentlyCreated ? $resultado['creados']++ : $resultado['actualizados']++;
            } catch (\Throwable $e) {
                $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => 'Error al guardar: ' . $e->getMessage()];
            }
        }

        return response()->json($resultado);
    }

    private function limpiar($valor)
    {
        if (is_string($valor)){
            $valor=trim($valor); 
        }

        return $valor=== '' ? null : $valor;
    }

    private function normalizar(array $d):array
    {
        if (isset($d['cct'])) {
            $d['cct'] = strtoupper(str_replace(' ', '', (string) $d['cct']));
        }

        // Excel quita los ceros a la izquierda del CP (01000 -> 1000)
        if (isset($d['domicilio_cp'])) {
            $cp = preg_replace('/\D/', '', (string) $d['domicilio_cp']);
            $d['domicilio_cp'] = $cp === '' ? null : str_pad($cp, 5, '0', STR_PAD_LEFT);
        }

        foreach (['clave_corde', 'municipio', 'localidad', 'telefono_plantel', 'nombre_escuela',
                  'nivel_educativo', 'turno', 'sostenimiento', 'domicilio_calle_numero',
                  'domicilio_colonia', 'correo_institucional', 'accesibilidad_otros'] as $campo) {
            if (isset($d[$campo])) {
                $d[$campo] = (string) $d[$campo];
            }
        }

        // si / sí / x / 1 / true = verdadero; lo demás = falso
        foreach (self::BOOLEANOS as $campo) {
            if (array_key_exists($campo, $d)) {
                $v = mb_strtolower(trim((string) $d[$campo]));
                $d[$campo] = in_array($v, ['1', 'si', 'sí', 's', 'x', 'true', 'verdadero', 'yes']);
            }
        }

        // Números vacíos = 0
        foreach (self::NUMEROS as $campo) {
            if (array_key_exists($campo, $d)) {
                $d[$campo] = $d[$campo] === null ? 0 : $d[$campo];
                if (is_float($d[$campo]) && floor($d[$campo]) == $d[$campo]) {
                    $d[$campo] = (int) $d[$campo];
                }
            }
        }

        return $d;
    }
}
