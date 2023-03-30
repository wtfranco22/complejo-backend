<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class CourtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courts = Court::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de las canchas',
            'courts' => $courts
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
        if (isset($request->active_at) || isset($request->inactive_at)) {
            if (!(isset($request->active_at) && isset($request->inactive_at))) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'Activo e inactivo deber estar aclarados'
                ]);
            }
        }
        $court = new Court();
        $court->active = isset($request->active) ? $request->active : false;
        $court->name = $request->name;
        $court->active_at = isset($request->active_at) ? $request->active_at : null;
        $court->inactive_at = isset($request->inactive_at) ? $request->inactive_at : null;
        $court->image_url = isset($request->image_url) ? $request->image_url : null;
        $court->description = $request->description;
        $court->save();
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
        $court = Court::find($id);
        if (isset($court->id)) {
            $data = [
                'status' => 1,
                'msg' => 'Datos de la cancha',
                'court' => $court
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Cancha no encontrada'
            ];
        }
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $court = Court::find($id);
        if (isset($court->$id)) {
            $data = null;
            if (isset($request->active_at) || isset($request->inactive_at)) {
                if (isset($request->active_at) && isset($request->inactive_at)) {
                    $court->active_at = $request->active_at;
                    $court->inactive_at = $request->inactive_at;
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'Activo e inactivo deben ser aclaradas'
                    ];
                }
            }
            if (isNull($data)) {
                $court->active = (isset($request->active)) ? $request->active : $court->active;
                $court->name = (isset($request->name)) ? $request->name : $court->name;
                $court->image_url = (isset($request->image_url)) ? $request->image_url : $court->image_url;
                $court->description = (isset($request->description)) ? $request->description : $court->description;
                $court->save();
                $data = [
                    'status' => 1,
                    'msg' => '¡Datos de la cancha actualizada!'
                ];
            }
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, cancha no encontrada'
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $court = Court::find($id);
        if (isset($court->id)) {
            $court->active = false;
            $court->save();
            $data = [
                'status' => 1,
                'msg' => '¡Cancha deshabilitada!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, cancha no encontrada'
            ];
        }
        return response()->json($data);
    }
}
