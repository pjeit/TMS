<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanKreditCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_KREDIT_CUSTOMER', ['only' => ['index', 'export']]);
		$this->middleware('permission:CREATE_LAPORAN_KREDIT_CUSTOMER', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_KREDIT_CUSTOMER', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_KREDIT_CUSTOMER', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $customers = Customer::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.laporan.kredit_customer.index',[
            'judul' => "Laporan Kredit Customer",
            'customers' => $customers,
        ]);
    }

    public function load_data(Request $request)
    {
        $data = $request->collect();
        try {
            $results = DB::select("SELECT   s.no_sewa as kode, CONCAT('[', c.kode, '] ' , c.nama) as customer, null as tgl_invoice, null as jatuh_tempo , s.total_tarif as total
                                    FROM sewa as s
                                    LEFT JOIN customer as c on c.id = s.id_customer and c.is_aktif = 'Y'
                                    LEFT JOIN sewa_biaya as sb on sb.id_sewa = s.id_sewa and c.is_aktif = 'Y' and deskripsi = 'TL'
                                    WHERE
                                        s.is_aktif = 'Y'
                                        AND s.status IN ('PROSES DOORING', 'MENUNGGU INVOICE')
                                    UNION
                                    
                                    SELECT  no_invoice as kode, CONCAT('[', c.kode, '] ' , c.nama) as customer, tgl_invoice, jatuh_tempo, total_tagihan as total
                                    FROM
                                        invoice i
                                    LEFT JOIN customer c ON c.id = i.billing_to AND c.is_aktif = 'Y'
                                    WHERE
                                        i.is_aktif = 'Y' AND total_sisa <> 0 
                                ");
            if(count($results)> 0){
                return response()->json(["result" => "success",'data' => $results], 200);
            }else{
                return response()->json(["result" => "error",'data' => null]);
            }
        } catch (ValidationException $e) {
            return response()->json(["result" => "error",'data' => null], 404);
        }
    }
}
