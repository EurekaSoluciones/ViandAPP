<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $user=auth()->user();

        Gate::define('EsAdmin', function (User $user) {
            return $user->perfil_id == config('global.PERFIL_Admin')? Response::allow()
                : Response::deny();
        });

        Gate::define('EsOperador', function (User $user) {
            return $user->perfil_id == config('global.PERFIL_Operador')? Response::allow()
                : Response::deny();
        });

        Gate::define('EsComercio', function (User $user) {
            return $user->perfil_id === config('global.PERFIL_Comercio')? Response::allow()
                : Response::deny();
        });

        Gate::define('EsPersona', function (User $user) {
            return $user->perfil_id === config('global.PERFIL_Persona')? Response::allow()
                : Response::deny();
        });
        //
    }
}
