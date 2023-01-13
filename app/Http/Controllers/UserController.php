<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $nombre=$request->get('nombre');
        $login=$request->get('login');
        $perfil=$request->get('perfil');

        $usuarios = User::nombre($nombre)
            ->login($login)
            ->perfil($perfil)
            ->orderby('name')->paginate();

        $perfiles=Perfil::devolverArrForCombo();

       return view('usuarios.index', compact('usuarios'))
                ->with('titulo','Usuarios')
                ->with('nombre', $nombre)
                ->with('login', $login)
                ->with('perfil', $perfil)
                ->with('perfiles', $perfiles)
                ->with('i', (request()->input('page', 1) - 1) * $usuarios->perPage());
    }

    public function create()
    {
        $usuario = new User();

        $perfiles=Perfil::devolverArrForComboForCreate();

        return view('usuarios.create', compact('usuario'))
            ->with('perfiles', $perfiles)
            ->with('ACTION', 'Alta');
    }

    public function store(Request $request)
    {
        request()->validate(User::$rules);

        $data = request()->all();

        $login=$data['login'];

        User::create([
            'email'=>$data['login'],
            'name'=>$data['nombre'],
            'password'=>Hash::make($login),
            'perfil_id'=>$data['perfil']
        ]);

        return redirect()->route('usuarios.index');


    }

    public function edit($idUsuario)
    {
        $usuario=User::where('id',$idUsuario)->first();

        $perfiles=Perfil::devolverArrForCombo();

        return view('usuarios.edit', compact('usuario'))
            ->with('perfiles', $perfiles)
            ->with('ACTION', 'Edit');

    }

    public function show($idUsuario)
    {
        return $this->cambiarcontrasenia();

    }


    public function update( $idUsuario)
    {
        User::$rules['login'] = User::$rules['login'] .','. $idUsuario;

        $data = request()->all();

        User::where('id',$idUsuario)->update(['name'=>$data['nombre'], 'perfil_id'=>$data['perfil']]);

        return redirect()->route('usuarios.index');

    }

    public function destroy(Request $request, $idUsuario)
    {

        $data = request()->all();

        User::where('id',$idUsuario)->update(array('activo'=>0, 'fechabaja'=>Carbon::now() ));

        return redirect()->route('usuarios.index');

    }

    public function reactivar(Request $request, $idUsuario)
    {
        User::where('id',$idUsuario)->update(array('activo'=>1,'fechabaja'=>null ));

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
        $usuario=auth()->user();

        return view('usuarios.cambiopassword',compact('usuario'))->with('title','Cambiar Contraseña');

    }

    public function guardarclave(Request $request)
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

        $usuario=auth()->user();

        $resultado= $usuario->cambiarContrasenia($data['new_password']);

        if ($resultado["exitoso"]==true) {
            session()->flash('message', 'Contraseña Modificada exitosamente!');
            return HomeController::index();
        }
        else
        {
            session()->flash('error', $resultado["error"]);
            return back();
        }




    }
}
