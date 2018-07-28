<?php

namespace LdapHook;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

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
        $config = app_path('/vendor/adldap2/adldap2-laravel/src/Config/config.php');
        $ldapConfig = app_path('/vendor/adldap2/adldap2-laravel/src/config/auth.php');

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
        $this->registerRoute();
    }

    public function registerRoute()
    {
        $filesystem = new Filesystem();
        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (strpos($routes_contents, "Voyager::routes()")) {
            $filesystem->append(
                base_path('routes/web.php'),
                " //Ldap Login \n\nRoute::post('login',['uses' => '\LdapHook\Http\Controllers\LdapHookAuthController@postLogin', 'as' => 'postlogin']);\n"
            );
        }
    }
}
