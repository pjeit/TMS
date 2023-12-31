<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\JobOrder;
use App\Models\Sewa;
use App\Models\SewaOperasional;
use App\Helper\VariableHelper;
use App\Models\JobOrderDetail;
use Carbon\Carbon;

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

        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();

        return view('pages.finance.biaya_operasional.index',[
            'judul' => "Biaya Operasional",
            'dataKas' => $dataKas,
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
            $storeData = [];
            // dd($data['data']);

            foreach ($data['data'] as $key => $value) {
                $keterangan = $item.' : ';
                if($value['dicairkan'] != null){
                    $nominal = floatval(str_replace(',', '', $value['nominal']));
                    $dicairkan = floatval(str_replace(',', '', $value['dicairkan']));
                    
                    $sewa_o = new SewaOperasional();
                    $sewa_o->id_sewa = $key;
                    $sewa_o->deskripsi = $item == 'OPERASIONAL' ? 'OPERASIONAL ' . ($value['pick_up'] != null? $value['pick_up']:'') :$item;
                    $sewa_o->catatan = $value['catatan'];
                    $sewa_o->total_operasional = $nominal;
                    $sewa_o->total_dicairkan = $dicairkan;
                    $sewa_o->created_by = $user;
                    $sewa_o->status = 'SUDAH DICAIRKAN';
                    $sewa_o->created_at = now();
                    $sewa_o->tgl_dicairkan = now();
                    $sewa_o->is_aktif = 'Y';
                    $sewa_o->save();

                    $saldo = DB::table('kas_bank')
                                ->select('*')
                                ->where('is_aktif', '=', "Y")
                                ->where('kas_bank.id', '=', $data['pembayaran'])
                                ->get();

                    $saldo_baru = $saldo[0]->saldo_sekarang - $dicairkan;

                    DB::table('kas_bank')
                        ->where('id', $data['pembayaran'])
                        ->update(array(
                            'saldo_sekarang' => $saldo_baru,
                            'updated_at'=> now(),
                            'updated_by'=> $user,
                        )
                    );

                    $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                    if($sewa){
                        $sewa->total_uang_jalan += $dicairkan;
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->save();
                    }

                    if($item != 'OPERASIONAL' && $item != 'TALLY' && $item != 'SEAL PELAYARAN'){
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'], // id kas_bank dr form
                                now(), //tanggal
                                0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                $sewa_o->total_dicairkan, //uang keluar (kredit)
                                1015, //kode coa
                                'pencairan_operasional',
                                $keterangan .= $value['keterangan'], //keterangan_transaksi
                                $key, //keterangan_kode_transaksi
                                $user, //created_by
                                now(), //created_at
                                $user, //updated_by
                                now(), //updated_at
                                'Y'
                            ) 
                        );
                    }
                }
            }

            if($item == 'OPERASIONAL' || $item == 'TALLY' || $item == 'SEAL PELAYARAN'){
                $i=1;
                foreach ($data['data'] as $key => $value) {
                    $driver = $value['supplier'] != 'null'? $value['supplier']:$value['driver'];

                    if($value['dicairkan'] != null){
                        if (array_key_exists($value['tujuan'], $storeData)) {
                            // If the customer already exists in $storeData, increment the "dicairkan" value
                            $storeData[$value['tujuan']]['dicairkan'] += floatval(str_replace(',', '', $value['dicairkan']));
                            $storeData[$value['tujuan']]['driver'] .= ' #'. $value['nopol'] .' ('.$driver.')';
                            $storeData[$value['tujuan']]['id_opr'] .= ', '.$sewa_o->id;
                            $storeData[$value['tujuan']]['index'] += 1;
                        } else {
                            // If the customer is not in $storeData, create a new entry
                            $storeData[$value['tujuan']] = [
                                'dicairkan' => floatval(str_replace(',', '', $value['dicairkan'])),
                                'driver' => '#'. $value['nopol'] .' ('.$driver.')',
                                'id_opr' => $sewa_o->id,
                                'index' => $i,
                            ];
                        }
                    }
                }

                foreach ($storeData as $key => $value) {
                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        array(
                            $data['pembayaran'], // id kas_bank dr form
                            now(), //tanggal
                            0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                            $value['dicairkan'], //uang keluar (kredit)
                            1015, //kode coa
                            'pencairan_operasional',
                            $item.": ".$value['index'].'x ' .$key." ".$value['driver'], //keterangan_transaksi
                            $value['id_opr'], //keterangan_kode_transaksi
                            $user, //created_by
                            now(), //created_at
                            $user, //updated_by
                            now(), //updated_at
                            'Y'
                        ) 
                    );
                }
            }

            return redirect()->route('biaya_operasional.index')->with(['status' => 'Success', 'msg' => 'Data berhasil dicairkan!']);
        } catch (ValidationException $e) {
            return redirect()->back()->with(['status' => 'Error', 'msg' => 'Terjadi Kesalahan!']);
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

            // query lama
                // $data = db::table('sewa as s')
                //         ->leftJoin('job_order_detail AS jod', function($join) {
                //             $join->on('s.id_jo_detail', '=', 'jod.id')
                //                 ->where('jod.is_aktif', 'Y')
                //                 ->where('jod.status', 'PROSES DOORING');
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
            //

            $dataJO = JobOrder::from('job_order AS jo')
                    ->leftJoin('job_order_detail AS jod', function($join) {
                        $join->on('jo.id', '=', 'jod.id_jo')
                            ->where('jod.is_aktif', 'Y')
                            ->where('jod.status', 'PROSES DOORING');
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
                    ->where('s.status', 'PROSES DOORING')
                    ->where(function($where) use($item){
                        if($item == 'TAMBAHAN UJ'){
                            $where->where('gt.uang_jalan', '>', DB::raw('s.total_uang_jalan'));
                        }
                    })
                    ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                    ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                    ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                    ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                    ->select(
                        's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
                        's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                        'so.id AS so_id', 'so.deskripsi AS deskripsi_so', 'so.id as id_oprs', 'so.total_dicairkan',
                        'so.total_operasional AS so_total_oprs','k.nama_panggilan','jod.pick_up',
                        DB::raw('COALESCE(gt.tally, 0) as tally'),
                        DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), 
                        'gt.nama_tujuan', 'gt.uang_jalan as uj_tujuan', 's.total_uang_jalan as uj_sewa',
                        'c.nama as customer',
                        'g.nama_grup as nama_grup',
                        'sp.nama as namaSupplier'
                    )
            ->get();
            // var_dump($data); die;

            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
