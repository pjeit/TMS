<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\KlaimSupir;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LaporanKlaimSupirController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_KLAIM_SUPIR', ['only' => ['index', 'export']]);
		$this->middleware('permission:CREATE_LAPORAN_KLAIM_SUPIR', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_KLAIM_SUPIR', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_KLAIM_SUPIR', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        return view('pages.laporan.klaim_supir.index',[
            'judul' => "Laporan Klaim Supir",
        ]);
    }

    public function load_data(Request $request)
    {
        $data = $request->collect();
        try {
            $klaim = KlaimSupir::where('is_aktif', 'Y')->with('karyawan', 'kendaraan', 'klaimRiwayat')
                            ->where(function($where) use($data){
                                $where->whereBetween('tanggal_klaim', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->where(function($where) use($data){
                                if($data['status'] != 'SEMUA STATUS'){
                                    $where->where('status_klaim', $data['status']);
                                }
                            })
                            ->orderBy('id', 'DESC')
                            ->get();
            
            if(count($klaim)> 0){
                return response()->json(["result" => "success",'data' => $klaim], 200);
            }else{
                return response()->json(["result" => "error",'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error",'data' => null], 404);
        }
    }
}
