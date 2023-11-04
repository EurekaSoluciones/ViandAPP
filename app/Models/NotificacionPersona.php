<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionPersona extends Model
{

    use HasFactory;

    protected $perPage = 30;

    protected $table = "notificaciones_personas";

    protected $fillable = ['notificacion_id','persona_id','leido','fechalectura'];

    public function notificacion()
    {
        return $this->belongsTo('App\Models\Notificacion');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona');
    }

    public static function dePersona($persona_id)
    {

        $notificaciones= NotificacionPersona::where('persona_id', $persona_id)

            ->whereHas('notificacion', function($q){
            $q->whereDate('fecha','>=', Carbon::now()->addMonth(-2));})
            ->orderBy('id', 'DESC')->get();

        return $notificaciones;
    }

    public function noLeidasDePersona($persona_id)
    {

        $notificaciones= NotificacionPersona::where('persona_id', $persona_id)
            ->where('leido',0)
            ->orderBy('id', 'DESC')->get();

        return $notificaciones;
    }

    public function scopeDePersona($query, $persona_id)
    {
         $query->where('persona_id', $persona_id);
    }

    public function scopeDesdeFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereHas('notificacion', function($q) use ($fecha)
            {
                $q->whereDate('fecha','>=', $fecha->startOfDay());});


    }
    public function scopeHastaFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereHas('notificacion', function($q) use ($fecha)
            {
                $q->whereDate('fecha','<=', $fecha->endOfDay());});

    }

    public function getFechaLecturaAttribute($fecha)
    {
        if ($fecha==null)
            return null;

        return new Carbon($fecha);

    }


}
