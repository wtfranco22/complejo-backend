<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $days = Day::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de dias',
            'days' => $days
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $day = Day::find($id);
        if (isset($day->id)) {
            $day->active = isset($request->active) ? $request->active : $day->active;
            $day->description = isset($request->description) ? $request->description : $day->description;
            $day->save();
            $data = [
                'status' => 1,
                'msg' => '¡Actualizado con exito!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, dia no encontrado'
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $day = Day::find($id);
        if (isset($day->id)) {
            $day->active = false;
            $day->save();
            $data = [
                'status' => 1,
                'msg' => '¡Día deshabilitado!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, dia no encontrado'
            ];
        }
        return response()->json($data);
    }

    public function workday($id)
    {
        return Day::find($id)->active;
    }
}
