<?php

namespace App\Http\Controllers;

use App\Models\Municipio; 
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

class MunicipioController extends Controller
{
    public function index()
    {
        return response()->json([
            'municipios'=> Municipio::with(['localidades'=>fn($q)=> $q->orderBy('nombre')])
                ->withCount('localidades')
                ->orderBy('nombre')
                ->get(), 
        ]); 
    }

    public function plantilla()
    {
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja -> setTitle('Municipios'); 

        $hoja->fromArray([
            ['municipio', 'localidad'],
            ['Tehuacán', 'Tehuacán'],
            ['Tehuacán', 'Santa María Coapan'],
            ['Ajalpan', 'Ajalpan'],
        ], null, 'A1');

        $hoja->getStyle('1:1')->getFont()->setBold(true);
        $hoja->getColumnDimension('A')->setAutoSize(true);
        $hoja->getColumnDimension('B')->setAutoSize(true);

        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
        }, 'plantilla_municipios.xlsx', [
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

        $filas = IOFactory::createReader($tipos[$extension])
            ->load($archivo->getRealPath())
            ->getActiveSheet()
            ->toArray(null, true, false, false);

        // Fila 1 = encabezados; busca en qué columna está cada uno
        $encabezados = array_map(fn ($h) => strtolower(trim((string) $h)), $filas[0] ?? []);
        $colMunicipio = array_search('municipio', $encabezados);
        $colLocalidad = array_search('localidad', $encabezados);

        if ($colMunicipio === false) {
            return response()->json(['mensaje' => 'Falta la columna obligatoria "municipio" en la fila 1'], 422);
        }

        $municipios = [];
        $localidades = [];
        $resultado = ['municipios_nuevos' => 0, 'localidades_nuevas' => 0, 'ya_existian' => 0, 'errores' => []];

        foreach (array_slice($filas, 1) as $i => $fila) {
            $numFila = $i + 2; // número de fila como se ve en Excel

            $nomMunicipio = trim((string) ($fila[$colMunicipio] ?? ''));
            $nomLocalidad = $colLocalidad !== false ? trim((string) ($fila[$colLocalidad] ?? '')) : '';

            // Salta filas vacías
            if ($nomMunicipio === '' && $nomLocalidad === '') {
                continue;
            }

            if ($nomMunicipio === '') {
                $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => 'Tiene localidad pero no municipio'];
                continue;
            }

            if (mb_strlen($nomMunicipio) > 255 || mb_strlen($nomLocalidad) > 255) {
                $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => 'El nombre es demasiado largo (máx. 255 caracteres)'];
                continue;
            }

            try {
                // Municipio (se crea solo si no existe)
                $llaveMun = mb_strtolower($nomMunicipio);
                if (! isset($municipios[$llaveMun])) {
                    $municipios[$llaveMun] = Municipio::firstOrCreate(['nombre' => $nomMunicipio]);
                    if ($municipios[$llaveMun]->wasRecentlyCreated) {
                        $resultado['municipios_nuevos']++;
                    }
                }

                // Localidad dentro de su municipio (se crea solo si no existe)
                if ($nomLocalidad !== '') {
                    $llaveLoc = $llaveMun . '|' . mb_strtolower($nomLocalidad);
                    if (isset($localidades[$llaveLoc])) {
                        $resultado['ya_existian']++; // repetida dentro del mismo archivo
                        continue;
                    }

                    $localidades[$llaveLoc] = $municipios[$llaveMun]->localidades()->firstOrCreate(['nombre' => $nomLocalidad]);
                    $localidades[$llaveLoc]->wasRecentlyCreated
                        ? $resultado['localidades_nuevas']++
                        : $resultado['ya_existian']++;
                }
            } catch (\Throwable $e) {
                $resultado['errores'][] = ['fila' => $numFila, 'mensaje' => 'Error al guardar: ' . $e->getMessage()];
            }
        }

        return response()->json($resultado);
    }
}
