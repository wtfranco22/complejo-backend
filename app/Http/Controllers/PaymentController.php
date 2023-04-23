<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Listado de todos los pagos realizados por los clientes
     * @return Response
     */
    public function index()
    {
        // $payments es la coleccion de elementos Payment
        $payments = Payment::all();
        return response()->json([
            'status' => 1,
            'msg' => 'Lista de pagos',
            'payments' => $payments
        ]);
    }

    /**
     * Mostramos todos los datos que tiene el pago particularmente
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * $payment es un elemento Payment encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
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

    /**
     * Agregamos un nuevo pago realizado por el cliente
     * las ocasiones son: pagar turno, cancelar con tiempo, pagar cuenta por si mismo o por administracion
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        /**
         * $account controlador de cuenta para actualizar el nuevo pago, o devolucion del dinero por cancelar turno
         * $resultAccount es el resultado de la actualizacion de la cuenta afectada, podemos recibir problemas en los msg
         * $shift es el turno a registrar, solicitado por el cliente
         * $resultShift es el resultado del nuevo registro del turno, podemos recibir problemas en los msg
         * $payment es un elemento Payment que contiene los datos del nuevo pago del cliente en su cuenta
         * $data es el mensaje informativo del proceso para el cliente
         */
        $request->validate([
            'reason' => 'required',
            'price' => 'required'
        ]);
        switch ($request->reason) {
            case 'payShift':
                // este caso es cuando el usuario solicita un turno
                $request->validate([
                    'date' => 'required',
                    'method' => 'required',
                    'day_hour_id' => 'required',
                    'court_id' => 'required',
                ]);
                $account = new AccountController();
                $resultAccount = $account->updateBalance($request);
                if ($resultAccount['approved']) {
                    // verificamos que se realizara el pago sobre el saldo de la cuenta
                    $shift = new ShiftController();
                    $resultShift = $shift->store($request);
                    if ($resultShift['approved']) {
                        // verificamos si se registro el turno solicitado
                        $payment = new Payment();
                        $payment->account_id = auth()->user()->account->id;
                        $payment->method = $request->method;
                        $payment->description = 'Turno solicitado: ' . $resultShift['description'] . ', por $' . $request->price;
                        if ($payment->save()) {
                            // verificamos si se registro el nuevo pago con exito
                            $data = [
                                'status' => 1,
                                'msg' => 'Se realizo el pago con exito'
                            ];
                        } else {
                            $data = [
                                'status' => 0,
                                'msg' => 'ERROR, No registrado el pago, se cobro el turno'
                            ];
                        }
                    } else {
                        $data = [
                            'status' => 0,
                            'msg' => $resultShift['msg']
                        ];
                    }
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => $resultAccount['msg']
                    ];
                }
                break;
            case 'payAccount':
                // este caso es cuando el usuario ingresa dinero a su propia cuenta
                $request->validate(['method' => 'required']);
                $account = new AccountController();
                $resultAccount = $account->updateBalance($request);
                if ($resultAccount['approved']) {
                    // verificamos si se actualizo el saldo del usuario con el ingreso de dinero
                    $payment = new Payment();
                    $payment->account_id = auth()->user()->account->id;
                    $payment->method = $request->method;
                    $payment->description = 'Ingreso de dinero a la cuenta $' . $request->price;
                    $payment->save();
                    $data = [
                        'status' => 1,
                        'msg' => 'Ingreso del dinero a la cuenta con exito'
                    ];
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'No tuvo exito el ingreso de dinero en la cuenta'
                    ];
                }
                break;
            case 'payAccountAdmin':
                // este caso es cuando el usuario pasa por administracion a cancelar su deuda de la cuenta
                $request->validate(['method' => 'required', 'account_id' => 'required']);
                $account = new AccountController();
                $resultAccount = $account->updateBalance($request);
                if ($resultAccount['approved']) {
                    // verificamos que se actualizo correctamente la cuenta y registramos el pago
                    $payment = new Payment();
                    $payment->account_id = $request->account_id;
                    $payment->method = $request->method;
                    $payment->description = 'Pago por el local, entrega de $' . $request->price;
                    $payment->save();
                    $data = [
                        'status' => 1,
                        'msg' => 'Ingreso del dinero a la cuenta con exito'
                    ];
                } else {
                    $data = [
                        'status' => 0,
                        'msg' => 'Ingreso del dinero a la cuenta con exito'
                    ];
                }
                break;
            case 'returnPayShift':
                // este caso es para el retorno de dinero por cancelar turno con anticipacion
                $account = new AccountController();
                $request->merge(['reason' => 'payAccount']);
                $resultAccount = $account->updateBalance($request);
                if ($resultAccount['approved']) {
                    // verificamos que la cuenta se actualizara con la devolucion del dinero
                    $payment = new Payment();
                    $payment->account_id = auth()->user()->account->id;
                    $payment->method = $request->method;
                    $payment->description = 'Turno cancelado, devolucion $' . $request->price;
                    $payment->save();
                    $data = [
                        'approved' => true,
                        'msg' => 'Cancelacion y devolucion del dinero con exito'
                    ];
                } else {
                    $data = [
                        'approved' => false,
                        'msg' => 'ERROR, cuenta no actualizada'
                    ];
                }
                return $data; //caso excepcional, retornamos ya que el llamado fue realizado de otro controlador
                break;
            default:
                $data = [
                    'status' => 0,
                    'msg' => 'razon no justificada'
                ];
        }
        return response()->json($data);
    }
}
