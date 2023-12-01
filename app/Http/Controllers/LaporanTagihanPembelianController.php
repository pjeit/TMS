<?php

namespace App\Http\Controllers;

use App\Models\KlaimSupir;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LaporanTagihanPembelianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_TAGIHAN_PEMBELIAN', ['only' => ['index', 'export']]);
		$this->middleware('permission:CREATE_LAPORAN_TAGIHAN_PEMBELIAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_TAGIHAN_PEMBELIAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_TAGIHAN_PEMBELIAN', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $suppliers = Supplier::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();
        return view('pages.laporan.tagihan_pembelian.index',[
            'judul' => "Laporan Tagihan Pembelian",
            'suppliers' => $suppliers,
        ]);
    }

    public function load_data(Request $request)
    {
        $data = $request->collect();
        try {
            $klaim = TagihanPembelian::where('is_aktif', 'Y')->with('getSupplier', 'getPembayaran')
                            ->where(function($where) use($data){
                                $where->whereBetween('tgl_nota', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->where(function($where) use($data){
                                if($data['status'] == 'LUNAS'){
                                    $where->where('sisa_tagihan', 0);
                                }else if($data['status'] == 'BELUM LUNAS'){
                                    $where->where('sisa_tagihan', '>', 0);
                                }
                            })
                            ->where(function($where) use($data){
                                if($data['supplier'] != 'SEMUA SUPPLIER'){
                                    $where->where('id_supplier', $data['supplier']);
                                }
                            })
                            ->orderBy('id', 'DESC')
                            ->get();
            
            if(count($klaim)> 0){
                return response()->json(["result" => "success", 'data' => $klaim], 200);
            }else{
                return response()->json(["result" => "error", 'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error", 'data' => null], 404);
        }
    }
}
