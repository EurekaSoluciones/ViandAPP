<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('articulo_id')->unsigned()->references('id')->on('articulos');
            $table->bigInteger('persona_id')->unsigned()->references('id')->on('personas');
            $table->dateTime('fechadesde');
            $table->dateTime('fechahasta');
            $table->string('cc',20);
            $table->integer('stock');
            $table->integer('saldo');
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
        Schema::dropIfExists('stock');
    }
}
