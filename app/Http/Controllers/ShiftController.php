<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Court;
use App\Models\DayHour;
use DateTime;

class ShiftController extends Controller
{
    /**
     * Listado de todos los turnos con dia, hora, cuenta y cancha
     * @return Response
     */
    public function index()
    {
        // $shifts es la coleccion de elementos Shift con sus relaciones
        $shifts = Shift::with('dayhour.hour', 'dayhour.day', 'account', 'court');
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de todos los turnos',
            'shifts' => $shifts
        ]);
    }

    /**
     * Agregamos un nuevo turno reservado
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        /**
         * $reserved es el del turno registrado, si es que este posee un turno alquilado anteriormente
         * $last es el ultimo turno alquilado en esa hora y en esa cancha, ya que puede estar cancelado el turno
         * $approved es la bandera de la posibilidad de registrar un nuevo turno
         * $shift es el nuevo turno a registrar
         * $data es el mensaje informativo del proceso para el cliente
         */
        $reserved = DB::table('shifts')
            ->where('day_hour_id', $request->day_hour_id)
            ->where('court_id', $request->court_id)
            ->get();
        if (count($reserved) > 0) {
            $last = $reserved[count($reserved) - 1]; // obtenemos el ultimo estado del turno solicitado
            if ($last->available) {
                // verificamos si el ultimo en solicitar el turno lo cancelo y esta habilitado para alquilar
                $approved = true;
            } else {
                $approved = false;
            }
        } else {
            $approved = true;
        }
        if ($approved) {
            $shift = new Shift();
            $dayHour = DayHour::with('hour')->find($request->day_hour_id);
            $shift->day_hour_id = $request->day_hour_id;
            $shift->account_id = auth()->user()->account->id;
            $shift->court_id = $request->court_id;
            $dateTimeNow = $request->date;
            $shift->date = DateTime::createFromFormat('d-m-Y H:i:s', $dateTimeNow . ' ' . $dayHour->hour->hour);
            $shift->available = false;
            $shift->price = $request->price;
            if ($shift->save()) {
                $data = [
                    'approved' => true,
                    'msg' => 'Turno reservado con exito',
                    'description' => $shift->date->format('d-m-Y H:i:s')
                ];
            } else {
                $data = [
                    'approved' => false,
                    'msg' => 'ERROR, Problemas al realizar la reserva del turno'
                ];
            }
        } else {
            $data = [
                'approved' => false,
                'msg' => 'ERROR, El turno ya fue solicitado, ya fue ocupado'
            ];
        }
        return $data;
    }

    /**
     * Verificamos el dia ingresado y retornamos todos los horarios disponibles para los turnos
     * @param String $dayUser
     * @return Response
     */
    public function freeShifts($dayUser)
    {
        /**
         * $dayController ayuda a validar el dia ingresado por el usuario
         * $result es la respuesta de la validacion del dia, retorna si es valido con la fecha
         * $dateTimeNow es el horario del momento que se va a realizar la consulta
         * $now es el horario en este momento pero en string para comparar con el dia ingresado por el usuario
         * $day es la fecha del usuario con los formatos correspondientes para realizar operaciones
         * $timetable es el dia con las horas disponibles que atienden
         * $courts son todas las canchas disponibles que estan activas
         * $courtHours es el dia selecionado con, las canchas y los horarios
         * $freeShifts son todos los turnos libres con su cancha y hora del dia solicitado
         * $shifts son los turnos que fueron reservados para descartar o agregar si es que fueron cancelados
         * $data es el mensaje informativo del proceso para el cliente
         */
        $dayController = new DayController();
        $result = $dayController->validateDay($dayUser);
        if ($result['valido']) {
            // verificamos si la fecha fue valida 
            $dateTimeNow = date('d-m-Y H:i:s');
            $now = DateTime::createFromFormat('d-m-Y H:i:s', $dateTimeNow);
            $day = $result['date'];
            if (($now->diff($day))->days != 0) {
                // verificamos si el dia ingresado es un dia proximo y no el mismo dia 
                $timetable = DB::table('day_hour')
                    ->join('hours', 'day_hour.hour_id', '=', 'hours.id')
                    ->where('day_id', $day->format('N'))
                    ->where('hours.active', true)
                    ->select('day_hour.id as dayhourid', 'hours.id as hourid', 'hours.hour as hour')
                    ->get();
            } else {
                // restringimos el horario segun la hora que consulta
                $timetable = DB::table('day_hour')
                    ->join('hours', 'day_hour.hour_id', '=', 'hours.id')
                    ->where('hours.hour', '>', $now->format('H:i:s'))
                    ->where('day_id', $day->format('N'))
                    ->where('hours.active', true)
                    ->select('day_hour.id as dayhourid', 'hours.id as hourid', 'hours.hour as hour')
                    ->get();
            }
            $courts = Court::all()->where('active', true);
            $courtHours = []; // coleccion de las horas con las canchas
            foreach ($timetable as $horario) {
                foreach ($courts as $court) {
                    $courtHours[] = [
                        'dayhour_id' => $horario->dayhourid,
                        'court_id' => $court->id,
                        'hour' => $horario->hour,
                        'court' => $court->description
                    ];
                }
            }
            $freeShifts = [];
            foreach ($courtHours as $courtHour) {
                // del listado completo del dia con sus horarios y canchas, eliminamos los turnos que ya existe
                $shifts = DB::table('shifts')
                    ->whereDate('date', '=', $day->format('Y-m-d'))
                    ->where('day_hour_id', $courtHour['dayhour_id'])
                    ->where('court_id', $courtHour['court_id'])
                    ->get();
                if (count($shifts) > 0) {
                    // si hay turno a la hora, verificamos que canchas quedan disponibles o si el turno fue cancelado
                    $last = $shifts[count($shifts) - 1]; // obtenemos el ultimo estado del turno solicitado
                    if ($last->available) {
                        $freeShifts[] = $courtHour;
                    }
                } else {
                    // ingresa aca cuando no existe turno solicitado a dicha hora en dicha cancha
                    $freeShifts[] = $courtHour;
                }
            }
            $data = [
                'status' => 1,
                'msg' => 'Listado de los turnos libres',
                'dia' => $day,
                'shifts' => $freeShifts

            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => $result['msg']
            ];
        }
        return response()->json($data);
    }

    /**
     * Resuelve la cancelacion del turno,
     * en caso de ser anticipado mayor a 24hs se devuelve el dinero
     * @param Request $request
     * @return Response
     */
    public function cancelShift(Request $request)
    {
        /**
         * $shift es el turno el cual sera cancelado
         * $now es la hora del momento de la consulta en el formato datetime
         * $turn es la hora del turno que sera cancelada
         * $hoursDiff son las horas de diferencias al momento de cancelar el turno
         * $requestPay datos preparados en un Request para el envio al controlador Payment
         * $payment es el controlador encargado de registrar la actualizacion
         * $resultPayment es el resultado de la devolucion del controlador
         * $data es el mensaje informativo del proceso para el cliente
         */
        $shift = Shift::with('account')->find($request->shift_id);
        $dateTimeNow = date('d-m-Y H:i:s');
        $now = DateTime::createFromFormat('d-m-Y H:i:s', $dateTimeNow); // convertimos la hora de la consulta a DateTime
        $dateShift = DateTime::createFromFormat('Y-m-d H:i:s', $shift->date)->format('d-m-Y H:i:s');
        $turn = DateTime::createFromFormat('d-m-Y H:i:s', $dateShift); // convertimos el formato de mysql al nuestro y creamos datetime
        $shift->available = true;
        $shift->save();
        $hoursDiff = $now->diff($turn)->h + $now->diff($turn)->days * 24;
        if ($hoursDiff > 24) {
            // verificamos si la cancelacion fue con anticipacion
            $requestPay = new Request([
                'reason' => 'returnPayShift',
                'method' => 'Mercadopago',
                'price' => $shift->price
            ]);
            $payment = new PaymentController();
            $resultPayment = $payment->store($requestPay);
            if ($resultPayment['approved']) {
                $data = [
                    'status' => 1,
                    'msg' => 'Turno cancelado y devolucion del dinero con exito'
                ];
            } else {
                $data = [
                    'status' => 0,
                    'msg' => $resultPayment['msg']
                ];
            }
        } else {
            $data = [
                'status' => 1,
                'msg' => 'El turno se cancelo con exito, sin devolucion de dinero por poca anticipacion'
            ];
        }
        return response()->json($data);
    }
}
