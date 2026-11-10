<?php

namespace App\Http\Controllers;

use App\Models\Corde; 
use App\Models\Municipio; 
use App\Models\Plantel; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class PlantelController extends Controller
{
    public function index()
    {
        return response()->json([
            'planteles' => Plantel::with(['localidad.municipio', 'corde'])->orderBy('nombre_escuela')->get(),
            'municipios' => Municipio::with('localidades')->orderBy('nombre')->get(),
            'cordes' => Corde::orderBy('nombre_corde')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->limpiarCct($request); 
        $datos=$request->validate($this->reglas()); 

        $plantel = Plantel::create($datos);

        return response()->json(['plantel'=>$plantel->load(['localidad.municipio', 'corde'])], 201); 

    }

    public function update(Request $request, Plantel $plantel)
    {
        $this->limpiarCct($request); 
        $datos = $request->validate($this->reglas($plantel->id)); 

        $plantel->update($datos); 

        return response() ->json(['plantel'=>$plantel->load(['localidad.municipio', 'corde'])]); 
    }

    public function destroy(Plantel $plantel)
    {
        $plantel->delete(); 
        return response()->json(['mensaje'=>'Plantel eliminado']); 
    }

    public function reglas($id=null): array
    {
        return [ 
            'cct' => ['required', 'string', 'size:10', Rule::unique('planteles', 'cct')->ignore($id)],
            'nombre_escuela'=>'required|string|max:255', 
            'localidad_id' => 'nullable|exists:localidades,id', 
            'corde_id' => 'nullable|exists:cordes,id',
            'turno' => 'nullable|string|max:50', 
            'nivel_educativo'=>'nullable|string|max:100', 
            'sostenimiento'=>'nullable|string|max:100', 
            'domicilio_calle_numero'=>'nullable|string|max:255', 
            'domicilio_colonia'=>'nullable|string|max:255', 
            'domicilio_cp'=>'nullable|digits:5', 
            'telefono_plantel'=>'nullable|string|max:50', 
            'correo_institucional'=>'nullable|email|max:255', 
            'accesibilidad_rampas'=>'boolean', 
            'accesibilidad_banos_adaptados'=>'boolean', 
            'accesibilidad_senaletica_braille'=>'boolean', 
            'accesibilidad_otros'=>'nullable|string|max:2000', 
            'total_alumnos'=>'required|integer|min:0', 
            'total_docentes'=>'required|integer|min:0', 
            'total_administrativos'=>'required|integer|min:0', 
            'latitud'=>'nullable|numeric|between:-90,90', 
            'longitud'=>'nullable|numeric|between:-180,180', 
        ]; 
    }

    private function limpiarCct(Request $request): void 
    {
        $request->merge(['cct'=> strtoupper(trim((string) $request->cct))]); 
    }
}
