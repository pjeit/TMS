<?php

namespace App\Http\Controllers;

use App\Models\Karantina;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\SewaOperasional;
use App\Models\SewaOperasionalPembayaran;
use App\Models\SewaOperasionalPembayaranDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\CoaHelper;
use Exception;
use Carbon\Carbon;

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

                                $oprs = SewaOperasionalPembayaranDetail::where('is_aktif', 'Y')->find($keyOprs);
                                // dd($keyOprs);
                                if($oprs){
                                    $oprs->total_operasional = floatval(str_replace(',', '', $value['total_operasional']));
                                    $oprs->total_dicairkan = floatval(str_replace(',', '', $value['total_dicairkan']));
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
                                    if (array_key_exists($value['tujuan'], $storeData)) {
                                        $storeData[$value['tujuan']]['customer'] = $value['customer'];
                                        $storeData[$value['tujuan']]['driver'] .= ' >> '. $value['driver_nopol'];
                                        // $storeData[$value['tujuan']]['id_opr'][] = $keyOprs;
                                        $storeData[$value['tujuan']]['index'] += 1;
                                        // CONTOHNYA:
                                        // array:1 [▼
                                        // "**PT. Cargil Indonesia - PIER  20 (Perak)" => array:5 [▼
                                        //         "driver" => ">> L 8902 UUC (BASMAN) >> L 9813 UC (HASAN) >> L 8901 UUC (TAROM)"
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
                                            
                                            'driver' => '>> '. $value['driver_nopol'],
                                            'customer' => '>> '. $value['customer'],

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
                                    // $history_baru = KasBankTransaction::where('is_aktif', 'Y')
                                    //         ->where('jenis', $jenis)
                                    //         ->where('keterangan_kode_transaksi', $pembayaran->id)
                                    //         ->first();
        
                                    if($history_lama){

                                        // $keterangan_transaksi = $history_lama->keterangan_transaksi;
                                        foreach ($storeData as $keyNamaTujuan => $dump) {
                                            $history_lama->keterangan_transaksi = 'REVISI - ' .$data['item'].": ".$dump['index'].'X >>'."(".$dump['customer'].")".'>>' .$keyNamaTujuan." ".$dump['driver'];
                                        }
                                        $history_lama->kredit = $total_dicairkan;
                                        $history_lama->updated_by = $user;
                                        $history_lama->updated_at = now();
                                        // $history_lama->is_aktif = 'N';
                                        if($history_lama->save()){
                                            $kasbank_update_saldo = KasBank::where('is_aktif', 'Y')->find($history_lama->id_kas_bank);
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
            catch (\Throwable $th) {
                db::rollBack();
                return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
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
                                $oprs = SewaOperasionalPembayaranDetail::where('is_aktif', 'Y')->find($keyOprs);
        
                                if($oprs){
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->is_aktif = 'N';
                                    $oprs->save();
                                }
                                $is_delete = true; //benar ada perubahan
                                
                            }else{
                                $oprs = SewaOperasionalPembayaranDetail::where('is_aktif', 'Y')->find($keyOprs);
                                $i=1;
                                if($oprs){
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
                                    $total_dicairkan += $oprs->total_dicairkan;
                                    $total_operasional += $oprs->total_operasional;
                                    if (array_key_exists($value['tujuan'], $storeData)) {

                                        $storeData[$value['tujuan']]['customer'] = $value['customer'];
                                        $storeData[$value['tujuan']]['driver'] .= ' >> '. $value['driver_nopol'];
                                        // $storeData[$value['tujuan']]['id_opr'][] = $keyOprs;
                                        $storeData[$value['tujuan']]['index'] += 1;
                                        // CONTOHNYA:
                                        // array:1 [▼
                                        // "**PT. Cargil Indonesia - PIER  20 (Perak)" => array:5 [▼
                                        //         "driver" => ">> L 8902 UUC (BASMAN) >> L 9813 UC (HASAN) >> L 8901 UUC (TAROM)"
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
                                            'driver' => '>> '. $value['driver_nopol'],
                                            'customer' => '>> '. $value['customer'],
                                            // 'id_opr' => [$keyOprs],
                                            'index' => $i,
                                        ];
                                    }
                                }
                            }
                        }
                        // dd($storeData);
                        if($is_delete == true){
                            // dd($data);
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
                                              
                                                // $keterangan_transaksi = $history_lama->keterangan_transaksi;
                                                $history_lama->kredit = $total_dicairkan;
                                                // $history_lama->keterangan_transaksi = 'REVISI - ' .$keterangan_transaksi;
                                                foreach ($storeData as $keyNamaTujuan => $dump) {
                                                    $history_lama->keterangan_transaksi = 'REVISI - ' .$data['item'].": ".$dump['index'].'X >>'."(".$dump['customer'].")".'>>' .$keyNamaTujuan." ".$dump['driver'];
                                                }
                                                $history_lama->updated_by = $user;
                                                $history_lama->updated_at = now();
                                                if ($total_dicairkan == 0) {
                                                    $history_lama->is_aktif = 'N';
                                                }
                                                if($history_lama->save()){
                                                    $kasbank_update_saldo = KasBank::where('is_aktif', 'Y')->find($history_lama->id_kas_bank);
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
            catch (\Throwable $th) {
                db::rollBack();
                return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
            }

        }

    }
    public function edit(SewaOperasionalPembayaran $revisi_biaya_operasional)
    {
        //
        $data = SewaOperasionalPembayaran::where('is_aktif', 'Y')
        ->whereHas('getOperasionalDetail', function ($query){
            $query->where('is_aktif', 'Y');
        })
        ->where('total_refund',0)
        ->where('total_kasbon',0)
        ->where('id',$revisi_biaya_operasional->id)
        ->whereNull('total_kembali_stok')
        ->with('getOperasionalDetail')
        ->with('getKas')
        ->with('getOperasionalDetail.getSewaDetail.getCustomer.getGrup')
        ->with('getOperasionalDetail.getSewaDetail.getKaryawan')
        ->with('getOperasionalDetail.getSewaDetail.getSupplier')
        ->first();
        $kembaliCek = false;
        // dd($data);
        foreach ($data->getOperasionalDetail as $value) {
            if($value->id_kasbon||$value->id_stok||$value->id_refund)
            {
                $kembaliCek = true;
            }
        }
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
        // dd($data);
        return view('pages.revisi.revisi_biaya_operasional.edit',[
            'judul' => 'Revisi Operasional '.$data->deskripsi,
            'data' => $data,
            'dataKas' => $dataKas,
            'kembaliCek' => $kembaliCek,
        ]);
    }
    public function update(Request $request, SewaOperasionalPembayaran $revisi_biaya_operasional)
    {
        //
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);
        // try {
            $count = 0;
            $total_stok = 0;
            $total_kasbon = 0;
            $total_refund = 0;
            $customer ='';
            $tujuan='';
            $no_pol_driver='';
            
            // dd('masuk');
            $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($revisi_biaya_operasional->id);
            if($data['kembali']!='KEMBALI_STOK'&&$data['kembali']!='DATA_DI_HAPUS'&&$data['kembali']!='kasbon')
            {

                $so_refund = new SewaOperasionalRefund();
                $so_refund ->id_kas_bank = $data['kembali'];
                $so_refund ->tanggal_refund = now();
                $so_refund ->id_pembayaran = $refund_biaya_operasional->id;
                $so_refund ->deskripsi_ops =$refund_biaya_operasional->deskripsi;
                $so_refund ->total_refund = (float)str_replace(',', '', $data['total_dicairkan']);
                $so_refund->catatan_refund = $data['catatan'];
                $so_refund->created_by = $user;
                $so_refund->created_at = now();
                $so_refund->is_aktif = 'Y';
                if($so_refund->save())
                {
                //  dd($data);
                    // dd($so_pembayaran);
                    if($so_pembayaran){
                        $so_pembayaran->total_refund += (float)str_replace(',', '', $data['total_dicairkan']);
                        $so_pembayaran->updated_by = $user;
                        $so_pembayaran->updated_at = now();
                        $so_pembayaran->is_aktif = 'N';
                        $so_pembayaran->save();
                        if( $so_pembayaran->save());
                        {
                            foreach ($data['data'] as $value) {
                                $status = 'HAPUS';
                                $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                                $customer = $value['customer'];
                                $tujuan = $value['tujuan'];
                                $no_pol_driver .= '# '.$value['no_pol'];
                                $total_refund+=(float)str_replace(',', '', $value['total_dicairkan']);
                                SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                ->where('id', $value['id_pembayaran_detail'])
                                ->update([
                                        'is_aktif' => 'N',
                                        'status' => $status,
                                        'keterangan_internal'=>$keterangan_internal,
                                        'id_refund'=>$so_refund->id,
                                        'total_refund'=>(float)str_replace(',', '', $value['total_dicairkan'])
                                    ]);
                                $count++;
                            }
                        }
                    }

                    
                }
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        array(
                            $data['kembali'],// id kas_bank dr form
                            now(),//tanggal
                            (float)str_replace(',', '', $data['total_dicairkan']),// debit 
                            0, //uang keluar (kredit)
                            CoaHelper::DataCoa(1100), //kode coa piutang usaha
                            'operasional_refund',
                            'Pengembalian Operasional : '.$refund_biaya_operasional->deskripsi.' '.$count.' X >>'.'('.$customer.') - >'.$tujuan. $no_pol_driver.'Catatan :'.$so_refund->catatan_refund, //keterangan_transaksi
                            $so_refund->id,//keterangan_kode_transaksi id refundnya
                            $user,//created_by
                            now(),//created_at
                            $user,//updated_by
                            now(),//updated_at
                            'Y'
                        ) 
                    );
                    $kas_bank = KasBank::where('is_aktif', 'Y')->find($data['kembali']);
                    $kas_bank->saldo_sekarang += (float)str_replace(',', '', $data['total_dicairkan']);
                    $kas_bank->updated_by = $user;
                    $kas_bank->updated_at = now();
                    $kas_bank->save();
                    DB::commit();

            }
                   
         
            DB::commit();
            return redirect()->route('refund_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Refund Operasional berhasil!']);

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     db::rollBack();
        // return redirect()->route('refund_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);

        // }
    }
   

    public function load_data($item,$tanggal_mulai,$tanggal_akhir){
        $currentDate = Carbon::now();
        $tanggal_mulai_convert = date_create_from_format('d-M-Y', $tanggal_mulai);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);

        
        
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
                                        ->whereHas('getOperasionalDetail', function ($query){
                                            $query->where('is_aktif', 'Y');
                                        })
                                        // ->where(function ($query) use ($currentDate) {
                                        //     $query->whereBetween('tanggal_berangkat', [
                                        //         $currentDate->copy()->subDay()->startOfDay(),
                                        //         $currentDate->copy()->addDay()->endOfDay()
                                        //     ]);
                                        // })
                                        // ->where('tgl_dicairkan', date("Y-m-d 00:00:00",  $currentDate->toDateString().' 00:00:00'))
                                        ->whereBetween( DB::raw('cast(tgl_dicairkan as date)'), [date_format($tanggal_mulai_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                                        // ->whereDate('tgl_dicairkan', $tanggal_akhir_convert)
                                        ->where('total_refund',0)
                                        ->where('total_kasbon',0)
                                        ->whereNull('total_kembali_stok')
                                        ->with('getOperasionalDetail')
                                        ->with('getKas')
                                        ->with('getOperasionalDetail.getSewaDetail.getCustomer.getGrup')
                                        ->with('getOperasionalDetail.getSewaDetail.getKaryawan')
                                        ->with('getOperasionalDetail.getSewaDetail.getSupplier')
                                        ->get();
            }
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }

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
                $oprs = SewaOperasionalPembayaranDetail::where('is_aktif', 'Y')->find($data['key']);
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
}
