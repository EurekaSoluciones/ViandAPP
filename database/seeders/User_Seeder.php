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
        $valores=["30568457451|ADMINISTRACION SACDE|1",
            "30710398433|EUREKA|1"];



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
