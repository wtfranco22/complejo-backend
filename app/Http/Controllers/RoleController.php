<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Role::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de roles',
            'role' => $role
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::with('users')->find($id);
        if (isset($role->id)) {
            $data = [
                'status' => 1,
                'msg' => 'Datos del rol',
                'role' => $role
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, rol no encontrado'
            ];
        }
        return response()->json([$data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
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
