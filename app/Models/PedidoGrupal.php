<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoGrupal extends Model
{
    use HasFactory;
    protected $table = "pedidosgrupales";
    protected $perPage = 30;


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['comercio_id','fecha','cantidad','observaciones','usuario_id','fechacumplido','usuariocumple_id'];

    public function items()
    {
        return $this->hasMany(PedidoGrupalItem::class, 'pedidogrupal_id')->orderBy('id','asc');
    }

    public function getCantidadDesayunosAttribute($fecha)
    {
        return $this->items()->where('articulo_id', 1)->sum('cantidad');

    }

    public function getCantidadViandasAttribute($fecha)
    {
        return $this->items()->where('articulo_id', 2)->sum('cantidad');

    }
    public function comercio()
    {
        return $this->belongsTo('App\Models\Comercio');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function usuariocumple()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function devolverUltimosPedidosgrupales()
    {

        $pedidos= PedidoGrupal::
            whereDate( 'fecha', '>=',  Carbon::now()->addDays(-3))
             ->orderBy('id', 'DESC')
            ->get();

        return $pedidos;
    }

    public function devolverPedidosgrupales($comercio)
    {

        $pedidos= PedidoGrupal::whereNull('usuariocumple_id')
            ->where('comercio_id', $comercio->id)
            ->orderBy('id', 'DESC')
            ->get();

        return $pedidos;
    }

    public function getFechaAttribute($fecha)
    {
        return new Carbon($fecha);

    }

    public function getestadoclassAttribute()
    {
        $clase = "";
        switch(strtoupper($this->estado))
        {
            case "GENERADO":
                $clase = "fas fa-edit text-warning";
                break;
            case "CONFIRMADO":
                $clase = "fa fa-thumbs-up text-success";
                break;
            case "RECHAZADO":
                $clase = "fa fa-thumbs-down text-danger";
                break;
        }

        return $clase;
    }
}
