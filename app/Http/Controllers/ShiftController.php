<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Court;
use DateTime;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::with('dayhour.hour', 'dayhour.day', 'account', 'court');
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de todos los turnos reservados',
            'shifts' => $shifts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_hour_id' => 'required',
            'account_id' => 'required',
            'court_id' => 'required',
            'date' => 'required'
        ]);
        $shift = new Shift();
        $shift->day_hour_id;
        $shift->account_id;
        $shift->court_id;
        $shift->date = new DateTime('d-m-Y H:i:s');
        $shift->available = false;
        $shift->price = 2000;
        $shift->save();
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
        $shift = Shift::find($id);
        if (isset($shift->id)) {
            $account = Account::find($request->account_id);
            if (isset($account->id)) {
                $court = Court::find($request->court_id);
                if (isset($court->id) && $court->active) {
                    $shift->court_id = isset($request->court_id) ? $court->id : $shift->court_id;
                    $shift->available = isset($request->available) ? $request->available : $shift->available;
                    $shift->save();
                    $data = [
                        'status' => 1,
                        'msg' => 'Actualizado con exito'
                    ];
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'Error, no se encontro la cancha o esta deshabilitada'
                    ];
                }
            } else {
                $data = [
                    'status' => 0,
                    'msg' => 'Error, no se encontro la cuenta del usuario'
                ];
            }
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, no se encontro el turno'
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $shift = Shift::find($id);
        if (isset($shift->id)) {
            $data = [
                'status' => 1,
                'msg' => 'Deshabilitado con exito'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, no se encontro el turno'
            ];
        }
        return response()->json([$data]);
    }
}
