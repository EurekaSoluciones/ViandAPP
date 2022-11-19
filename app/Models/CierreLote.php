<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreLote extends Model
{
    use HasFactory;

    protected $perPage = 30;

    static $rules = [
        'comercio_id' => 'required',

    ];

    protected $fillable = ['comercio_id','fecha', 'observaciones','usuario_id'];

    public function movimientos()
    {
        return $this->hasMany(StockMovimiento::class, 'cierrelote_id')->orderBy('id','asc');
    }

    public function comercio()
    {
        return $this->belongsTo('App\Models\Comercio');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }
}
