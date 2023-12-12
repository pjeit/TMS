<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sewa;
use App\Models\Customer;
class LaporanPackingListController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->middleware('permission:READ_LAPORAN_KENDARAAN_DIJUAL', ['only' => ['index_laporan_kendaraan_dijual']]);
    // }
    public function index_laporan_packing_list()
    {
        $dataCustomer = Customer::where('is_aktif','Y')->get();
        return view('pages.laporan.Admin.laporan_packing_list',[
                'judul'=>"Laporan Packing list",
                'dataCustomer'=>$dataCustomer,
        ]);
    }
    public function load_data_ajax(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $customer     = $request->input('customer');

        
        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);
        try {
            $data = Sewa::where('sewa.is_aktif', 'Y')
                        // ->select('sbc.tgl_batal_muat_cancel','sbc.alasan_batal','getCustomer','getTujuan','getKaryawan')
                        ->with('getCustomer')
                        ->with('getTujuan')
                        // ->with('getSupplier')
                        // ->has('getSupplier') 
                        // ->where('sewa.is_kembali', 'Y')
                        // ->whereNotNull('sewa.tanggal_kembali')
                        ->where('sewa.jenis_tujuan','FTL')
                        ->where('sewa.id_customer',$customer)
                        ->whereBetween('sewa.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                        ->get();
            return response()->json(["result" => "success", 'data' => $data], 200);
        } catch (\Throwable $th) {
            //throw $th;
        return response()->json(["result" => "error", 'data' =>/*$request->input('tanggal_awal')*/$th->getMessage()], 500);

        }
        
    }
}
