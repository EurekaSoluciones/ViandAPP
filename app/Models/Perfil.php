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
}
