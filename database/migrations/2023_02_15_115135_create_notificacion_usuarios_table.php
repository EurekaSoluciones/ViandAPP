<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones_personas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notificacion_id')->unsigned()->references('id')->on('notificaciones');
            $table->bigInteger('persona_id')->unsigned()->references('id')->on('personas');
            $table->boolean('leido')->default(0);
            $table->dateTime('fechalectura')->nullable(true);
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
        Schema::dropIfExists('notificaciones_personas');
    }
}
