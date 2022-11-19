<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierreLotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierre_lotes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comercio_id')->unsigned()->references('id')->on('comercios');
            $table->bigInteger('usuario_id')->unsigned()->references('id')->on('users');
            $table->dateTime('fecha');
            $table->string('observaciones',200)->nullable($value = true);
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
        Schema::dropIfExists('cierre_lotes');
    }
}
