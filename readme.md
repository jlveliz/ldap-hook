# Ldap Hook - Voyager

Released by [thejlmedia.com] (http://thejlmedia.com)

## Instalation

1. Clone this repository on folder /hooks
```
git clone https://gitlab.com/rikuhen/ldap-hook.git
```

2. Go to root folder and install Voyager Hook
```
php artisan hook:install ldap-hook
```

3. Register Provider in config/app.php
```
LdapHook\LdapHookServiceProvider::class
```

4. Publish vendor
```
php artisan vendor:publish --tag="adldap-hook"
```

5. Enable Voyager Hook
```
php artisan hook:enable ldap-hook
```

6. Go to routes/web.php and add the next route after ```Voyager::routes(); ```:
```
	//ldap login
	Route::post('login',['uses' => '\LdapHook\Http\Controllers\LdapHookAuthController@postLogin', 'as' => 'postlogin']);
```

```
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
	//ldap login
	Route::post('login',['uses' => '\LdapHook\Http\Controllers\LdapHookAuthController@postLogin', 'as' => 'postlogin']);
});
```

