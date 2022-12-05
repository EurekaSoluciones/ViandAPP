<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoGrupalItem extends Model
{
    use HasFactory;
    protected $table = "pedidosgrupales_items";
    protected $perPage = 30;

    protected $fillable = ['pedidogrupal_id','persona_id','articulo_id','cantidad'];

    public function pedidogrupal()
    {
        return $this->belongsTo('App\Models\PedidoGrupal');
    }

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona');
    }
}
