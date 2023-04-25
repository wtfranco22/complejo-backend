<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $persona1 = new Account();
        $persona1->user_id = 1;
        $persona1->balance = 0.0;
        $persona1->description = 'es la cuenta de la cancha, superadmin';
        $persona1->save();

        $persona2 = new Account();
        $persona2->user_id = 2;
        $persona2->balance = 0.0;
        $persona2->description = 'es cuenta privilegiada del señor jona';
        $persona2->save();

        $persona3 = new Account();
        $persona3->user_id = 3;
        $persona3->balance = 0.0;
        $persona3->description = 'cuenta del cliente nuevo';
        $persona3->save();

        $persona4 = new Account();
        $persona4->user_id = 4;
        $persona4->balance = 5000.00;
        $persona4->description = 'cuenta del cliente/familiar con mucha antiguedad';
        $persona4->save();
    }
}
