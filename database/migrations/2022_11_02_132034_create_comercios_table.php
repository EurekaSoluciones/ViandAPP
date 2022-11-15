<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComerciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comercios', function (Blueprint $table) {
            $table->id();
            $table->string('cuit',50);
            $table->string('razonsocial',50);
            $table->string('nombrefantasia',50);
            $table->string('domicilio',100)->nullable($value = true);
            $table->string('observaciones',500)->nullable($value = true);
            $table->boolean('activo')->default(1);
            $table->dateTime('fechabaja')->nullable($value = true);
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
        Schema::dropIfExists('comercios');
    }
}
