<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sewa;

class LaporanKendaraanRekananDijualController extends Controller
{
    //
    public function index_laporan_kendaraan_dijual()
    {
        return view('pages.laporan.Admin.laporan_kendaraan_rekanan',[
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
                        ->with('getSupplier')
                        ->has('getSupplier') 
                        ->where('sewa.is_kembali', 'Y')
                        ->whereNotNull('sewa.tanggal_kembali')
                        ->whereBetween('sewa.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                        ->get();
            return response()->json(["result" => "success", 'data' => $data], 200);
        } catch (\Throwable $th) {
            //throw $th;
        return response()->json(["result" => "error", 'data' =>/*$request->input('tanggal_awal')*/$th->getMessage()], 500);

        }
        
    }
}
