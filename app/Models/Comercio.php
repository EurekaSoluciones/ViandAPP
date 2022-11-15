<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    use HasFactory;

    protected $table = "comercios";
    protected $perPage = 30;

    static $rules = [
        'razonsocial' => 'required|max:50|unique:comercios,razonsocial',
        'cuit' => 'required|max:13|unique:comercios,cuit',
        'nombrefantasia'=> 'required|max:50',

    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['razonsocial','cuit', 'nombrefantasia','activo', 'observaciones','domicilio','fechabaja'];

    public static function  devolverComercioxCuit($cuit)
    {
        $query = Comercio::where('cuit','=', $cuit)
            ->first() ;
        return  $query ;
    }

    public static function  devolverComercioxId($id)
    {
        $query = Comercio::where('id','=', $id)
            ->first() ;
        return  $query ;
    }

    public static function devolverArrForCombo()
    {
        $comercios=Comercio::orderBy('nombrefantasia')->get()->pluck('nombrefantasia', 'id')->toArray();
        return $comercios;
    }

    public function scopeRazonSocial($query, $search)
    {
        if ($search!="")
            $query->where('razonsocial', 'LIKE', '%'.$search.'%');
    }
    public function scopeNombreFantasia($query, $search)
    {
        if ($search!="")
            $query->where('nombrefantasia','LIKE', '%'.$search.'%');
    }

    public function scopeCuit($query, $search)
    {
        if ($search!="")
            $query->where('cuit',$search);
    }

    public function scopeSoloActivos($query, $search)
    {
        if ($search!=null )
            $query->where('activo',true);
    }

}
