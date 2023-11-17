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
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Routing\Router;

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
        // ===================================MASTER=========================================================
        Route::get('/dashboard/reset', [App\Http\Controllers\DashboardController::class, 'reset'])->name('dashboard.reset');

        Route::resource('coa', 'App\Http\Controllers\CoaController');

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
        
        // ===================================MASTER=========================================================
        
       
        // ===================================INBOUND ORDER=========================================================
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

        // ===================================INBOUND ORDER=========================================================
        
        // ===================================TRUCKING ORDER=========================================================
        Route::get('/booking/getTujuan/{id}', [App\Http\Controllers\BookingController::class, 'getTujuan']);
        Route::resource('booking', 'App\Http\Controllers\BookingController');
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

        Route::post('/dalam_perjalanan/save_ubah_supir/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'save_ubah_supir'])->name('dalam_perjalanan.save_ubah_supir');
        Route::post('/dalam_perjalanan/save_batal_muat/{sewa}', [App\Http\Controllers\DalamPerjalananController::class, 'save_batal_muat'])->name('dalam_perjalanan.save_batal_muat');
        Route::post('/dalam_perjalanan/save_cancel/{sewa}', [App\Http\Controllers\DalamPerjalananController::class, 'save_cancel'])->name('dalam_perjalanan.save_cancel');
        Route::post('/dalam_perjalanan/save_cancel_uang_jalan/{sewa}', [App\Http\Controllers\DalamPerjalananController::class, 'save_cancel_uang_jalan'])->name('dalam_perjalanan.save_cancel_uang_jalan');
        
        Route::get('/dalam_perjalanan/ubah_supir/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'ubah_supir'])->name('dalam_perjalanan.ubah_supir');
        Route::get('/dalam_perjalanan/batal_muat/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'batal_muat'])->name('dalam_perjalanan.batal_muat');
        Route::get('/dalam_perjalanan/cancel/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'cancel'])->name('dalam_perjalanan.cancel');
        Route::get('/dalam_perjalanan/cancel_uang_jalan/{id}', [App\Http\Controllers\DalamPerjalananController::class, 'cancel_uang_jalan'])->name('dalam_perjalanan.cancel_uang_jalan');

        Route::resource('dalam_perjalanan', 'App\Http\Controllers\DalamPerjalananController');

        // ===================================TRUCKING ORDER=========================================================


        // ===================================FINANCE=========================================================
        Route::get('/biaya_operasional/load_data/{item}', 'App\Http\Controllers\BiayaOperasionalController@load_data')->name('biaya_operasional.load_data');
        Route::resource('biaya_operasional', 'App\Http\Controllers\BiayaOperasionalController');

        Route::get('pencairan_operasional/pencairan/{id}', 'App\Http\Controllers\PencairanOperasionalController@pencairan')->name('pencairan_operasional.pencairan');
        Route::resource('pencairan_operasional', 'App\Http\Controllers\PencairanOperasionalController');

        Route::get('/pencairan_uang_jalan/getDatasewaDetail/{id}', [App\Helper\SewaDataHelper::class, 'getDatasewaDetail'])->name('getDatasewaDetail.get');
        Route::post('/pencairan_uang_jalan/form', [App\Http\Controllers\PencairanUangJalanController::class, 'form'])->name('pencairan_uang_jalan.form');
        Route::resource('pencairan_uang_jalan', 'App\Http\Controllers\PencairanUangJalanController');

        Route::get('pencairan_uang_jalan_ltl/getData/{item}', 'App\Http\Controllers\PencairanUangJalanLTLController@get_data')->name('pencairan_uang_jalan_ltl.get_data');
        Route::resource('pencairan_uang_jalan_ltl', 'App\Http\Controllers\PencairanUangJalanLTLController');

        Route::get('pencairan_komisi_customer/load_data', 'App\Http\Controllers\PencairanKomisiCustomerController@load_data')->name('pencairan_komisi_customer.load_data');
        Route::get('pencairan_komisi_driver/load_data', 'App\Http\Controllers\PencairanKomisiDriverController@load_data')->name('pencairan_komisi_driver.load_data');
        Route::resource('pencairan_komisi_driver', 'App\Http\Controllers\PencairanKomisiDriverController');
        Route::resource('pencairan_komisi_customer', 'App\Http\Controllers\PencairanKomisiCustomerController');
        // Route::post('/pencairan-uang-jalan-ftl/form', 'YourController@edit')->name('pencairan_uang_jalan_ftl.edit');
      
        Route::get('/klaim_supir/pencairan/{id}', [App\Http\Controllers\KlaimSupirController::class, 'pencairan'])->name('pencairan_klaim_supir.edit');
        Route::post('/klaim_supir/pencairan_save/{id}', [App\Http\Controllers\KlaimSupirController::class, 'pencairan_save'])->name('pencairan_klaim_supir.save');
        Route::resource('klaim_supir', 'App\Http\Controllers\KlaimSupirController');

        Route::post('tagihan_rekanan/bayar_save', [App\Http\Controllers\TagihanRekananController::class, 'bayar_save'])->name('tagihan_rekanan.bayar_save');
        Route::post('tagihan_rekanan/bayar', [App\Http\Controllers\TagihanRekananController::class, 'bayar'])->name('tagihan_rekanan.bayar');
        Route::get('/tagihan_rekanan/loadData/{id}', [App\Http\Controllers\TagihanRekananController::class, 'load_data'])->name('tagihan_rekanan.load_data');
        Route::get('/tagihan_rekanan/filteredData/{id_tagihan},{id_supplier}', [App\Http\Controllers\TagihanRekananController::class, 'filtered_data'])->name('tagihan_rekanan.filtered_data');
        Route::resource('tagihan_rekanan', 'App\Http\Controllers\TagihanRekananController');

        Route::resource('cetak_uang_jalan', 'App\Http\Controllers\CetakUangJalanController');

        Route::resource('transaksi_lain', 'App\Http\Controllers\TransaksiLainController');
        Route::resource('transfer_dana', 'App\Http\Controllers\TransferDanaController');

        Route::post('tagihan_pembelian/bayar_save', [App\Http\Controllers\TagihanPembelianController::class, 'bayar_save'])->name('tagihan_pembelian.bayar_save');
        Route::post('tagihan_pembelian/bayar', [App\Http\Controllers\TagihanPembelianController::class, 'bayar'])->name('tagihan_pembelian.bayar');
        Route::get('tagihan_pembelian/loadData/{id}', [App\Http\Controllers\TagihanPembelianController::class, 'load_data'])->name('tagihan_pembelian.load_data');
        Route::get('tagihan_pembelian/filteredData/{id_tagihan},{id_supplier}', [App\Http\Controllers\TagihanPembelianController::class, 'filtered_data'])->name('tagihan_pembelian.filtered_data');
        Route::resource('tagihan_pembelian', 'App\Http\Controllers\TagihanPembelianController');
        // ===================================FINANCE=========================================================

        
        // ===================================INVOICE=========================================================
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

        Route::post('/pembayaran_invoice/update_resi', [App\Http\Controllers\PembayaranInvoiceController::class, 'updateResi'])->name('pembayaran_invoice.updateResi');
        Route::post('/pembayaran_invoice/update_bukti_potong/{id}', [App\Http\Controllers\PembayaranInvoiceController::class, 'updateBuktiPotong'])->name('pembayaran_invoice.updateBuktiPotong');
        Route::post('/pembayaran_invoice/set_invoice_id', [App\Http\Controllers\PembayaranInvoiceController::class, 'setInvoiceId'])->name('setInvoiceId.set');
        Route::get('/pembayaran_invoice/bayar', [App\Http\Controllers\PembayaranInvoiceController::class, 'bayar'])->name('pembayaran_invoice.bayar');
        Route::get('/pembayaran_invoice/loadData/{status}', [App\Http\Controllers\PembayaranInvoiceController::class, 'loadData'])->name('pembayaran_invoice.loadData');
        Route::resource('pembayaran_invoice', 'App\Http\Controllers\PembayaranInvoiceController');

        Route::post('pengembalian_jaminan/request', [App\Http\Controllers\PengembalianJaminanController::class, 'request'])->name('pengembalian_jaminan.request');
        Route::resource('pengembalian_jaminan', 'App\Http\Controllers\PengembalianJaminanController');

        Route::get('/bukti_potong/loadData/{status}', [App\Http\Controllers\BuktiPotongController::class, 'loadData'])->name('bukti_potong.loadData');
        Route::resource('bukti_potong', 'App\Http\Controllers\BuktiPotongController');

        Route::get('invoice_karantina/print/{id}', [App\Http\Controllers\InvoiceKarantinaController::class, 'print'])->name('invoice_karantina.print');
        Route::get('invoice_karantina/load_data/{id}', [App\Http\Controllers\InvoiceKarantinaController::class, 'load_data'])->name('invoice_karantina.load_data');
        Route::resource('invoice_karantina', 'App\Http\Controllers\InvoiceKarantinaController');

        Route::get('pembayaran_invoice_karantina/bayar', [App\Http\Controllers\PembayaranInvoiceKarantinaController::class, 'bayar'])->name('pembayaran_invoice_karantina.bayar');
        Route::resource('pembayaran_invoice_karantina', 'App\Http\Controllers\PembayaranInvoiceKarantinaController');

        Route::get('karantina/load_data/{id}', [App\Http\Controllers\KarantinaController::class, 'load_data'])->name('karantina.load_data');
        Route::resource('karantina', 'App\Http\Controllers\KarantinaController');

        Route::resource('pemutihan_invoice', 'App\Http\Controllers\PemutihanInvoiceController');

        // ===================================INVOICE=========================================================
        
        // ===================================REVISI=========================================================
        Route::resource('revisi_sewa_invoice', 'App\Http\Controllers\RevisiSewaBelumInvoiceController');

        Route::get('/revisi_tl/cair/{id}', [App\Http\Controllers\RevisiTLController::class, 'cair'])->name('revisi_tl.cair');
        Route::post('/revisi_tl/save_cair', [App\Http\Controllers\RevisiTLController::class, 'save_cair'])->name('revisi_tl.save_cair');
        Route::get('/revisi_tl/refund/{id}', [App\Http\Controllers\RevisiTLController::class, 'refund'])->name('revisi_tl.refund');
        Route::post('/revisi_tl/save_refund', [App\Http\Controllers\RevisiTLController::class, 'save_refund'])->name('revisi_tl.save_refund');
        Route::get('/revisi_tl/getData/{status}', [App\Http\Controllers\RevisiTLController::class, 'getData'])->name('revisi_tl.getData');
        Route::resource('revisi_tl', 'App\Http\Controllers\RevisiTLController');

        Route::get('/revisi_klaim_supir/pencairan/{id}', [App\Http\Controllers\KlaimSupirController::class, 'revisi_pencairan'])->name('pencairan_klaim_supir_revisi.edit');
        Route::post('/revisi_klaim_supir/pencairan_save/{id}', [App\Http\Controllers\KlaimSupirController::class, 'revisi_pencairan_save'])->name('pencairan_klaim_supir_revisi.save');
        Route::get('/revisi_klaim_supir/revisi', [App\Http\Controllers\KlaimSupirController::class, 'revisi'])->name('klaim_supir_revisi.index');

        Route::get('/revisi_uang_jalan/cairkan/{id_sewa}', 'App\Http\Controllers\RevisiUangJalanController@cairkan')->name('revisi_uang_jalan.cairkan');
        Route::get('/revisi_uang_jalan/kembalikan/{id_sewa}', 'App\Http\Controllers\RevisiUangJalanController@kembalikan')->name('revisi_uang_jalan.kembalikan');
        Route::get('/revisi_uang_jalan/load_data/{item}', 'App\Http\Controllers\RevisiUangJalanController@load_data')->name('revisi_uang_jalan.load_data');
        Route::resource('revisi_uang_jalan', 'App\Http\Controllers\RevisiUangJalanController');

        Route::get('revisi_tagihan_rekanan/delete/{id}', [App\Http\Controllers\RevisiTagihanRekananController::class, 'delete'])->name('revisi_tagihan_rekanan.delete');
        Route::get('revisi_tagihan_rekanan/load_data', [App\Http\Controllers\RevisiTagihanRekananController::class, 'load_data'])->name('revisi_tagihan_rekanan.load_data');
        Route::resource('revisi_tagihan_rekanan', 'App\Http\Controllers\RevisiTagihanRekananController');
      
        Route::get('revisi_tagihan_pembelian/delete/{id}', [App\Http\Controllers\RevisiTagihanPembelianController::class, 'delete'])->name('revisi_tagihan_pembelian.delete');
        Route::get('revisi_tagihan_pembelian/load_data', [App\Http\Controllers\RevisiTagihanPembelianController::class, 'load_data'])->name('revisi_tagihan_pembelian.load_data');
        Route::resource('revisi_tagihan_pembelian', 'App\Http\Controllers\RevisiTagihanPembelianController');

        Route::get('revisi_invoice_trucking/delete/{id}', [App\Http\Controllers\RevisiInvoiceTruckingController::class, 'delete'])->name('revisi_invoice_trucking.delete');
        Route::get('revisi_invoice_trucking/edit-pembayaran/{id}', [App\Http\Controllers\RevisiInvoiceTruckingController::class, 'editPembayaran'])->name('revisi_invoice_trucking.editPembayaran');
        Route::get('revisi_invoice_trucking/load_data', [App\Http\Controllers\RevisiInvoiceTruckingController::class, 'load_data'])->name('revisi_invoice_trucking.load_data');
        Route::resource('revisi_invoice_trucking', 'App\Http\Controllers\RevisiInvoiceTruckingController');

        Route::get('revisi_biaya_operasional/delete/{id}', [App\Http\Controllers\RevisiBiayaOperasionalController::class, 'delete'])->name('revisi_biaya_operasional.delete');
        Route::get('revisi_biaya_operasional/edit-pembayaran/{id}', [App\Http\Controllers\RevisiBiayaOperasionalController::class, 'editPembayaran'])->name('revisi_biaya_operasional.editPembayaran');
        Route::get('revisi_biaya_operasional/load_data/{id}', [App\Http\Controllers\RevisiBiayaOperasionalController::class, 'load_data'])->name('revisi_biaya_operasional.load_data');
        Route::resource('revisi_biaya_operasional', 'App\Http\Controllers\RevisiBiayaOperasionalController');

        // ===================================HRD=========================================================
        Route::resource('pembayaran_gaji', 'App\Http\Controllers\PembayaranGajiController');
        Route::put('karyawan_hutang/update/{id}', [App\Http\Controllers\KaryawanHutangController::class, 'update'])->name('karyawan_hutang.updates');
        Route::resource('karyawan_hutang', 'App\Http\Controllers\KaryawanHutangController');
        // ===================================HRD=========================================================

        // ===================================HRD=========================================================
        Route::resource('status_kendaraan', 'App\Http\Controllers\StatusKendaraanController');
        // ===================================HRD=========================================================
    });

    // Route::controller('JobOrderController::class')->group(function(){
    //     Route::get('/job_order', 'index');
    // });
    // Route::middleware(['is_admin'])->group(function () {
        // Route::get('/job_order/printJob/{JobOrder}', [App\Http\Controllers\JobOrderController::class, 'printJO'])->name('job_order.print');
        // Route::get('job_order/unloading_plan', 'App\Http\Controllers\JobOrderController@unloading_plan')->name('job_order.unloading_plan');
        // Route::post('job_order/unloading_plan/data', 'App\Http\Controllers\JobOrderController@unloading_data')->name('job_order.unloading_data');
        // Route::resource('job_order', 'App\Http\Controllers\JobOrderController');

    //     // storage_demurage
    //     Route::get('job_order/storage_demurage_input/{id}', 'App\Http\Controllers\JobOrderController@storage_demurage_input')->name('job_order.storage_demurage_input');
    //     Route::post('storage_demurage/load_data', 'App\Http\Controllers\StorageDemurageController@load_data')->name('storage_demurage.load_data');
    //     Route::resource('storage_demurage', 'App\Http\Controllers\StorageDemurageController');
    // });
   
});



// ========================================== master ==================================================


