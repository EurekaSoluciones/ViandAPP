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
        'cuit' => 'required|max:13|unique:personas,cuit',
        'dni' => 'required|max:8|unique:personas,dni',

    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['apellido','cuit', 'nombre','activo', 'dni','fechabaja', 'qr', 'situacion', 'cc'];


    public function getFullNameAttribute()
    {
        return $this->apellido . ' ' . $this->nombre . ' - '. $this->dni ;
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

    public static function devolverArrActivosForCombo()
    {
        $personas=Persona::where ('activo',1)->orderBy('apellido')->get()->pluck('full_name', 'id')->toArray();
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

    public function ultimosConsumos()
    {
        //DB::enableQueryLog();
        return $this->hasMany(StockMovimiento::class, 'persona_id')
            ->whereDate('fecha','>=', Carbon::now()->addMonth(-2))
            ->where('tipomovimiento_id',2)
            ->orderBy('id','desc');
        //dd(DB::getQueryLog());
        //return $consumos;
    }

    public function consumos()
    {
        return $this->hasMany(StockMovimiento::class, 'persona_id')
            ->where('tipomovimiento_id',2)
            ->orderBy('id','desc');
    }

    public function notificaciones()
    {
        return $this->hasMany(NotificacionPersona::class, 'persona_id')
            ->orderBy('id','desc');
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

    public function crearPersonayUsuario($apellido, $nombre, $dni, $cuit, $cc, $situacion)
    {
        $persona = Persona::create(['apellido' => $apellido,
            'nombre'=>$nombre,
            'cuit' => $cuit,
            'dni'=>$dni,
            'situacion'=>$situacion,
            'cc'=>$cc,
            'qr'=>""]);

        $persona->generarQR();

        /*Y tengo que crear un usuario para esa persona*/

        User::create([
            'email'=>$dni,
            'name'=>$apellido . " " . $nombre,
            'password'=>Hash::make($dni),
            'perfil_id'=>"3",
        ]);

        return $persona;
    }

    /**
     * Genera un nuevo QR para una persona
     *
     * @return string
     */

    public function generarQR()
    {
        /*tengo que generar el QR, tengo todos los datos de la persona*/
        /*El QR va a ser el ID padeado a 8 right + un numero random del 100000 al 999999*/

        $qrAnterior= $this->qr;

        do
        {
            $qr=str_pad($this->id, 8, "0", STR_PAD_LEFT).str_pad(rand(100000,999999), 8, "0",STR_PAD_LEFT);
        } while ($qrAnterior ==$qr );

        $this->update(["qr"=>$qr]);

        /*Y tengo que devolver el QR*/
        return $qr;
    }

    /**
     * Marca una notificación como leída
     *
     * @return string
     */

    public function confirmarLecturaNotificacion($notificacionId)
    {
        $personaId=$this->id;
        $notificacion=NotificacionPersona::where('notificacion_id','=', $notificacionId)
            ->where('persona_id', $personaId)
            ->first() ;

        $notificacion->update(['leido'=>1, 'fechalectura'=>new Carbon(now())]);


    }

}
