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
        if ($idComercio !=null && $idComercio !=0)
            $query= $query->where( 'comercio_id', '=', $idComercio);
    }
    public function scopeDeComercios($query, $arrayComercios)
    {

        if ($arrayComercios !=null)
        {
            $query= $query->whereIn('comercio_id',$arrayComercios);
        }
    }

    public function scopeDePersona($query, $idPersona)
    {
        if ($idPersona !=null && $idPersona !=0)
            $query= $query->where( 'persona_id', '=', $idPersona);
    }

    public function scopeDePersonas($query, $arrayPersonas)
    {
        if ($arrayPersonas !=null )
        {
            $query= $query->whereIn( 'persona_id',$arrayPersonas);
        }

    }

    public function scopeDeTipoMovimiento($query, $arrayTipoMovimientos)
    {
        if ($arrayTipoMovimientos !=null )
        {
            $query= $query->whereIn( 'tipomovimiento_id',$arrayTipoMovimientos);
        }

    }

    public function scopeDesdeFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereDate( 'fecha', '>=', $fecha->startOfDay());
    }
    public function scopeHastaFecha($query, Carbon $fecha)
    {
        if ($fecha !=null)
            $query= $query->whereDate( 'fecha', '<=', $fecha->endOfDay());
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

    public function Aumentar($persona, $articulo, $fecha, $cantidad,  $observaciones, $usuario, $stock, $cc, $situacion)
    {
        try
        {
            DB::beginTransaction();

            $movimiento= StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'situacion'=>$situacion,
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
                        'situacion'=>$situacion,
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
                'situacion'=>$stock->situacion,
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
                'situacion'=>$stock->situacion,
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

    public function devolverReportexCC(Carbon $fechaDesde, Carbon $fechaHasta, $comercio_id, $persona_id)
    {

        $movimientos = StockMovimiento::select('cc', DB::raw('SUM(case when articulo_id=1 then cantidad ELSE 0 END) AS desayunos'),
            DB::raw('SUM(case when articulo_id=2 then cantidad ELSE 0 END) AS viandas'))
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->decomercio($comercio_id)
            ->depersona($persona_id)
            ->where ('tipomovimiento_id',2)
            ->whereNotNull('cierrelote_id')
            ->groupBy('cc')
            ->get();
        return $movimientos;
    }

    public function devolverReportexSituacion(Carbon $fechaDesde, Carbon $fechaHasta, $comercio_id, $persona_id)
    {

        $movimientos = StockMovimiento::select('situacion', DB::raw('SUM(case when articulo_id=1 then cantidad ELSE 0 END) AS desayunos'),
            DB::raw('SUM(case when articulo_id=2 then cantidad ELSE 0 END) AS viandas'))
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->decomercio($comercio_id)
            ->depersona($persona_id)
            ->where ('tipomovimiento_id',2)
            ->whereNotNull('cierrelote_id')
            ->groupBy('situacion')
            ->get();
        return $movimientos;
    }

    public function devolverReportexComercio(Carbon $fechaDesde, Carbon $fechaHasta, $comercio_id, $persona_id)
    {

        $movimientos = StockMovimiento::select('comercios.nombrefantasia as comercio', DB::raw('SUM(case when articulo_id=1 then cantidad ELSE 0 END) AS desayunos'),
            DB::raw('SUM(case when articulo_id=2 then cantidad ELSE 0 END) AS viandas'))
            ->join('comercios', 'comercio_id', '=', 'comercios.id')
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->decomercio($comercio_id)
            ->depersona($persona_id)
            ->where ('tipomovimiento_id',2)
            ->whereNotNull('cierrelote_id')
            ->groupBy('comercios.nombrefantasia')
            ->get();
        return $movimientos;
    }

    public function devolverReportexPersona(Carbon $fechaDesde, Carbon $fechaHasta, $comercio_id, $persona_id)
    {

        $movimientos = StockMovimiento::select(DB::raw("CONCAT(personas.apellido,' ',personas.nombre)  AS persona"),
            DB::raw('SUM(case when articulo_id=1 then cantidad ELSE 0 END) AS desayunos'),
            DB::raw('SUM(case when articulo_id=2 then cantidad ELSE 0 END) AS viandas'))
            ->join('personas', 'persona_id', '=', 'personas.id')
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->decomercio($comercio_id)
            ->depersona($persona_id)
            ->where ('tipomovimiento_id',2)
            ->whereNotNull('cierrelote_id')
            ->groupBy('personas.apellido', 'personas.nombre')
            ->get();
        return $movimientos;
    }

    public function devolverDetalleConsumoxPersona(Carbon $fechaDesde, Carbon $fechaHasta, $comercios, $persona_id)
    {

        $movimientos = StockMovimiento::select(
            DB::raw("personas.dni"),
            DB::raw("CONCAT(personas.apellido,' ',personas.nombre)  AS persona"),
            DB::raw("stock_movimientos.cc"),
            DB::raw("stock_movimientos.situacion"),
            DB::raw("comercios.nombrefantasia"),
            DB::raw('SUM(case when articulo_id=1 then cantidad ELSE 0 END) AS desayunos'),
            DB::raw('SUM(case when articulo_id=2 then cantidad ELSE 0 END) AS viandas'))
            ->join('personas', 'persona_id', '=', 'personas.id')
            ->join('comercios', 'comercio_id', '=', 'comercios.id')
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->deComercios($comercios)
            ->depersona($persona_id)
            ->where ('tipomovimiento_id',2)
            ->whereNotNull('cierrelote_id')
            ->groupBy('personas.apellido', 'personas.nombre', 'personas.dni', 'stock_movimientos.cc','stock_movimientos.situacion', 'comercios.nombrefantasia')
            ->get();
        return $movimientos;
    }

    public function devolverMovimientos(Carbon $fechaDesde, Carbon $fechaHasta, $comercios, $personas, $tipomovimientos)
    {

        $movimientos = StockMovimiento::deComercios($comercios)
            ->dePersonas($personas)
            ->deTipoMovimiento($tipomovimientos)
            ->desdefecha($fechaDesde)
            ->hastafecha($fechaHasta)
            ->get();
        return $movimientos;
    }

    public function getFechaAttribute($fecha)
    {
        return new Carbon($fecha);

    }
}
