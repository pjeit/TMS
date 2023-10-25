<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\ChassisController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\DashboardController;
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

    Route::middleware(['is_admin','is_superadmin'])->group(function () {
        Route::get('dashboard/reset', 'App\Http\Controllers\DashboardController@reset')->name('dashboard.reset');
        Route::get('dashboard', [DashboardController::class, 'dashboard']); 



        Route::resource('coa', 'App\Http\Controllers\CoaController');

        Route::get('/booking/getTujuan/{id}', [App\Http\Controllers\BookingController::class, 'getTujuan']);
        Route::resource('booking', 'App\Http\Controllers\BookingController');

        Route::get('/job_order/printJob/{JobOrder}', [App\Http\Controllers\JobOrderController::class, 'printJO'])->name('job_order.print');
        Route::get('job_order/unloading_plan', 'App\Http\Controllers\JobOrderController@unloading_plan')->name('job_order.unloading_plan');
        Route::post('job_order/unloading_plan/data', 'App\Http\Controllers\JobOrderController@unloading_data')->name('job_order.unloading_data');
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
        Route::get('/truck_order/getSewaByStatus/{status}', [App\Helper\SewaDataHelper::class, 'getSewaByStatus'])->name('getSewaByStatus.get');
        Route::get('/truck_order/getJoDetail/{id}', [App\Helper\SewaDataHelper::class, 'getJoDetail'])->name('getJoDetail.get');
        Route::get('/truck_order/getTujuanCust/{id}', [App\Helper\SewaDataHelper::class, 'getTujuanCust'])->name('getTujuanCust.get');
        Route::get('/truck_order/getTujuanBiaya/{id}', [App\Helper\SewaDataHelper::class, 'getTujuanBiaya'])->name('getTujuanBiaya.get');
        Route::get('/truck_order/getDetailJOBiaya/{id}', [App\Helper\SewaDataHelper::class, 'getDetailJOBiaya'])->name('getDetailJOBiaya.get');
        Route::get('/truck_order/getDataBooking/{id}', [App\Helper\SewaDataHelper::class, 'getDataBooking'])->name('getDataBooking.get');
        Route::get('/truck_order/getDataChassisByModel/{id}', [App\Helper\SewaDataHelper::class, 'getDataChassisByModel'])->name('getDataChassisByModel.get');
        Route::get('/truck_order/getDataKendaraanByModel/{id}', [App\Helper\SewaDataHelper::class, 'getDataKendaraanByModel'])->name('getDataKendaraanByModel.get');

        

        Route::resource('truck_order', 'App\Http\Controllers\SewaController');
        Route::resource('truck_order_rekanan', 'App\Http\Controllers\SewaRekananController');

        Route::get('/biaya_operasional/load_data/{item}', 'App\Http\Controllers\BiayaOperasionalController@load_data')->name('biaya_operasional.load_data');
        Route::resource('biaya_operasional', 'App\Http\Controllers\BiayaOperasionalController');

        Route::get('pencairan_operasional/pencairan/{id}', 'App\Http\Controllers\PencairanOperasionalController@pencairan')->name('pencairan_operasional.pencairan');
        Route::resource('pencairan_operasional', 'App\Http\Controllers\PencairanOperasionalController');

        Route::get('/pencairan_uang_jalan_ftl/getDatasewaDetail/{id}', [App\Helper\SewaDataHelper::class, 'getDatasewaDetail'])->name('getDatasewaDetail.get');
        Route::post('/pencairan_uang_jalan_ftl/form', [App\Http\Controllers\PencairanUangJalanFtlController::class, 'form'])->name('pencairan_uang_jalan_ftl.form');
        Route::resource('pencairan_uang_jalan_ftl', 'App\Http\Controllers\PencairanUangJalanFtlController');

        Route::post('/dalam_perjalanan/save_batal_muat/{sewa}', [App\Http\Controllers\DalamPerjalananController::class, 'save_batal_muat'])->name('dalam_perjalanan.save_batal_muat');
        Route::post('/dalam_perjalanan/save_cancel/{sewa}', [App\Http\Controllers\DalamPerjalananController::class, 'save_cancel'])->name('dalam_perjalanan.save_cancel');
        Route::get('/dalam_perjalanan/batal_muat/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'batal_muat'])->name('dalam_perjalanan.batal_muat');
        Route::get('/dalam_perjalanan/cancel/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'cancel'])->name('dalam_perjalanan.cancel');
        
        Route::resource('dalam_perjalanan', 'App\Http\Controllers\DalamPerjalananController');

        Route::post('/invoice/set_sewa_id', [App\Http\Controllers\InvoiceController::class, 'setSewaID'])->name('setSewaID.set');
        Route::get('/invoice/print/{id}', [App\Http\Controllers\InvoiceController::class, 'print'])->name('invoice.print');
        Route::post('/invoice/invoiceKembali', [App\Http\Controllers\InvoiceController::class, 'invoiceKembali'])->name('invoiceKembali.set');
        Route::resource('invoice', 'App\Http\Controllers\InvoiceController');

        Route::post('/belum_invoice/set_sewa_id', [App\Http\Controllers\BelumInvoiceController::class, 'setSewaID'])->name('setSewaIDs.set');
        Route::get('/belum_invoice/print/{id}', [App\Http\Controllers\BelumInvoiceController::class, 'print'])->name('belum_invoice.print');
        Route::get('/belum_invoice/printGabung/{no_invoice}', [App\Http\Controllers\BelumInvoiceController::class, 'printGabung'])->where('no_invoice', '\w+\/\w+\/(\d+)')->name('belum_invoice_gabung.print');
        Route::post('/belum_invoice/invoiceKembali', [App\Http\Controllers\BelumInvoiceController::class, 'invoiceKembali'])->name('belum_invoiceKembali.set');
        Route::resource('belum_invoice', 'App\Http\Controllers\BelumInvoiceController');
        Route::resource('cetak_invoice', 'App\Http\Controllers\CetakInvoiceController');
        Route::resource('revisi_sewa_invoice', 'App\Http\Controllers\RevisiSewaBelumInvoiceController');



        Route::post('/pembayaran_invoice/set_invoice_id', [App\Http\Controllers\PembayaranInvoiceController::class, 'setInvoiceId'])->name('setInvoiceId.set');
        Route::post('/pembayaran_invoice/update_bukti_potong/{id}', [App\Http\Controllers\PembayaranInvoiceController::class, 'updateBuktiPotong'])->name('pembayaran_invoice.updateBuktiPotong');
        Route::get('/pembayaran_invoice/bayar', [App\Http\Controllers\PembayaranInvoiceController::class, 'bayar'])->name('pembayaran_invoice.bayar');
        Route::get('/pembayaran_invoice/loadData/{status}', [App\Http\Controllers\PembayaranInvoiceController::class, 'loadData'])->name('pembayaran_invoice.loadData');
        Route::resource('pembayaran_invoice', 'App\Http\Controllers\PembayaranInvoiceController');

        Route::get('/add_return_tl/cair/{id}', [App\Http\Controllers\AddReturnTLController::class, 'cair'])->name('add_return_tl.cair');
        Route::get('/add_return_tl/refund/{id}', [App\Http\Controllers\AddReturnTLController::class, 'refund'])->name('add_return_tl.refund');
        Route::get('/add_return_tl/getData/{status}', [App\Http\Controllers\AddReturnTLController::class, 'getData'])->name('add_return_tl.getData');
        Route::resource('add_return_tl', 'App\Http\Controllers\AddReturnTLController');

        Route::get('/revisi_uang_jalan/cairkan/{id_sewa}', 'App\Http\Controllers\RevisiUangJalanController@cairkan')->name('revisi_uang_jalan.cairkan');
        Route::get('/revisi_uang_jalan/kembalikan/{id_sewa}', 'App\Http\Controllers\RevisiUangJalanController@kembalikan')->name('revisi_uang_jalan.kembalikan');
        Route::get('/revisi_uang_jalan/load_data/{item}', 'App\Http\Controllers\RevisiUangJalanController@load_data')->name('revisi_uang_jalan.load_data');
        Route::resource('revisi_uang_jalan', 'App\Http\Controllers\RevisiUangJalanController');
        // Route::post('/pencairan-uang-jalan-ftl/form', 'YourController@edit')->name('pencairan_uang_jalan_ftl.edit');
        Route::get('pencairan_komisi_customer/load_data', 'App\Http\Controllers\PencairanKomisiCustomerController@load_data')->name('pencairan_komisi_customer.load_data');
        Route::get('pencairan_komisi_driver/load_data', 'App\Http\Controllers\PencairanKomisiDriverController@load_data')->name('pencairan_komisi_driver.load_data');
        Route::resource('pencairan_komisi_driver', 'App\Http\Controllers\PencairanKomisiDriverController');
        Route::resource('pencairan_komisi_customer', 'App\Http\Controllers\PencairanKomisiCustomerController');

    });

    // Route::middleware(['is_admin'])->group(function () {
    //     Route::get('/job_order/printJob/{JobOrder}', [App\Http\Controllers\JobOrderController::class, 'printJO'])->name('job_order.print');
    //     Route::get('job_order/unloading_plan', 'App\Http\Controllers\JobOrderController@unloading_plan')->name('job_order.unloading_plan');
    //     Route::post('job_order/unloading_plan/data', 'App\Http\Controllers\JobOrderController@unloading_data')->name('job_order.unloading_data');
    //     Route::resource('job_order', 'App\Http\Controllers\JobOrderController');

    //     // storage_demurage
    //     Route::get('job_order/storage_demurage_input/{id}', 'App\Http\Controllers\JobOrderController@storage_demurage_input')->name('job_order.storage_demurage_input');
    //     Route::post('storage_demurage/load_data', 'App\Http\Controllers\StorageDemurageController@load_data')->name('storage_demurage.load_data');
    //     Route::resource('storage_demurage', 'App\Http\Controllers\StorageDemurageController');
    // });
   
});



// ========================================== master ==================================================


