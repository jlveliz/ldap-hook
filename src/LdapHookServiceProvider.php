<?php

namespace LdapHook;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;

class LdapHookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Add routes with Voyager's prefix (group)
        app(Dispatcher::class)->listen('voyager.admin.routing', function ($router) {
            $router->get('ldap-hook', function () {
                return 'Hola Jorge!';
            });
        });
    }
}
