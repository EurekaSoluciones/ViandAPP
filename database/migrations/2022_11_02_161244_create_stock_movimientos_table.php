<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_movimientos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('articulo_id')->unsigned()->references('id')->on('articulos');
            $table->bigInteger('persona_id')->unsigned()->references('id')->on('personas');
            $table->bigInteger('tipomovimiento_id')->unsigned()->references('id')->on('tipo_movimientos');
            $table->bigInteger('comercio_id')->nullable($value = true)->unsigned()->references('id')->on('comercios');
            $table->string('cc',20);
            $table->dateTime('fecha');
            $table->integer('cantidad');
            $table->string('operacion',3);
            $table->integer('cantidadconsigno');
            $table->bigInteger('usuario_id')->unsigned()->references('id')->on('users');
            $table->string('estado',10)->nullable($value = true);
            $table->string('observaciones', 200)->nullable($value = true);
            $table->bigInteger('cierrelote_id')->nullable($value = true)->unsigned()->references('id')->on('cierre_lotes');
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
        Schema::dropIfExists('stock_movimientos');
    }
}
