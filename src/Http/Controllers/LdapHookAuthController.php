<?php
namespace LdapHook\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use Auth;

/**
 * 
 */
class LdapHookAuthController extends Controller
{
	
	use AuthenticatesUsers;

    
    public function postLogin(Request $request)
	{
		
		$this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        if (Adldap::auth()->attempt($credentials[$this->username()],$credentials['password'])) {
            //TODO UPDATE OR REGISTER ON TABLES
            // $user = Adldap::search()->users()->where('uid',$credentials[$this->username()])->first();
            // dd($user);
            dd("Hi " . $credentials[$this->username()]);
            return $this->sendLoginResponse($request);
        }elseif ($this->guard()->attempt($credentials, $request->has('remember'))){
            
        } 


        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
	}

	/*
     * Preempts $redirectTo member variable (from RedirectsUsers trait)
     */
    public function redirectTo()
    {
        return config('voyager.user.redirect', route('voyager.dashboard'));
    }
}