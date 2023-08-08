<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\ChassisController;
use App\Http\Controllers\SupplierController;

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

Route::get('/', function () {
    return view('home', [
        'judul'=>'Home'
        // 'username' => $user->name,
    ]);
});
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Auth::routes();

// ========================================== master ==================================================
Route::resource('coa', 'App\Http\Controllers\CoaController');

Route::resource('pengaturan_sistem', 'App\Http\Controllers\PengaturanSistemController');

Route::resource('kas_bank', 'App\Http\Controllers\KasBankController');

Route::resource('head', 'App\Http\Controllers\HeadController');

Route::resource('chassis', 'App\Http\Controllers\ChassisController');

Route::resource('supplier', 'App\Http\Controllers\SupplierController');

Route::resource('karyawan', 'App\Http\Controllers\KaryawanController');
// ========================================== master ==================================================


