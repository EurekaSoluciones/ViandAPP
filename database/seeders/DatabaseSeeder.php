<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $tables=['tipo_movimientos',
            'articulos',
            'perfiles',
            'personas',
            'cierre_lotes',
            'comercios',
            'stock',
            'stock_movimientos'
        ];

        $this->truncateTables($tables);
        $this->call(Perfiles_Seeder::class);
        $this->call(User_Seeder::class);
        $this->call(TipoMovimiento_Seeder::class);
        $this->call(Articulo_Seeder::class);
    }


    public function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Desactivamos la revisión de claves foráneas

        foreach($tables as $table)
        {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Desactivamos la revisión de claves foráneas
    }
}
