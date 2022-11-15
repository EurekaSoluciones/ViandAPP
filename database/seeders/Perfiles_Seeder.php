<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Perfiles_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valores=['Administrador',
            'Operador',
            'Usuario',
            'Comercio',
        ];

        foreach ($valores as $valor)
        {
            $datos =explode('|', $valor);

            \App\Models\Perfil::create([
                'descripcion'=>$datos[0],

            ]);
        }
    }
}
