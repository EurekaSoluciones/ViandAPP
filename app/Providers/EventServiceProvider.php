<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
//            if (auth()->user()!=null) {
//                $user=auth()->user();
//                switch ($user->perfil_id)
//                {
//                    case config('global.PERFIL_Comercio'):
//
//                        $event->menu->addAfter('search',
//                            [
//                                'text'        => 'Consumir',
//                                'route'       => 'consumir',
//                                'icon'        => 'fas fa-mug-hot'
//                            ]);
//
//                        $event->menu->addAfter('search',
//                            [
//                                'text'        => 'Consumir',
//                                'route'       => 'consumir',
//                                'icon'        => 'fas fa-mug-hot'
//                            ]);
//
//                        break;
//
//                    case config('global.PERFIL_Persona'):
//                        $event->menu->addAfter('search',
//                            [
//                                'text' => 'Todas las comisiones',
//                                'route' => 'comisionesservicio.indexadm',
//                                'icon' => 'fas fa-file',
//                            ]);
//                        break;
//
//                    default:
//                        $event->menu->addAfter('search',
//                            [
//                                'text'        => 'Importar Excel',
//                                'route'       => 'importarexcel',
//                                'icon'        => 'far fa-fw fa-file-excel',
//                            ],  [
//                                'text'        => 'Aumentar',
//                                'route'       => 'aumentarstock',
//                                'icon'        => 'fas fa-plus',
//                                'label_color' => 'success',
//                            ],
//                            [
//                                'text'        => 'Disminuir',
//                                'route'       => 'disminuirstock',
//                                'icon'        => 'fas fa-minus',
//                                'label_color' => 'success',
//                            ],
//                            ['header' => 'Configuracion'],
//                            [
//                                'text'        => 'Personas',
//                                'route'       => 'personas.index',
//                                'icon'        => 'far fa-fw fa-user',
//                                'label_color' => 'success',
//                            ],
//                            [
//                                'text'        => 'Comercios',
//                                'route'       => 'comercios.index',
//                                'icon'        => 'fas fa-store',
//                                'label_color' => 'success',
//                            ]);
//
//                        break;
//
//                }
//            }
//            else
//            {
//
//            }

        });

    }
}
