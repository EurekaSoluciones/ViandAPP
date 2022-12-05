<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovimiento extends Model
{
    use HasFactory;
    protected $table = "stock_movimientos";
    protected $perPage = 30;

    protected $errorMessage="";

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['articulo_id','persona_id', 'fecha', 'tipomovimiento_id',
        'comercio_id', 'cc','cantidad', 'operacion', 'cantidadconsigno', 'usuario_id',
        'observaciones', 'cierrelote_id', 'estado','situacion', 'qr'];

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona');
    }

    public function tipomovimiento()
    {
        return $this->belongsTo('App\Models\TipoMovimiento');
    }

    public function comercio()
    {
        return $this->belongsTo('App\Models\Comercio');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getPersonaFullNameAttribute()
    {
        return $this->persona()->apellido . ' ' . $this->persona()->nombre;
    }

    public static function  devolverStockMovimientoxId($id)
    {
        $query = StockMovimiento::where('id','=', $id)
            ->first() ;
        return  $query ;
    }
    public static function  devolverStockMovimientoNoAnuladoxId($id)
    {
        $query = StockMovimiento::where('id','=', $id)
            ->where('estado','=', 'PENDIENTE')
            ->first() ;
        return  $query ;
    }
    public function scopeAFecha($query, $fecha)
    {
        $fechaTope= Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        if ($fechaTope !=null)
           $query= $query->whereDate( 'fecha', '>=', $fechaTope);
    }

    public function scopeDeComercio($query, $idComercio)
    {
        if ($idComercio !=null)
            $query= $query->where( 'comercio_id', '=', $idComercio);
    }

    public function scopePendientesDeLiquidacion($query)
    {
        $query= $query->whereNull('cierrelote_id')
        ->where('estado', 'PENDIENTE');
    }

    public function scopeConsumos($query)
    {
        $query= $query->where('tipomovimiento_id',config('global.TM_Consumo'));
    }


    public function Asignar($persona, $articulo, $fechadesde, $fechahasta, $cantidad, $cc, $situacion, $observaciones, $usuario)
    {
        try
        {
            DB::beginTransaction();

            $movimiento=StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fechadesde,
                'cc'=>$cc,
                'situacion'=>$situacion,
                'cantidad'=>$cantidad,
                'operacion'=>'INC',
                'cantidadconsigno'=>$cantidad,
                'usuario_id'=>$usuario,
                'tipomovimiento_id'=>config('global.TM_Asignacion'),
                'observaciones'=>$observaciones]);

            Stock::create(
                ['articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fechadesde'=>$fechadesde,
                'fechahasta'=>$fechahasta,
                'situacion'=>$situacion,
                'cc'=>$cc,
                'stock'=>$cantidad,
                'saldo'=>$cantidad]);
            DB::commit();

            return ["exitoso"=>true, "error"=>"", "movimiento_id"=>$movimiento->id];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento_id"=>null];
        }

    }

    public function Consumir($persona, $articulo, $fecha, $cantidad, $comercio, $observaciones, $usuario, $stock, $consumoPorQr)
    {

        /*Tengo que buscar si hay stock disponible*/
        try
        {
            DB::beginTransaction();

            $movimiento=StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'comercio_id'=>$comercio->id,
                'cc'=>$stock->cc,
                'situacion'=>$stock->situacion,
                'cantidad'=>$cantidad,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$cantidad * -1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Consumo'),
                'estado'=>'PENDIENTE',
                'observaciones'=>$observaciones,
                'qr'=>$consumoPorQr?$persona->qr:null]);

            $stock->update(['saldo'=>$stock->saldo-$cantidad]);

            DB::commit();
            return ["exitoso"=>true, "error"=>"", "movimiento"=>$movimiento];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento"=>null];
        }
    }

    public function AnularConsumo($consumo,$usuario, $observaciones)
    {

        $stock=Stock::devolverStock($consumo->persona->id, $consumo->fecha, $consumo->articulo->id);

        /*Tengo que buscar si hay stock disponible*/
        try
        {
            DB::beginTransaction();

            $consumo->update([
                'estado'=>'ANULADO',
                'observaciones'=>$consumo->observaciones. " - " .$observaciones]);

            StockMovimiento::create([
                'articulo_id'=>$consumo->articulo_id,
                'persona_id'=>$consumo->persona->id,
                'fecha'=>$consumo->fecha,
                'cc'=>$consumo->cc,
                'situacion'=>$consumo->persona->situacion,
                'cantidad'=>$consumo->cantidad,
                'comercio_id'=>$consumo->comercio_id,
                'operacion'=>'INC',
                'cantidadconsigno'=>$consumo->cantidad,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Anulacion'),
                'observaciones'=>$observaciones]);

            if ($stock==null)
                Stock::create(
                    ['articulo_id'=>$consumo->articulo_id,
                        'persona_id'=>$consumo->persona->id,
                        'fechadesde'=>$consumo->fecha,
                        'fechahasta'=>$consumo->fecha,
                        'cc'=>$consumo->cc,
                        'stock'=>$consumo->cantidad,
                        'saldo'=>$consumo->cantidad]);
            else
                $stock->update(['saldo'=>$stock->saldo + $consumo->cantidad]);


            DB::commit();
            return ["exitoso"=>true, "error"=>"", "movimiento_id"=>$consumo->id];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento_id"=>null];

        }
    }

    public function Aumentar($persona, $articulo, $fecha, $cantidad,  $observaciones, $usuario, $stock, $cc)
    {
        try
        {
            DB::beginTransaction();

            $movimiento= StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'situacion'=>$persona->situacion,
                'cc'=>$cc,
                'cantidad'=>$cantidad,
                'operacion'=>'INC',
                'cantidadconsigno'=>$cantidad,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Alta'),
                'observaciones'=>$observaciones]);

            if ($stock==null)
                Stock::create(
                    ['articulo_id'=>$articulo->id,
                        'persona_id'=>$persona->id,
                        'fechadesde'=>$fecha,
                        'fechahasta'=>$fecha,
                        'situacion'=>$persona->situacion,
                        'cc'=>$cc,
                        'stock'=>$cantidad,
                        'saldo'=>$cantidad]);
            else
                $stock->update(['saldo'=>$stock->saldo +$cantidad , 'stock'=> $stock->stock + $cantidad]);

            DB::commit();


            return ["exitoso"=>true, "error"=>"", "movimiento_id"=>$movimiento->id];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento_id"=>null];
        }

    }

    public function Disminuir($persona, $articulo, $fecha, $cantidad, $observaciones, $usuario, $stock)
    {

        /*Tengo que buscar si hay stock disponible*/
        try
        {
            DB::beginTransaction();

            $movimiento=StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'cc'=>$stock->cc,
                'situacion'=>$persona->situacion,
                'cantidad'=>$cantidad,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$cantidad*-1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Baja'),
                'observaciones'=>$observaciones]);

            $stock->update(['saldo'=>$stock->saldo-$cantidad]);
            DB::commit();
            return ["exitoso"=>true, "error"=>"", "movimiento_id"=>$movimiento->id];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento_id"=>null];
        }
    }

    public function Vencer($persona, $articulo, $fecha, $observaciones, $usuario, $stock)
    {

        try
        {
            DB::beginTransaction();

            $movimiento= StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'cc'=>$stock->cc,
                'situacion'=>$persona->situacion,
                'cantidad'=>$stock->saldo,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$stock->saldo*-1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Vencimiento'),
                'observaciones'=>$observaciones]);

            $stock->update(['saldo'=>0]);
            DB::commit();
            return ["exitoso"=>true, "error"=>"", "movimiento_id"=>$movimiento->id];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return ["exitoso"=>false, "error"=>$e->getMessage(), "movimiento_id"=>null];
        }
    }


    public function getFechaAttribute($fecha)
    {
        return new Carbon($fecha);

    }
}
