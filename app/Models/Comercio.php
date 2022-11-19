<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comercio extends Model
{
    use HasFactory;

    protected $table = "comercios";
    protected $perPage = 30;

    static $rules = [
        'razonsocial' => 'required|max:50|unique:comercios,razonsocial',
        'cuit' => 'required|max:13|unique:comercios,cuit',
        'nombrefantasia'=> 'required|max:50',

    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['razonsocial','cuit', 'nombrefantasia','activo', 'observaciones','domicilio','fechabaja'];

    public static function  devolverComercioxCuit($cuit)
    {
        $query = Comercio::where('cuit','=', $cuit)
            ->first() ;
        return  $query ;
    }

    public static function  devolverComercioxId($id)
    {
        $query = Comercio::where('id','=', $id)
            ->first() ;
        return  $query ;
    }

    public function devolverConsumosPendientesDeLiquidar($fecha, $comercioId)
    {
        //DB::enableQueryLog();
        $consumos= StockMovimiento::where('tipomovimiento_id',config('global.TM_Consumo'))
            ->whereNull('cierrelote_id')
            ->where('estado', 'PENDIENTE')
            ->where( 'comercio_id', '=', $comercioId)
            ->whereDate( 'fecha', '<', $fecha)
            ->get();

        //dd(DB::getQueryLog());
        return $consumos;
    }

    public static function devolverArrForCombo()
    {
        $comercios=Comercio::orderBy('nombrefantasia')->get()->pluck('nombrefantasia', 'id')->toArray();
        return $comercios;
    }

    /**
     * Genera un nuevo cierre de lote con los consumos pendientes
     *
     * @param string $observaciones
     * @param array $movimientos
     * @param User $usuario
     * @return \Illuminate\Http\Response
     */
    public function CerrarLote($observaciones, $movimientos, $usuario)
    {
        try
        {
            DB::beginTransaction();

            /*Tengo que crear el lote*/
            $cierreLote=CierreLote::create(
                [
                    'comercio_id'=>$this->id,
                    'fecha'=>new Carbon(now()),
                    'observaciones'=>$observaciones,
                    'usuario_id'=>$usuario->id]);

            /*Actualizar los movimientos que lo incluyen*/
            $movimientos=StockMovimiento:: whereIn('id',$movimientos);
            $movimientos->update(['cierrelote_id'=>$cierreLote->id,
                            'estado'=>'CERRADO']);
            DB::commit();

            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }

    }


    ///SCOPES
    public function scopeRazonSocial($query, $search)
    {
        if ($search!="")
            $query->where('razonsocial', 'LIKE', '%'.$search.'%');
    }
    public function scopeNombreFantasia($query, $search)
    {
        if ($search!="")
            $query->where('nombrefantasia','LIKE', '%'.$search.'%');
    }

    public function scopeCuit($query, $search)
    {
        if ($search!="")
            $query->where('cuit',$search);
    }

    public function scopeSoloActivos($query, $search)
    {
        if ($search!=null )
            $query->where('activo',true);
    }

}
