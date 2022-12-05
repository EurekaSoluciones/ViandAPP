<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('apellido', 50);
            $table->string('nombre', 50);
            $table->integer('dni')->unique();
            $table->string('cuit', 11)->unique();
            $table->string('cc', 20);
            $table->string('situacion', 10);
            $table->boolean('activo')->default(1);
            $table->dateTime('fechabaja')->nullable($value = true);
            $table->string('qr', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
