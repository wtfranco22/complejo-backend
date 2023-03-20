<?php

namespace Database\Seeders;

use App\Models\Hour;
use Illuminate\Database\Seeder;

class HourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $horas=['14:30','16:00','17:30','19:00','20:30','22:00'];
        for($i=0;$i<6;$i++){
            $hour = new Hour();
            $hour->hour = $horas[$i];
            $hour->active = true;
            $hour->description = 'descripcion';
            $hour->save();
        }
    }
}
