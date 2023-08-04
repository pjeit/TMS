<?php

use App\Http\Controllers\CoaController;
use App\Http\Controllers\PengaturanSistemController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return view('home');
});
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Auth::routes();

// ========================================== master ==================================================
Route::resource('coa', 'App\Http\Controllers\CoaController');

Route::resource('pengaturan_sistem', 'App\Http\Controllers\PengaturanSistemController');
// ========================================== master ==================================================


