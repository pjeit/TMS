<?php

namespace App\Http\Controllers;

use App\Models\TagihanPembelianPembayaran;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use App\Models\TagihanPembelianDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class RevisiTagihanGabunganController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:READ_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['index']]);
	// 	$this->middleware('permission:CREATE_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['create','store']]);
	// 	$this->middleware('permission:EDIT_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['edit','update']]);
	// 	$this->middleware('permission:DELETE_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['destroy']]);  
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\TagihanPembelianPembayaran  $tagihanPembelianPembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(TagihanPembelianPembayaran $tagihanPembelianPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TagihanPembelianPembayaran  $tagihanPembelianPembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(TagihanPembelianPembayaran $tagihanPembelianPembayaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TagihanPembelianPembayaran  $tagihanPembelianPembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        $keterangan = 'Pembayaran Nota Ke: '. $data['nama_supplier'] . ' -';
        $i = 0;
        // dd($data);

        try {
            // history kas bank di nonaktifkan
            $pembayaran_lama = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
            $biaya_admin = floatval(str_replace(',', '', $data['biaya_admin']));
            if($pembayaran_lama){
                // dana dikembalikan
                $returnKas = KasBank::where('is_aktif','Y')->find($pembayaran_lama->id_kas);
                $returnKas->saldo_sekarang += $pembayaran_lama->total_bayar;
                $returnKas->updated_by = $user;
                $returnKas->updated_at = now();
                $returnKas->save();
    
                if(isset($data['data'])){
                    foreach ($data['data'] as $key => $value) {
                        $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($key);
                        // $tagihan->no_nota = $value['no_nota'];
                        $tagihan->id_pembayaran = $id;
                        $tagihan->pph = $value['pph'];
                        $tagihan->bukti_potong = $value['bukti_potong'];
                        $tagihan->sisa_tagihan =  $tagihan->total_tagihan;
                        // $tagihan->sisa_tagihan -= ($value['total_bayar'] + $value['pph']);
                        $tagihan->tagihan_dibayarkan = $value['tagihan_dibayarkan'];
                        if($i == 0){
                            $tagihan->sisa_tagihan -= ($value['tagihan_dibayarkan'] + $value['pph']+$biaya_admin);
                            // $tagihan->tagihan_dibayarkan = $value['tagihan_dibayarkan'] - $biaya_admin;
                            $tagihan->biaya_admin = $biaya_admin;
                        }else{
                            $tagihan->sisa_tagihan -= ($value['tagihan_dibayarkan'] + $value['pph']);
                            // $tagihan->tagihan_dibayarkan = $value['tagihan_dibayarkan'];
                        }
                        if($tagihan->sisa_tagihan == 0){
                            $tagihan->status = 'LUNAS';
                        }
                        // $tagihan->biaya_admin = $value['biaya_admin'];
                        // $tagihan->total_tagihan = $value['total_tagihan'];
                        // $tagihan->tagihan_dibayarkan = $value['tagihan_dibayarkan'];


                        $tagihan->updated_by = $user;
                        $tagihan->updated_at = now();
                        $tagihan->save();
    
                        $keterangan .= ' >> NOTA: '. $value['no_nota'] . ' >> TOTAL BAYAR: ' . $tagihan->tagihan_dibayarkan;
                        if($value['pph'] != 0){
                            $keterangan .= ' >> PPh23: '. $value['pph'];
                        }
                        if($i == 0 && $value['biaya_admin'] != 0){
                            $keterangan .= ' >> BIAYA ADMIN: '. $value['biaya_admin'];
                        }
                        $i++;
                    }
                }
                // hapus data
                if($data['data_deleted'] != null){
                    $array = explode(",", $data['data_deleted']);
                    foreach ($array as $key => $value) {
                        $del_pembelian = TagihanPembelian::where('is_aktif', 'Y')->find($value);
                        $del_pembelian->id_pembayaran = null;
                        $del_pembelian->sisa_tagihan =  $del_pembelian->total_tagihan;
                        $del_pembelian->status = 'MENUNGGU PEMBAYARAN';
                        $del_pembelian->tagihan_dibayarkan = 0;
                        $del_pembelian->biaya_admin = 0;
                        $del_pembelian->pph = 0;
                        $del_pembelian->updated_by = $user;
                        $del_pembelian->updated_at = now();
                        $del_pembelian->save();
                        // if($del_pembelian->save()){
                        //     $del_details = TagihanPembelianDetail::where('is_aktif', 'Y')->where('id_tagihan_pembelian', $value)->get();
                        //     foreach ($del_details as $key => $item) {
                        //         $item->updated_by = $user;
                        //         $item->updated_at = now();
                        //         $item->is_aktif = 'N';
                        //         $item->save();
                        //     }
                        // }
                    }
                }
                $pembayaran = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
                $pembayaran->catatan = 'REVISI - CATATAN: ' . $data['catatan'];
                $pembayaran->total_bayar = floatval(str_replace(',', '', $data['total_bayar']));
                $pembayaran->updated_by = $user;
                $pembayaran->updated_at = now();
                $pembayaran->save();
                if($pembayaran->save())
                {
                    $history = KasBankTransaction::where('is_aktif','Y')
                    ->where('keterangan_kode_transaksi', $id)
                    ->where('jenis', 'tagihan_supplier')
                    ->first();
                    $history->keterangan_transaksi = 'REVISI:'. $keterangan ;
                    $history->kredit = floatval(str_replace(',', '', $data['total_bayar'])) ;
                    // $history->is_aktif = 'N';
                    $history->updated_by = $user;
                    $history->updated_at = now();
                    // $history->save();
                    if( $history->save())
                    {
                        // kurangi kasbank sekarang
                        $kurangiKas = KasBank::where('is_aktif','Y')->find($data['id_kas']);
                        $kurangiKas->saldo_sekarang -= floatval(str_replace(',', '', $data['total_bayar']));
                        $kurangiKas->updated_by = $user;
                        $kurangiKas->updated_at = now();
                        if($kurangiKas->save()){
                            DB::commit();
                        }
                    }
                }
            }
            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'Success', 'msg'  => 'Berhasil revisi nota pembelian!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TagihanPembelianPembayaran  $tagihanPembelianPembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagihanPembelianPembayaran $tagihanPembelianPembayaran)
    {
        //
    }
}
