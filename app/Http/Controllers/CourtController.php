<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class CourtController extends Controller
{
    /**
     * Listado de todas las canchas
     * @return Response
     */
    public function index()
    {
        // $courts es la coleccion de elementos Court
        $courts = Court::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de las canchas',
            'courts' => $courts
        ]);
    }

    /**
     * Agregamos nueva cancha para alquilar
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // $court es un elemento Court que contiene los datos de la nueva cancha para jugar
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        if (isset($request->active_at) || isset($request->inactive_at)) {
            // si una hora es declarada, ambas deben serlo
            if (!(isset($request->active_at) && isset($request->inactive_at))) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'Activo e inactivo deber estar aclarados'
                ]);
            }
        }
        $court = new Court();
        $court->active = isset($request->active) ? $request->active : false; //por defecto agregamos false
        $court->name = $request->name;
        $court->active_at = isset($request->active_at) ? $request->active_at : null; //por defecto agregamos null
        $court->inactive_at = isset($request->inactive_at) ? $request->inactive_at : null; //por defecto agregamos null
        $court->image_url = isset($request->image_url) ? $request->image_url : null; //por defecto agregamos null
        $court->description = $request->description;
        $court->save();
        return response()->json([
            'status' => 1,
            'msg' => '¡Registrado con exito!'
        ]);
    }

    /**
     * Mostramos todos los datos que tiene la cancha particularmente
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * $court es un elemento Court encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
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
     * Actualizamos los datos como estado y/o cambio automatico declarando horas y/o descripcion y/o imagen
     * @param  Request $id
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        /**
         * $court es un elemento Court encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
        $court = Court::find($id);
        if (isset($court->$id)) {
            $data = null;
            if (isset($request->active_at) || isset($request->inactive_at)) {
                // si una hora es declarada, ambas deben serlo
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
                // verificamos que la actualizacion siga siendo correcta
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
}
