<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valores=["22664811|Claudia Carrasco|1",
            "24784660|Daniel Dolz|2",
            "23494449|Fabian Parada|3",
            "25255369|Marcela Carrasco|4" ];



        foreach ($valores as $valor)
        {
            $datos =explode('|', $valor);

            \App\Models\User::create([
                'email'=>$datos[0],
                'name'=>$datos[1],
                'password'=>Hash::make($datos[0]), //Hash::make(substr( $datos[0], strlen($datos[0])-4, 4)),
                'perfil_id'=>$datos[2],
            ]);

        }
    }
}
