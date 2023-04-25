<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cancha1 = new Court();
        $cancha1->name='1';
        $cancha1->active=true;
        $cancha1->active_at=null;
        $cancha1->inactive_at=null;
        $cancha1->image_url='inserte una imagen';
        $cancha1->description='es la cancha 1 que tiene alfombra roja y cuenta con mayor espacio de salida';
        $cancha1->save();

        $cancha2 = new Court();
        $cancha2->name='2';
        $cancha2->active=true;
        $cancha2->active_at=null;
        $cancha2->inactive_at=null;
        $cancha2->image_url='inserte una imagen x2';
        $cancha2->description='es la cancha 2 que se encuentra a la mitad de las canchas, cuenta con alfombra verde';
        $cancha2->save();

        $cancha3 = new Court();
        $cancha3->name='3';
        $cancha3->active=true;
        $cancha3->active_at=null;
        $cancha3->inactive_at=null;
        $cancha3->image_url='inserte una imagen x3';
        $cancha3->description='es la cancha 3 que tiene alfombra azul y se encuentra al fondo, al lado de la pileta';
        $cancha3->save();

        $cancha4 = new Court();
        $cancha4->name='4';
        $cancha4->active=true;
        $cancha4->active_at=null;
        $cancha4->inactive_at=null;
        $cancha4->image_url='inserte una imagen x4';
        $cancha4->description='es la cancha 4 que tiene alfombra azul tambien pero esta del lado del ingreso a las canchas, esta cancha es un poco mas lenta que las demas';
        $cancha4->save();

        $cancha3 = new Court();
        $cancha3->name='5';
        $cancha3->active=true;
        $cancha3->active_at=null;
        $cancha3->inactive_at=null;
        $cancha3->image_url='inserte una imagen x infinito';
        $cancha3->description='es la cancha 5 que tiene alfombra rosa, cuenta con blindex polarizados para la luz y es mas rapida que las demas';
        $cancha3->save();
    }
}
