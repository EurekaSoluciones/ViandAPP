<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'perfil_id',
        'activo',
        'fechabaja'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    static $rules = [
        'login' => 'required|max:50|unique:users,email',
        'nombre' => 'required|max:50',
        'perfil' => 'required',
    ];

    protected $perPage = 60;

    public function perfil()
    {
        return $this->belongsTo('App\Models\Perfil');

    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function cambiarContrasenia($nuevaContrasenia)
    {
        $password=Hash::make($nuevaContrasenia);

        try
        {
            $this->update(['password'=>$password]);

            return ["exitoso"=>true, "error"=>""];
        }
        catch (\Exception $e)
        {
            return ["exitoso"=>false, "error"=>$e->getMessage()];
        }
    }

    public function scopeNombre($query, $search)
    {
        if ($search!="")
            $query->where('name','LIKE', '%'.$search.'%');
    }
    public function scopeLogin($query, $search)
    {
        if ($search!="")
            $query->where('email','LIKE', '%'.$search.'%');
    }
    public function scopePerfil($query, $perfil)
    {
        if ($perfil!="")
            $query->where('perfil_id',$perfil);

    }

    public function getFechaBajaAttribute($fecha)
    {
        if ($fecha==null)
            return null;

        return new Carbon($fecha);

    }
}
