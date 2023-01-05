<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreLote extends Model
{
    use HasFactory;

    protected $perPage = 30;

    protected $table = "cierre_lotes";

    static $rules = [
        'comercio_id' => 'required',

    ];

    protected $fillable = ['comercio_id','fecha', 'observaciones','usuario_id', 'visado', 'usuariovisa_id'];

    public function movimientos()
    {
        return $this->hasMany(StockMovimiento::class, 'cierrelote_id')->orderBy('id','asc');
    }

    public function viandas()
    {
        return $this->hasMany(StockMovimiento::class, 'cierrelote_id')->where('articulo_id',2)->orderBy('id','asc');
    }
    public function desayunos()
    {
        return $this->hasMany(StockMovimiento::class, 'cierrelote_id')->where('articulo_id',1)->orderBy('id','asc');
    }

    public function comercio()
    {
        return $this->belongsTo('App\Models\Comercio');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function devolverCierresDeLoteSinVisar()
    {

        $cierres= CierreLote::where('visado',0)
            ->orderBy('fecha', 'ASC')
            ->get();

        return $cierres;
    }

    public function devolverCierresDeLoteDeComercio($comercio_id, $fechaDesde)
    {

        $cierres= CierreLote::where('comercio_id',$comercio_id)
            ->whereDate('fecha',">=", $fechaDesde)
            ->orderBy('fecha', 'ASC')
            ->get();

        return $cierres;
    }

    public function devolverCierresDeLote( $fechaDesde)
    {

        $cierres= CierreLote::whereDate('fecha',">=", $fechaDesde)
            ->orderBy('fecha', 'ASC')
            ->get();

        return $cierres;
    }



    public function gettimeclassAttribute()
    {
        $clase = "";
        $now = Carbon::now();

        if ($now->diffInDays($this->fecha) <= 2)
            $clase = "badge bg-danger";
        else
            if ($now->diffInDays($this->fecha) <= 7)
                $clase = "badge bg-warning";
            else
                $clase = "badge bg-success";

        return $clase;
    }
}
