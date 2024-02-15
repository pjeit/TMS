<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\Sewa;
use App\Models\SewaOperasional;
use App\Models\Karantina;
use App\Models\KarantinaDetail;
use Carbon\Carbon;
use App\Helper\CoaHelper;
use App\Models\KasBank;
use App\Models\SewaOperasionalPembayaran;
use Exception;
use App\Helper\VariableHelper;

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
        $dataDriver = DB::table('karyawan')
        ->select('karyawan.*')
        ->distinct()
        ->Join('sewa as s', function($join) {
                    $join->on('karyawan.id', '=', 's.id_karyawan')
                    ->whereNull('s.id_supplier') 
                    // ->where('s.status_pencairan_driver', 'BELUM DICAIRKAN')
                    // ->where('s.total_komisi_driver', '!=', 0)
                    ->where('s.is_aktif', '=', "Y")
                    ;
                })
        ->where('karyawan.is_aktif', 'Y')
        ->where('karyawan.role_id', VariableHelper::Role_id('Driver'))//5 itu driver
        ->orderBy('karyawan.nama_panggilan', 'asc')
        ->get();
        $currentDate = Carbon::now();
        // $dataCustomerSewa = Sewa::with('getCustomer')
        // ->where('is_aktif', "Y")
        // ->where('status', "PROSES DOORING")
        // ->where(function ($where) use ($currentDate) {
        //     $where->where(function ($query) use ($currentDate) {
        //         // h-1 
        //         $query->whereDay('tanggal_berangkat', '=', $currentDate->subDay()->day)
        //             ->whereMonth('tanggal_berangkat', '=', $currentDate->month)
        //             ->whereYear('tanggal_berangkat', '=', $currentDate->year);
        //     })
        //     ->orWhere(function ($query) use ($currentDate) {
        //         // h0 
        //         $query->whereDay('tanggal_berangkat', '=', $currentDate->day)
        //             ->whereMonth('tanggal_berangkat', '=', $currentDate->month)
        //             ->whereYear('tanggal_berangkat', '=', $currentDate->year);
        //     })
        //     ->orWhere(function ($query) use ($currentDate) {
        //         // h+1 
        //         $query->whereDay('tanggal_berangkat', '=', $currentDate->addDay()->day) // Add 2 to get h+1
        //             ->whereMonth('tanggal_berangkat', '=', $currentDate->month)
        //             ->whereYear('tanggal_berangkat', '=', $currentDate->year);
        //     });
        // })
        // ->distinct('id_customer') 
        // ->get();

        // dd($dataCustomerSewa);
        // $item = "ALAT";
        // $data = DB::table('sewa AS s')
        //                 ->select(
        //                     's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
        //                     's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
        //                     'so.id AS so_id', 'so.deskripsi AS deskripsi_so', 'so.id as id_oprs', 'so.total_dicairkan',
        //                     'so.total_operasional AS so_total_oprs','k.nama_panggilan','jod.pick_up',
        //                     DB::raw('COALESCE(gt.tally, 0) as tally'), // ini get data tally di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
        //                     DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), // ini get data seal di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
        //                     'gt.nama_tujuan', 'gt.uang_jalan as uj_tujuan', 's.total_uang_jalan as uj_sewa',
        //                     'c.nama as customer',
        //                     'g.nama_grup as nama_grup',
        //                     'sp.nama as namaSupplier','s.tanggal_berangkat'
        //                 )
        //                 ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
        //                     if($item == 'ALAT'){
        //                         $join->on('s.id_sewa', '=', 'so.id_sewa')
        //                             ->where('so.is_aktif', 'Y')
        //                             ->where('so.deskripsi', 'like', $item.'%');
                                    
        //                             // ini nge get data alat, pakai like soalnya ALAT blablabla
        //                     }else {
        //                         $join->on('s.id_sewa', '=', 'so.id_sewa')
        //                             ->where('so.is_aktif', 'Y')
        //                             ->where('so.deskripsi', '=', $item);
        //                             // ini get kalau misal selain alat, langsung get datanya gapake like, karna udah fix deskripsinya
        //                     }
        //                 })
        //                 ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
        //                 ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
        //                 ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
        //                 ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
        //                 ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
        //                 ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
        //                 ->where('s.is_aktif', 'Y')
        //                 // ->whereNotNull('so.total_dicairkan')
        //                 ->where('s.status', 'PROSES DOORING')
        //                 ->where('s.jenis_tujuan', 'FTL')
        //                 // ->when($currentDate, function ($query) use ($currentDate) {
        //                 //     $query->where(function ($query) use ($currentDate) {
        //                 //         // h-1 
        //                 //         $query->whereDate('tanggal_berangkat', $currentDate->copy()->subDay());
        //                 //     })
        //                 //     ->orWhere(function ($query) use ($currentDate) {
        //                 //         // h0 
        //                 //         $query->whereDate('tanggal_berangkat', $currentDate);
        //                 //     })
        //                 //     ->orWhere(function ($query) use ($currentDate) {
        //                 //         // h+1 
        //                 //         $query->whereDate('tanggal_berangkat', $currentDate->copy()->addDay());
        //                 //     });
        //                 // })
        //                 // ->where(function($where) use($item){
        //                 //     // if($item == 'TAMBAHAN UJ'){
        //                 //     //     $where->where('gt.uang_jalan', '>', DB::raw('s.total_uang_jalan'));
        //                 //     // }
        //                 //     // if($item == 'BURUH' || $item == 'TIMBANG' || $item == 'LEMBUR'){
        //                 //     //     $where->where('s.id_supplier', '=', null);
        //                 //     // }
        //                 //     if($item == 'SEAL PELAYARAN'){
        //                 //         $where->where('s.jenis_order', 'OUTBOUND')->where('gt.seal_pelayaran','!=',0);
        //                 //     }
        //                 //     else if($item == 'TALLY'){
        //                 //         $where->where('gt.tally','!=',0);
        //                 //     }
        //                 // })
        //                 ->when($item == 'SEAL PELAYARAN', function ($query) {
        //                     $query->where('s.jenis_order', 'OUTBOUND')->where('gt.seal_pelayaran', '!=', 0);
        //                 })
        //                 ->when($item == 'TALLY', function ($query) {
        //                     $query->where('gt.tally', '!=', 0);

        //                 })
        //                 ->orderBy('s.id_sewa', 'DESC')
        //                 ->orderBy('s.tanggal_berangkat', 'DESC')
        //                 ->get();
        // dd($data);
        return view('pages.finance.biaya_operasional.index',[
            'judul' => "Biaya Operasional",
            'dataKas' => $dataKas,
            'dataDriver' => $dataDriver,
            // 'dataCustomerSewa' => $dataCustomerSewa,
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
            $item = $data['item_hidden'];
            $storeData = [];
            // dd($data);   
            
            if($item == 'KARANTINA'){
                foreach ($data['data'] as $key => $value) {
                    if($value['cek_cair'] == 'Y'){
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

                }
                DB::commit();
            }else{
                foreach ($data['data'] as $key => $value) {
                    $keterangan = $item.' : ';
                    // dd('masuk');
                    if($value['cek_cair'] == 'Y'){
                        $total_operasional = floatval(str_replace(',', '', $value['total_operasional']));
                        $dicairkan = floatval(str_replace(',', '', $value['dicairkan']));
                        // dd('masuk');
                        // dd($dicairkan);
                        // $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                        // if($sewa){
                        //     $sewa->total_uang_jalan += $dicairkan;
                        //     $sewa->updated_by = $user;
                        //     $sewa->updated_at = now();
                        //     $sewa->save();
                        // }
                        
                        $sewa_o = new SewaOperasional();
                        $sewa_o->id_sewa = $key;
                        $sewa_o->deskripsi = $item == 'ALAT'? 'ALAT' . ($value['pick_up'] != 'null'? ' '. $value['pick_up']:'') : $item;
                        // $sewa_o->catatan = $value['catatan'];
                        // if($item != 'TIMBANG' && $item != 'BURUH' && $item != 'LEMBUR'){
                            $sewa_o->total_operasional = $total_operasional;
                        // }
                        $sewa_o->total_dicairkan = $dicairkan;
                        $sewa_o->created_by = $user;
                        if ($value['dicairkan'] == 0) {
                            $sewa_o->status = 'TAGIHKAN DI INVOICE';
                            $sewa_o->catatan = $value['catatan'];
                            $sewa_o->keterangan_internal = "[TIDAK-ADA-PENCAIRAN]";
                            

                        } else {
                            $sewa_o->status = 'SUDAH DICAIRKAN';
                            $sewa_o->catatan = $value['catatan'];
                            $sewa_o->keterangan_internal = "[ADA-PENCAIRAN]";

                        }
                        
                        $sewa_o->created_at = now();
                        // $sewa_o->tgl_dicairkan = now();
                        $sewa_o->created_by = $user;
                        $sewa_o->is_aktif = 'Y';
                        $sewa_o->save();
                        // dd($sewa_o->id);
                        if($value['dicairkan'] != 0){
                            if($item == 'ALAT' || $item == 'TALLY' || $item == 'SEAL PELAYARAN'){
                                $i=1;
                                $driver = $value['supplier'] != 'null'? $value['supplier']:$value['driver'];
                                // ketika item == alat, tally, seal
                                // data bakal dikumpulin ke array, selain 3 data itu, langsung dicairin ke dump
    
                                // if($value['dicairkan'] != null){
                                    if (array_key_exists($value['tujuan'], $storeData)) {
                                        // tambah data jika tujuan sudah ada
                                        // ini ngedit datanya, misal X tadi udah di input
                                        // kalau udah ada, di concat data yg lama, misal tujuan X, tadi sudah ada driver Y,
                                        // nah didata selanjutnya ada driver Z ke tujuan X juga, data di concat jadi driver: #Y #Z
                                        // terus total_operasional di increment 100+200 
                                        // intinya disini ngeconcat data yg ada
                                        $storeData[$value['tujuan']]['operasional'] += floatval(str_replace(',', '', $value['total_operasional']));
                                        $storeData[$value['tujuan']]['dicairkan'] += floatval(str_replace(',', '', $value['dicairkan']));
                                        $storeData[$value['tujuan']]['driver'] .= ' #'. $value['nopol'] .' ('.$driver.')';
                                        $storeData[$value['tujuan']]['id_opr'][] = $sewa_o->id;
                                        $storeData[$value['tujuan']]['index'] += 1;
                                        // CONTOHNYA:
                                        // array:1 [▼
                                        // "**PT. Cargil Indonesia - PIER  20 (Perak)" => array:5 [▼
                                        //         "operasional" => 45000.0
                                        //         "dicairkan" => 45000.0
                                        //         "driver" => "#L 8902 UUC (BASMAN) #L 9813 UC (HASAN) #L 8901 UUC (TAROM)"
                                        //         "id_opr" => array:3 [▼
                                        //         0 => 9567
                                        //         1 => 9568
                                        //         2 => 9569
                                        //         ]
                                        //         "index" => 3
                                        //     ]
                                        // ]
                                    } else {
                                        // buat data baru kalau data tujuan belum ada
                                        // ini nyimpen data per tujuan, misal tujuan X, driver Y, dicairkan 100
                                        $storeData[$value['tujuan']] = [
                                            'operasional' => floatval(str_replace(',', '', $value['total_operasional'])),
                                            'dicairkan' => floatval(str_replace(',', '', $value['dicairkan'])),
                                            'driver' => '#'. $value['nopol'] .' ('.$driver.')',
                                            'id_opr' => [$sewa_o->id],
                                            'index' => $i,
                                        ];
                                    }
                                // }
                            }else{
                                $pembayaran = new SewaOperasionalPembayaran();
                                $pembayaran->id_kas_bank = $data['pembayaran'];
                                $pembayaran->tgl_dicairkan = now();
                                $pembayaran->deskripsi = $item;
                                $pembayaran->total_operasional = $total_operasional;
                                $pembayaran->total_dicairkan = $dicairkan;
                                // $pembayaran->catatan = '';$data['pembayaran']
                                $pembayaran->created_at = now();
                                $pembayaran->created_by = $user;
                                if($pembayaran->save()){
                                    $sewa_o->id_pembayaran = $pembayaran->id;
                                    $sewa_o->save();
    
                                    // ini langsung dicairin ke dump
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
                }
                // dd($storeData);
            }

            if($item == 'ALAT' || $item == 'TALLY' || $item == 'SEAL PELAYARAN'){
                        foreach ($storeData as $key => $dump) {
                            if($dump['dicairkan'] != 0){
                                // ini ngecreate data pembayaran
                                $pembayaran = new SewaOperasionalPembayaran();
                                $pembayaran->id_kas_bank = $data['pembayaran'];
                                $pembayaran->deskripsi = $item;
                                $pembayaran->tgl_dicairkan = now();
                                $pembayaran->total_operasional = $dump['operasional'];
                                $pembayaran->total_dicairkan = $dump['dicairkan'];
                                // $pembayaran->catatan = '';
                                $pembayaran->created_at = now();
                                $pembayaran->created_by = $user;
                                if($pembayaran->save()){
                                    // ini ngedit sewa operasional, ditempelin id pembayaran
                                    // logic mirip invoice pembayaran
                                    SewaOperasional::whereIn('id', $dump['id_opr'])
                                                    ->where('is_aktif', 'Y')
                                                    ->update(['id_pembayaran' => $pembayaran->id]);
                                    
                                    // ini ngedump data array yg concat tadi
                                    // misal tujuan X, driver #Y #Z, total_operasional 300
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
                    }
                DB::commit();
            }
            DB::commit();
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
            $currentDate = Carbon::now();
            if($item == 'KARANTINA'){
                $data = Karantina::where('is_aktif', 'Y')->where('total_dicairkan', NULL)->with('details', 'getJO', 'getCustomer.getGrup')->get();
            }else{
                $data = DB::table('sewa AS s')
                        ->select(
                            's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
                            's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                            'so.id AS so_id', 'so.deskripsi AS deskripsi_so', 'so.id as id_oprs', 'so.total_dicairkan',
                            'so.total_operasional AS so_total_oprs','k.nama_panggilan','jod.pick_up',
                            DB::raw('COALESCE(gt.tally, 0) as tally'), // ini get data tally di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
                            DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), // ini get data seal di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
                            'gt.nama_tujuan', 'gt.uang_jalan as uj_tujuan', 's.total_uang_jalan as uj_sewa',
                            'c.nama as customer',
                            'g.nama_grup as nama_grup',
                            'sp.nama as namaSupplier','s.tanggal_berangkat'
                        )
                        ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
                            if($item == 'ALAT'){
                                $join->on('s.id_sewa', '=', 'so.id_sewa')
                                    ->where('so.is_aktif', 'Y')
                                    ->where('so.deskripsi', 'like', $item.'%');
                                    
                                    // ini nge get data alat, pakai like soalnya ALAT blablabla
                            }else {
                                $join->on('s.id_sewa', '=', 'so.id_sewa')
                                    ->where('so.is_aktif', 'Y')
                                    ->where('so.deskripsi', '=', $item);
                                    // ini get kalau misal selain alat, langsung get datanya gapake like, karna udah fix deskripsinya
                            }
                        })
                        ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                        ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                        ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                        ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                        ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                        ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                        // ->where('s.is_aktif', 'Y')
                        // ->whereNull('so.total_dicairkan')
                        // ->where('s.status', 'PROSES DOORING')
                        // ->where('s.jenis_tujuan', 'FTL')
                        // ->when($currentDate, function ($query) use ($currentDate) {
                        //     $query->where(function ($query) use ($currentDate) {
                        //         // h-1 
                        //         $query->whereDate('tanggal_berangkat', $currentDate->copy()->subDay());
                        //     })
                        //     ->orWhere(function ($query) use ($currentDate) {
                        //         // h0 
                        //         $query->whereDate('tanggal_berangkat', $currentDate);
                        //     })
                        //     ->orWhere(function ($query) use ($currentDate) {
                        //         // h+1 
                        //         $query->whereDate('tanggal_berangkat', $currentDate->copy()->addDay());
                        //     });
                        // })
                        ->where(function ($query) use ($currentDate) {
                            $query->whereBetween('tanggal_berangkat', [
                                $currentDate->copy()->subDay()->startOfDay(),
                                $currentDate->copy()->addDay()->endOfDay()
                            ]);
                        })
                        ->where(function ($query) use ($currentDate, $item ) {
                            // $query->whereNull('s.id_supplier');
                            $query->whereNull('so.total_dicairkan');
                            $query->where('s.jenis_tujuan', 'FTL');
                            $query->where('s.is_aktif', 'Y');
                            $query->where('s.status', 'PROSES DOORING');
                            if ($item == 'SEAL PELAYARAN') {
                                $query->where('s.jenis_order', 'OUTBOUND')->where('gt.seal_pelayaran', '!=', 0);

                            }
                            if ($item == 'TALLY') {
                                $query->where('gt.tally', '!=', 0);

                            }
                        })
                        // ->when($item == 'SEAL PELAYARAN', function ($query) {
                        //     $query->where('s.jenis_order', 'OUTBOUND')->where('gt.seal_pelayaran', '!=', 0);
                        // })
                        // ->when($item == 'TALLY', function ($query) {
                        //     $query->where('gt.tally', '!=', 0);

                        // })
                        ->orderBy('s.id_sewa', 'DESC')
                        ->orderBy('s.tanggal_berangkat', 'DESC')
                        ->get();
                        // intinya, pertama get data sewa
                        // terus kamu left join ke data sewa operasional, kalau misal ada sewa operasional, dengan deskripsi tersebut
                        // pasti bakal muncul total_operasionalnya, misal buruh 15000, alat 30000, tp misal waktu di join ga ada, maka nanti total_operasional 0
                        // nah, kalau data 0, nanti datanya dimunculin di frontend, kalau ga 0, ga dimunculin
                        // tapi kalau tally sama seal pelayaran, pokok data master di grup tujuan, baru muncul jika ada angkanya
                        // kebalikan dari sewa operasional
                        // sewa operasional = ketika join data 0, ga muncul di front end
                        // grup tujuan (tally, seal) = ketika data TIDAK 0, maka muncul di front end
            }
            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
    public function load_data_gabung($item,$customer,$tujuan){
        try {
                $currentDate = Carbon::now();
                $data = DB::table('sewa AS s')
                        ->select(
                            's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
                            's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                            'so.id AS so_id', 'so.deskripsi AS deskripsi_so', 'so.id as id_oprs', 'so.total_dicairkan',
                            'so.total_operasional AS so_total_oprs','k.nama_panggilan','jod.pick_up',
                            // DB::raw('COALESCE(gt.tally, 0) as tally'), // ini get data tally di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
                            // DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), // ini get data seal di grup tujuan (gt), kalau di gt gak diset, nanti total_operasional 0, kalau 0 ga bakal muncul di frontend
                            'gt.nama_tujuan', 
                            'gt.uang_jalan as uj_tujuan', 
                            's.total_uang_jalan as uj_sewa',
                            'c.nama as customer',
                            'g.nama_grup as nama_grup',
                            'sp.nama as namaSupplier','s.tanggal_berangkat',
                            's.buruh_pje'
                        )
                        ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
                            $join->on('s.id_sewa', '=', 'so.id_sewa')
                                ->where('so.is_aktif', 'Y')
                                ->where('so.deskripsi', '=', $item);
                                // ini get kalau misal selain alat, langsung get datanya gapake like, karna udah fix deskripsinya
                        })
                        ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                        ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                        ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                        ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                        ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                        ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                        ->where(function ($query) use ($currentDate) {
                            $query->whereBetween('tanggal_berangkat', [
                                $currentDate->copy()->subDay()->startOfDay(),
                                $currentDate->copy()->addDay()->endOfDay()
                            ]);
                        })
                        ->where(function ($query) use ($currentDate, $item, $customer, $tujuan) {
                            $query->whereNull('s.id_supplier');
                            $query->whereNull('so.total_dicairkan');
                            $query->where('s.jenis_tujuan', 'FTL');
                            $query->where('s.is_aktif', 'Y');
                            $query->where('s.status', 'PROSES DOORING');
                            if ($item == 'BURUH') {
                                $query->where('s.buruh_pje', 'Y');
                            }
                            if($customer!='ALL')
                            {
                                $query->where('s.id_customer', '=', $customer);
                            }
                            if($tujuan!='ALL')
                            {
                                $query->where('s.id_grup_tujuan', '=', $tujuan);
                            }
                        })
                        ->orderBy('s.id_sewa', 'DESC')
                        ->orderBy('s.tanggal_berangkat', 'DESC')
                        ->get();
                        // intinya, pertama get data sewa
                        // terus kamu left join ke data sewa operasional, kalau misal ada sewa operasional, dengan deskripsi tersebut
                        // pasti bakal muncul total_operasionalnya, misal buruh 15000, alat 30000, tp misal waktu di join ga ada, maka nanti total_operasional 0
                        // nah, kalau data 0, nanti datanya dimunculin di frontend, kalau ga 0, ga dimunculin
                        // tapi kalau tally sama seal pelayaran, pokok data master di grup tujuan, baru muncul jika ada angkanya
                        // kebalikan dari sewa operasional
                        // sewa operasional = ketika join data 0, ga muncul di front end
                        // grup tujuan (tally, seal) = ketika data TIDAK 0, maka muncul di front end
            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
    
    public function load_customer_sewa($item){
        try {
            $currentDate = Carbon::now();
            $dataCustomerSewa = Sewa::with('getCustomer')
            ->leftJoin('sewa_operasional AS so', function ($join) use($item) {
                    $join->on('sewa.id_sewa', '=', 'so.id_sewa')
                        ->where('so.is_aktif', 'Y')
                        ->where('so.deskripsi', '=', $item);
            })
            ->whereNull('so.total_dicairkan')
            ->where('sewa.is_aktif', "Y")
            ->where('sewa.status', "PROSES DOORING")
            ->where(function ($query) use ($currentDate) {
                $query->whereBetween('tanggal_berangkat', [
                    $currentDate->copy()->subDay()->startOfDay(),
                    $currentDate->copy()->addDay()->endOfDay()
                ]);
            })
            ->where(function ($query) use ( $item) {
                // Other conditions
                $query->where('sewa.id_supplier', '=', null);

                if ($item == 'BURUH') {
                    $query->where('sewa.buruh_pje', 'Y');
                }
                
            })
            
            ->groupBy('sewa.id_customer')
            ->get();
            return response()->json(["result" => "success",'data' => $dataCustomerSewa], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
    public function load_tujuan_sewa($customer,$item){
        try {
            $currentDate = Carbon::now();
            $dataTujuanSewa = Sewa::with('getTujuan')
            ->where('is_aktif', "Y")
            ->where('status', "PROSES DOORING")
            ->where('id_customer', $customer)
            ->where(function ($query) use ($currentDate) {
                $query->whereBetween('tanggal_berangkat', [
                    $currentDate->copy()->subDay()->startOfDay(),
                    $currentDate->copy()->addDay()->endOfDay()
                ]);
            })
            ->where(function ($query) use ($item) {
                $query->where('sewa.id_supplier', '=', null);

                if ($item == 'BURUH') {
                    $query->where('sewa.buruh_pje', 'Y');
                }
                
            })
            ->groupBy('sewa.id_grup_tujuan')
            ->get();
            return response()->json(["result" => "success",'data' => $dataTujuanSewa], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
