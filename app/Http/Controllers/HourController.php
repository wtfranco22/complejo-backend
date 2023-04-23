<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use Illuminate\Http\Request;

class HourController extends Controller
{
    /**
     * Listado de las horas de trabajo
     * @return Response
     */
    public function index()
    {
        // $hours es la coleccion de elementos Hour
        $hours = Hour::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de horas',
            'hours' => $hours
        ]);
    }

    /**
     * Agregamos nuevas horas de trabajo
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // $hour es un elemento Hour que contiene los datos de la nueva hora de atencion
        $request->validate([
            'hour' => 'required'
        ]);
        $hour = new Hour();
        $hour->name = $request->name;
        $hour->active = true;
        $hour->description = isset($request->description) ? $request->description : null; // por defecto agregamos el valor null
        $hour->save();
        return response()->json([
            'status' => 1,
            'msg' => 'Registrado con exito'
        ]);
    }

    /**
     * Actualizamos el estado de la hora y/o la descripcion
     * @param  Request $id
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        /**
         * $hour es un elemento Hour encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
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
}
