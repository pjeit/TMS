<?php

namespace App\Http\Controllers;

use App\Models\Karantina;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\SewaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RevisiBiayaOperasionalController extends Controller
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
        dd($data);

        try {
            foreach ($data['data'] as $key => $value) {
                if(isset($data['check'])){
                    // kalau ada data check, berarti data di centang dan dirubah
                    if($data['item'] == 'KARANTINA'){
                        $oprs = Karantina::where('is_aktif', 'Y')->find($key);
                        $jenis = 'karantina';
                        $coa = 1015;
                    }else{
                        $oprs = SewaOperasional::where('is_aktif', 'Y')->find($key);
                        $jenis = 'pencairan_operasional';
                        $coa = 1015;
                    }

                    if($oprs){
                        $oprs->total_operasional = $value['total_operasional'];
                        $oprs->total_dicairkan = floatval(str_replace(',', '', $value['dicairkan']));
                        $oprs->catatan = $value['catatan'];
                        $oprs->updated_by = $user;
                        $oprs->updated_at = now(); 
                        if($oprs->save()){
                            // find history dump
                            $history = KasBankTransaction::where('is_aktif', 'Y')
                                                            ->where('jenis', $jenis)
                                                            ->where('keterangan_kode_transaksi', $oprs->id)
                                                            ->first();

                            if($history){
                                // history dump dinon-aktifkan
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

                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                        array(
                                            $history->id_kas_bank, // id kas_bank dr form
                                            $history->tanggal, //tanggal
                                            0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                            $oprs->total_dicairkan, //uang keluar (kredit)
                                            $coa, //kode coa
                                            $jenis,
                                            'REVISI - '.$keterangan_transaksi, //keterangan_transaksi
                                            $oprs->id, //keterangan_kode_transaksi // id_sewa_operasional
                                            $user, //created_by
                                            now(), //created_at
                                            $user, //updated_by
                                            now(), //updated_at
                                            'Y'
                                        )
                                    );
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($history->id_kas_bank);
                                    if($kasbank){
                                        $kasbank->saldo_sekarang -= $oprs->total_dicairkan;
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
            return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('revisi_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
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
                $coa = 1015;
            }else{
                $oprs = SewaOperasional::where('is_aktif', 'Y')->find($data['key']);
                $jenis = 'pencairan_operasional';
                $coa = 1015;
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
                $data = SewaOperasional::where('sewa_operasional.is_aktif', 'Y')
                                        ->with('getSewa.getTujuan.getGrup')
                                        ->with('getSewa.getCustomer')
                                        ->with('getSewa.getSupplier')
                                        ->with('getTransaction')
                                        ->whereHas('getSewa', function ($query) {
                                            $query->whereIn('status', ['PROSES DOORING', 'MENUNGGU INVOICE']);
                                        })
                                        // ->whereHas('kas_bank_transaction', function ($query) {
                                        //     $query->where('kas_bank_transaction.keterangan_kode_transaksi', 'like', '%'.'727'.'%');
                                        // })
                                        // ->leftJoin('kas_bank_transaction as kbt', 'keterangan_kode_transaksi', 'like', '%'.'sewa_operasional.id'.'%')
                                        ->where('deskripsi', $item)
                                        ->get();
                                        
                // foreach ($data as $key => $value) {
                //     $transaction = KasBankTransaction::where('is_aktif', 'Y')
                //                                         ->where('jenis', 'pencairan_operasional')
                //                                         ->where('keterangan_kode_transaksi', 'like', '%'.$value->id.'%')
                //                                         ->get();
                // }
                dd($data);

            }
            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
