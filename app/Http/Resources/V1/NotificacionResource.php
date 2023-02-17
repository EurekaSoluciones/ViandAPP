<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->notificacion->id,
            "fecha"=>$this->notificacion->fecha,
            "titulo"=>$this->notificacion->titulo,
            "descripcion"=>$this->notificacion->descripcion,
            "leida"=>$this->leido,
            "fechalectura"=>$this->fechalectura,
        ];
    }
}
