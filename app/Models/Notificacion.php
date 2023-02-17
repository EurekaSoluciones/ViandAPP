<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{

    use HasFactory;

    protected $perPage = 30;

    protected $table = "notificaciones";

    static $rules = [ 'titulo'=> 'required', 'descripcion' => 'required',

    ];

    protected $fillable = ['titulo','usuario_id','fecha','descripcion'];

    public function personas()
    {
        return $this->hasMany(NotificacionPersona::class, 'notificacion_id')->orderBy('id','asc');
    }

    public function personasqueleyeron()
    {
        return $this->hasMany(NotificacionPersona::class, 'notificacion_id')->where('leido',true)->orderBy('id','asc');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function dePersona($persona_id)
    {

        $pedidos= Notificacion::
                 whereHas('personas', function($query) use ($persona_id) {
            $query->where('persona_id', $persona_id)
          ;
        })->orderBy('id', 'DESC')->get();

        return $pedidos;
    }


    public function scopeSinLeer($query, $persona_id)
    {
        $query->whereHas('personas', function($query) use ($persona_id) {
            $query->where('persona_id', $persona_id)
            ->where('leido',0);
        })->get();
    }
    public function scopeDePersona($query, $persona_id)
    {
        $query->whereHas('personas', function($query) use ($persona_id) {
            $query->where('persona_id', $persona_id);
        })->get();
    }

    public function scopeDesdeFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereDate( 'fecha', '>=', $fecha->startOfDay());
    }
    public function scopeHastaFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereDate( 'fecha', '<=', $fecha->endOfDay());
    }
    public function getFechaAttribute($fecha)
    {
        return new Carbon($fecha);

    }

}
