<?php

namespace App\Http\Controllers;

// use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role', 'account')->get();
        return response()->json([
            'status' => 1,
            'msg' => 'Listado de users',
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'lastname' => 'required',
            'dni' => 'required',
            'phone' => 'required|unique',
            'password' => 'required'
        ]);
        $role = Role::find($request->role_id);
        if(isset($role->id)){
            $user = new User();
            $user->active = true;
            $user->role_id = $role->id;
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->dni = $request->dni;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            $accountUser = new AccountController();
            $description = isset($request->description) ? $request->description : $role->description;
            $accountUser->createAccount($user->id,$description);
            $data = [
                'status' => 1,
                'msg' => 'Registro con exito'
            ];
        }else{
            $data = [
                'status' => 0,
                'msg' => 'Error, el rol no existe'
            ];
        }
        return response()->json($data);
    }*/

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('account', 'role')->find($id);
        if (isset($user->id)) {
            $data = [
                'status' => 1,
                'user' => $user
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => '¡Usuario no encontrado!'
            ];
        }
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (isset($user->id)) {
            $user->active = isset($request->active) ? $request->active : $user->active;
            $user->rol_id = isset($request->rol_id) ? $request->rol_id : $user->rol_id;
            $user->name = isset($request->name) ? $request->name : $user->name;
            $user->lastname = isset($request->lastname) ? $request->lastname : $user->lastname;
            $user->dni = isset($request->dni) ? $request->dni : $user->dni;
            $user->phone = isset($request->phone) ? $request->phone : $user->phone;
            $user->save();
            $data = [
                'status' => 1,
                'msg' => '¡Actualizado con exito!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => 'Error, usuario no encontrado'
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (isset($user->id)) {
            $user->active = false;
            $user->save();
            $data = [
                'status' => 1,
                'msg' => '¡Usuario deshabilitado!'
            ];
        } else {
            $data = [
                'status' => 0,
                'msg' => '¡Error, usuario no encontrado!'
            ];
        }
        return response()->json($data);
    }
}
