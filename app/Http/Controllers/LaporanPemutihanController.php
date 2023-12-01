<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PemutihanInvoice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LaporanPemutihanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_PEMUTIHAN', ['only' => ['index', 'export']]);
		$this->middleware('permission:CREATE_LAPORAN_PEMUTIHAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_PEMUTIHAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_PEMUTIHAN', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $customers = Customer::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.laporan.pemutihan.index',[
            'judul' => "Laporan Pemutihan",
            'customers' => $customers,
        ]);
    }

    public function load_data(Request $request)
    {
        $data = $request->collect();
        try {
            $pemutihan = PemutihanInvoice::where('is_aktif', 'Y')->with('invoice', 'invoice.getBillingTo')
                            ->where(function($where) use($data){
                                $where->whereBetween('tanggal', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->whereHas('invoice', function ($query) use($data) {
                                if($data['customer'] != 'SEMUA CUSTOMER'){
                                    $query->where('id', $data['customer']);
                                }
                            })
                            ->orderBy('id', 'DESC')
                            ->get();
            
            if(count($pemutihan)> 0){
                return response()->json(["result" => "success",'data' => $pemutihan], 200);
            }else{
                return response()->json(["result" => "error",'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error",'data' => null], 404);
        }
    }
}
