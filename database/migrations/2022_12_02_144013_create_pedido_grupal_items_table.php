<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoGrupalItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidosgrupales_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidogrupal_id')->unsigned()->references('id')->on('pedidosgrupales');
            $table->bigInteger('persona_id')->unsigned()->references('id')->on('personas');
            $table->bigInteger('articulo_id')->unsigned()->references('id')->on('articulos');
            $table->integer('cantidad');
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
        Schema::dropIfExists('pedidosgrupales_items');
    }
}
