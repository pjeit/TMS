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
                        $is_off = false;
                        foreach ($operasionals as $keyOprs => $value) {
                            $jenis = 'pencairan_operasional';
                            $coa =  CoaHelper::DataCoa(5009);
    
                            if(isset($value['check'])){
                                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($keyOprs);
    
                                if($oprs){
                                    $oprs->total_operasional = floatval(str_replace(',', '', $value['total_operasional']));
                                    $oprs->total_dicairkan = floatval(str_replace(',', '', $value['total_dicairkan']));
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
    
                                    $total_dicairkan += $oprs->total_dicairkan;
                                }
                                $is_off = true; //benar ada perubahan
                            }
                        }
    
                        if($is_off == true){
                            $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
                            $jenis = 'pencairan_operasional';
    
                            if($pembayaran){ 
                                $pembayaran->catatan = 'REVISI OFF - '. $data['alasan'] .' - '. $pembayaran->catatan;
                                $pembayaran->updated_by = $user;
                                $pembayaran->updated_at = now();
                                $pembayaran->is_aktif = 'N';
                                $pembayaran->save();
        
                                // find history dump
                                $history = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('jenis', $jenis)
                                            ->where('keterangan_kode_transaksi', $pembayaran->id)
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
    
                                $newPembayaran = new SewaOperasionalPembayaran();
                                $newPembayaran->deskripsi = $data['item'];
                                $newPembayaran->total_dicairkan = $total_dicairkan;
                                $newPembayaran->catatan = 'REVISI';
                                $newPembayaran->created_by = $user;
                                $newPembayaran->created_at = now();
                                if($newPembayaran->save()){
                                    DB::table('sewa_operasional')
                                        ->where('id_pembayaran', $pembayaran->id) 
                                        ->where('is_aktif', 'Y')
                                        ->update([
                                            'id_pembayaran' => $newPembayaran->id,
                                        ]);
    
                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                        array(
                                            $history->id_kas_bank, // id kas_bank dr form
                                            $history->tanggal, //tanggal
                                            0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                            $total_dicairkan, //uang keluar (kredit)
                                            $coa, //kode coa
                                            $jenis,
                                            'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
                                            $newPembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional
                                            $user, //created_by
                                            now(), //created_at
                                            $user, //updated_by
                                            now(), //updated_at
                                            'Y'
                                        )
                                    );
    
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                    if($kasbank){
                                        $kasbank->saldo_sekarang -= $total_dicairkan;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        $kasbank->save();
                                    }
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
                            $coa = 1015;

                            if($krnt){
                                $krnt->catatan = $karantina['catatan'];
                                $krnt->updated_by = $user;
                                $krnt->updated_at = now();
                                $krnt->is_aktif = 'N';
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
                                if($oprs){
                                    $oprs->catatan = $value['catatan'];
                                    $oprs->updated_by = $user;
                                    $oprs->updated_at = now(); 
                                    $oprs->save();
        
                                    $total_dicairkan += $oprs->total_dicairkan;
                                }
        
                            }
                        }
        
                        if($is_delete == true){
                            $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($key);
                            $jenis = 'pencairan_operasional';
        
                            if($pembayaran){ 
                                $pembayaran->catatan = 'REVISI OFF - '. $data['alasan'] .' - '. $pembayaran->catatan;
                                $pembayaran->updated_by = $user;
                                $pembayaran->updated_at = now();
                                $pembayaran->is_aktif = 'N';
                                $pembayaran->save();
        
                                // find history dump
                                $history = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('jenis', $jenis)
                                            ->where('keterangan_kode_transaksi', $pembayaran->id)
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
        
                                $newPembayaran = new SewaOperasionalPembayaran();
                                $newPembayaran->deskripsi = $data['item'];
                                $newPembayaran->total_dicairkan = $total_dicairkan;
                                $newPembayaran->catatan = 'REVISI';
                                $newPembayaran->created_by = $user;
                                $newPembayaran->created_at = now();
                                if($newPembayaran->save()){
                                    DB::table('sewa_operasional')
                                        ->where('id_pembayaran', $pembayaran->id) 
                                        ->where('is_aktif', 'Y')
                                        ->update([
                                            'id_pembayaran' => $newPembayaran->id,
                                        ]);
        
                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                        array(
                                            $history->id_kas_bank, // id kas_bank dr form
                                            $history->tanggal, //tanggal
                                            0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                            $total_dicairkan, //uang keluar (kredit)
                                            $coa, //kode coa
                                            $jenis,
                                            'REVISI - ' . $keterangan_transaksi, //keterangan_transaksi
                                            $newPembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional
                                            $user, //created_by
                                            now(), //created_at
                                            $user, //updated_by
                                            now(), //updated_at
                                            'Y'
                                        )
                                    );
        
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                    if($kasbank){
                                        $kasbank->saldo_sekarang -= $total_dicairkan;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        $kasbank->save();
                                    }
                                }
                            }
                        }
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
