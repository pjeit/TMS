<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\ChassisController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SupplierController;
use App\Models\Karyawan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Auth::routes();

// login
Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom'); 
Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');
Route::get('dashboard', [CustomAuthController::class, 'dashboard']); 
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom'); 

///    

// ========================================== master ==================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home', [
            'judul'=>'Home'
        ]);
    });

    Route::middleware(['is_admin'])->group(function () {
        Route::resource('coa', 'App\Http\Controllers\CoaController');

        Route::resource('pengaturan_sistem', 'App\Http\Controllers\PengaturanSistemController');
    
        Route::resource('kas_bank', 'App\Http\Controllers\KasBankController');
    
        Route::resource('head', 'App\Http\Controllers\HeadController');
    
        Route::resource('chassis', 'App\Http\Controllers\ChassisController');
    
        Route::resource('supplier', 'App\Http\Controllers\SupplierController');
    
        Route::get('karyawan/getData/', [App\Http\Controllers\KaryawanController::class, 'index']);
        
        Route::resource('karyawan', 'App\Http\Controllers\KaryawanController');
    
        Route::resource('grup', 'App\Http\Controllers\GrupController');
    
        Route::resource('customer', 'App\Http\Controllers\CustomerController');
        
        Route::resource('role', 'App\Http\Controllers\RoleController');
    
        Route::resource('users', 'App\Http\Controllers\UsersController');
    
        Route::resource('grup_member', 'App\Http\Controllers\GrupMemberController');
    });

    Route::middleware(['is_marketing'])->group(function () {
        Route::get('/marketing1', function () {
            return view('pages.dummy.marketing1', [
                'judul'=>'marketing1'
            ]);
        });
        Route::get('/marketing2', function () {
                return view('pages.dummy.marketing2', [
                    'judul'=>'marketing2'
                ]);
        });
        Route::get('/marketing3', function () {
                return view('pages.dummy.marketing3', [
                    'judul'=>'marketing3'
                ]);
        });
    });

    Route::middleware(['is_finnance'])->group(function () {
        Route::get('/finnance1', function () {
            return view('pages.dummy.finnance1', [
                'judul'=>'finnance1'
            ]);
        });
        Route::get('/finnance2', function () {
                return view('pages.dummy.finnance2', [
                    'judul'=>'finnance2'
                ]);
        });
        Route::get('/finnance3', function () {
                return view('pages.dummy.finnance3', [
                    'judul'=>'finnance3'
                ]);
        });
    });
        
   
});



// ========================================== master ==================================================


