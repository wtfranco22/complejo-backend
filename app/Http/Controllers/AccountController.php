<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::with('user')->get();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de cuentas',
            'accounts' => $accounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createAccount($id, $description)
    {
        $account = new Account();
        $account->user_id = $id;
        $account->balance = 0.0;
        $account->description = $description;
        $account->save();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $account = Account::find($id);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $id)
    {
        $account = Account::find($id);
        if (isset($account->id)) {
            $account->balance = isset($request->balance) ? $request->balance : $account->balance;
            $account->description = isset($request->description) ? $request->description : $account->description;
            $account->save();
            $data = [
                'status' => 1,
                'msg' => 'Registro actualizado'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Cuenta no encontrada'
            ];
        }
        return response()->json($data);
    }
}
