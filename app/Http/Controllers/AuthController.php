<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AuthController extends Controller
{
    //Devuelve el usuario con sesion activa o null
    public function usuario()
    {
        return response()->json(['usuario'=>Auth::user()]); 
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email'=>'required|email', 
            'password'=>'required', 
        ]);
        
        if (! Auth::attempt($credenciales, $request->boolean('recordar'))){
            return response()->json(['mensaje'=>'Correo o contraseña incorrecta'], 422); 
        }

        $request->session()->regenerate(); 

        return response()->json([
            'usuario'=> Auth::user(), 
            'token'=> csrf_token(), 
        ]); 

    }

    public function logout(Request $request)
    {
        Auth::logout(); 
        $request->session()->invalidate();
        $request->session()->regenerateToken(); 

        return response()->json(['token'=> csrf_token()]);
    }
}
