<?php

namespace App\Http\Controllers;

use App\Helper\UserHelper;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                        ->where('jenis_supplier_id', 6) // jenis pelayaran
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

            $dataJO = DB::table('job_order AS jo')
                    ->select('jo.*','jod.*','jo.status as statusJO','jod.tgl_dooring','jod.status as statusDetail','c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp')
                    ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
                    ->where(function($where) use($data){
                        $where->whereBetween('jod.tgl_dooring', [date("Y-m-d 00:00:00", strtotime($data['tgl_mulai'])), date('Y-m-d 23:59:59', strtotime($data['tgl_akhir']))]);
                    })
                    ->join('job_order_detail AS jod', function($join) use($data){
                        if($data['status'] != 'SEMUA STATUS'){
                            $join->on('jo.id', 'jod.id_jo') 
                            ->where('jod.status', $data['status'])
                            ->where('jod.is_aktif', "Y");
                        }else{
                            $join->on('jo.id', 'jod.id_jo') 
                            ->where('jod.is_aktif', "Y");
                        }
                    })
                    ->where(function($where) use($data){
                        if($data['customer'] != 'SEMUA DATA'){
                            $where->where('jo.id_customer', $data['customer']);
                        }
                    })
                    ->where(function($where) use($data){
                        if($data['pelayaran'] != 'SEMUA DATA'){
                            $where->where('jo.id_supplier', $data['pelayaran']);
                        }
                    })
                    ->leftJoin('user as u', 'u.id', '=', 'jod.created_by')
                    ->leftJoin('karyawan as k', 'k.id', '=', 'u.karyawan_id')
                    ->where(function ($query) use ($id_role, $cabang) {
                        if(!in_array($id_role, [1,3])){
                            $query->where('k.cabang_id', $cabang); // selain id [1,3] atau role [superadmin, admin nasional] lock per kota
                        }
                    })
                    ->leftJoin('grup_tujuan AS gt', 'jod.id_grup_tujuan', '=', 'gt.id')
                    ->where('jo.is_aktif', 'Y')

                    ->where('jo.status', 'like', "PROSES DOORING")
                    ->groupBy('jod.id_jo','jod.id')
                    ->get();
            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);

        }
    }
}
