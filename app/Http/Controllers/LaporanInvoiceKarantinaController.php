<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceKarantina;
use App\Models\Karantina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanInvoiceKarantinaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_INVOICE_KARANTINA', ['only' => ['index', 'export']]);
		$this->middleware('permission:CREATE_LAPORAN_INVOICE_KARANTINA', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_INVOICE_KARANTINA', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_INVOICE_KARANTINA', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $customers = Customer::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();
        
        return view('pages.laporan.invoice_karantina.index',[
            'judul' => "Laporan Invoice Karantina",
            'customers' => $customers,
        ]);
    }

    public function load_data(Request $request)
    {
        $data = $request->collect();

        try {
            $invoices = InvoiceKarantina::where('is_aktif', 'Y')->with('getCustomer')
                            ->where(function($where) use($data){
                                $where->whereBetween('tgl_invoice', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->where(function($where) use($data){
                                if($data['status'] == 'LUNAS'){
                                    $where->where('sisa_tagihan', 0);
                                }else{
                                    $where->where('sisa_tagihan', '>', 0);
                                }   
                            })
                            ->where(function($where) use($data){
                                if($data['customer'] != 'ALL DATA'){
                                    $where->where('id_customer', $data['customer']);
                                }
                            })
                            ->orderBy('id', 'DESC')
                            ->get();

            if(count($invoices)> 0){
                return response()->json(["result" => "success", 'data' => $invoices], 200);
            }else{
                return response()->json(["result" => "error", 'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error",'data' => null], 404);
        }
    }
}
