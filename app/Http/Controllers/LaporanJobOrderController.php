<?php

namespace App\Http\Controllers;

use App\Helper\UserHelper;
use App\Models\Customer;
use App\Models\JobOrder;
use App\Models\JobOrderDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
class LaporanJobOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_JOB_ORDER', ['only' => ['index']]);
		$this->middleware('permission:CREATE_LAPORAN_JOB_ORDER', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_LAPORAN_JOB_ORDER', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_LAPORAN_JOB_ORDER', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $supplier = Supplier::where('supplier.is_aktif', '=', "Y")
                        ->where('jenis_supplier_id',VariableHelper::Jenis_supplier_id('PELAYARAN')) // jenis pelayaran
                        ->orderBy('nama')
                        ->get();

        $customers = Customer::where('customer.is_aktif', "Y")
                        ->orderBy('nama')
                        ->get();

        return view('pages.laporan.job_order.index',[
            'judul' => "Laporan Job Order",
            'supplier' => $supplier,
            'customers' => $customers,
        ]);
    }

    public function load_data(Request $request){
        try {
            $data = $request->collect();
            $id_role = Auth::user()->role_id; 
            $cabang = UserHelper::getCabang();

            $data = JobOrderDetail::where('is_aktif', 'Y')->with('getJO', 'getJO.getCustomer', 'getJO.getSupplier', 'getTujuan')
                            // ->where(function($where) use($data){ //tgl_dooring
                            //     $where->whereBetween('tgl_dooring', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            // })
                            ->whereHas('getJO', function ($query) use($data) {
                                $query->whereBetween('created_at', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                            })
                            ->whereHas('getJO.getCustomer', function ($query) use($data) {
                                if($data['customer'] != 'SEMUA DATA'){ 
                                    $query->where('id_customer', $data['customer']);
                                }      
                            })
                            ->whereHas('getJO.getSupplier', function ($query) use($data) {
                                if($data['pelayaran'] != 'SEMUA DATA'){
                                    $query->where('id_supplier', $data['pelayaran']);
                                }
                            })
                            ->where(function($where) use($data){
                                if($data['status'] != 'SEMUA STATUS'){
                                    $where->where('status', $data['status'])->where('is_aktif', "Y");
                                }else{
                                    $where->where('is_aktif', "Y");
                                }
                            })
                            ->whereHas('getJO', function ($query) use($data) {
                                $query->where('is_aktif', "Y");
                            })
                            ->get();
                            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);

        }
    }
}
