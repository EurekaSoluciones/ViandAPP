<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Articulo_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valores=[
            'Desayuno','Vianda'
        ];

        foreach ($valores as $valor)
        {
            $datos =explode('|', $valor);

            \App\Models\Articulo::create([
                'descripcion'=>$datos[0],
            ]);
        }
    }
}
