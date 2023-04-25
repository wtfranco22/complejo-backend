<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['duenio','empleado','cliente','cliente+'];
        for($i=0;$i<4;$i++){
            $rol = new Role();
            $rol->name = $roles[$i];
            $rol->description = 'descripcion';
            $rol->save();
        }
    }
}
