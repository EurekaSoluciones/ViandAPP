<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\ComercioResource;
use App\Models\CierreLote;
use App\Models\Comercio;
use App\Models\PedidoGrupal;
use App\Models\Persona;
use App\Models\StockMovimiento;
use App\Models\User;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ComercioController extends Controller
{
    public function index(Request $request)
    {
        $razonsocial=$request->get('razonsocial');
        $nombrefantasia=$request->get('nombrefantasia');
        $cuit=$request->get('cuit');
        $soloActivos=$request->get('ckactivos');

        $comercios = Comercio::razonsocial($razonsocial)
            ->nombrefantasia($nombrefantasia)
            ->cuit($cuit)
            ->soloactivos($soloActivos)
            ->orderby('razonsocial')->orderby('nombrefantasia')->get();

        return view('comercios.index', compact('comercios'))
            ->with('razonsocial', $razonsocial)
            ->with('nombrefantasia', $nombrefantasia)
            ->with('cuit', $cuit)
            ->with('soloactivos', $soloActivos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $comercio = new Comercio();

        return view('comercios.create', compact('comercio'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Comercio::$rules);

        $altaOK=false;
        $data=$request->all();

        try
        {
            DB::beginTransaction();


            $comercio = Comercio::create($data);

            /*Y tengo que crear un usuario para ese comercio*/

            User::create([
                'email'=>$data["cuit"],
                'name'=>$data["nombrefantasia"],
                'password'=>Hash::make($data["cuit"]),
                'perfil_id'=>"4", //Hash::make(substr( $datos[0], strlen($datos[0])-4, 4)),
            ]);


            DB::commit();

            $altaOK= true;
        }
        catch (\Exception $e)
        {
            DB::rollBack();

        }

        session()->flash('message' , 'Comercio creado exitosamente');

        return redirect()->route('comercios.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comercio = Comercio::find($id);

        return view('comercios.show', compact('comercio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comercio = Comercio::find($id);

        return view('comercios.edit', compact('comercio'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Comercio::$rules['razonsocial'] = Comercio::$rules['razonsocial'] .','. $id;
        Comercio::$rules['cuit'] = Comercio::$rules['cuit'] .','. $id;

        $validateddata=request()->validate(Comercio::$rules);

        $comercio=Comercio::findOrFail($id);

        $data=$request->all();

        $comercio->update($data);
        session()->flash('message' , 'Comercio modificado exitosamente');

        return redirect()->route('comercios.index');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $comercio = Comercio::find($id)->delete();

        $comercio->update(['fechabaja'=>Carbon::now(),
            'activo'=>false]);

        session()->flash('message' , 'Comercio inactivado exitosamente');
        return redirect()->route('comercios.index');
    }

    public function reactivate($id)
    {
        $comercio = Comercio::find($id)->delete();

        $comercio->update(['fechabaja'=>null,
            'activo'=>true]);

        session()->flash('message' , 'Comercio reactivado exitosamente');
        return redirect()->route('comercios.index');
    }


    public function consumosPendientesDeRendir (Request $request)
    {
        $fechaHasta=$request->get('fechaHasta');

        if ($fechaHasta==null)
            $fechaHasta=Carbon::now();

        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $consumos=Comercio::devolverConsumosPendientesDeLiquidar($fechaHasta, $comercio->id);

        return view('comercios.consumospendientes')
            ->with('movimientos', $consumos)
            ->with('fechaHasta', $fechaHasta);

    }

    public function cierresDeLote (Request $request)
    {

        $usuario= auth('sanctum')->user() ;

        $fecha=$request->get('fechaDesde');
        $comercio=$request->get('comercio');

        if ($fecha==null)
            $fechaDesde=Carbon::now();
        else
            $fechaDesde =Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        if ($usuario->perfil_id == config('global.PERFIL_Comercio'))
        {
            $comercio=Comercio::devolverComercioxCuit($usuario->email);
            $cierres=CierreLote::devolverCierresDeLoteDeComercio($comercio->id, $fechaDesde);

        }
        else
        {
            $comercios=Comercio::devolverArrForCombo();
            $cierres=CierreLote::devolverCierresDeLoteDeComercio($comercio==null?0:$comercio, $fechaDesde);
        }
        $comercios=Comercio::devolverArrForCombo();

        return view('comercios.cierresdelote')
            ->with('cierres', $cierres)
            ->with('comercio', $comercio)
            ->with('comercios', $comercios)
            ->with('fechaDesde', $fechaDesde);

    }
    /**
     * Genera un nuevo cierre de lote con los consumos pendientes
     *
     * @param Request $request --> debe tener: ["observaciones",
     *                                          "movimientos:["movimiento_id"]"] como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function cerrarLote(Request $request)
    {

        $fechaHasta= Carbon::parse(strtotime(str_replace('/', '-', $request->get('fechaHasta')))) ;

        if ($fechaHasta==null)
            $fechaHasta=Carbon::now();

        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $consumos=Comercio::devolverConsumosPendientesDeLiquidar($fechaHasta, $comercio->id);

        return view('comercios.cerrarlote')
            ->with('movimientos', $consumos)
            ->with('fechaHasta', $fechaHasta);
    }

    public function generarCierreLote(Request $request)
    {
        $data=$request->all();

        if (isset($data["fechaHasta"]))
            $fecha=Carbon::parse($data["fechaHasta"]);
        else
            $fecha=Carbon::now();

        $observaciones=$data["observaciones"];
        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $cierreLoteOk= $comercio->CerrarLote($observaciones, $fecha, $usuario);

        if ($cierreLoteOk["exitoso"])
        {
            session()->flash('message' , 'Lote Cerrado - Nro Lote: ' .$cierreLoteOk["nroLote"]);
            return $this->mostrarWelcome();
        }
        else
        {
            session()->flash('error' , 'Ha ocurrido un error: ' .$cierreLoteOk["error"]);
            return back();
        }




    }

    public function detalleLote($id)
    {
        $lote=CierreLote::findOrFail($id);

        return view('comercios.cierrelotedetalle', compact('lote'));
    }

    public static function mostrarWelcome()
    {
        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $fecha= \Illuminate\Support\Carbon::now();

        $desayunos=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', config('global.TM_Consumo'))
            ->where('comercio_id',$comercio->id)
            ->where('estado','!=',"ANULADO")
            ->where('articulo_id',config('global.ART_Desayuno'))->get()->sum('cantidad');

        $viandas=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', config('global.TM_Consumo'))
            ->where('comercio_id',$comercio->id)
            ->where('estado','!=',"ANULADO")
            ->where('articulo_id',config('global.ART_Vianda'))->get()->sum('cantidad');

        $ultimosLotes= $comercio->cierreslote()->orderBy('id','DESC')->take(10)->get();

        $consumosPendientess= $comercio->devolverConsumosPendientesDeLiquidar($fecha, $comercio->id )->sum('cantidad');
        $pedidosGrupales=$comercio->pedidosgrupales()->orderBy('id','DESC')->take(10)->get();
        ;

        return view('comercios.welcome')
            ->with('comercio', $comercio)
            ->with('ultimosLotes',$ultimosLotes)
            ->with('consumosPendientes',$consumosPendientess)
            ->with('viandas', $viandas)
            ->with('pedidosGrupales', $pedidosGrupales)
            ->with('desayunos',$desayunos);


    }
}
