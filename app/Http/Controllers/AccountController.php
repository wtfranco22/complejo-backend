<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Listamos todas las cuentas con su respectivo usuario
     * @return Response
     */
    public function index()
    {
        // $accounts es la coleccion de elementos Account con su usuario
        $accounts = Account::with('user', 'user.role')->get();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de cuentas',
            'accounts' => $accounts
        ]);
    }

    /**
     * Agregamos una nueva cuenta al momento de verificar sus datos el usuario
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        /**
         * $user es un elemento User registrado en el sistema
         * $account es un elemento Account que contiene los datos para una cuenta de un usuario verificado
         * $data es el mensaje informativo del proceso para el cliente
         */
        $request->validate([
            'user_id' => 'required',
            'code' => 'required'
        ]);
        $user = User::find($request->user_id);
        if (isset($user->id)) {
            if ($request->code == 123) {
                $account = new Account();
                $account->user_id = $request->user_id;
                $account->balance = 0.0;
                $account->description = isset($request->description) ? $request->description : 'Cuenta nueva';
                $account->save();
                $data = [
                    'status' => 1,
                    'msg' => 'Cuenta verificada'
                ];
            } else {
                $data = [
                    'status' => 0,
                    'msg' => 'Error, codigo invalido'
                ];
            }
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, usuario no encontrado'
            ];
        }
        return response()->json($data);
    }

    /**
     * Mostramos todos los datos que tiene la cuenta con su usuario respectivamente
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * $account es un elemento Account encontrado por su id $id
         * $data es el mensaje informativo del proceso para el cliente
         */
        $account = Account::with('user')->find($id);
        if (isset($account->id)) {
            $data = [
                'status' => 1,
                'msg' => 'Datos de la cuenta',
                'account' => $account
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Cuenta no encontrada'
            ];
        }
        return response()->json($data);
    }

    /**
     * Validar cuenta es para verificar el usuario y poder comenzar a solicitar turnos
     * @param Request $request
     * @return Response
     */
    public function validateAccount(Request $request)
    {
        $request->validate(['user_phone' => 'required']);
        $user = User::where('phone', $request->user_phone)->get();
        return response()->json([
            'status' => 1,
            'msg' => 'Usuario encontrado',
            'user_id' => $user->id
        ]);
    }

    /**
     * Actualizar el saldo de la cuenta depende de distintos motivos:
     * por pagar un turno, pagar deuda o ingresar plata a favor, cancelar un turno a tiempo
     * sino por pasar en administracion a pagar la deuda de la cuenta
     * @param Request $request
     * @return Array
     */
    public function updateBalance(Request $request)
    {
        /**
         * $account es la cuenta la cual sera actualizado el saldo
         * $data es el mensaje informativo del proceso para el cliente
         */
        $request->validate([
            'price' => 'required',
            'reason' => 'required'
        ]);
        switch ($request->reason) {
            case 'payShift':
                // caso del pago de un turno solicitado por el mismo usuario
                if (isset(auth()->user()->phone_verified_at)) {
                    // verificamos si el usuario esta verificado
                    if ((auth()->user()->account->balance - $request->price) > -10000) {
                        // verificamos si el usuario excede el limite de deudas
                        $account = Account::find(auth()->user()->account->id);
                        $account->balance = $account->balance - $request->price;
                        $account->save();
                        $data = [
                            'approved' => true,
                        ];
                    } else {
                        $data = [
                            'approved' => false,
                            'msg' => 'ERROR, saldo de la cuenta es de $' . auth()->user()->account->balance
                        ];
                    }
                } else {
                    $data = [
                        'approved' => false,
                        'msg' => 'ERROR, el usuario no esta verificado, por lo tanto no posee cuenta'
                    ];
                }
                break;
            case 'payAccount':
                // el caso es cuando el usuario agrega o paga su deuda de la cuenta por si mismo
                $account = Account::find(auth()->user()->account->id);
                $account->balance = $account->balance + $request->price;
                $data = ['approved' => $account->save()];
                break;
            case 'payAccountAdmin':
                // este caso es cuando el usuario pasa por administracion a pagar la deuda de la cuenta
                $account = Account::find($request->account_id);
                $account->balance = $account->balance + $request->price;
                $data = ['approved' => $account->save()];
                break;
            default:
                $data = [
                    'approved' => false,
                    'msg' => 'ERROR, razon de la consulta no valida'
                ];
        }
        return $data;
    }
}
