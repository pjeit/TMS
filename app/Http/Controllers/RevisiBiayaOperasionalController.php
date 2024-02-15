<?php

namespace App\Http\Controllers;

use App\Models\Karantina;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\SewaOperasional;
use App\Models\SewaOperasionalPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\CoaHelper;
use Exception;

class RevisiBiayaOperasionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_REVISI_BIAYA_OPERASIONAL', ['only' => ['index']]);
		$this->middleware('permission:CREATE_REVISI_BIAYA_OPERASIONAL', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_REVISI_BIAYA_OPERASIONAL', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_REVISI_BIAYA_OPERASIONAL', ['only' => ['destroy']]);  
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

        return view('pages.revisi.revisi_biaya_operasional.index',[
            'judul' => "Revisi Biaya Operasional",
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
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);
        $storeData = [];
        

        if($data['type'] == 'save'){
            try {
                if($data['item'] == 'KARANTINA'){
                    foreach ($data['data'] as $key => $karantina) {
                        if(isset($karantina['check'])){
                            $krnt = Karantina::where('is_aktif', 'Y')->find($key);
                            $jenis = 'karantina';
                            $coa =  CoaHelper::DataCoa(5003);

                            if($krnt){
                                $krnt->total_dicairkan = floatval(str_replace(',', '', $karantina['dicairkan']));
                                $krnt->catatan = $karantina['catatan'];
                                $krnt->updated_by = $user;
                                $krnt->updated_at = now();
                                if($krnt->save()){
                                    // find history dump
                                    $history = KasBankTransaction::where('is_aktif', 'Y')
                                                ->where('jenis', $jenis)
                                                ->where('keterangan_kode_transaksi', $krnt->id)
                                                ->first();

                                    if($history){
                                        $keterangan_transaksi = $history->keterangan_transaksi;
                                        $history->keterangan_transaksi = 'REVISI - ' .$keterangan_transaksi;
                                        $history->updated_by = $user;
                                        $history->updated_at = now();
                                        $history->is_aktif = 'N';
                                        if($history->save()){
                                            // uang kasbank dikembalikan
                                            $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                            if($kasbank){
                                                $kasbank->saldo_sekarang += $history->kredit;
                                                $kasbank->updated_by = $user;
                                                $kasbank->updated_at = now();
                                                if($kasbank->save()){
                                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                        array(
                                                            $history->id_kas_bank, // id kas_bank dr form
                                                            $history->tanggal, //tanggal
                                                            0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                            $krnt->total_dicairkan, //uang keluar (kredit)
                                                            $coa, //kode coa
                                                            $jenis,
                                                            'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
                                                            $krnt->id, //keterangan_kode_transaksi // id_sewa_operasional
                                                            $user, //created_by
                                                            now(), //created_at
                                                            $user, //updated_by
                                                            now(), //updated_at
                                                            'Y'
                                                        )
                                                    );
                    
                                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                                    if($kasbank){
                                                        $kasbank->saldo_sekarang -= $krnt->total_dicairkan;
                                                        $kasbank->updated_by = $user;
                                                        $kasbank->updated_at = now();
                                                        $kasbank->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    }

                    DB::commit();
                    return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
                }else{
                    foreach ($data['data'] as $key => $operasionals) {
                        $total_dicairkan = 0;
                        $total_operasional = 0;

                        $is_berubah = false;
                        
                        foreach ($operasionals as $keyOprs => $value) {
                            $jenis = 'pencairan_operasional';
                            $coa =  CoaHelper::DataCoa(5009);
                            //value check sama keyOprs itu id sewa_operasional
                            if(isset($value['check'])){
                                $i=1;

                                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
                                // dd($keyOprs);
                                if($oprs){
                                    $oprs->total_operasional = floatval(str_replace(',', '', $value['total_operasional']));
                                    $oprs->total_dicairkan = floatval(str_replace(',', '', $value['total_dicairkan']));
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
                                    if (array_key_exists($value['tujuan'], $storeData)) {
                                        $storeData[$value['tujuan']]['driver'] .= ' #'. $value['driver_nopol'];
                                        // $storeData[$value['tujuan']]['id_opr'][] = $keyOprs;
                                        $storeData[$value['tujuan']]['index'] += 1;
                                        // CONTOHNYA:
                                        // array:1 [▼
                                        // "**PT. Cargil Indonesia - PIER  20 (Perak)" => array:5 [▼
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
                                        // buat insialiasi awal misal tujuan 1 driver 1
                                        $storeData[$value['tujuan']] = [
                                            'driver' => '#'. $value['driver_nopol'],
                                            // 'id_opr' => [$keyOprs],
                                            'index' => $i,
                                        ];
                                    }
    
                                    $total_dicairkan += $oprs->total_dicairkan;
                                    $total_operasional += $oprs->total_operasional;
                                }
                                $is_berubah = true; //benar ada perubahan
                                //terus disni kalau ada tujuan 1 masuk sini dia
                                // dd($storeData);

                            }
                        }
                                // dd($storeData[14]['Daan Mogot 20']);
                        // $data['item'].": ".$total_item.'X ' .$value['tujuan']." ".$driver_nopol;//keterangan_transaksi
                        // dd( $data['item'].": ".$total_item.'X ' .$value['tujuan']." ".$driver_nopol);
                        // dd($storeData);
                        if($is_berubah == true){

                            //key itu id sewa operasional pembayaran
                            $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
                            $jenis = 'pencairan_operasional';
    
                            if($pembayaran){ 
                                // carik dulu transaksinya di dump
                                $history_lama = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('jenis', $jenis)
                                            ->where('keterangan_kode_transaksi', $pembayaran->id)
                                            ->first();
        
                                if($history_lama){
                                    // uang kasbank dikembalikan
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history_lama->id_kas_bank);
                                    if($kasbank){
                                        $kasbank->saldo_sekarang += $history_lama->kredit;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        $kasbank->save();
                                    }
                                }
                                $pembayaran->total_dicairkan = $total_dicairkan;
                                $pembayaran->total_operasional = $total_operasional;
                                $pembayaran->catatan = 'REVISI PERUBAHAN PENCAIRAN - '. $data['alasan'];
                                $pembayaran->updated_by = $user;
                                $pembayaran->updated_at = now();
                                // $pembayaran->save();
                                if($pembayaran->save()){
                                    $history_baru = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('jenis', $jenis)
                                            ->where('keterangan_kode_transaksi', $pembayaran->id)
                                            ->first();
        
                                    if($history_baru){

                                        $keterangan_transaksi = $history_baru->keterangan_transaksi;
                                        foreach ($storeData as $keyNamaTujuan => $dump) {
                                            $history_baru->keterangan_transaksi = 'REVISI - ' .$data['item'].": ".$dump['index'].'X ' .$keyNamaTujuan." ".$dump['driver'];
                                        }
                                        $history_baru->kredit = $total_dicairkan;
                                        $history_baru->updated_by = $user;
                                        $history_baru->updated_at = now();
                                        // $history_baru->is_aktif = 'N';
                                        if($history_baru->save()){
                                            $kasbank_update_saldo = KasBank::where('is_aktif', 'Y')->find($history_baru->id_kas_bank);
                                            if($kasbank_update_saldo){
                                                $kasbank_update_saldo->saldo_sekarang -= $total_dicairkan;
                                                $kasbank_update_saldo->updated_by = $user;
                                                $kasbank_update_saldo->updated_at = now();
                                                $kasbank_update_saldo->save();
                                            }
                                        }
                                    }
                                    // dd( $history_baru);
                                }
                            }
                        }
                        
                    }

                    DB::commit();
                    return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
                }
    
            } catch (ValidationException $e) {
                db::rollBack();
                return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
            }
        }else if($data['type'] == 'delete'){
            try {
                // dd($data);
                if($data['item'] == 'KARANTINA'){
                    foreach ($data['data'] as $key => $karantina) {
                        if(isset($karantina['check'])){
                            $krnt = Karantina::where('is_aktif', 'Y')->find($key);
                            $jenis = 'karantina';
                            $coa = CoaHelper::DataCoa(5003);
                            if($krnt){
                                $krnt->catatan = $karantina['catatan'];
                                $krnt->total_dicairkan = null;
                                $krnt->is_ditagihkan = 'N';
                                $krnt->updated_by = $user;
                                $krnt->updated_at = now();
                                // $krnt->is_aktif = 'N';
                                if($krnt->save()){
                                    // find history dump
                                    $history = KasBankTransaction::where('is_aktif', 'Y')
                                                ->where('jenis', $jenis)
                                                ->where('keterangan_kode_transaksi', $krnt->id)
                                                ->first();

                                    if($history){
                                        $keterangan_transaksi = $history->keterangan_transaksi;
                                        $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
                                        $history->updated_by = $user;
                                        $history->updated_at = now();
                                        $history->is_aktif = 'N';
                                        if($history->save()){
                                            // uang kasbank dikembalikan
                                            $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                            if($kasbank){
                                                $kasbank->saldo_sekarang += $history->kredit;
                                                $kasbank->updated_by = $user;
                                                $kasbank->updated_at = now();
                                                $kasbank->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    DB::commit();
                    return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
                }else{
                    foreach ($data['data'] as $key => $operasionals) {
                        $total_dicairkan = 0;
                        $total_operasional = 0;

                        $is_delete = false;
                        foreach ($operasionals as $keyOprs => $value) {
                            $jenis = 'pencairan_operasional';
                            $coa = CoaHelper::DataCoa(5009);
        
                            if(isset($value['check'])){ // ini data yg dihapus
                                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
        
                                if($oprs){
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->is_aktif = 'N';
                                    $oprs->save();
                                }
                                $is_delete = true; //benar ada perubahan
                                
                            }else{
                                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
                                $i=1;
                                if($oprs){
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
                                    $total_dicairkan += $oprs->total_dicairkan;
                                    $total_operasional += $oprs->total_operasional;
                                    if (array_key_exists($value['tujuan'], $storeData)) {
                                        $storeData[$value['tujuan']]['driver'] .= ' #'. $value['driver_nopol'];
                                        // $storeData[$value['tujuan']]['id_opr'][] = $keyOprs;
                                        $storeData[$value['tujuan']]['index'] += 1;
                                        // CONTOHNYA:
                                        // array:1 [▼
                                        // "**PT. Cargil Indonesia - PIER  20 (Perak)" => array:5 [▼
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
                                        // buat insialiasi awal misal tujuan 1 driver 1
                                        $storeData[$value['tujuan']] = [
                                            'driver' => '#'. $value['driver_nopol'],
                                            // 'id_opr' => [$keyOprs],
                                            'index' => $i,
                                        ];
                                    }
                                }
                            }
                        }
                        // dd($storeData);
                        if($is_delete == true){
                            // dd($total_dicairkan);
                            //key itu id sewa operasional pembayaran
                            $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
                            $jenis = 'pencairan_operasional';
                            if($pembayaran){ 
                                // carik dulu transaksinya di dump
                                $history_lama = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('jenis', $jenis)
                                            ->where('keterangan_kode_transaksi', $pembayaran->id)
                                            ->first();
                                if($history_lama){
                                    // uang kasbank dikembalikan
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history_lama->id_kas_bank);
                                    if($kasbank){
                                        $kasbank->saldo_sekarang += $history_lama->kredit;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        // $kasbank->save();
                                        if($kasbank->save())
                                        {
                                            $pembayaran->total_operasional = $total_operasional;
                                            $pembayaran->total_dicairkan = $total_dicairkan;
                                            $pembayaran->catatan = 'REVISI - '. $data['alasan'];
                                            if ($total_dicairkan == 0) {
                                                //JADI INI KALAU PEMBAYARANNYA 0 TERUS ANAKANNYA GA ADA, DI NONAKTIFIN artinya hapus transaksi
                                                $pembayaran->is_aktif = "N";
                                            }
                                            $pembayaran->updated_by = $user;
                                            $pembayaran->updated_at = now();
                                            // $pembayaran->save();
                                            if($pembayaran->save()){
                                                $history_baru = KasBankTransaction::where('is_aktif', 'Y')
                                                        ->where('jenis', $jenis)
                                                        ->where('keterangan_kode_transaksi', $pembayaran->id)
                                                        ->first();
                                                if($history_baru){
                                                    $keterangan_transaksi = $history_baru->keterangan_transaksi;
                                                    $history_baru->kredit = $total_dicairkan;
                                                    // $history_baru->keterangan_transaksi = 'REVISI - ' .$keterangan_transaksi;
                                                    foreach ($storeData as $keyNamaTujuan => $dump) {
                                                        $history_baru->keterangan_transaksi = 'REVISI - ' .$data['item'].": ".$dump['index'].'X ' .$keyNamaTujuan." ".$dump['driver'];
                                                    }
                                                    $history_baru->updated_by = $user;
                                                    $history_baru->updated_at = now();
                                                    if ($total_dicairkan == 0) {
                                                        $history_baru->is_aktif = 'N';
                                                    }
                                                    if($history_baru->save()){
                                                        $kasbank_update_saldo = KasBank::where('is_aktif', 'Y')->find($history_baru->id_kas_bank);
                                                        if($kasbank_update_saldo){
                                                            $kasbank_update_saldo->saldo_sekarang -= $total_dicairkan;
                                                            $kasbank_update_saldo->updated_by = $user;
                                                            $kasbank_update_saldo->updated_at = now();
                                                            $kasbank_update_saldo->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                
                            }
                        }
        
                        // if($is_delete == true){
                        //     $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
                        //     $jenis = 'pencairan_operasional';
        
                        //     if($pembayaran){ 
                        //         $pembayaran->catatan = 'REVISI OFF - '. $data['alasan'] .' - '. $pembayaran->catatan;
                        //         $pembayaran->updated_by = $user;
                        //         $pembayaran->updated_at = now();
                        //         $pembayaran->is_aktif = 'N';
                        //         $pembayaran->save();
        
                        //         // find history dump
                        //         $history = KasBankTransaction::where('is_aktif', 'Y')
                        //                     ->where('jenis', $jenis)
                        //                     ->where('keterangan_kode_transaksi', $pembayaran->id)
                        //                     ->first();
        
                        //         if($history){
                        //             $keterangan_transaksi = $history->keterangan_transaksi;
                        //             $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
                        //             $history->updated_by = $user;
                        //             $history->updated_at = now();
                        //             $history->is_aktif = 'N';
                        //             if($history->save()){
                        //                 // uang kasbank dikembalikan
                        //                 $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                        //                 if($kasbank){
                        //                     $kasbank->saldo_sekarang += $history->kredit;
                        //                     $kasbank->updated_by = $user;
                        //                     $kasbank->updated_at = now();
                        //                     $kasbank->save();
                        //                 }
                        //             }
                        //         }
        
                        //         $newPembayaran = new SewaOperasionalPembayaran();
                        //         $newPembayaran->deskripsi = $data['item'];
                        //         $newPembayaran->total_dicairkan = $total_dicairkan;
                        //         $newPembayaran->catatan = 'REVISI';
                        //         $newPembayaran->created_by = $user;
                        //         $newPembayaran->created_at = now();
                        //         if($newPembayaran->save()){
                        //             DB::table('sewa_operasional')
                        //                 ->where('id_pembayaran', $pembayaran->id) 
                        //                 ->where('is_aktif', 'Y')
                        //                 ->update([
                        //                     'id_pembayaran' => $newPembayaran->id,
                        //                 ]);
        
                        //             DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        //                 array(
                        //                     $history->id_kas_bank, // id kas_bank dr form
                        //                     $history->tanggal, //tanggal
                        //                     0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                        //                     $total_dicairkan, //uang keluar (kredit)
                        //                     $coa, //kode coa
                        //                     $jenis,
                        //                     'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
                        //                     $newPembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional
                        //                     $user, //created_by
                        //                     now(), //created_at
                        //                     $user, //updated_by
                        //                     now(), //updated_at
                        //                     'Y'
                        //                 )
                        //             );
        
                        //             $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                        //             if($kasbank){
                        //                 $kasbank->saldo_sekarang -= $total_dicairkan;
                        //                 $kasbank->updated_by = $user;
                        //                 $kasbank->updated_at = now();
                        //                 $kasbank->save();
                        //             }
                        //         }
                        //     }
                        // }
                    }
    
                    DB::commit();
                    return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
                }
            } catch (ValidationException $e) {
                db::rollBack();
                return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Hapus data gagal!']);
            }


        }

    }
    // public function storeBackup(Request $request)
    // {
    //     $user = Auth::user()->id;
    //     $data = $request->collect();
    //     DB::beginTransaction(); 
    //     // dd($data);

    //     if($data['type'] == 'save'){
    //         try {
    //             if($data['item'] == 'KARANTINA'){
    //                 foreach ($data['data'] as $key => $karantina) {
    //                     if(isset($karantina['check'])){
    //                         $krnt = Karantina::where('is_aktif', 'Y')->find($key);
    //                         $jenis = 'karantina';
    //                         $coa =  CoaHelper::DataCoa(5003);

    //                         if($krnt){
    //                             $krnt->total_dicairkan = floatval(str_replace(',', '', $karantina['dicairkan']));
    //                             $krnt->catatan = $karantina['catatan'];
    //                             $krnt->updated_by = $user;
    //                             $krnt->updated_at = now();
    //                             if($krnt->save()){
    //                                 // find history dump
    //                                 $history = KasBankTransaction::where('is_aktif', 'Y')
    //                                             ->where('jenis', $jenis)
    //                                             ->where('keterangan_kode_transaksi', $krnt->id)
    //                                             ->first();

    //                                 if($history){
    //                                     $keterangan_transaksi = $history->keterangan_transaksi;
    //                                     $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
    //                                     $history->updated_by = $user;
    //                                     $history->updated_at = now();
    //                                     $history->is_aktif = 'N';
    //                                     if($history->save()){
    //                                         // uang kasbank dikembalikan
    //                                         $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                         if($kasbank){
    //                                             $kasbank->saldo_sekarang += $history->kredit;
    //                                             $kasbank->updated_by = $user;
    //                                             $kasbank->updated_at = now();
    //                                             if($kasbank->save()){
    //                                                 DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
    //                                                     array(
    //                                                         $history->id_kas_bank, // id kas_bank dr form
    //                                                         $history->tanggal, //tanggal
    //                                                         0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
    //                                                         $krnt->total_dicairkan, //uang keluar (kredit)
    //                                                         $coa, //kode coa
    //                                                         $jenis,
    //                                                         'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
    //                                                         $krnt->id, //keterangan_kode_transaksi // id_sewa_operasional
    //                                                         $user, //created_by
    //                                                         now(), //created_at
    //                                                         $user, //updated_by
    //                                                         now(), //updated_at
    //                                                         'Y'
    //                                                     )
    //                                                 );
                    
    //                                                 $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                                 if($kasbank){
    //                                                     $kasbank->saldo_sekarang -= $krnt->total_dicairkan;
    //                                                     $kasbank->updated_by = $user;
    //                                                     $kasbank->updated_at = now();
    //                                                     $kasbank->save();
    //                                                 }
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }

    //                     }
    //                 }

    //                 DB::commit();
    //                 return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
    //             }else{
    //                 foreach ($data['data'] as $key => $operasionals) {
    //                     $total_dicairkan = 0;
    //                     $is_off = false;
    //                     foreach ($operasionals as $keyOprs => $value) {
    //                         $jenis = 'pencairan_operasional';
    //                         $coa =  CoaHelper::DataCoa(5009);
    
    //                         if(isset($value['check'])){
    //                             $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
    
    //                             if($oprs){
    //                                 $oprs->total_operasional = floatval(str_replace(',', '', $value['total_operasional']));
    //                                 $oprs->total_dicairkan = floatval(str_replace(',', '', $value['total_dicairkan']));
    //                                 $oprs->catatan = $value['catatan'];
    //                                 $oprs->updated_by = $user;
    //                                 $oprs->updated_at = now(); 
    //                                 $oprs->save();
    
    //                                 $total_dicairkan += $oprs->total_dicairkan;
    //                             }
    //                             $is_off = true; //benar ada perubahan
    //                         }
    //                     }
    
    //                     if($is_off == true){
    //                         $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
    //                         $jenis = 'pencairan_operasional';
    
    //                         if($pembayaran){ 
    //                             $pembayaran->catatan = 'REVISI OFF - '. $data['alasan'] .' - '. $pembayaran->catatan;
    //                             $pembayaran->updated_by = $user;
    //                             $pembayaran->updated_at = now();
    //                             $pembayaran->is_aktif = 'N';
    //                             $pembayaran->save();
        
    //                             // find history dump
    //                             $history = KasBankTransaction::where('is_aktif', 'Y')
    //                                         ->where('jenis', $jenis)
    //                                         ->where('keterangan_kode_transaksi', $pembayaran->id)
    //                                         ->first();
        
    //                             if($history){
    //                                 $keterangan_transaksi = $history->keterangan_transaksi;
    //                                 $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
    //                                 $history->updated_by = $user;
    //                                 $history->updated_at = now();
    //                                 $history->is_aktif = 'N';
    //                                 if($history->save()){
    //                                     // uang kasbank dikembalikan
    //                                     $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                     if($kasbank){
    //                                         $kasbank->saldo_sekarang += $history->kredit;
    //                                         $kasbank->updated_by = $user;
    //                                         $kasbank->updated_at = now();
    //                                         $kasbank->save();
    //                                     }
    //                                 }
    //                             }
    
    //                             $newPembayaran = new SewaOperasionalPembayaran();
    //                             $newPembayaran->deskripsi = $data['item'];
    //                             $newPembayaran->total_dicairkan = $total_dicairkan;
    //                             $newPembayaran->catatan = 'REVISI';
    //                             $newPembayaran->created_by = $user;
    //                             $newPembayaran->created_at = now();
    //                             if($newPembayaran->save()){
    //                                 DB::table('sewa_operasional')
    //                                     ->where('id_pembayaran', $pembayaran->id) 
    //                                     ->where('is_aktif', 'Y')
    //                                     ->update([
    //                                         'id_pembayaran' => $newPembayaran->id,
    //                                     ]);
    
    //                                 DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
    //                                     array(
    //                                         $history->id_kas_bank, // id kas_bank dr form
    //                                         $history->tanggal, //tanggal
    //                                         0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
    //                                         $total_dicairkan, //uang keluar (kredit)
    //                                         $coa, //kode coa
    //                                         $jenis,
    //                                         'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
    //                                         $newPembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional
    //                                         $user, //created_by
    //                                         now(), //created_at
    //                                         $user, //updated_by
    //                                         now(), //updated_at
    //                                         'Y'
    //                                     )
    //                                 );
    
    //                                 $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                 if($kasbank){
    //                                     $kasbank->saldo_sekarang -= $total_dicairkan;
    //                                     $kasbank->updated_by = $user;
    //                                     $kasbank->updated_at = now();
    //                                     $kasbank->save();
    //                                 }
    //                             }
    //                         }
    //                     }
                        
    //                 }
    //                 DB::commit();
    //                 return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
    //             }
    
    //         } catch (ValidationException $e) {
    //             db::rollBack();
    //             return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
    //         }
    //     }else if($data['type'] == 'delete'){
    //         try {
    //             // dd($data);
    //             if($data['item'] == 'KARANTINA'){
    //                 foreach ($data['data'] as $key => $karantina) {
    //                     if(isset($karantina['check'])){
    //                         $krnt = Karantina::where('is_aktif', 'Y')->find($key);
    //                         $jenis = 'karantina';
    //                         $coa = 1015;

    //                         if($krnt){
    //                             $krnt->catatan = $karantina['catatan'];
    //                             $krnt->updated_by = $user;
    //                             $krnt->updated_at = now();
    //                             $krnt->is_aktif = 'N';
    //                             if($krnt->save()){
    //                                 // find history dump
    //                                 $history = KasBankTransaction::where('is_aktif', 'Y')
    //                                             ->where('jenis', $jenis)
    //                                             ->where('keterangan_kode_transaksi', $krnt->id)
    //                                             ->first();

    //                                 if($history){
    //                                     $keterangan_transaksi = $history->keterangan_transaksi;
    //                                     $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
    //                                     $history->updated_by = $user;
    //                                     $history->updated_at = now();
    //                                     $history->is_aktif = 'N';
    //                                     if($history->save()){
    //                                         // uang kasbank dikembalikan
    //                                         $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                         if($kasbank){
    //                                             $kasbank->saldo_sekarang += $history->kredit;
    //                                             $kasbank->updated_by = $user;
    //                                             $kasbank->updated_at = now();
    //                                             $kasbank->save();
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }

    //                     }
    //                 }

    //                 DB::commit();
    //                 return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
    //             }else{
    //                 foreach ($data['data'] as $key => $operasionals) {
    //                     $total_dicairkan = 0;
    //                     $is_delete = false;
    //                     foreach ($operasionals as $keyOprs => $value) {
    //                         $jenis = 'pencairan_operasional';
    //                         $coa = CoaHelper::DataCoa(5009);
        
    //                         if(isset($value['check'])){ // ini data yg dihapus
    //                             $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
        
    //                             if($oprs){
    //                                 $oprs->catatan = $value['catatan'];
    //                                 $oprs->updated_by = $user;
    //                                 $oprs->updated_at = now(); 
    //                                 $oprs->is_aktif = 'N';
    //                                 $oprs->save();
    //                             }
    //                             $is_delete = true; //benar ada perubahan
    //                         }else{
    //                             $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
    //                             if($oprs){
    //                                 $oprs->catatan = $value['catatan'];
    //                                 $oprs->updated_by = $user;
    //                                 $oprs->updated_at = now(); 
    //                                 $oprs->save();
        
    //                                 $total_dicairkan += $oprs->total_dicairkan;
    //                             }
        
    //                         }
    //                     }
        
    //                     if($is_delete == true){
    //                         $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
    //                         $jenis = 'pencairan_operasional';
        
    //                         if($pembayaran){ 
    //                             $pembayaran->catatan = 'REVISI OFF - '. $data['alasan'] .' - '. $pembayaran->catatan;
    //                             $pembayaran->updated_by = $user;
    //                             $pembayaran->updated_at = now();
    //                             $pembayaran->is_aktif = 'N';
    //                             $pembayaran->save();
        
    //                             // find history dump
    //                             $history = KasBankTransaction::where('is_aktif', 'Y')
    //                                         ->where('jenis', $jenis)
    //                                         ->where('keterangan_kode_transaksi', $pembayaran->id)
    //                                         ->first();
        
    //                             if($history){
    //                                 $keterangan_transaksi = $history->keterangan_transaksi;
    //                                 $history->keterangan_transaksi = 'REVISI OFF - ' .$keterangan_transaksi;
    //                                 $history->updated_by = $user;
    //                                 $history->updated_at = now();
    //                                 $history->is_aktif = 'N';
    //                                 if($history->save()){
    //                                     // uang kasbank dikembalikan
    //                                     $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                     if($kasbank){
    //                                         $kasbank->saldo_sekarang += $history->kredit;
    //                                         $kasbank->updated_by = $user;
    //                                         $kasbank->updated_at = now();
    //                                         $kasbank->save();
    //                                     }
    //                                 }
    //                             }
        
    //                             $newPembayaran = new SewaOperasionalPembayaran();
    //                             $newPembayaran->deskripsi = $data['item'];
    //                             $newPembayaran->total_dicairkan = $total_dicairkan;
    //                             $newPembayaran->catatan = 'REVISI';
    //                             $newPembayaran->created_by = $user;
    //                             $newPembayaran->created_at = now();
    //                             if($newPembayaran->save()){
    //                                 DB::table('sewa_operasional')
    //                                     ->where('id_pembayaran', $pembayaran->id) 
    //                                     ->where('is_aktif', 'Y')
    //                                     ->update([
    //                                         'id_pembayaran' => $newPembayaran->id,
    //                                     ]);
        
    //                                 DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
    //                                     array(
    //                                         $history->id_kas_bank, // id kas_bank dr form
    //                                         $history->tanggal, //tanggal
    //                                         0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
    //                                         $total_dicairkan, //uang keluar (kredit)
    //                                         $coa, //kode coa
    //                                         $jenis,
    //                                         'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
    //                                         $newPembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional
    //                                         $user, //created_by
    //                                         now(), //created_at
    //                                         $user, //updated_by
    //                                         now(), //updated_at
    //                                         'Y'
    //                                     )
    //                                 );
        
    //                                 $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
    //                                 if($kasbank){
    //                                     $kasbank->saldo_sekarang -= $total_dicairkan;
    //                                     $kasbank->updated_by = $user;
    //                                     $kasbank->updated_at = now();
    //                                     $kasbank->save();
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    
    //                 DB::commit();
    //                 return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
    //             }
    //         } catch (ValidationException $e) {
    //             db::rollBack();
    //             return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Hapus data gagal!']);
    //         }


    //     }

    // }
    //ga dipake
    public function delete(Request $request)
    {
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 

        try {
            if($data['modal_item'] == 'KARANTINA'){
                $oprs = Karantina::where('is_aktif', 'Y')->find($data['key']);
                $jenis = 'karantina';
                $coa = CoaHelper::DataCoa(5003);
            }else{
                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($data['key']);
                $jenis = 'pencairan_operasional';
                $coa =CoaHelper::DataCoa(5009);
            }
            $oprs->catatan =  'REVISI OFF - '. $data['alasan'] . ' | ' .$oprs->catatan;
            $oprs->updated_by = $user;
            $oprs->updated_at = now(); 
            $oprs->is_aktif = 'N';
            if($oprs->save()){
                $history = KasBankTransaction::where('is_aktif', 'Y')
                                                ->where('jenis', $jenis)
                                                ->where('keterangan_kode_transaksi', $oprs->id)
                                                ->first();
    
                if($history){
                    // history dump dinon-aktifkan
                    $keterangan_transaksi = $history->keterangan_transaksi;
                    $history->keterangan_transaksi = 'REVISI OFF - '. $data['alasan'] . ' | ' .$keterangan_transaksi;
                    $history->updated_by = $user;
                    $history->updated_at = now();
                    $history->is_aktif = 'N';
                    if($history->save()){
                        // uang kasbank dikembalikan
                        $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                        if($kasbank){
                            $kasbank->saldo_sekarang += $history->kredit;
                            $kasbank->updated_by = $user;
                            $kasbank->updated_at = now();
                            $kasbank->save();
                        }
                    }
                }

            }

            DB::commit();
            return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Hapus data gagal!']);
        }
    }

    public function load_data($item){
        try {
            if($item == 'KARANTINA'){
                $data = Karantina::where('is_aktif', 'Y')->where('total_dicairkan', '<>' ,NULL)
                                    ->with('details', 'getJO', 'getCustomer.getGrup')
                                    ->get();
            }else{
                $data = SewaOperasionalPembayaran::where('is_aktif', 'Y')
                                        ->where(function($where) use($item){
                                            if($item == 'LAIN-LAIN'){
                                                $where->whereNotIn('deskripsi', ['ALAT','TALLY','SEAL PELAYARAN','BIAYA DEPO','KARANTINA','BURUH','TIMBANG','LEMBUR']);
                                            }else{
                                                $where->where('deskripsi', $item);
                                            }
                                        })
                                        ->whereHas('getOperasional', function ($query){
                                            $query->where('is_aktif', 'Y');
                                        })
                                        ->where('total_refund',0)
                                        ->whereNull('total_kembali_stok')
                                        ->with('getOperasional')
                                        ->with('getOperasional.getSewa.getCustomer.getGrup')
                                        ->with('getOperasional.getSewa.getKaryawan')
                                        ->with('getOperasional.getSewa.getSupplier')
                                        ->get();
            }
             
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
