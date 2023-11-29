<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\KasBank;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LaporanInvoiceTruckingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:READ_LAPORAN_INVOICE', ['only' => ['index']]);
		// $this->middleware('permission:CREATE_LAPORAN_INVOICE', ['only' => ['create','store']]);
		// $this->middleware('permission:EDIT_LAPORAN_INVOICE', ['only' => ['edit','update']]);
		// $this->middleware('permission:DELETE_LAPORAN_INVOICE', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $customers = Customer::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();
        
        return view('pages.laporan.invoice_trucking.index',[
            'judul' => "Laporan Invoice Trucking",
            'customers' => $customers,
        ]);
    }

    // public function load_data($tgl_mulai, $tgl_akhir, $customer, $status)
    public function load_data(Request $request)
    {
        $data = $request->collect();
        try {
            $invoices = Invoice::where('is_aktif', 'Y')->with('getBillingTo')
                            ->where(function($where) use($data){
                                $where->whereBetween('tgl_invoice', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->where(function($where) use($data){
                                if($data['status'] == 'LUNAS'){
                                    $where->where('total_sisa', 0);
                                }else{
                                    $where->where('total_sisa', '>', 0);
                                }   
                            })
                            ->where(function($where) use($data){
                                if($data['customer'] != 'ALL DATA'){
                                    $where->where('billing_to', $data['customer']);
                                }
                            })
                            ->get();

            if(count($invoices)> 0){
                return response()->json(["result" => "success",'data' => $invoices], 200);
            }else{
                return response()->json(["result" => "error",'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error",'data' => null], 404);
        }
    }
}
