<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Listamos todos los usuarios
     * @return Response
     */
    public function index()
    {
        // $users es la coleccion de elementos Users con su rol
        $users = User::with('role')->get();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de users',
            'users' => $users
        ]);
    }

    /**
     * Mostramos todos los datos que tiene el usuario particularmente
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * $user es un elemento User encontrado por su id $id con su rol asignado
         * $data es el mensaje informativo del proceso para el cliente
         */
        $user = User::with('role')->find($id);
        if (isset($user->id)) {
            $data = [
                'status' => 1,
                'msg' => 'Datos del usuario',
                'user' => $user
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => '¡Usuario no encontrado!'
            ];
        }
        return response()->json($data);
    }

    /**
     * Actualizamos los datos como, estado y/o nombres y/o celular y/o dni
     * @param  Request $id
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        /**
         * $user es un elemento User encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
        $user = User::find($id);
        if (isset($user->id)) {
            if (isset($request->active)) {
                $user = User::with('account')->find($id);
                if ($user->account->balance == 0.0) {
                    $user->active = false;
                    $user->save();
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'Error, Aun tiene deudas'
                    ];
                }
            } else {
                $user->name = isset($request->name) ? $request->name : $user->name;
                $user->lastname = isset($request->lastname) ? $request->lastname : $user->lastname;
                $user->dni = isset($request->dni) ? $request->dni : $user->dni;
                $user->phone = isset($request->phone) ? $request->phone : $user->phone;
                $user->save();
                $data = [
                    'status' => 1,
                    'msg' => '¡Actualizado con exito!'
                ];
            }
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, usuario no encontrado'
            ];
        }
        return response()->json($data);
    }

    /**
     * Actualizamos al usuario con los permisos suficientes como administrador
     * @param Request $request
     * @return Response
     */
    public function adminUpdate(Request $request)
    {
        /**
         * $user es un elemento User que contiene los datos del usuario
         * $role es un elemento Role que ya se encuentra en el sistema
         * $idRole es el id que se le asignara al usuario
         */
        $request->validate([
            'user_id' => 'required'
        ]);
        $user = User::find($request->user_id);
        if (isset($user->id)) {
            $idRole = isset($request->role_id) ? $request->role_id : $user->role_id; // verificamos si admin actualizo el rol
            $role = Role::find($idRole);
            if (isset($role->id)) {
                $user->role_id = $role->id;
                $user->active = isset($request->active) ? $request->active : $user->active;
                $data = [
                    'status' => 1,
                    'msg' => 'Usuario actualizado'
                ];
            } else {
                $data = [
                    'status' => 0,
                    'msg' => 'Error, rol no encontrado'
                ];
            }
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, usuario no encontrado'
            ];
        }
        $user->save();
        return response()->json($data);
    }

    /**
     * Mostramos todos los pagos realizados por el usuario
     * @return Array
     */
    public function myPayments(){
        // $user es el usuario que esta solicitando el listado de los pagos realizados
        $user = User::with('account')->find(auth()->user()->id);
        return response()->json([
            'status' => 1,
            'msg' => 'Pagos realizados',
            'Payments' => $user->account->payments
        ]);
    }

    /**
     * Mostramos todos los turnos realizados por el usuario
     * @return Array
     */
    public function myShifts(){
        // $user es el usuario que esta solicitando el listado de los pagos realizados
        $user = User::with('account')->find(auth()->user()->id);
        return response()->json([
            'status' => 1,
            'msg' => 'Pagos realizados',
            'Payments' => $user->account->shifts
        ]);
    }
}
