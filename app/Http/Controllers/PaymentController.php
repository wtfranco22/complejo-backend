<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Lista de pagos',
            'payments' => $payments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'method' => 'required',
            'account_id' => 'required'
        ]);
        $payment = new Payment();
        $payment->account_id = $request->account_id;
        $payment->method = $request->method;
        $payment->description = $request->description;
        $payment->save();
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
        $payment = Payment::find($id);
        if (isset($payment->id)) {
            $payment = Payment::find($id)->with('account');
            $data = [
                'status' => 1,
                'msg' => 'Datos del pago',
                'payment' => $payment
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, pagoo no encontrado'
            ];
        }
        return response()->json($data);
    }
}
