<?php

namespace App\Http\Controllers;

use App\Exports\ExcelDetalleConsumos;
use App\Exports\ExcelReportesAgrupados;
use App\Models\Articulo;
use App\Models\CierreLote;
use App\Models\Comercio;
use App\Models\PedidoGrupal;
use App\Models\PedidoGrupalItem;
use App\Models\Persona;
use App\Models\Stock;
use App\Models\StockMovimiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public static function mostrarWelcome()
    {
        $fecha= \Illuminate\Support\Carbon::now();

        $user=auth()->user();

        $empleados=count(Persona:: where('activo', true)->get());

        $comercios=count(Comercio:: where('activo', true)->get());

        $desayunos=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', 2)
            ->where('articulo_id',1)->get()->sum('cantidad');

        $viandas=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', 2)
            ->where('articulo_id',2)->get()->sum('cantidad');

        $ultimosLotes= CierreLote::devolverCierresDeLoteSinVisar();

        $pedidosGrupales=PedidoGrupal::devolverUltimosPedidosgrupales();

        return view('admin.welcome')
            ->with('empleados',$empleados)
            ->with('comercios',$comercios)
            ->with('viandas', $viandas)
            ->with('desayunos',$desayunos)
            ->with('ultimosLotes',$ultimosLotes)
            ->with('pedidosGrupales',$pedidosGrupales);

    }

    public static function pedidogrupal()
    {
        $fecha= \Illuminate\Support\Carbon::now();

        $user=auth()->user();

        $empleados=Persona::devolverArrActivosForCombo();
        $articulos=Articulo::devolverArrForCombo();
        $comercios=Comercio::devolverArrActivosForCombo();


        return view('admin.pedidogrupalcreate')
            ->with('personas',$empleados)
            ->with('articulos', $articulos)
            ->with('comercios',$comercios)  ;

    }


    public function generarPedidoGrupal(Request $request)
    {
        /*Veamos entonces si puede consumir*/

        $validator = Validator::make($request->all(), [
            'personas' => 'required',
            'comercio' => 'required',
        ]);

        $data=$request->all();

        $fecha= Carbon::parse(strtotime(str_replace('/', '-', $data['fecha'])));
        $personas=$data["personas"];

        $articulo=Articulo::devolverArticuloxId($data["articulo"]);
        $comercio=Comercio::devolverComercioxId($data["comercio"]);
        $cantidad=intval($data["cantidad"]);

        $validator->after(function ($validator) use ($personas, $fecha, $articulo, $cantidad) {
            $personasinStock=$this->personaSinStock($personas, $fecha, $articulo->id, $cantidad) ;

            if ($personasinStock!=null) {
                $validator->errors()->add(
                    'personasinStock', 'Existen personas sin Stock: '.$personasinStock->fullname
                );
            }
        });

        $seleccionadas= implode(", ",$personas);

        if ($validator->fails()) {
            session()->put('seleccionadas' , $seleccionadas);
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        session()->put('seleccionadas' , null);
        $usuario=auth()->user();
        $observaciones=$data["observaciones"];

        /*Si está todo bien, crear el pedido*/

        try{
            DB::beginTransaction();

            $pedido=PedidoGrupal::create(['comercio_id'=>$comercio->id,'fecha'=>$fecha,'cantidad'=>$cantidad,
                'observaciones'=>$observaciones,'usuario_id'=>$usuario->id,
                'estado'=>'GENERADO']);

            foreach ($personas as $value) {
                PedidoGrupalItem::create(['pedidogrupal_id'=>$pedido->id,'persona_id'=>$value,'articulo_id'=>$articulo->id,'cantidad'=>$cantidad]);
            }

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            session()->flash('error' , 'Ha ocurrido un error al guardar: '.$e->getMessage() );
            return back();
        }


        return AdminController::mostrarWelcome();
    }

    public function generarPedidoGrupalOld(Request $request)
    {
        /*Veamos entonces si puede consumir*/

        $data=$request->all();

        $fecha= Carbon::parse(strtotime(str_replace('/', '-', $data['fecha'])));
        $personas=$data["personas"];

        $articulo=Articulo::devolverArticuloxId($data["articulo"]);
        $comercio=Comercio::devolverComercioxId($data["comercio"]);
        $cantidad=intval($data["cantidad"]);


        $usuario=auth()->user();
        $observaciones=$data["observaciones"];

        /*antes que nada, tengo que chequear que alcance el stock para todos los integrantes*/
        $stockSuficiente=true;

        foreach ($personas as $value)
        {
            $stock=Stock::devolverStock($value, $fecha,$articulo->id);
            if ($stock==null)
            {
                $stockSuficiente=false;
                $personaSinStock=Persona::devolverPersonaxId($value);
                break;
            }
            else
            {
                if ($stock->saldo<$cantidad)
                {
                    $stockSuficiente=false;
                    $personaSinStock=Persona::devolverPersonaxId($value);
                    break;
                }
            }
        }

        if (!$stockSuficiente)
        {
            session()->flash('error' , 'No existe stock suficiente para '.$personaSinStock->fullname );
            return back();
        }

        /*Si está todo bien, crear el pedido*/

        try{
            DB::beginTransaction();

            $pedido=PedidoGrupal::create(['comercio_id'=>$comercio->id,'fecha'=>$fecha,'cantidad'=>$cantidad,
                'observaciones'=>$observaciones,'usuario_id'=>$usuario->id,
                'estado'=>'GENERADO']);

            foreach ($personas as $value) {
                PedidoGrupalItem::create(['pedidogrupal_id'=>$pedido->id,'persona_id'=>$value,'articulo_id'=>$articulo->id,'cantidad'=>$cantidad]);
            }

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            session()->flash('error' , 'Ha ocurrido un error al guardar: '.$e->getMessage() );
            return back();
        }


        return AdminController::mostrarWelcome();
    }

    public function personaSinStock($personas, $fecha, $articulo_id, $cantidad)
    {

        /*antes que nada, tengo que chequear que alcance el stock para todos los integrantes*/
        $stockSuficiente=true;
        $personaSinStock=null;
        foreach ($personas as $value)
        {
            $stock=Stock::devolverStock($value, $fecha,$articulo_id);
            if ($stock==null)
            {
                $stockSuficiente=false;
                $personaSinStock=Persona::devolverPersonaxId($value);
                break;
            }
            else
            {
                if ($stock->saldo<$cantidad)
                {
                    $stockSuficiente=false;
                    $personaSinStock=Persona::devolverPersonaxId($value);
                    break;
                }
            }
        }

        return $personaSinStock;
    }

    public function detallePedido($id)
    {
        $pedido=PedidoGrupal::findOrFail($id);

        return view('admin.pedidogrupaldetalle', compact('pedido'));
    }


    public function visarLote($id)
    {
        $cierre=CierreLote::findOrFail($id);


        $cierre->update(['visado'=>true]);

        return AdminController::mostrarWelcome();

    }


    public static function reportes(Request $request)
    {

        $fechaDesde=Carbon::parse(strtotime(str_replace('/', '-', $request->get('fechaDesde'))));
        $fechaHasta=Carbon::parse(strtotime(str_replace('/', '-', $request->get('fechaHasta'))));

        $comercio=$request->get('comercio');
        $persona=$request->get('persona');

        $fecha= \Illuminate\Support\Carbon::now();

        $empleados=Persona::devolverArrActivosForCombo();
        $comercios=Comercio::devolverArrActivosForCombo();

        $salida=$request->get('salida');


        if ($salida=="pantalla" || $salida==null ) {
            $movimientosxCC = StockMovimiento::devolverReportexCC($fechaDesde,$fechaHasta, $comercio, $persona );
            $movimientosxSituacion=StockMovimiento::devolverReportexSituacion($fechaDesde,$fechaHasta, $comercio, $persona );
            $movimientosxComercio=StockMovimiento::devolverReportexComercio($fechaDesde,$fechaHasta, $comercio, $persona );
            $movimientosxPersona=StockMovimiento::devolverReportexPersona($fechaDesde,$fechaHasta, $comercio, $persona );

            return view('admin.reportes')
                ->with('personas',$empleados)
                ->with('comercios',$comercios)
                ->with('fechaDesde',$fechaDesde)
                ->with('fechaHasta',$fechaHasta)
                ->with('comercio',$comercio)
                ->with('persona',$persona)
                ->with('movimientosxCC', $movimientosxCC)
                ->with('movimientosxSituacion', $movimientosxSituacion)
                ->with('movimientosxComercio', $movimientosxComercio)
                ->with('movimientosxPersona', $movimientosxPersona);
        }
        else
        {
            if ($salida=="excel")
                return Excel::download(new ExcelReportesAgrupados($fechaDesde,$fechaHasta,$comercio, $persona), 'TotalesAgrupados.xlsx');
            else
                return Excel::download(new ExcelReportesAgrupados($fechaDesde,$fechaHasta,$comercio, $persona), 'TotalesAgrupados.pdf');
        }



    }

    public static function reportesdetalleconsumos(Request $request)
    {

        $fechaDesde=Carbon::parse(strtotime(str_replace('/', '-', $request->get('fechaDesde'))));
        $fechaHasta=Carbon::parse(strtotime(str_replace('/', '-', $request->get('fechaHasta'))));

        $comercio=$request->get('comercio');
        $persona=$request->get('persona');

        $fecha= \Illuminate\Support\Carbon::now();

        $empleados=Persona::devolverArrActivosForCombo();
        $comercios=Comercio::devolverArrActivosForCombo();

        $salida=$request->get('salida');


       if ($salida=="pantalla" || $salida==null ) {
           $movimientos = StockMovimiento::devolverDetalleConsumoxPersona($fechaDesde, $fechaHasta, $comercio, $persona);

           return view('admin.reportesdetalleconsumos')
               ->with('personas', $empleados)
               ->with('comercios', $comercios)
               ->with('fechaDesde', $fechaDesde)
               ->with('fechaHasta', $fechaHasta)
               ->with('comercio', $comercio)
               ->with('persona', $persona)
               ->with('movimientos', $movimientos);
       }
       else
       {
           if ($salida=="excel")
               return Excel::download(new ExcelDetalleConsumos($fechaDesde,$fechaHasta,$comercio, $persona), 'detalleConsumos.xlsx');
           else
               return Excel::download(new ExcelDetalleConsumos($fechaDesde,$fechaHasta,$comercio, $persona), 'detalleConsumos.xlsx');
       }

    }

}
