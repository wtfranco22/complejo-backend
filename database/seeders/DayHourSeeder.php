<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\DayHour;
use App\Models\Hour;
use Illuminate\Database\Seeder;

class DayHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $dias, $horas es una coleccion de todas las tuplas que hay en la base de datos
        $dias = Day::all();
        $horas = Hour::all();
        foreach($dias as $dia){
            foreach($horas as $hora){
                $day_hour = new DayHour();
                $day_hour->day_id = $dia->id;
                $day_hour->hour_id = $hora->id;
                $day_hour->save();
            }
        }
    }
}
