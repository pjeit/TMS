<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Sewa;
use Exception;
class LaporanBatalMuatController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_BATAL_MUAT', ['only' => ['index_laporan_batal_muat']]);
    }
    public function index_laporan_batal_muat()
    {
        return view('pages.laporan.Admin.laporan_batal_muat',[
                'judul'=>"Laporan Batal Muat",
        ]);
    }
    public function load_data_ajax(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $tipe_group     = $request->input('tipe_group');

        
        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);
        try {
            $data = Sewa::where('sewa.is_aktif', 'Y')
                        // ->select('sbc.tgl_batal_muat_cancel','sbc.alasan_batal','getCustomer','getTujuan','getKaryawan')
                        ->with('getCustomer')
                        ->with('getTujuan')
                        ->with('getKaryawan')
                        ->with('getBatalCancel')
                        ->with('getSupplier')
                        ->has('getBatalCancel') 
                        // ->leftJoin('sewa_batal_cancel as sbc', function($join) {
                        //     $join->on('sewa.id_sewa', '=', 'sbc.id_sewa')
                        //     ->where('sbc.jenis',  "BATAL")
                        //     ->where('sbc.is_aktif', '=', "Y");
                        // })
                        // ->where('no_polisi', $item)
                        // ->where('status', 'PROSES DOORING')
                        ->whereBetween('sewa.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                        ->get();
            return response()->json(["result" => "success", 'data' => $data], 200);
        } catch (\Throwable $th) {
            //throw $th;
        return response()->json(["result" => "error", 'data' =>/*$request->input('tanggal_awal')*/$th->getMessage()], 500);

        }
        
    }
}
