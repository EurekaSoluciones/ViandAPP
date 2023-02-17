<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoGrupalResource extends JsonResource
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
            "id"=>$this->id,
            "comercio_id"=>$this->comercio_id,
            "comercio"=>ucwords(strtolower($this->comercio->nombrefantasia)),
            "fecha"=>$this->fecha,
            "usuario_id"=>$this->usuario_id,
            "usuario"=>ucwords(strtolower($this->usuario->name)),
            "observaciones"=>$this->observaciones,
            "cantidadViandas"=>intval($this->cantidadviandas),
            "cantidadDesayunos"=>intval($this->cantidaddesayunos),
        ];
    }
}
