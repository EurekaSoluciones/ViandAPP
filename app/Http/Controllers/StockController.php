<?php

namespace App\Http\Controllers;

use App\Imports\AsignacionesImport;
use App\Models\Articulo;
use App\Models\Asignacion;
use App\Models\Comercio;
use App\Models\Persona;
use App\Models\Stock;
use App\Models\StockMovimiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{

    public function index()
    {
        return view('stock.importacion');
    }


    /*************************************************************************************
    /*Importacion de Excel*/
    /*************************************************************************************/
    public function import(Request $request)
    {
        Asignacion::truncate();

        Excel::import(new AsignacionesImport, $request->file('file')->store('temp'));

        session()->flash('message' , 'Archivo procesado');

        $asignaciones = Asignacion::all();

        return view('stock.importacion_confirmar', compact('asignaciones'));

    }

    public function confirmarImportacion(Request $request)
    {
        /*Tengo que leer la tabla con los datos y generar el stock en cada persona*/
        /*Antes tengo que verificar que esa persona exista
        Si no existe, lo creo como persona y como usuario*/
        $data=$request->all();

        $fechaDesde= Carbon::parse(strtotime(str_replace('/', '-', $data['fechadesde'])));
        $fechaHasta=Carbon:: parse(strtotime(str_replace('/', '-', $data['fechahasta'])));

        $articuloVianda=Articulo::devolverArticuloVianda();
        $articuloDesayuno=Articulo::devolverArticuloDesayuno();
        /*Primero veamos que no exista ya esa importación*/
        //Lo voy a dejar para después

        $asignaciones = Asignacion::all();
        /*Ahora si, ver si existe la persona*/

        try
        {
            DB::beginTransaction();

            foreach ($asignaciones as $asignacion)
            {
                $persona = Persona::devolverPersonaxCuit($asignacion["dni"]);
                if ($persona == null) {
                    $cuit = $asignacion["dni"];
                    $dni = substr($asignacion["dni"], 2, 8);
                    $apellidoynombre = $asignacion["apellidoynombre"];
                    $apellido = substr($apellidoynombre, 0, strpos($apellidoynombre, " "));
                    $nombre = substr($apellidoynombre, strpos($apellidoynombre, " ") + 1);

                    $persona = Persona::crearPersonayUsuario($apellido, $nombre, $dni, $cuit);
                }

                /*Ahora si, ya tengo la persona, a crear el movimiento de stock*/
                if ($asignacion["desayunos"] > 0) {
                    $stock = StockMovimiento::asignar($persona, $articuloDesayuno, $fechaDesde, $fechaHasta,
                        $asignacion["desayunos"], $asignacion["cc"], 'Importacion Excel', 1);

                }

                if ($asignacion["viandas"] > 0) {
                    $stock = StockMovimiento::asignar($persona, $articuloVianda, $fechaDesde, $fechaHasta,
                        $asignacion["viandas"], $asignacion["cc"], 'Importacion Excel', 1);

                }
            }

            DB::commit();
            session()->flash('message' , 'Importacion Correcta');
            return AdminController::mostrarWelcome();

        } catch (\Exception $e)
        {
            DB::rollBack();
            session()->flash('error' , 'Ocurrió un error en la importación: ' . $e->getMessage());
            $asignaciones = Asignacion::all();
            return view('stock.importacion_confirmar', compact('asignaciones'));
        }

    }

    /*************************************************************************************
    /*Consumo de COMERCIOS*/
    /*************************************************************************************/
    public function consumir(Request $request)
    {
        $comercios=Comercio::devolverArrForCombo();
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();


        return view('stock.consumir')
            ->with('comercios', $comercios)
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

    public function generarconsumo(Request $request)
    {
        /*Veamos entonces si puede consumir*/
        $data=$request->all();
        $fecha= Carbon::parse(strtotime(str_replace('/', '-', $data['fecha'])));
        $persona=Persona::devolverPersonaxId($data["persona"]);

        $articulo=Articulo::devolverArticuloxId($data["articulo"]);
        $comercio=Comercio::devolverComercioxId($data["comercio"]);
        $usuario=auth()->user();

        $stock=Stock::devolverStock( $data["persona"], $fecha, $data["articulo"]);

        $cantidad=(int) $data["cantidad"];

        if ($stock ==null)
        {
            session()->flash('error' , 'No existe stock disponible para esa persona y artículo');
            return back();
        }
        else
        {
            if ($stock->saldo >=$cantidad)
            {
                $consumoOK=StockMovimiento::Consumir($persona, $articulo, $fecha, $cantidad, $comercio, $data["observaciones"], $usuario, $stock);
                if ($consumoOK["exitoso"])
                {
                    session()->flash('message' , 'Consumo registrado' );
                }
                else
                {
                    session()->flash('error' , 'Ha ocurrido un error: '.$consumoOK["error"] );
                    return back();
                }
            }
            else
            {
                session()->flash('error' , 'Stock insuficiente. Stock actual: '.$stock->cantidad );
                return back();
            }
        }

        $comercios=Comercio::devolverArrForCombo();
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.consumir')
            ->with('comercios', $comercios)
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }


    /*************************************************************************************
    /*Aumento de Stock*/
    /*************************************************************************************/
    public function aumentarstock(Request $request)
    {
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();
        $usuario=auth()->user();

        return view('stock.aumentarstock')
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

    public function generaraumento(Request $request)
    {

        /*Estamos aumentando Stock, no deberia haber muchos problemas*/
        $data=$request->all();
        $fecha= Carbon::parse(strtotime(str_replace('/', '-', $data['fecha'])));
        $persona=Persona::devolverPersonaxId($data["persona"]);

        $articulo=Articulo::devolverArticuloxId($data["articulo"]);
        $usuario=auth()->user();

        $stock=Stock::devolverStock( $data["persona"], $fecha, $data["articulo"]);

        $cantidad=(int) $data["cantidad"];

        /*Si no encontré stock, tengo que crear un registro, sino se lo tengo que sumar al existente*/
        $movimientoOK=StockMovimiento::Aumentar($persona, $articulo, $fecha, $cantidad,  $data["observaciones"], $usuario, $stock, $data["cc"]);

        if ($movimientoOK["exitoso"])
        {
            session()->flash('message' , 'Aumento registrado' );
        }
        else
        {
            session()->flash('error' , 'Ha ocurrido un error: '.$movimientoOK["error"] );
        }


        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.aumentarstock')
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

    /*************************************************************************************
    /*Disminución de Stock*/
    /*************************************************************************************/
    public function disminuirstock(Request $request)
    {
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();
        $usuario=auth()->user();

        return view('stock.disminuirstock')
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

    public function generardisminucion(Request $request)
    {

        /*Estamos aumentando Stock, no deberia haber muchos problemas*/
        $data=$request->all();
        $fecha= Carbon::parse(strtotime(str_replace('/', '-', $data['fecha'])));
        $persona=Persona::devolverPersonaxId($data["persona"]);

        $articulo=Articulo::devolverArticuloxId($data["articulo"]);
        $usuario=auth()->user();

        $stock=Stock::devolverStock( $data["persona"], $fecha, $data["articulo"]);

        $cantidad=(int) $data["cantidad"];

        /*Si no encontré stock, no puedo disminuir*/
        if ($stock == null)
        {
            session()->flash('error' , 'No hay stock disponible para disminuir' );
        }
        else
        {
            $disminucionOK=StockMovimiento::Disminuir($persona, $articulo, $fecha, $cantidad,  $data["observaciones"], $usuario, $stock);
        }

        if ($disminucionOK["exitoso"])
        {
            session()->flash('message' , 'Disminución registrada' );
        }
        else
        {
            session()->flash('error' , 'Ha ocurrido un error: '.$disminucionOK["error"] );
        }

        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.disminuirstock')
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

}
