<?php

namespace App\Http\Controllers;

use App\Models\Municipio; 
use App\Models\Plantel; 
use App\Models\Corde; 
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

        $datos = $request->validate([
            'cct'=>'required|string|size:10|unique:planteles,cct', 
            'nombre_escuela'=>'required|string|max:255', 
            'localidad_id'=>'nullable|exists:localidades,id', 
            'corde_id' => 'nullable|exists:cordes,id',
        ]);

        $plantel = Plantel::create($datos); 
        return response()->json(['plantel' => $plantel->load(['localidad.municipio', 'corde'])], 201);
    }

    public function update(Request $request, Plantel $plantel)
    {
        $this->limpiarCct($request);

        $datos = $request->validate([
            'cct' => ['required', 'string', 'size:10', Rule::unique('planteles', 'cct')->ignore($plantel->id)],
            'nombre_escuela'=>'required|string|max:255', 
            'localidad_id'=>'nullable|exists:localidades,id', 
            'corde_id' => 'nullable|exists:cordes,id',
        ]);

        $plantel->update($datos); 
        return response()->json(['plantel' => $plantel->load('localidad.municipio', 'corde')]);
    }

    public function destroy(Plantel $plantel)
    {
        $plantel->delete();
        return response()->json(['mensaje' => 'Plantel eliminado']);
    }

    private function limpiarCct(Request $request): void 
    {
        $request->merge(['cct'=> strtoupper(trim((string) $request->cct))]); 
    }
}
