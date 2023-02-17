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

    public function dePersona($persona_id)
    {

        $notificaciones= NotificacionPersona::where('persona_id', $persona_id)
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

    public function getFechaLecturaAttribute($fecha)
    {
        if ($fecha==null)
            return null;

        return new Carbon($fecha);

    }


}
