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

        $stock=$this->stockActual()->get();

        $totalViandas = $stock->where('articulo_id', config('global.ART_Vianda'))->sum('saldo');
        $totalDesayunos = $stock->where('articulo_id', config('global.ART_Desayuno'))->sum('saldo');

        return ['id'=>$this->id,
            'apellido'=> ucwords(strtolower($this->apellido)),
            'nombre'=>ucwords(strtolower( $this->nombre)),
            'apellidoYNombre'=>ucwords(strtolower($this->fullname)),
            'dni'=>$this->dni,
            'cuit'=>$this->cuit,
            'qr' =>$this->qr,
            'stockViandas'=>$totalViandas,
            'stockDesayunos'=>$totalDesayunos
        ];


    }
}
