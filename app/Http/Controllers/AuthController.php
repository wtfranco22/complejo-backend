<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    /**
     * Registramos a un nuevo cliente
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        /**
         * $user es un elemento User que contiene los datos del nuevo usuario del sistema
         * $role es un elemento Role que ya se encuentra en el sistema
         */
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'dni' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required|confirmed'
        ]);
        $role = Role::find(3);
        $user = new User();
        $user->role_id = $role->id; // por defecto lo agregamos como cliente
        $user->active = true; // por defecto decimos que el estado es activo
        $user->dni = $request->dni;
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->notify(new VerifyEmail());
        return response()->json([
            'status' => 1,
            'msg' => 'registrado con exito'
        ]);
    }

    /**
     * Verificamos los datos ingresados para darle acceso al usuario
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            // utilizamos la autenticacion que ofrece sanctum
            $data = [
                'status' => 0,
                'msg' => 'Acceso denegado'
            ];
        } else {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;
            $data = [
                'status' => 1,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        }
        return response()->json($data);
    }

    /**
     * Mostramos todos los datos que tiene el usuario en particular
     * @return Response
     */
    public function profile()
    {
        return response()->json([
            'status' => 1,
            'msg' => 'Datos del usuario',
            'user' => auth()->user()
        ]);
    }

    /**
     * Eliminamos la sesion del respectivo usuario
     * utilizando lo que nos ofrece sanctum
     * @return Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 1,
            'msg' => 'Cierre de sesion'
        ]);
    }

    /**
     * Verificamos la cuenta del usuario a traves del correo
     * @param int $id
     * @return Response
     */
    public function verifyUser($id)
    {
        if (time() < request('expires')) {
            // verificamos si el link no haya expirado
            if (URL::hasValidSignature(request())) {
                // verificamos que el link ingresado no sea manipulado, que siga intacta la firma 
                $user = User::findOrFail($id);
                if (!$user->hasVerifiedEmail()) {
                    // verificamos que el usuario ya tenga el email validado
                    if ($user->markEmailAsVerified()) {
                        // validamos email e iniciamos sesion, generamos token
                        Auth::login($user);
                        $token = $user->createToken('auth_toekn')->plainTextToken;
                        $account = new AccountController();
                        $msg = $account->store();
                        $data = [
                            'status' => $msg['status'],
                            'msg' => $msg['msg'],
                            'token' => $token
                        ];
                    } else {
                        $data = [
                            'status' => 0,
                            'msg' => 'Error de validacion de email'
                        ];
                    }
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'Error Email ya verificado'
                    ];
                }
            } else {
                $data = [
                    'status' => 0,
                    'msg' => 'Error de enlace ingresado'
                ];
            }
        }else{
            $data = [
                'status' => 0,
                'msg' => 'El link ya ha expirado'
            ];
        }
        return response()->json($data);
    }
}
