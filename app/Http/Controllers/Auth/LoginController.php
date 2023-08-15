<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        $credentials = $request->only('username', 'password');
        if (Auth::attempt(['name' => $user->name, 'password' => $user->password])) {
            // Authentication successful
            return redirect()->intended('dashboard');
        } else {
            // Authentication failed
            return back()->withErrors([
                'username' => 'These credentials do not match our records.',
            ]);
        }
    }
    public function name()
    {
        return 'name';
    }

    public function login(){
        die('xxx');
        return view('auth.login',[
            'judul' => "Chassis",
        ]);
    }
}
