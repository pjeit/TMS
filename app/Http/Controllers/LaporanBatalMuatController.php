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
            $data = Sewa::where('is_aktif', 'Y')
                        ->where('jenis_tujuan', 'LTL')
                        ->with('getCustomer')
                        ->with('getKaryawan.getHutang')
                        // ->where('no_polisi', $item)
                        ->where('status', 'PROSES DOORING')
                        ->whereBetween('s.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                        ->get();
            return response()->json(["result" => "success", 'data' => $data], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["result" => "error", 'data' => $th->getMessage()], 500);

        }
        
    }
}
