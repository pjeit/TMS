<?php

namespace App\Http\Controllers;

use App\Models\InvoiceKarantina;
use App\Models\InvoiceKarantinaPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use Illuminate\Support\Facades\Auth;
class RevisiInvoiceKarantinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $data = InvoiceKarantinaPembayaran::where('is_aktif', 'Y')
        ->with('detail_invoice')
        ->with('billing_to_pembayaran')
        ->get();
        // dd($data[0]->detail_invoice);
        return view('pages.revisi.revisi_invoice_karantina.index',[
            'judul' => "Revisi Invoice Karantina",
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
     * @param  \App\Models\InvoiceKarantinaPembayaran  $invoiceKarantinaPembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceKarantinaPembayaran $invoiceKarantinaPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceKarantinaPembayaran  $invoiceKarantinaPembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceKarantinaPembayaran $invoiceKarantinaPembayaran)
    {
        //
        $data = InvoiceKarantinaPembayaran::where('is_aktif', 'Y')->where('id',$invoiceKarantinaPembayaran->id)
        ->with('detail_invoice')
        ->with('billing_to_pembayaran')
        ->first();
        // dd($data);
        return view('pages.revisi.revisi_invoice_karantina.edit_pembayaran',[
            'judul' => "Revisi Invoice Karantina",
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvoiceKarantinaPembayaran  $invoiceKarantinaPembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceKarantinaPembayaran $invoiceKarantinaPembayaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvoiceKarantinaPembayaran  $invoiceKarantinaPembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceKarantinaPembayaran $revisi_invoice_karantina)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($revisi_invoice_karantina);
        try {
            $inv_bayar = InvoiceKarantinaPembayaran::where('is_aktif', 'Y')->find($revisi_invoice_karantina->id);
            $inv_bayar->updated_by = $user;
            $inv_bayar->updated_at = now();
            $inv_bayar->is_aktif = 'N';
            if($inv_bayar->save()){
                $invoice = InvoiceKarantina::where('is_aktif', 'Y')->where('id_pembayaran',$inv_bayar->id)->get();
                foreach ($invoice as $invoices) {
                    $invoices->id_pembayaran = null;
                    $invoices->sisa_tagihan = $invoices->tagihan_dibayar;
                    $invoices->tagihan_dibayar = 0;
                    $invoices->updated_by = $user;
                    $invoices->updated_at = now();
                    $invoices->save();
                }
                $history = KasBankTransaction::where('is_aktif','Y')
                ->where('keterangan_kode_transaksi', $inv_bayar->id)
                ->where('jenis', 'pembayaran_invoice_karantina')
                ->first();
                $history->keterangan_transaksi = 'HAPUS - ' . isset($history->keterangan_transaksi)? $history->keterangan_transaksi:'';
                $history->is_aktif = 'N';
                $history->updated_by = $user;
                $history->updated_at = now();
                if($history->save()){
                    // kembalikan kasbank sekarang
                    $returnKas = KasBank::where('is_aktif','Y')->find($inv_bayar->id_kas);
                    $returnKas->saldo_sekarang += floatval(str_replace(',', '', $history['kredit']));
                    $returnKas->updated_by = $user;
                    $returnKas->updated_at = now();
                    $returnKas->save();
                }
            }
            DB::commit();
            return redirect()->route('revisi_invoice_karantina.index')->with(['status' => 'Success', 'msg' => 'Pembayaran Invoice berhasil dihapus!']);

        } catch (\Throwable $th) {
            //throw $th;
            db::rollBack();
            return redirect()->route('revisi_invoice_karantina.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
            
        }
    }
}
