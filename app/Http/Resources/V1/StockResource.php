<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'articulo_id'=> $this->articulo_id,
            'articulo'=> $this->articulo->descripcion,
            'fechaDesde'=> $this->fechadesde,
            'fechaHasta'=> $this->fechahasta,
            'stock'=>$this->stock,
            'saldo'=>$this->saldo
        ];
    }
}
