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

        Route::get('/booking/getTujuan/{id}', [App\Http\Controllers\BookingController::class, 'getTujuan']);
        // Route::get('/booking/getTujuan/{id}', ['uses' => 'UserController@attendance']);
        Route::resource('booking', 'App\Http\Controllers\BookingController');

        Route::get('/job_order/printJob/{JobOrder}', [App\Http\Controllers\JobOrderController::class, 'printJO'])->name('job_order.print');
        // Route::resource('job_order', 'App\Http\Controllers\JobOrderController');
        Route::get('job_order/unloading_plan', 'App\Http\Controllers\JobOrderController@unloading_plan')->name('job_order.unloading_plan');
        // Route::get('job_order/storage_demurage/{jobOrder}', 'App\Http\Controllers\JobOrderController@storage_demurage')->name('job_order.storage_demurage');
        Route::post('job_order/unloading_plan/data', 'App\Http\Controllers\JobOrderController@unloading_data')->name('job_order.unloading_data');
        // Route::get('/unloading_plan', 'JobOrderController@unloading_plan')->name('unloading_plan');
        Route::resource('job_order', 'App\Http\Controllers\JobOrderController');

        Route::get('job_order/storage_demurage_input/{id}', 'App\Http\Controllers\JobOrderController@storage_demurage_input')->name('job_order.storage_demurage_input');
        Route::post('storage_demurage/load_data', 'App\Http\Controllers\StorageDemurageController@load_data')->name('storage_demurage.load_data');
        Route::resource('storage_demurage', 'App\Http\Controllers\StorageDemurageController');

        Route::resource('pembayaran_jo', 'App\Http\Controllers\PaymentJobController');

        Route::post('pembayaran_sdt/load_data', 'App\Http\Controllers\PaymentSDTController@load_data')->name('pembayaran_sdt.load_data');
        Route::resource('pembayaran_sdt', 'App\Http\Controllers\PaymentSDTController');

        Route::get('/mutasi_kendaraan/filter', [App\Http\Controllers\MutasiKendaraanController::class, 'filterMutasi'])->name('filterMutasi.cari');

        Route::get('mutasi_kendaraan/get_data/{id}', 'App\Http\Controllers\MutasiKendaraanController@get_data')->name('mutasi_kendaraan.get_data');
        Route::resource('mutasi_kendaraan', 'App\Http\Controllers\MutasiKendaraanController');

        Route::resource('pengaturan_keuangan', 'App\Http\Controllers\PengaturanKeuanganController');
    
        Route::resource('kas_bank', 'App\Http\Controllers\KasBankController');
    
        Route::resource('head', 'App\Http\Controllers\HeadController');
    
        Route::resource('chassis', 'App\Http\Controllers\ChassisController');
        // filterSupplier itu nama method yang ada di controller
        Route::get('/supplier/filter', [App\Http\Controllers\SupplierController::class, 'filterSupplier'])->name('filterSupplier.cari');
    
        Route::resource('supplier', 'App\Http\Controllers\SupplierController');
    
        Route::get('karyawan/getData/', [App\Http\Controllers\KaryawanController::class, 'index']);
        
        Route::resource('karyawan', 'App\Http\Controllers\KaryawanController');
    
        Route::resource('grup', 'App\Http\Controllers\GrupController');

        Route::get('grup_tujuan/getMarketing/{groupId}', [App\Http\Controllers\GrupTujuanController::class, 'getMarketing']);
        Route::get('/grup_tujuan/printJob/{grup}', [App\Http\Controllers\GrupTujuanController::class, 'printDetail']);
        Route::resource('grup_tujuan', 'App\Http\Controllers\GrupTujuanController');
        // Route::resource('grup_tujuans', 'App\Http\Controllers\GrupTujuansController');
    
        Route::resource('customer', 'App\Http\Controllers\CustomerController');
        
        Route::resource('role', 'App\Http\Controllers\RoleController');
    
        Route::resource('users', 'App\Http\Controllers\UsersController');
    
        Route::resource('marketing', 'App\Http\Controllers\MarketingController');
        Route::get('/pair_kendaraan/filter', [App\Http\Controllers\PairKendaraanController::class, 'filterTruck'])->name('pair_kendaraan.cari');

        Route::resource('pair_kendaraan', 'App\Http\Controllers\PairKendaraanController');

        Route::resource('laporan_kas', 'App\Http\Controllers\LaporanKasController');
        Route::resource('laporan_bank', 'App\Http\Controllers\LaporanBankController');
        Route::get('/truck_order/getJoDetail/{id}', [App\Helper\SewaDataHelper::class, 'getJoDetail'])->name('getJoDetail.get');
        Route::get('/truck_order/getTujuanCust/{id}', [App\Helper\SewaDataHelper::class, 'getTujuanCust'])->name('getTujuanCust.get');
        Route::get('/truck_order/getTujuanBiaya/{id}', [App\Helper\SewaDataHelper::class, 'getTujuanBiaya'])->name('getTujuanBiaya.get');
        Route::get('/truck_order/getDetailJOBiaya/{id}', [App\Helper\SewaDataHelper::class, 'getDetailJOBiaya'])->name('getDetailJOBiaya.get');
        Route::get('/truck_order/getDataBooking/{id}', [App\Helper\SewaDataHelper::class, 'getDataBooking'])->name('getDataBooking.get');

        Route::resource('truck_order', 'App\Http\Controllers\SewaController');
        Route::resource('biaya_operasional', 'App\Http\Controllers\BiayaOperasionalController');
        Route::resource('truck_order_rekanan', 'App\Http\Controllers\SewaRekananController');

        Route::get('/pencairan_uang_jalan_ftl/getDatasewaDetail/{id}', [App\Helper\SewaDataHelper::class, 'getDatasewaDetail'])->name('getDatasewaDetail.get');
        Route::post('/pencairan_uang_jalan_ftl/form', [App\Http\Controllers\PencairanUangJalanFtlController::class, 'form'])->name('pencairan_uang_jalan_ftl.form');
        Route::resource('pencairan_uang_jalan_ftl', 'App\Http\Controllers\PencairanUangJalanFtlController');

        // Route::post('/pencairan-uang-jalan-ftl/form', 'YourController@edit')->name('pencairan_uang_jalan_ftl.edit');


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


