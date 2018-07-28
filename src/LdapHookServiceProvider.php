<?php

namespace LdapHook;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\Events\Authenticated;
use Adldap\Laravel\Auth\DatabaseUserProvider;
use Adldap\AdldapInterface;


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
        $config = base_path('/vendor/adldap2/adldap2-laravel/src/Config/config.php');
        $ldapConfig = base_path('/vendor/adldap2/adldap2-laravel/src/Config/auth.php');

        $this->publishes([
            $config => config_path('adldap.php'),
            $ldapConfig => config_path('adldap_auth.php'),
        ], 'adldap-hook');

        $this->mergeConfigFrom($config, 'adldap-hook');


        $auth = Auth::getFacadeRoot();

        if (method_exists($auth, 'provider')) {
            $auth->provider('adldap', function ($app, array $config) {
                return $this->makeUserProvider($app['hash'], $config);
            });
        } else {
            $auth->extend('adldap', function ($app) {
                return $this->makeUserProvider($app['hash'], $app['config']['auth']);
            });
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->registerRoute();

        $this->registerBindings();

        $this->registerListeners();
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

     /**
     * Registers the application bindings.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind(ResolverInterface::class, function () {
            $ad = $this->app->make(AdldapInterface::class);

            return new UserResolver($ad);
        });
    }

    /**
     * Registers the event listeners.
     *
     * @return void
     */
    protected function registerListeners()
    {
        // Here we will register the event listener that will bind the users LDAP
        // model to their Eloquent model upon authentication (if configured).
        // This allows us to utilize their LDAP model right
        // after authentication has passed.
        Event::listen(Authenticated::class, Listeners\BindsLdapUserModel::class);

        if ($this->isLogging()) {
            // If logging is enabled, we will set up our event listeners that
            // log each event fired throughout the authentication process.
            foreach ($this->getLoggingEvents() as $event => $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    /**
     * Returns a new Adldap user provider.
     *
     * @param Hasher $hasher
     * @param array  $config
     *
     * @throws InvalidArgumentException
     * 
     * @return \Illuminate\Contracts\Auth\UserProvider
     */
    protected function makeUserProvider(Hasher $hasher, array $config)
    {
        $provider = Config::get('adldap_auth.provider', DatabaseUserProvider::class);


        // The DatabaseUserProvider has some extra dependencies needed,
        // so we will validate that we have them before
        // constructing a new instance.
        if ($provider == DatabaseUserProvider::class) {
            $model = array_get($config, 'model');

            if (!$model) {
                throw new InvalidArgumentException(
                    "No model is configured. You must configure a model to use with the {$provider}."
                );
            }

            return new $provider($hasher, $model);
        }
        
        return new $provider;
    }

     /**
     * Determines if authentication requests are logged.
     *
     * @return bool
     */
    protected function isLogging()
    {
        return Config::get('adldap_auth.logging.enabled', false);
    }

    /**
     * Returns the configured authentication events to log.
     *
     * @return array
     */
    protected function getLoggingEvents()
    {
        return Config::get('adldap_auth.logging.events', []);
    }
}
