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

    public function movimientos()
    {
        return $this->hasMany(StockMovimiento::class, 'comercio_id')->orderBy('id','asc');
    }

    public function cierreslote()
    {
        return $this->hasMany(CierreLote::class, 'comercio_id')->orderBy('id','desc');
    }

    public function devolverConsumosPendientesDeLiquidar($fecha, $comercioId)
    {
        //DB::enableQueryLog();
        $fechaParaAnulados= Carbon::now()->addDays(-15);

        $consumos= StockMovimiento::where(function($query) use ($comercioId, $fecha) {
                    $query->where('estado', 'PENDIENTE')
                    ->where('tipomovimiento_id',config('global.TM_Consumo'))
                    ->where( 'comercio_id', '=', $comercioId)
                    ->whereDate( 'fecha', '<=', $fecha)
                    ->whereNull('cierrelote_id');
                    })
                ->orWhere(function($query) use ($comercioId, $fechaParaAnulados) {
                    $query->where('estado', 'ANULADO')
                    ->where('tipomovimiento_id',config('global.TM_Consumo'))
                    ->where( 'comercio_id', '=', $comercioId)
                    ->whereDate( 'fecha', '>', $fechaParaAnulados);
                    })
            ->join('personas', 'personas.id', '=', 'persona_id')
            ->orderBy('fecha', 'ASC')
            ->orderBy('personas.apellido', 'ASC')
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
     * @param Carbon $fecha
     * @param User $usuario
     * @return \Illuminate\Http\Response
     */
    public function CerrarLote($observaciones, $fecha, $usuario)
    {
        try
        {
            DB::beginTransaction();

            $movimientos= $this->devolverConsumosPendientesDeLiquidar($fecha, $this->id);

            /*Tengo que crear el lote*/
            $cierreLote=CierreLote::create(
                [
                    'comercio_id'=>$this->id,
                    'fecha'=>new Carbon(now()),
                    'observaciones'=>$observaciones,
                    'usuario_id'=>$usuario->id]);

            /*Actualizar los movimientos que lo incluyen*/
            $keys=$movimientos->modelKeys();

           StockMovimiento::whereIn('id', $keys)->update(['cierrelote_id'=>$cierreLote->id,
                'estado'=>'CERRADO']);;

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
