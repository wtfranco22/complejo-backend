<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        for($i=0;$i<7;$i++){
            $day = new Day();
            $day->name = $dias[$i];
            $day->active = true;
            $day->description = 'descripcion';
            $day->save();
        }
    }
}
