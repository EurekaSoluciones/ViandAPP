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
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'archivo' => 'required|max:10000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        Asignacion::truncate();

        Excel::import(new AsignacionesImport, $request->file('archivo')->store('temp'));

        /*Voy a hacer unas validaciones por las dudas*/

        $asignaciones = Asignacion::whereNotNull('cuit')->get();

        $validator->after(function ($validator) use ($asignaciones) {

            foreach($asignaciones as $asignacion)
            {
                /*Veamos que el cuit sea un numero de 11 digitos*/
                if (strlen($asignacion->cuit) != 11 )
                {
                    $validator->errors()->add(
                        'cuitIncorrecto'.$asignacion->cuit, 'ERROR - CUIT INCORRECTO: '.$asignacion->cuit
                    );

                    $asignacion->update(['estado'=> "ERROR - CUIT INCORRECTO"]);
                }

                /*Que tengan CC y Situacion*/

                if ($asignacion->cc=="" || $asignacion->situacion=="")
                {
                    $validator->errors()->add(
                        'FaltanDatos', 'ERROR - CC o Situación sin datos: '.$asignacion->cuit
                    );
                    $asignacion->update(['estado'=> "ERROR - CC o Situación sin datos"]);
                }

                $persona=Persona::devolverPersonaxCuit($asignacion->cuit);

                if ($persona==null)
                {
                    $asignacion->update(['estado'=> "OK - CUIT no encontrado - Se creará la persona"]);
                }
                else
                {
                    $asignacion->update(['estado'=> "OK"]);
                }
            }
        });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        else
        {
            session()->flash('message' , 'Archivo procesado');

            $asignaciones = Asignacion::whereNotNull('cuit')->get();

            return view('stock.importacion_confirmar', compact('asignaciones'));
        }


    }

    public function messages()
    {
        return [
            'file.required' => 'Debe seleccionar un archivo',
        ];
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

        $asignaciones = Asignacion::whereNotNull('cuit')->get();
        /*Ahora si, ver si existe la persona*/

        try
        {
            DB::beginTransaction();

            foreach ($asignaciones as $asignacion)
            {
                $persona = Persona::devolverPersonaxCuit($asignacion["cuit"]);
                if ($persona == null) {
                    $cuit = $asignacion["cuit"];
                    $dni = mb_substr($cuit,  2, 8);
                    $apellidoynombre = $asignacion["apellidoynombre"];
                    $apellido = substr($apellidoynombre, 0, strpos($apellidoynombre, " "));
                    $nombre = substr($apellidoynombre, strpos($apellidoynombre, " ") + 1);

                    $persona = Persona::crearPersonayUsuario($apellido, $nombre, $dni, $cuit, $asignacion["cc"], $asignacion["situacion"]);
                }

                /*Ahora si, ya tengo la persona, a crear el movimiento de stock*/
                if ($asignacion["desayunos"] > 0) {
                    $stock = StockMovimiento::asignar($persona, $articuloDesayuno, $fechaDesde, $fechaHasta,
                        $asignacion["desayunos"], $asignacion["cc"],   $asignacion["situacion"], 'Importacion Excel', auth()->user()->id);

                }

                if ($asignacion["viandas"] > 0) {
                    $stock = StockMovimiento::asignar($persona, $articuloVianda, $fechaDesde, $fechaHasta,
                        $asignacion["viandas"], $asignacion["cc"],  $asignacion["situacion"], 'Importacion Excel', auth()->user()->id);

                }
            }

            DB::commit();
            session()->flash('message' , 'Importacion Correcta');
            return AdminController::mostrarWelcome();

        } catch (\Exception $e)
        {
            DB::rollBack();
            session()->flash('error' , 'Ocurrió un error en la importación: ' . $e->getMessage());
            $asignaciones = Asignacion::whereNotNull('cuit')->get();
            return view('stock.importacion_confirmar', compact('asignaciones'));
        }

    }

    /*************************************************************************************
    /*Consumo de COMERCIOS*/
    /*************************************************************************************/
    public function consumir(Request $request)
    {
        $comercio=Comercio::devolverComercioxCuit(auth()->user()->email);
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.consumir')
            ->with('comercio', $comercio)
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

        $cantidadAConsumir=(int) $data["cantidad"];
        $cantidadEnStock=Stock::devolverCantidadEnStockParaConsumo( $data["persona"], $fecha, $data["articulo"]);
        $stock=Stock::devolverStockParaConsumo($data["persona"], $fecha, $data["articulo"]);

        if ($stock ==null)
        {
            session()->flash('error' , 'No existe stock disponible para esa persona y artículo');
            return back();
        }
        else
        {
            if ($cantidadEnStock >=$cantidadAConsumir)
            {
                $consumoOK=StockMovimiento::Consumir($persona, $articulo, $fecha, $cantidadAConsumir, $comercio, $data["observaciones"],  $stock, $usuario,false);
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

        $comercio=Comercio::devolverComercioxCuit(auth()->user()->email);
        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.consumir')
            ->with('comercio', $comercio)
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
        $cc=$data['cc'];
        $situacion=$data['situacion'];

        /*Si no encontré stock, tengo que crear un registro, sino se lo tengo que sumar al existente*/
        $movimientoOK=StockMovimiento::Aumentar($persona, $articulo, $fecha, $cantidad,  $data["observaciones"], $usuario, $stock, $cc, $situacion);

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

            if ($disminucionOK["exitoso"])
            {
                session()->flash('message' , 'Disminución registrada' );
            }
            else
            {
                session()->flash('error' , 'Ha ocurrido un error: '.$disminucionOK["error"] );
            }
        }

        $personas=Persona::devolverArrForCombo();
        $articulos=Articulo::devolverArrForCombo();

        return view('stock.disminuirstock')
            ->with('personas', $personas)
            ->with('articulos', $articulos);

    }

}
