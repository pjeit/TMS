<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Jaminan;
use App\Models\JobOrderDetail;
use App\Models\JobOrderDetailBiaya;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use App\Helper\UserHelper;
use App\Models\Supplier;

class StorageDemurageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('jenis_supplier_id', 6) // jenis pelayaran
            ->orderBy('nama')
            ->get();
        $customer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', "Y")
            ->orderBy('nama')
            ->get();

        return view('pages.order.storage_demurage.index',[
            'judul'=>"Storage Demurage",
            'supplier'=>$supplier,
            'customer'=>$customer,
        ]);
    }
    public function storage_demurage(){
        // $data = DB::table('job_order AS jo')
        //     ->select('jo.*','jo.status as statusJO','jod.status as statusDetail','c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp', 
        //         DB::raw('GROUP_CONCAT(CONCAT(\'{"id":\', jod.id, \',"no_kontainer":"\', jod.no_kontainer, \'"}\')) AS json_detail'))
        //     ->join('job_order_detail AS jod', function ($join) {
        //         $join->on('jo.id', '=', 'jod.id_jo')->where('jod.is_aktif', 'Y');
        //     })
        //     ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
        //     ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
        //     ->where('jo.is_aktif', 'Y')
        //     ->groupBy('jo.id')
        //     ->get();
        // // dd($data);
        // // var_dump($data); die;    
        // $data = DB::table('job_order AS jo')
        // ->select('jo.*', 'jo.status as statusJO', 'jod.status as statusDetail', 'c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp',
        //     DB::raw('CONCAT("[", GROUP_CONCAT(JSON_OBJECT(
        //         "id", jod.id, 
        //         "no_kontainer", jod.no_kontainer,
        //         "seal", jod.seal,
        //         "nama_tujuan", jod.nama_tujuan,
        //     )), "]") AS json_detail')
        // )
        // ->join('job_order_detail AS jod', function ($join) {
        //     $join->on('jo.id', '=', 'jod.id_jo')
        //     ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 'jod.id_grup_tujuan')
        //     ->select('jod.*', 'nama_tujuan as nama_tujuan')
        //     ->where('jod.is_aktif', 'Y');
        // })
        // ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
        // ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
        // ->where('jo.is_aktif', 'Y')
        // ->groupBy('jo.id')
        // ->get();
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $detail = JobOrderDetail::where('is_aktif', 'Y')->find($id);
        $data['detail'] = $detail;

        $biaya = JobOrderDetailBiaya::where('is_aktif', 'Y')->where('id_jo_detail', $id)->get();
        $data['biaya'] = $biaya;

        $jobOrder = JobOrder::where('is_aktif', 'Y')->find($detail['id_jo']);
        $data['JO'] = $jobOrder;

        return view('pages.order.storage_demurage.edit',[
            'judul'=>"Input Storage Demurage Detention",
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        // dd($data);

        try {
            if(isset($data['data'])){
                foreach ($data['data'] as $key => $value) {
                    $newBiaya = new JobOrderDetailBiaya();
                    $newBiaya->id_jo = $data['id_jo'];
                    $newBiaya->id_jo_detail = $id;
                    $newBiaya->storage = floatval(str_replace(',', '', $value['storage']));
                    $newBiaya->demurage = floatval(str_replace(',', '', $value['demurage']));
                    $newBiaya->detention = floatval(str_replace(',', '', $value['detention']));
                    $newBiaya->status_bayar = "MENUNGGU PEMBAYARAN";
                    $newBiaya->created_by = $user;
                    $newBiaya->created_at = now();
                    $newBiaya->save();
                }
            }

            return redirect()->route('storage_demurage.index')->with('status','Sukses Menambahkan Data!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function load_data(Request $request){
        try {
            $data = $request->collect();
            $pengirim  = $data['pengirim'];
            $pelayaran   =  $data['pelayaran'];
            $id_role = Auth::user()->role_id; 
            $cabang = UserHelper::getCabang();
            // var_dump($statusJO);die;

            $dataJO = DB::table('job_order AS jo')
                    ->select('jo.*','jod.*','jo.status as statusJO','jod.status as statusDetail','c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp')
   
                    ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
                    ->join('job_order_detail AS jod', function($join){
                            $join->on('jo.id', '=', 'jod.id_jo') ->where('jod.is_aktif',"Y");
                    })
                    ->leftJoin('user as u', 'u.id', '=', 'jod.created_by')
                    ->leftJoin('karyawan as k', 'k.id', '=', 'u.karyawan_id')
                    ->where(function ($query) use ($id_role, $cabang) {
                        if(!in_array($id_role, [1,3])){
                            $query->where('k.cabang_id', $cabang); // selain id [1,3] atau role [superadmin, admin nasional] lock per kota
                        }
                    })
                    ->leftJoin('grup_tujuan AS gt', 'jod.id_grup_tujuan', '=', 'gt.id')
                    ->where('jo.is_aktif', '=', 'Y')
                        ->where('jo.status', 'like', "DALAM PERJALANAN")
                    ->groupBy('jod.id_jo','jod.id')
                    ->get();
            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);

        }
       
        // return view('pages.order.job_order.unloading_plan',[
        //         'judul'=>"Uloading Plan Job Order",
        //         'dataJO' => $dataJO,
        //         // 'dataJODetail'=>$dataJODetail
        //     ]);
    }
}
