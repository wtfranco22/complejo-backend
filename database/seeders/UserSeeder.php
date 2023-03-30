<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $persona1 = new User();
        $persona1->role_id = 1;
        $persona1->name = 'complejo';
        $persona1->lastname = 'center';
        $persona1->active = true;
        $persona1->dni = 12345678901;
        $persona1->phone = 5492996017699;
        $persona1->password = Hash::make('complejocenter');
        $persona1->save();

        $persona2 = new User();
        $persona2->role_id = 2;
        $persona2->name = 'jona';
        $persona2->lastname = 'rios';
        $persona2->active = true;
        $persona2->dni = 11222333;
        $persona2->phone = 5492996017699;
        $persona2->password = Hash::make('jonarios');
        $persona2->save();

        $persona3 = new User();
        $persona3->role_id = 3;
        $persona3->name = 'franco';
        $persona3->lastname = 'rodriguez';
        $persona3->active = true;
        $persona3->dni = 33222111;
        $persona3->phone = 5492996017699;
        $persona3->password = Hash::make('francorodriguez');
        $persona3->save();

        $persona4 = new User();
        $persona4->role_id = 4;
        $persona4->name = 'cliente';
        $persona4->lastname = 'plus';
        $persona4->active = true;
        $persona4->dni = 12123123;
        $persona4->phone = 5492996017699;
        $persona4->password = Hash::make('clienteplus');
        $persona4->save();
    }
}
