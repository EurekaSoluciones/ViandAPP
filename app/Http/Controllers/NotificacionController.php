<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\NotificacionPersona;
use App\Models\Perfil;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NotificacionController extends Controller
{

    public function index(Request $request)
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


        $notificaciones = Notificacion::desdefecha($fechadesde)
            ->hastafecha($fechahasta)
            ->orderby('fecha','desc')->get();

        return view('notificaciones.index', compact('notificaciones'))
            ->with('titulo','Notificaciones')
            ->with('notificaciones', $notificaciones)
            ->with('fechadesde', $fechadesde)
            ->with('fechahasta', $fechahasta);
    }


    public function create()
    {
        $notificacion = new Notificacion();
        $empleados=Persona::devolverArrActivosForCombo();
         return view('notificaciones.create', compact('notificacion'))
             ->with('personas', $empleados)
            ->with('ACTION', 'Alta');
    }

    public function store(Request $request)
    {
        request()->validate(Notificacion::$rules);
        $user=auth()->user();
        $data = request()->all();

        $todos=isset($data["rdpersonas"]) ? true:false;

        if ($data["rdpersonas"]=="todos") {
            //Tengo que generar una notificacción para todas las personas activas
            $personas = Persona::soloactivos(true)->get()->pluck('id')->toArray();;
        }
        else
            $personas=$data["personas"];



        try{
            DB::beginTransaction();

            $notificacion= Notificacion::create([
                'titulo'=>$data['titulo'],
                'descripcion'=>$data['descripcion'],
                'usuario_id'=>$user->id,
                'fecha'=>Carbon::now()
            ]);

            foreach ($personas as $value) {
                $persona= NotificacionPersona::create(['notificacion_id'=>$notificacion->id,'persona_id'=>$value, 'leido'=>0]);
            }

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            session()->flash('error' , 'Ha ocurrido un error al guardar: '.$e->getMessage() );
            return back();
        }



        return redirect()->route('notificaciones.index');


    }



    public function show($id)
    {
        $notificacion = Notificacion::find($id);

        return view('notificaciones.show', compact('notificacion'));

    }

    public function destroy(Request $request, $id)
    {

//        $data = request()->all();
//
//        Notificacion::where('id',$id);

        return redirect()->route('notificaciones.index');

    }
}
