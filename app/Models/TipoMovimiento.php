<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;
    protected $table = "tipo_movimientos";
    protected $perPage = 20;

    static $rules = [
        'descripcion' => 'required|max:50|unique:tipo_movimientos,descripcion'
    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['descripcion','operacion'];

    public static function devolverArrForCombo()
    {
        $items=TipoMovimiento::all()->sortBy('descripcion')->pluck('descripcion', 'id')->toArray();
        return $items;

    }

    //Devolver un tipo de movimiento
    public static function devolverMovimiento($tipoMovimiento_id)
    {
        $tipomovimiento=TipoMovimiento::where ('id', $tipoMovimiento_id)->first();
        return $tipomovimiento;

    }
}
