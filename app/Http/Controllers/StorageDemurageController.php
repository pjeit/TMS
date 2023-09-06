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
        //
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
        //
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
}
