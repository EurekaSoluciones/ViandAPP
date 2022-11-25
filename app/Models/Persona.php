<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Persona extends Model
{
    use HasFactory;
    protected $table = "personas";
    protected $perPage = 30;

    static $rules = [
        'apellido' => 'required|max:50',
        'nombre'=> 'required|max:50',
        'cuit' => 'required|max:13',
        'dni' => 'required|max:8|unique:personas,dni',

    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['apellido','cuit', 'nombre','activo', 'dni','fechabaja', 'qr'];


    public function getFullNameAttribute()
    {
        return $this->apellido . ' ' . $this->nombre;
    }

    public static function  devolverPersonaxDni($dni)
    {
        $query = Persona::where('dni','=', $dni)
            ->first() ;
        return  $query ;
    }

    public static function  devolverPersonaxCuit($cuit)
    {
        $query = Persona::where('cuit','=', $cuit)
            ->first() ;
        return  $query ;
    }

    public static function  devolverPersonaxId($id)
    {
        $query = Persona::where('id','=', $id)
            ->first() ;
        return  $query ;
    }

    public static function  devolverPersonaxQR($qr)
    {
        $query = Persona::where('qr','=', $qr)
            ->first() ;
        return  $query ;
    }

    public static function devolverArrForCombo()
    {
        $personas=Persona::orderBy('apellido')->get()->pluck('full_name', 'id')->toArray();
        return $personas;
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'persona_id')->orderBy('id','desc');
    }

    public function stockmovimientos()
    {
        return $this->hasMany(StockMovimiento::class, 'persona_id')->orderBy('id','desc');
    }

    public function ultimosmovimientos()
    {
        return $this->hasMany(StockMovimiento::class, 'persona_id')->where('fecha','>=', Carbon::now()->addMonth(-2))->orderBy('id','desc');
    }

    public function stockActual()
    {
        $fecha=Carbon::now();
        return $this->hasMany(Stock::class, 'persona_id')
            ->where('saldo',">", 0)
            ->whereDate( 'fechadesde', '<=', $fecha)
            ->whereDate( 'fechahasta', '>=', $fecha)
            ->orderBy('id','desc');
    }

    public function stockActualParaComercio()
    {
        $fecha=Carbon::now();

        return $this->hasMany(Stock::class, 'persona_id')
            ->where('saldo',">", 0)
            ->whereDate( 'fechadesde', '<=', $fecha)
            ->whereDate( 'fechahasta', '>=', $fecha)
            ->groupBy('articulo_id')
            ->selectRaw('sum(saldo) as saldo, articulo_id')
            ->pluck('saldo','articulo_id');

//
//        return Persona::with(['articulo' => function($query){
//            $query->select('id','descripcion');
//        }
//        ])->withCount(['stock as saldoStock' => function($query) {
//            $query->select(DB::raw('SUM(saldo)'));
//        }
//        ])->get();
    }

    public function scopeApellido($query, $search)
    {
        if ($search!="")
            $query->where('apellido', 'LIKE', '%'.$search.'%');
    }

    public function scopeNombre($query, $search)
    {
        if ($search!="")
            $query->where('nombre','LIKE', '%'.$search.'%');
    }
    public function scopeDni($query, $search)
    {
        if ($search!="")
            $query->where('dni',$search);
    }

    public function scopeCuit($query, $search)
    {
        if ($search!="")
            $query->where('cuit',$search);
    }

    public function scopeSoloActivos($query, $search)
    {
        if ($search!=null )
            $query->where('activo',true);
    }

    public function crearPersonayUsuario($apellido, $nombre, $dni, $cuit)
    {
        $persona = Persona::create(['apellido' => $apellido,
            'nombre'=>$nombre,
            'cuit' => $cuit,
            'dni'=>$dni,
            'qr'=>""]);

        /*tengo que generar el QR*/
        /*El QR va a ser el ID padeado a 8 right + un numero random del 100000 al 999999*/

        $qr=str_pad($persona->id, 8, "0").str_pad(rand(100000,999999), 8, "0");

        $persona->update(["qr"=>$qr]);

        /*Y tengo que crear un usuario para esa persona*/

        User::create([
            'email'=>$dni,
            'name'=>$apellido . " " . $nombre,
            'password'=>Hash::make($dni),
            'perfil_id'=>"3",
        ]);

        return $persona;
    }
}
