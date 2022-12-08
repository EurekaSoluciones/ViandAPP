<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;
    protected $table = 'perfiles';

    protected $fillable = [
        'descripcion',
    ];

    public static function devolverArrForCombo()
    {
        $items=Perfil::all()->sortBy('descripcion')->pluck('descripcion', 'id')->toArray();
        return $items;

    }


    public function geticonclassAttribute()
    {
        $clase = "";
        switch(strtoupper($this->id))
        {
            case config('global.PERFIL_Admin'):
                $clase="fas fa-user-tie";
                break;
            case config('global.PERFIL_Operador'):
                break;
            case config('global.PERFIL_Persona'):
            case config('global.PERFIL_Usuario'):
            case config('global.PERFIL_Empleado'):
                $clase="fas fa-user";
                break;
            case config('global.PERFIL_Comercio'):
                $clase="fas fa-store";
                break;
        }

        return $clase;
    }

}
