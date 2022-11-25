<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class StockParaComercioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return ['articulo_id'=> $this->articulo_id,
            'articulo'=> $this->articulo->descripcion,
            'saldo'=>$this->saldo
        ];

    }
}
