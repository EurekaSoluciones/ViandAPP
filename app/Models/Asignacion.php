<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = "asignaciones";
    protected $perPage = 30;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
        protected $fillable = ['dni','situacion', 'apellidoynombre','cc', 'desayunos','viandas'];


}

