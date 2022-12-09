<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
//        $nombre=$request->get('name');
//        $perfil=$request->get('nombrefantasia');
//        $soloActivos=$request->get('ckactivos');
//
//        $comercios = Comercio::razonsocial($razonsocial)
//            ->nombrefantasia($nombrefantasia)
//            ->cuit($cuit)
//            ->soloactivos($soloActivos)
//            ->orderby('razonsocial')->orderby('nombrefantasia')->paginate();
//
//        return view('comercios.index', compact('comercios'))
//            ->with('razonsocial', $razonsocial)
//            ->with('nombrefantasia', $nombrefantasia)
//            ->with('cuit', $cuit)
//            ->with('soloactivos', $soloActivos)
//            ->with('i', (request()->input('page', 1) - 1) * $comercios->perPage());

        $perfiles=Perfil::devolverArrForCombo();

        $usuarios =  User::orderby('name')->paginate();

        return view('admin.usuarios.index', compact('usuarios'))
            ->with('titulo','Usuarios')
            ->with('i', (request()->input('page', 1) - 1) * $usuarios->perPage());
    }

    public function create()
    {
        $usuario = new User();

        $perfiles=Perfil::devolverArrForCombo();
        $personas=Persona::devolverSinUsuarioArrForCombo();
        return view('usuarios.create', compact('usuario'))
            ->with('perfiles', $perfiles)
            ->with('personas', $personas);
    }

    public function store(Request $request)
    {
        request()->validate(User::$rules);

        $data = request()->all();

        $login=$data['email'];

        User::create([
            'email'=>$data['email'],
            'name'=>$data['name'],
            'password'=>Hash::make($login),
            'perfil_id'=>$data['perfil']
        ]);

        return redirect()->route('usuarios.index');


    }

    public function edit($idUsuario)
    {
        $usuario=User::where('id',$idUsuario)->first();
        $personas=Persona::devolverArrForCombo();
        $perfiles=Perfil::devolverArrForCombo();

        return view('usuarios.edit', compact('usuario'))
            ->with('perfiles', $perfiles)
            ->with('personas', $personas);

    }

    public function update(Request $request, $idUsuario)
    {
        User::$rules['cuit'] = User::$rules['cuit'] .','. $idUsuario;

        $data = request()->all();
        $persona=Persona::where('id',$data['persona_id'])->first();

        User::where('id',$idUsuario)
            ->update(
                array('nombre'=>$persona->nombre." ".$persona->apellido,
                    'persona_id'=>$persona->id,
                    'perfil_id'=>$data['perfil_id']));

        return redirect()->route('usuarios.index');

    }

    public function inactivar(Request $request, $idUsuario)
    {

        $data = request()->all();

        User::where('id',$idUsuario)->update(array('active'=>0));

        return redirect()->route('usuarios.index');

    }

    public function reactivar(Request $request, $idUsuario)
    {
        User::where('id',$idUsuario)->update(array('active'=>1));

        return redirect()->route('usuarios.index');

    }

    public function reiniciarclave(Request $request, $idUsuario)
    {

        $data = request()->all();

        $usuario=User::where('id',$idUsuario)->first();

        $password=Hash::make($usuario->email);

        $usuario->update(array('password'=>$password));

        return redirect()->route('usuarios.index');

    }

    public function cambiarcontrasenia()
    {
        $usuario=auth()->user;

        return view('usuarios.cambiopassword',compact('usuario'))->with('title','Cambiar Contraseña');

    }

    public function guardarclave(Request $request, $idUsuario)
    {
        $v = \Validator::make($request->all(), [
            'new_password' => 'required',
            'new_confirm_password' => 'required|same:new_password'
        ]);
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        $data = request()->all();

        $usuario=User::where('id',$idUsuario)->first();

        $password=Hash::make($data['new_password']);

        $usuario->update(array('password'=>$password));

        return redirect()->route('home');

    }
}
