<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stock extends Model
{
    use HasFactory;

    protected $table = "stock";
    protected $perPage = 30;


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['articulo_id','persona_id', 'fechadesde','fechahasta', 'cc','stock','saldo'];

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Personas');
    }

    public function getArticuloDescripcionAttribute()
    {
        return $this->articulo->descripcion;

    }

    public function scopeDisponible($query, $persona_id, $fecha, $articulo_id)
    {
        DB::enableQueryLog();
        $query->where('persona_id', $persona_id)
            ->where('articulo_id',$articulo_id)
            ->whereDate( 'fechadesde', '>=', $fecha)
            ->whereDate( 'fechahasta', '<=', $fecha)->get();
        //dd(DB::getQueryLog());
    }

    public function devolverStock($persona_id, $fecha, $articulo_id)
    {
        //DB::enableQueryLog();
        $stock= Stock::where('persona_id', $persona_id)
            ->where('articulo_id',$articulo_id)
            ->whereDate( 'fechadesde', '<=', $fecha->formatLocalized('%Y-%m-%d'))
            ->whereDate( 'fechahasta', '>=', $fecha->formatLocalized('%Y-%m-%d'))->first();
        //dd(DB::getQueryLog());
        return $stock;
    }

    public function devolverStockdePersona($persona_id, $fecha)
    {
        //DB::enableQueryLog();
        $stock= Stock::where('persona_id', $persona_id)
            ->whereDate( 'fechadesde', '<=', $fecha->formatLocalized('%Y-%m-%d'))
            ->whereDate( 'fechahasta', '>=', $fecha->formatLocalized('%Y-%m-%d'))->get();
        //dd(DB::getQueryLog());
        return $stock;
    }


    public function getFechaDesdeAttribute($fecha)
    {
        return new Carbon($fecha);

    }

    public function getFechaHastaAttribute($fecha)
    {
        return new Carbon($fecha) ;

    }
}
