<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Listado de los roles del sistema
     * @return Response
     */
    public function index()
    {
        // $role es la coleccion de elementos Role
        $role = Role::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de roles',
            'role' => $role
        ]);
    }

    /**
     * Agregamos nuevo rol de trabajo al sistema
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // $role es un elemento Role que contiene los datos del nuevo rol de trabajo
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        $role = new Role();
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();
        return response()->json([
            'status' => 1,
            'msg' => '¡Registrado con exito!'
        ]);
    }

    /**
     * Actualizamos el nombre y/o descripcion del rol
     * @param  Request $id
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        /**
         * $role es un elemento Role encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
        $role = Role::find($id);
        if (isset($role->id)) {
            $role->name = isset($request->name) ? $request->name : $role->name;
            $role->description = isset($request->description) ? $request->description : $role->description;
            $data = [
                'status' => 1,
                'msg' => '¡Actualizado con exito!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, rol no encontrado'
            ];
        }
        return response()->json([$data]);
    }
}
