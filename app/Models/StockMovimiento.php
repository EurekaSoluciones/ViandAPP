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
        'observaciones'];

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Personas');
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

    public function Asignar($persona, $articulo, $fechadesde, $fechahasta, $cantidad, $cc, $observaciones, $usuario)
    {
        try
        {
            DB::beginTransaction();

            StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fechadesde,
                'cc'=>$cc,
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
                'cc'=>$cc,
                'stock'=>$cantidad,
                'saldo'=>$cantidad]);
            DB::commit();

            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }

    }

    public function Consumir($persona, $articulo, $fecha, $cantidad, $comercio, $observaciones, $usuario, $stock)
    {

        /*Tengo que buscar si hay stock disponible*/
        try
        {
            DB::beginTransaction();

            StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'comercio_id'=>$comercio->id,
                'cc'=>$stock->cc,
                'cantidad'=>$cantidad,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$cantidad * -1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Consumo'),
                'observaciones'=>$observaciones]);

            $stock->update(['saldo'=>$stock->saldo-$cantidad]);

            DB::commit();
            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            return false;
        }
    }

    public function Aumentar($persona, $articulo, $fecha, $cantidad,  $observaciones, $usuario, $stock, $cc)
    {
        try
        {
            DB::beginTransaction();

            StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
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
                        'cc'=>$cc,
                        'stock'=>$cantidad,
                        'saldo'=>$cantidad]);
            else
                $stock->update(['saldo'=>$stock->saldo +$cantidad , 'stock'=> $stock->stock + $cantidad]);

            DB::commit();

            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }

    }

    public function Disminuir($persona, $articulo, $fecha, $cantidad, $observaciones, $usuario, $stock)
    {

        /*Tengo que buscar si hay stock disponible*/
        try
        {
            DB::beginTransaction();

            StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'cc'=>$stock->cc,
                'cantidad'=>$cantidad,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$cantidad*-1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Baja'),
                'observaciones'=>$observaciones]);

            $stock->update(['saldo'=>$stock->saldo-$cantidad]);
            DB::commit();
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }

    public function Vencer($persona, $articulo, $fecha, $observaciones, $usuario, $stock)
    {

        try
        {
            DB::beginTransaction();

            StockMovimiento::create([
                'articulo_id'=>$articulo->id,
                'persona_id'=>$persona->id,
                'fecha'=>$fecha,
                'cc'=>$stock->cc,
                'cantidad'=>$stock->saldo,
                'operacion'=>'DEC',
                'cantidadconsigno'=>$stock->saldo*-1,
                'usuario_id'=>$usuario->id,
                'tipomovimiento_id'=>config('global.TM_Vencimiento'),
                'observaciones'=>$observaciones]);

            $stock->update(['saldo'=>0]);
            DB::commit();
            return true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return false;
        }
    }


    public function getFechaAttribute($fecha)
    {
        return new Carbon($fecha);

    }
}
