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
use App\Models\Karantina;
use App\Models\KarantinaDetail;
use Carbon\Carbon;
use App\Helper\CoaHelper;
use App\Models\KasBank;
use App\Models\SewaOperasionalPembayaran;

class BiayaOperasionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_BIAYA_OPERASIONAL', ['only' => ['index']]);
		$this->middleware('permission:CREATE_BIAYA_OPERASIONAL', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_BIAYA_OPERASIONAL', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_BIAYA_OPERASIONAL', ['only' => ['destroy']]);  
    }
    
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
        DB::beginTransaction(); 

        try {
            $user = Auth::user()->id;
            $data = $request->post();
            $item = $data['item'];
            $storeData = [];
            // dd($data);
            
            if($item == 'KARANTINA'){
                foreach ($data['data'] as $key => $value) {
                    if($value['dicairkan'] != 0 || $value['dicairkan'] != null){
                        $karantina = Karantina::where('is_aktif', 'Y')->find($key);
                        $karantina->total_dicairkan = floatval(str_replace(',', '', $value['dicairkan']));
                        $karantina->catatan = $value['catatan'];
                        $karantina->is_ditagihkan = 'Y';
                        $karantina->updated_by = $user;
                        $karantina->updated_at = now();
                        $karantina->save();
                        

                        $no_kontainer = ' - No. Kontainer:';
                        $detail = KarantinaDetail::where('is_aktif', 'Y')->where('id_karantina', $key)->get();
                        foreach ($detail as $key => $item) {
                            $no_kontainer .= ' #'.$item->getJOD->no_kontainer;
                        }

                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'], // id kas_bank dr form
                                now(), //tanggal
                                0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                floatval(str_replace(',', '', $value['dicairkan'])), //uang keluar (kredit)
                                CoaHelper::DataCoa(5003), //kode coa karantina
                                'karantina',
                                'No. BL: '.$karantina->getJO->no_bl . ' #Kapal: '.$karantina->getJO->kapal .' #Voyage: '. $karantina->getJO->voyage . $no_kontainer, //keterangan_transaksi
                                $karantina->id, //keterangan_kode_transaksi
                                $user, //created_by
                                now(), //created_at
                                $user, //updated_by
                                now(), //updated_at
                                'Y'
                            ) 
                        );
                        $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $data['pembayaran'])
                                    ->first();
                        $saldo_baru = $saldo->saldo_sekarang -  floatval(str_replace(',', '', $value['dicairkan']));
                        DB::table('kas_bank')
                            ->where('id', $data['pembayaran'])
                            ->update(array(
                                'saldo_sekarang' => $saldo_baru,
                                'updated_at'=> now(),
                                'updated_by'=> $user,
                            )
                        );
                    }
                }
                DB::commit();
            }else{
                foreach ($data['data'] as $key => $value) {
                    $keterangan = $item.' : ';
                    if($value['dicairkan'] != null){
                        $total_operasional = floatval(str_replace(',', '', $value['nominal']));
                        $dicairkan = floatval(str_replace(',', '', $value['dicairkan']));

                        $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                        if($sewa){
                            $sewa->total_uang_jalan += $dicairkan;
                            $sewa->updated_by = $user;
                            $sewa->updated_at = now();
                            $sewa->save();
                        }
                        
                        $sewa_o = new SewaOperasional();
                        $sewa_o->id_sewa = $key;
                        $sewa_o->deskripsi = $item == 'ALAT'? 'ALAT' . ($value['pick_up'] != 'null'? ' '. $value['pick_up']:'') : $item;
                        $sewa_o->catatan = $value['catatan'];
                        if($item != 'TIMBANG' && $item != 'BURUH' && $item != 'LEMBUR'){
                            $sewa_o->total_operasional = $total_operasional;
                        }
                        $sewa_o->total_dicairkan = $dicairkan;
                        $sewa_o->created_by = $user;
                        $sewa_o->status = 'SUDAH DICAIRKAN';
                        $sewa_o->created_at = now();
                        $sewa_o->tgl_dicairkan = now();
                        $sewa_o->is_aktif = 'Y';
                        $sewa_o->save();

                        if($item == 'ALAT' || $item == 'TALLY' || $item == 'SEAL PELAYARAN'){
                            $i=1;
                            $driver = $value['supplier'] != 'null'? $value['supplier']:$value['driver'];
        
                            if($value['dicairkan'] != null){
                                if (array_key_exists($value['tujuan'], $storeData)) {
                                    // tambah data jika tujuan sudah ada
                                    $storeData[$value['tujuan']]['operasional'] += floatval(str_replace(',', '', $value['nominal']));
                                    $storeData[$value['tujuan']]['dicairkan'] += floatval(str_replace(',', '', $value['dicairkan']));
                                    $storeData[$value['tujuan']]['driver'] .= ' #'. $value['nopol'] .' ('.$driver.')';
                                    $storeData[$value['tujuan']]['id_opr'][] = $sewa_o->id;
                                    $storeData[$value['tujuan']]['index'] += 1;
                                } else {
                                    // buat data baru kalau data tujuan belum ada
                                    $storeData[$value['tujuan']] = [
                                        'operasional' => floatval(str_replace(',', '', $value['nominal'])),
                                        'dicairkan' => floatval(str_replace(',', '', $value['dicairkan'])),
                                        'driver' => '#'. $value['nopol'] .' ('.$driver.')',
                                        'id_opr' => [$sewa_o->id],
                                        'index' => $i,
                                    ];
                                }
                            }
                        }else{
                            $pembayaran = new SewaOperasionalPembayaran();
                            $pembayaran->deskripsi = $item;
                            $pembayaran->total_operasional = $total_operasional;
                            $pembayaran->total_dicairkan = $dicairkan;
                            // $pembayaran->catatan = '';
                            $pembayaran->created_by = $user;
                            $pembayaran->created_at = now();
                            if($pembayaran->save()){
                                $sewa_o->id_pembayaran = $pembayaran->id;
                                $sewa_o->save();

                                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['pembayaran'], // id kas_bank dr form
                                        now(), //tanggal
                                        0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        $sewa_o->total_dicairkan, //uang keluar (kredit)
                                        CoaHelper::DataCoa(5009), //kode coa
                                        'pencairan_operasional',
                                        $keterangan .= $value['keterangan'], //keterangan_transaksi
                                        $pembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional_pembayaran
                                        $user, //created_by
                                        now(), //created_at
                                        $user, //updated_by
                                        now(), //updated_at
                                        'Y'
                                    ) 
                                );
                                $saldo = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                                $saldo->saldo_sekarang -= $dicairkan;
                                $saldo->updated_by = $user;
                                $saldo->updated_at = now();
                                $saldo->save();

                                DB::commit(); // lakukan commit kalau bukat operasional/tally/seal
                            }
                        }
                    }
                }
                // dd($storeData);
            }

            if($item == 'ALAT' || $item == 'TALLY' || $item == 'SEAL PELAYARAN'){
                foreach ($storeData as $key => $dump) {

                    $pembayaran = new SewaOperasionalPembayaran();
                    $pembayaran->deskripsi = $item;
                    $pembayaran->total_operasional = $dump['operasional'];
                    $pembayaran->total_dicairkan = $dump['dicairkan'];
                    // $pembayaran->catatan = '';
                    $pembayaran->created_by = $user;
                    $pembayaran->created_at = now();
                    if($pembayaran->save()){
                        SewaOperasional::whereIn('id', $dump['id_opr'])
                                        ->where('is_aktif', 'Y')
                                        ->update(['id_pembayaran' => $pembayaran->id]);

                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'], //id kas_bank dr form
                                now(), //tanggal
                                0, //debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                $dump['dicairkan'], //uang keluar (kredit)
                                CoaHelper::DataCoa(5007), //kode coa
                                'pencairan_operasional',
                                $item.": ".$dump['index'].'X ' .$key." ".$dump['driver'], //keterangan_transaksi
                                $pembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional_pembayaran
                                $user, //created_by
                                now(), //created_at
                                $user, //updated_by
                                now(), //updated_at
                                'Y'
                            ) 
                        );
    
                        $saldo = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                        $saldo->saldo_sekarang -= $dump['dicairkan'];
                        $saldo->updated_by = $user;
                        $saldo->updated_at = now();
                        $saldo->save();
                    }
                }
    
                DB::commit();
            }

            return redirect()->route('biaya_operasional.index')->with(['status' => 'Success', 'msg' => 'Data berhasil dicairkan!']);
        } catch (ValidationException $e) {
            db::rollBack();
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

    public function load_data($item){
        try {
            if($item == 'KARANTINA'){
                $data = Karantina::where('is_aktif', 'Y')->where('total_dicairkan', NULL)->with('details', 'getJO', 'getCustomer.getGrup')->get();
            }else{
                $data = DB::table('sewa AS s')
                        ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
                            if($item == 'ALAT'){
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
                            'sp.nama as namaSupplier','s.tanggal_berangkat'
                        )
                        ->orderBy('s.id_sewa', 'DESC')
                        ->orderBy('s.tanggal_berangkat', 'DESC')
                        ->get();
            }
            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
