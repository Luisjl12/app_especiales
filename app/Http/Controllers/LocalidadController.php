<?php

namespace App\Http\Controllers;

use App\Models\Localidad; 
use App\Models\Municipio; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class LocalidadController extends Controller
{
    public function municipios()
    {
        return response()->json([
            'municipios'=>Municipio::withCount('localidades')->orderBy('nombre')->get(['id', 'nombre']), 
        ]); 
    }

    public function index(Request $request)
    {
        $request->validate([
            'municipio_id'=>'required|exists:municipios,id', 
        ]); 

        return response()->json([
            'localidades'=>Localidad::where('municipio_id', $request->municipio_id)
                ->withCount('planteles')
                ->orderBy('nombre')
                ->get(), 
        ]); 
    }

    public function store(Request $request)
    {
        $datos= $request->validate($this->reglas($request));

        $localidad=Localidad::create($datos);

        return response()->json(['localidad'=>$localidad], 201); 
    }

    public function update(Request $request, Localidad $localidad)
    {
        $datos= $request->validate($this->reglas($request, $localidad->id)); 

        $localidad->update($datos); 

        return response()->json(['localidad'=>$localidad]); 
    }

    public function destroy(Localidad $localidad)
    {
        $usos = $localidad->planteles()->count(); 
        if ($usos>0){
            return response()->json([
                'mensaje'=>"No se puede eliminar: la usan $usos plantel(es). Cambiales la localidad primero.", 
            ], 422); 
        }

        $localidad->delete(); 
        return response()->json(['mensaje'=>'Localidad eliminada']); 
    }

    private function reglas(Request $request, $id=null):array
    {
        return[
            'municipio_id' => 'required|exists:municipios,id',
            'nombre'=>[
                'required', 'string', 'max:255', 
                Rule::unique('localidades', 'nombre')
                    ->where('municipio_id', $request->municipio_id)
                    ->ignore($id), 
            ], 
        ]; 
    }
}
