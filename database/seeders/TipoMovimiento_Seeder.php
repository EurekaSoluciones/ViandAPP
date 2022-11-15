<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TipoMovimiento_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valores=['Asignación|INC',
        'Consumo en Comercio|DEC',
        'Anulación Consumo|INC',
        'Vencimiento|DEC',
        'Baja|DEC',
        'Aumento|INC'
    ];

        foreach ($valores as $valor)
        {
            $datos =explode('|', $valor);

            \App\Models\TipoMovimiento::create([
                'descripcion'=>$datos[0],
                'operacion'=>$datos[1],
            ]);
        }
    }
}
