<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class StockMovimientoResource extends JsonResource
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
            "articulo_id"=>$this->articulo_id,
            "articulo"=>$this->articulo->descripcion ,
            "persona_id"=>$this->persona_id,
            "persona"=>ucfirst(strtolower($this->persona->fullname)),
            "tipomovimiento_id"=>$this->tipomovimiento_id,
            "tipomovimiento"=>$this->tipomovimiento->descripcion,
            "comercio_id"=>$this->comercio_id,
            "comercio"=>ucfirst(strtolower($this->comercio->nombrefantasia)),
            "cc"=>$this->cc,
            "fecha"=>$this->fecha,
            "cantidad"=>$this->cantidad,
            "operacion"=>$this->operacion,
            "usuario_id"=>$this->usuario_id,
            "usuario"=>ucfirst(strtolower($this->usuario->name)),
            "observaciones"=>$this->observaciones,
            "estado" =>ucfirst(strtolower($this->estado))];

    }
}
