<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use Illuminate\Http\Request;

class HourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hours = Hour::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de horas',
            'hours' => $hours
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hour' => 'required'
        ]);
        $hour = new Hour();
        $hour->name = $request->name;
        $hour->active = true;
        $hour->description = isset($request->description) ? $request->description : null;
        $hour->save();
        return response()->json([
            'status' => 1,
            'msg' => 'Registrado con exito'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $hour = Hour::find($id);
        if (isset($hour->id)) {
            $hour->active = isset($request->active) ? $request->active : $hour->active;
            $hour->description = isset($request->description) ? $request->description : $hour->description;
            $hour->save();
            $data = [
                'status' => 1,
                'msg' => '¡Actualizado con exito!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, Hora no encontrada'
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hour = Hour::find($id);
        if (isset($hour->id)) {
            $hour->active = false;
            $hour->save();
            $data = [
                'status' => 1,
                'msg' => '¡Hora deshabilitada!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, Hora no encontrada'
            ];
        }
        return response()->json($data);
    }
}
