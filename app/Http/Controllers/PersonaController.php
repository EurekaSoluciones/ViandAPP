<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Persona;
use App\Models\Stock;
use App\Models\StockMovimiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $apellido=$request->get('apellido');
        $nombre=$request->get('nombre');
        $dni=$request->get('dni');
        $cuit=$request->get('cuit');
        $soloActivos=$request->get('ckactivos');


        $personas = Persona::apellido($apellido)
            ->nombre($nombre)
            ->dni($dni)
            ->cuit($cuit)
            ->soloactivos($soloActivos)
            ->orderby('apellido')->get();;

        return view('personas.index', compact('personas'))
            ->with('apellido', $apellido)
            ->with('nombre', $nombre)
            ->with('dni', $dni)
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
        $persona = new Persona();

        return view('personas.create', compact('persona'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Persona::$rules);

        $data=$request->all();

        $persona = Persona::crearPersonayUsuario($data["apellido"],$data["nombre"], $data["dni"], $data["cuit"], $data["cc"], $data["situacion"]);

        session()->flash('message' , 'Persona creada exitosamente. Se creó también el usuario correspondiente.');

        return redirect()->route('personas.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $persona = Persona::find($id);

        return view('personas.show', compact('persona'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $persona = Persona::find($id);

        return view('personas.edit', compact('persona'));
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
        Persona::$rules['dni'] = Persona::$rules['dni'] .','. $id;
        Persona::$rules['cuit'] = Persona::$rules['cuit'] .','. $id;

        $validateddata=request()->validate(Persona::$rules);

        $persona=Persona::findOrFail($id);

        $data=$request->all();

        $persona->update($data);
        session()->flash('message' , 'Persona modificada exitosamente');

        return redirect()->route('personas.index');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $persona = Persona::find($id)->delete();

        $persona->update(['fechabaja'=>Carbon::now(),
            'activo'=>false]);

        session()->flash('message' , 'Persona inactivada exitosamente');
        return redirect()->route('personas.index');
    }

    public static function mostrarWelcome()
    {
        $usuario= auth('sanctum')->user() ;

        $persona=Persona::devolverPersonaxDni($usuario->email);

        $fecha= \Illuminate\Support\Carbon::now();

        $comercios=count(Comercio:: where('activo', true)->get());
        $desayunos=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', config('global.TM_Consumo'))
            ->where('persona_id',$persona->id)
            ->where('estado','!=',"ANULADO")
            ->where('articulo_id',config('global.ART_Desayuno'))->get()->sum('cantidad');

        $viandas=StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', config('global.TM_Consumo'))
            ->where('persona_id',$persona->id)
            ->where('estado','!=',"ANULADO")
            ->where('articulo_id',config('global.ART_Vianda'))->get()->sum('cantidad');

        return view('personas.welcome')
            ->with('persona', $persona)
            ->with('comercios', $comercios)
            ->with('viandas', $viandas)
            ->with('desayunos',$desayunos);


    }

    public function generarNuevoQr()
    {
        $usuario= auth('sanctum')->user() ;

        $persona=Persona::devolverPersonaxDni($usuario->email);

        $persona->generarQR();

        session()->flash('message' , 'Nuevo QR Generado');

        return $this->mostrarWelcome();

    }

    public function misnotificaciones(Request $request)
    {
        $fecha=$request->get('fechadesde');

        if ($fecha==null)
            $fechadesde=Carbon::now()->addMonth(-2);
        else
            $fechadesde =Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        $fecha=$request->get('fechahasta');

        if ($fecha==null)
            $fechahasta=Carbon::now();
        else
            $fechahasta =Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        $usuario=auth()->user();
        $persona=Persona::devolverPersonaxDni($usuario->email);

        $notificaciones=$persona->notificaciones()
            ->desdefecha($fechadesde)
            ->hastafecha($fechahasta)
            ->orderby('id','desc')->paginate();

        return view('personas.misnotificaciones', compact('notificaciones'))
            ->with('titulo','Notificaciones')
            ->with('notificaciones', $notificaciones)
            ->with('fechadesde', $fechadesde)
            ->with('fechahasta', $fechahasta)
            ->with('i', (request()->input('page', 1) - 1) * $notificaciones->perPage());
    }

    public function misconsumos(Request $request)
    {
        $fecha=$request->get('fechadesde');

        if ($fecha==null)
            $fechadesde=Carbon::now()->addMonth(-2);
        else
            $fechadesde =Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        $fecha=$request->get('fechahasta');

        if ($fecha==null)
            $fechahasta=Carbon::now();
        else
            $fechahasta =Carbon::parse(strtotime(str_replace('/', '-', $fecha)));

        $usuario=auth()->user();
        $persona=Persona::devolverPersonaxDni($usuario->email);

        $consumos=$persona->consumos()
            ->desdefecha($fechadesde)
            ->hastafecha($fechahasta)
            ->orderby('id','desc')->paginate();

        return view('personas.misconsumos', compact('consumos'))
            ->with('titulo','Consumos')
            ->with('consumos', $consumos)
            ->with('fechadesde', $fechadesde)
            ->with('fechahasta', $fechahasta)
            ->with('i', (request()->input('page', 1) - 1) * $consumos->perPage());
    }

}
