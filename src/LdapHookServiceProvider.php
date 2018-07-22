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
        /**
            config file
        **/
        $config = __DIR__.'/Config/config.php';
        $ldapConfig = __DIR__.'/Config/auth.php';

        $this->publishes([
            $config => config_path('adldap-hook.php'),
            $ldapConfig => config_path('adldap-hook-auth.php'),
        ], 'adldap-hook');

        $this->mergeConfigFrom($config, 'adldap-hook');

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
