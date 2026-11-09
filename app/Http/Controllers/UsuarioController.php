<?php

namespace App\Http\Controllers;

use App\Models\Role; 
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json([
            'usuarios' => User::with('role')->orderBy('name')->get(),
            'roles'=>Role::all(), 
        ]);
    }

    public function cambiarRol(Request $request, User $user)
    {
        $datos = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($user->id === $request->user()->id) {
            return response()->json(['mensaje' => 'No puedes cambiar tu propio rol'], 422);
        }

        $user->forceFill(['role_id' => $datos['role_id']])->save();

        return response()->json(['usuario' => $user->load('role')]);
    }

    public function store(Request $request)
    {
        $datos= $request->validate([
            'name'=>'required|string|max:255', 
            'email'=>'required|email|max:255|unique:users,email',
            'password'=>'required|string|min:8',
            'role_id' => 'required|exists:roles,id', 
        ]); 

        $user = User::create($datos); 
        $user->forceFill(['role_id'=> $datos['role_id']])->save(); 

        return response()->json(['usuario'=>$user->load('role')], 201); 
    }

    public function update(Request $request, User $user)
    {
        $datos = $request->validate([
            'name'=>'required|string|max:255', 
            'email'=>['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'=>'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $datos['name'];
        $user->email = $datos['email'];
        
        if (!empty($datos['password'])) {
            $user->password = $datos['password'];
        }

        if ($user->id !== $request->user()->id){
            $user->role_id = $datos['role_id'];
        }

        $user->save(); 

        return response()->json(['usuario'=>$user->load('role')]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id===$request->user()->id){
            return response()->json(['mensaje'=>'No puedes eliminar tu propio usuario'], 422); 
        }

        $user->delete();

        return response()->json(['mensaje'=>'Usuario eliminado']); 
    }
}