<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoGrupalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidosgrupales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comercio_id')->unsigned()->references('id')->on('comercios');
            $table->dateTime('fecha');
            $table->integer('cantidad');
            $table->string('observaciones',200)->nullable($value = true);
            $table->bigInteger('usuario_id')->unsigned()->nullable(true)->references('id')->on('users');
            $table->dateTime('fechacumplido')->nullable(true);
            $table->bigInteger('usuariocumple_id')->unsigned()->nullable(true)->references('id')->on('users');
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
        Schema::dropIfExists('pedidosgrupales');
    }
}
