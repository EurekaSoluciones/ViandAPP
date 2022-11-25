<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
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
            'apellido'=> $this->apellido,
            'nombre'=> $this->nombre,
            'apellidoYNombre'=>$this->fullname,
            'dni'=>$this->dni,
            'cuit'=>$this->cuit,
            'qr' =>$this->qr,
            'stockActual'=> StockResource::collection($this->stockActual()->get())
        ];


    }
}
