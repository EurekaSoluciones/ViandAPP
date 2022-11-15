<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $table = "articulos";
    protected $perPage = 20;

    static $rules = [
        'descripcion' => 'required|max:50|unique:articulos,descripcion'
    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['descripcion','activo'];

    public static function devolverArrForCombo()
    {
        $items=Articulo::all()->sortBy('descripcion')->pluck('descripcion', 'id')->toArray();
        return $items;

    }

    public static function devolverArticuloxId($id)
    {
        $item=Articulo::where ('id', $id)->first();
        return $item;

    }
    //El artículo vianda es del de id=1
    public static function devolverArticuloVianda()
    {
        $item=Articulo::where ('id', 2)->first();
        return $item;

    }

    //El artículo desayuno es del de id=2
    public static function devolverArticuloDesayuno()
    {
        $item=Articulo::where ('id', 1)->first();
        return $item;

    }
}
