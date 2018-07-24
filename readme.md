# Ldap Hook - Voyager

Released by [thejlmedia.com] (http://thejlmedia.com)

## Instalation

1. Clone this repository on folder /hooks
```
git clone https://gitlab.com/rikuhen/ldap-hook.git
```

2. Go to root folder and execute 
```
composer require ldap-hook
```

3. Copy the provider to config.php
```
LdapHook\LdapHookServiceProvider::class
```

4. Publish vendor
```
php artisan vendor:publish --provider="LdapHook\LdapHookServiceProvider::class"
```

5. Install Voyager Hook
```
php artisan hook:install ldap-hook
```

6. Enable Voyager Hook
```
php artisan hook:install ldap-hook
```

6. Enable Voyager Hook
```
php artisan hook:enable ldap-hook
```

7. Go to routes/web.php and move the line:
```
	//ldap login
	Route::post('login',['uses' => '\LdapHook\Http\Controllers\LdapHookAuthController@postLogin', 'as' => 'postlogin']);
```
to
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
	//ldap login
	Route::post('login',['uses' => '\LdapHook\Http\Controllers\LdapHookAuthController@postLogin', 'as' => 'postlogin']);
});

