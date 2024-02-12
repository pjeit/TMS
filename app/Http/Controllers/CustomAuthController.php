<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class CustomAuthController extends Controller
{
    public $table = "user";

    public function index()
    {
        return view('auth.login');
    }  

    public function customLogin(Request $request)
    {
        
        try {
            //code...
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                // return redirect()->intended('/')
                //             ->withSuccess('Signed in');
                return redirect()->intended('/')->with(['status' => 'Success', 'msg' => 'Berhasil Login!']);
                
            }
            else
            {
                // return redirect("login")->withSuccess('Username / Password salah');
                return redirect()->route('login')->with('status', 'Username / Password salah');
            }
        }  catch (Exception $ex) {
            // cancel input db
            // DB::rollBack();
            // return redirect()->back()->withErrors($ex->getMessage())->withInput();
            // return redirect("login")->withErrors($ex->getMessage())->withInput();
            return redirect()->route('login')->with('status', 'Terjadi Kesalahan :'.$ex->getMessage());

        }
        catch (\Throwable $th) {
            return redirect()->route('login')->with('status', 'Terjadi Kesalahan :'.$th->getMessage());
        }
    }

    public function signOut() {
        // $data = session()->all();
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }
}
