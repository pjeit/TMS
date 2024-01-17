<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusKendaraan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;

class LaporanStatusKendaraanController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_STATUS_KENDARAAN', ['only' => ['index_laporan_status_kendaraan']]);
    }
    public function index_laporan_status_kendaraan()
    {
        $dataKendaraan=DB::table('kendaraan as k')
            ->select('k.*','kkm.nama as kategoriKendaraan')
            ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
            ->where('k.is_aktif', '=', "Y")
            ->get();
        return view('pages.laporan.Admin.laporan_status_kendaraan',[
                'judul'=>"Laporan Status Kendaraan",
                'dataKendaraan' => $dataKendaraan,
        ]);
    }
    public function load_data_ajax(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $kendaraan  = $request->input('select_kendaraan');

        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);
        try {
            $data = StatusKendaraan::where('status_kendaraan.is_aktif', 'Y')
                ->select('status_kendaraan.id as idStatusKendaraan',
                'status_kendaraan.tanggal_mulai as tanggal_mulai_servis', 
                'status_kendaraan.tanggal_selesai as tanggal_selesai_servis', 
                'status_kendaraan.detail_perawatan as detail_perawatan_servis', 
                'k.no_polisi')
                ->leftJoin('kendaraan as k', function($join) {
                    $join->on('status_kendaraan.kendaraan_id', '=', 'k.id')->where('k.is_aktif', '=', 'Y');
                })
                // ->whereBetween(DB::raw('cast(status_kendaraan.tanggal_mulai)'), [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                ->whereBetween(DB::raw('cast(status_kendaraan.tanggal_mulai as date)'), [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')]);

            if ($kendaraan != 'all') {
                $data->where('status_kendaraan.kendaraan_id', $kendaraan);
            }
            $result = $data->get();
            return response()->json(["result" => "success", 'data' => $result], 200);
        } catch (\Throwable $th) {
            //throw $th;
        return response()->json(["result" => "error", 'data' =>$th->getMessage()], 500);
        }
    }
}
