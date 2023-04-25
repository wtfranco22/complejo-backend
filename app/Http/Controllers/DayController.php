<?php

namespace App\Http\Controllers;

use App\Models\Day;
use DateTime;
use Illuminate\Http\Request;

class DayController extends Controller
{
    /**
     * Listado de los 7 dias de la semana
     * @return Response
     */
    public function index()
    {
        // $days es la coleccion de elementos Day
        $days = Day::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de dias',
            'days' => $days
        ]);
    }

    /**
     * Actualizamos Day, cambiamos su estado activo y/o descripcion sobre el dia
     * @param Resquest $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        /**
         * $day es un elemento Day encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
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
     * Verificamos que el dia ingresado no tenga inconveniente para la reserva del turno
     * @param String $dayUser
     * @return Array
     */
    public function validateDay($dayUser)
    {
        /**
         * $day es el dia con otro formato del turno ingresado por el usuario
         * $dateTimeNow es el horario del momento que se va a realizar la consulta
         * $now es el horario en este momento pero en string para comparar con el dia ingresado por el usuario
         * $numberDay es el dia (1 al 7) de la semana
         * $workDay es el elemento Day que segun el dia ingresado verifica si esta activo (trabaja)
         * $data es el mensaje informativo del proceso para el cliente
         */
        $day = DateTime::createFromFormat('d-m-Y H:i:s', $dayUser . ' 23:59:59');
        if ($day !== false) {
            // verificamos si existe la fecha
            // $dateTimeNow = date('d-m-Y H:i:s');
            $now = DateTime::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s'));
            if (!($now->diff($day))->invert) {
                // verificamos si el dia y hora son proximas y no una fecha atrasada
                $numberDay = $day->format('N');
                $workDay = Day::find($numberDay);
                if ($workDay->active) {
                    // verificamos si el dia ingresado esta abierto
                    $data = [
                        'valido' => true,
                        'date' => $day
                    ];
                } else {
                    $data = [
                        'valido' => false,
                        'msg' => 'No esta abierto',
                        'date' => $day
                    ];
                }
            } else {
                $data = [
                    'valido' => false,
                    'msg' => 'La fecha ingresada ya paso',
                    'date' => $day
                ];
            }
        } else {
            $data = [
                'valido'  => false,
                'msg' => 'fecha incorrecta'
            ];
        }
        return $data;
    }
}
