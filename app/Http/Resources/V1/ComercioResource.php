<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ComercioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ['id'=>$this->id,
            'razonSocial'=>ucfirst(strtolower($this->razonsocial)),
            'nombreFantasia'=>ucfirst(strtolower($this->nombrefantasia)),
            'cuit'=>$this->cuit,
            'domicilio' =>$this->domicilio];
    }
}
