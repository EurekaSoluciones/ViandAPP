<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\ComercioResource;
use App\Models\Comercio;
use App\Models\User;
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
            ->orderby('razonsocial')->orderby('nombrefantasia')->paginate();

        return view('comercios.index', compact('comercios'))
            ->with('razonsocial', $razonsocial)
            ->with('nombrefantasia', $nombrefantasia)
            ->with('cuit', $cuit)
            ->with('soloactivos', $soloActivos)
            ->with('i', (request()->input('page', 1) - 1) * $comercios->perPage());
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
                'name'=>$data["cuit"],
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
}
