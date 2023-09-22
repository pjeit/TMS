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
use App\Models\SewaOperasional;

class BiayaOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.finance.biaya_operasional.index',[
            'judul' => "Biaya Operasional",
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
        try {
            $user = Auth::user()->id;
            $data = $request->post();
            $item = $data['item'];

            foreach ($data['data'] as $key => $value) {
                if(isset($value['item'])){
                    $sewa_o = new SewaOperasional();
                    $sewa_o->id_sewa = $key;
    
                    if($item == 'OPERASIONAL'){
                        $sewa_o->deskripsi = $value['pick_up'] == 'null' ? 'OPERASIONAL DEPO':'OPERASIONAL '.$value['pick_up'];
                        $sewa_o->total_operasional = $value['nominal'];
                    }else{
                        $sewa_o->deskripsi = $item;
                        $sewa_o->total_operasional = floatval(str_replace(',', '', $value['nominal']));
                    }
    
                    $sewa_o->created_by = $user;
                    $sewa_o->created_at = now();
                    $sewa_o->is_aktif = 'Y';
                    $sewa_o->save();
                }
            }

            return redirect()->route('biaya_operasional.index')->with('status','Sukses!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        
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

    public function pencairan($id)
    {
        return view('pages.finance.biaya_operasional.pencairan',[
            'judul' => "Pencairan Biaya Operasional",
        ]);
    }

    public function load_dataOldNotUsed($item){
        try {
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
                    ->when($item == 'SEAL', function ($query) use ($item) {
                        $query->leftJoin('sewa_operasional as soplyrn', function ($join) {
                            $join->on('s.id_sewa', '=', 'soplyrn.id_sewa')
                                ->where('soplyrn.is_aktif', 'Y')
                                ->whereIn('soplyrn.deskripsi', 'SEAL PELAYARAN');
                        });
                        $query->leftJoin('sewa_operasional as sopje', function ($join) {
                            $join->on('s.id_sewa', '=', 'sopje.id_sewa')
                                ->where('sopje.is_aktif', 'Y')
                                ->whereIn('sopje.deskripsi', 'SEAL PJE');
                        });
                    }, function ($query) use ($item) {
                        $query->leftJoin('sewa_operasional as so', function ($join) use ($item) {
                            $join->on('s.id_sewa', '=', 'so.id_sewa')
                                ->where('so.is_aktif', 'Y')
                                ->where('so.deskripsi', $item);
                        });
                    })
                    ->select('jo.*', 'jod.*', 'gt.nama_tujuan as nama_tujuan','c.nama as customer', 'jod.status as status_jod',
                            'gt.nama_tujuan', 's.id_sewa as id_sewa','jo.id as id_jo','so.id as id_oprs','so.deskripsi as deskripsi_so',
                            DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), 
                            DB::raw('COALESCE(gt.seal_pje, 0) as seal_pje'), 
                            DB::raw('COALESCE(gt.tally, 0) as tally'), 
                            DB::raw('COALESCE(gt.plastik, 0) as plastik'))
                    ->orderBy('jo.id_customer', 'asc')
                    ->orderBy('jod.id_jo', 'asc')
                    ->get();
                // var_dump($dataJO); die;
            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
       
    }

    public function load_data($item){
        try {
            // $data = db::table('sewa as s')
            //         ->leftJoin('job_order_detail AS jod', function($join) {
            //             $join->on('s.id_jo_detail', '=', 'jod.id')
            //                 ->where('jod.is_aktif', 'Y')
            //                 ->where('jod.status', 'DALAM PERJALANAN');
            //         })
            //         ->leftJoin('grup_tujuan AS gt', function($join) {
            //             $join->on('gt.id', '=', 's.id_grup_tujuan')
            //                 ->where('gt.is_aktif', 'Y');
            //         })
            //         ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            //         ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            //         ->leftJoin('sewa_operasional as so', function ($join) use ($item) {
            //             if($item == 'TALLY'){
            //                 $join->on('s.id_sewa', '=', 'so.id_sewa')
            //                     ->where('so.is_aktif', 'Y')
            //                     ->where('so.deskripsi', $item);
            //             }else {
            //                 $join->on('s.id_sewa', '=', 'so.id_sewa')
            //                 ->where('so.is_aktif', 'Y');
            //             }
            //         })
            //         ->where('s.is_aktif', 'Y')
            //         ->where('s.id_sewa', '<>', 'NULL')
            //         ->select('jod.*', 'gt.nama_tujuan as nama_tujuan','c.nama as customer', 'jod.status as status_jod',
            //                 's.id_sewa as id_sewa','s.id_jo as id_jo','so.id as id_oprs', 'c.id as id_customer',
            //                 'so.deskripsi as deskripsi_so', 's.no_polisi as no_polisi', 'k.nama_panggilan as nama_panggilan',
            //                 's.jenis_order as jenis_order','s.tipe_kontainer as tipe_kontainer',
            //                 DB::raw('COALESCE(gt.tally, 0) as tally'), 
            //                 )
            //         ->orderBy('s.id_customer', 'asc')
            //         ->orderBy('jod.id_jo', 'asc')
            // ->get();
            $data = DB::table('sewa AS s')
                    ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
                        if($item == 'OPERASIONAL'){
                            $join->on('s.id_sewa', '=', 'so.id_sewa')
                                ->where('so.is_aktif', 'Y')
                                ->where('so.deskripsi', 'like', $item.'%');
                        }else {
                            $join->on('s.id_sewa', '=', 'so.id_sewa')
                                ->where('so.is_aktif', 'Y')
                                ->where('so.deskripsi', '=', $item);
                        }
                    })
                    ->where('s.status', 'MENUNGGU OPERASIONAL')
                    ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                    ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                    ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                    ->select(
                        's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer',
                        's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                        'so.id AS so_id', 'so.deskripsi AS deskripsi_so', 'so.id as id_oprs', 
                        'so.total_operasional AS so_total_oprs','k.nama_panggilan','jod.pick_up',
                        DB::raw('COALESCE(gt.tally, 0) as tally'),
                        'gt.nama_tujuan',
                        'c.nama as customer',
                        'g.nama_grup as grup_nama'
                    )
            ->get();

            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
       
    }
}
