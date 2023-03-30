<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'dni' => 'required',
            'phone' => 'required',
            'password' => 'required|confirmed'
        ]);
        $usuario = new User();
        $usuario->role_id = 3;
        $usuario->active = true;
        $usuario->dni = $request->dni;
        $usuario->name = $request->name;
        $usuario->lastname = $request->lastname;
        $usuario->phone = $request->phone;
        $usuario->password = Hash::make($request->password);
        $usuario->save();
        return response()->json([
            'status' => 1,
            'msg' => 'registrado con exito'
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);
        if (!Auth::attempt($request->only('phone', 'password'))) {
            return response()->json([
                'message' => 'Acceso denegado'
            ], 401);
        }
        $usuario = User::where('phone', $request->phone)->firstOrFail();
        $token = $usuario->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function profile()
    {
        return response()->json([
            "status" => 0,
            "user" => auth()->user()
        ]);
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => 1,
            "msg" => "Cierre de sesion"
        ]);
    }
}
