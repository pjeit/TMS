<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
 use Carbon\Carbon;
use App\Helper\VariableHelper;
use App\Models\JobOrder;
use App\Models\JobOrderDetail;

class BiayaOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JobOrderDetail::
            // ->select('job_order_detail as jod')
            leftJoin('grup_tujuan as gt', 'gt.id', '=', 'job_order_detail.id_grup_tujuan')
            ->where('gt.is_aktif', '=', "Y")
            ->where('job_order_detail.is_aktif', '=', "Y")
            ->where('gt.tally', '<>', "0")
            ->select('job_order_detail.*', 'gt.*')
            ->get();

            // ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 'jod.id_grup_tujuan')
            // ->where('gt.is_aktif', '=', "Y")?
            // ->where('gt.tally', '<>', "0")
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

            return view('pages.finance.biaya_operasional.index',[
            'judul' => "Biaya Operasional",
            'data' => $data,
        ]);
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

    public function load_data(Request $request){
        try {
            $data = $request->collect();
            $item  = $data['item'];
            $dataJO = JobOrder::from('job_order AS jo')
                    ->leftJoin('job_order_detail AS jod', function($join) {
                        $join->on('jo.id', '=', 'jod.id_jo')
                            ->where('jod.is_aktif', 'Y')
                            ->where('jod.status', 'DALAM PERJALANAN');
                    })
                    ->leftJoin('grup_tujuan AS gt', function($join) {
                        $join->on('gt.id', '=', 'jod.id_grup_tujuan')
                            ->where('gt.is_aktif', 'Y');
                    })
                    ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('sewa as s', 's.id_jo_detail', '=', 'jod.id')
                    ->select('jo.*', 'jod.*', 'gt.nama_tujuan as nama_tujuan','c.nama as customer', 'jod.status as status_jod',
                            'gt.nama_tujuan', 's.id_sewa as id_sewa','jo.id as id_jo',
                            DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), 
                            DB::raw('COALESCE(gt.seal_pje, 0) as seal_pje'), 
                            DB::raw('COALESCE(gt.tally, 0) as tally'), 
                            DB::raw('COALESCE(gt.plastik, 0) as plastik'))
                    ->orderBy('jo.id_customer', 'asc')
                    ->get();

            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);
        }
       
    }
}
