<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceResi;
use App\Models\InvoiceResiDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvoiceResiController extends Controller
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
        $data = InvoiceResi::where('is_aktif','Y')->get();
        return view('pages.invoice.invoice_resi.index',[
            'judul' => "Resi Invoice",
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
        $data_billingto_invoice = Invoice::where('is_aktif','Y')
        ->with('getBillingTo')
        ->where('status','MENUNGGU PEMBAYARAN INVOICE')
        ->groupBy('billing_to')
        ->get();
        $data_invoice = Invoice::where('is_aktif','Y')
        ->with('getBillingTo')
        ->where('status','MENUNGGU PEMBAYARAN INVOICE')
        ->whereDoesntHave('get_invoice_resi')
        ->get();
        // dd(  $data_invoice);
        return view('pages.invoice.invoice_resi.create',[
            'judul' => "Input Resi Invoice",
            'data_billingto_invoice' => $data_billingto_invoice,
            'data_invoice' => $data_invoice,
        ]);
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
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        try {
            $resi = new InvoiceResi();
            $resi->jenis_pengiriman = $data['jenis_pengiriman'];
            $resi->no_resi = $data['no_resi'];
            $resi->status_resi = 'DALAM PROSES';
            $resi->tanggal_resi = date_create_from_format('d-M-Y', $data['tgl_resi']);
            $resi->created_by = $user;
            $resi->created_at = now();
            $resi->is_aktif = 'Y';
            if($resi->save()){
                foreach ($data['detail'] as $key => $value) {
                    if(isset($value['is_resi'])){
                        if($value['is_resi'] =="Y")
                        {
                            $detail = new InvoiceResiDetail();
                            $detail->id_resi = $resi->id;
                            $detail->id_invoice = $value['id_invoice'];
                            $detail->no_invoice = $value['no_invoice'];
                            $detail->jatuh_tempo_lama = $value['jatuh_tempo_lama'];
                            $detail->created_by = $user;
                            $detail->created_at = now();
                            $detail->is_aktif = 'Y';
                            $detail->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('invoice_resi.index')->with(['status' => 'Success', 'msg' => 'Resi Invoice berhasil dibuat']);
        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InvoiceResi  $invoiceResi
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceResi $invoice_resi)
    {
        //
        $resi = InvoiceResi::where('is_aktif','Y')
        ->where('id',$invoice_resi->id)
        ->with('get_invoice_resi_detail')
        ->with('get_invoice_resi_detail.get_invoice')
        ->with('get_invoice_resi_detail.get_invoice.getBillingTo')
        ->first();
        // dd($resi);
        return view('pages.invoice.invoice_resi.update_resi',[
            'judul' => "Cek Resi Invoice",
            'resi' => $resi,
        ]);
    }
    public function update_resi(Request $request,$id)
    {
        //
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        try {
            $resi = InvoiceResi::where('is_aktif','Y')->find($id);
            $resi->status_resi = $data['status'];
            $resi->updated_by = $user;
            $resi->updated_at = now();
            if($resi->save()){
                foreach ($data['detail'] as $key => $value) {
                    $detail = InvoiceResiDetail::where('is_aktif','Y')->find($value['id_resi_detail']);
                    $detail->jatuh_tempo_baru = date_create_from_format('d-M-Y', $value['jatuh_tempo_baru']);
                    $detail->updated_by = $user;
                    $detail->updated_at = now();
                    // $detail->save();
                    if($detail->save())
                    {
                        $detail = Invoice::where('is_aktif','Y')->find($value['id_invoice']);
                        $detail->jatuh_tempo = date_create_from_format('d-M-Y', $value['jatuh_tempo_baru']);
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->save();
                    }
                }
            }
            DB::commit();
            return redirect()->route('invoice_resi.index')->with(['status' => 'Success', 'msg' => 'Resi Invoice berhasil diupdate']);
        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('invoice_resi.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceResi  $invoiceResi
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceResi $invoice_resi)
    {
        //
        $data_resi = InvoiceResi::where('is_aktif','Y')
        ->where('id',$invoice_resi->id)
        ->with('get_invoice_resi_detail')
        ->with('get_invoice_resi_detail.get_invoice')
        ->with('get_invoice_resi_detail.get_invoice.getBillingTo')
        ->first();
  
        $data_gabung = DB::select("
                SELECT 'data_resi' as 'data', i_r.id as 'id_detail', i.tgl_invoice as 'tgl_invoice',i.id as 'id_invoice',i.no_invoice AS 'no_invoice',i.total_tagihan AS 'total_tagihan',i.jatuh_tempo as 'jatuh_tempo', c.nama as 'nama_customer' FROM invoice_resi_detail i_r
                LEFT join invoice i on i_r.id_invoice = i.id
                LEFT join customer c on i.billing_to = c.id
                WHERE i_r.is_aktif = 'Y' and i_r.id_resi = $invoice_resi->id
                UNION ALL
                SELECT 'data_invoice' as 'data', i_r.id as 'id_detail', i.tgl_invoice as 'tgl_invoice', i.id as 'id_invoice',i.no_invoice AS 'no_invoice',i.total_tagihan AS 'total_tagihan',i.jatuh_tempo as 'jatuh_tempo', c.nama as 'nama_customer' FROM invoice i
                LEFT join customer c on i.billing_to = c.id
                LEFT join invoice_resi_detail i_r on  i.id = i_r.id_invoice AND i_r.is_aktif = 'Y'
                WHERE i.is_aktif = 'Y'  AND i_r.id is null
          
                ");
       
        // dd($data_gabung);
        // $data_combined = $data_resi->merge($data_invoice);
        // dd( $data_combined);
        return view('pages.invoice.invoice_resi.edit',[
            'judul' => "Edit Cek Resi Invoice",
            'resi' => $data_resi,
            'data_gabung' => $data_gabung,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvoiceResi  $invoiceResi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceResi $invoice_resi)
    {
        //
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        try {
            $resi = InvoiceResi::where('is_aktif','Y')->find($invoice_resi->id);
            $resi->jenis_pengiriman = $data['jenis_pengiriman'];
            $resi->no_resi = $data['no_resi'];
            $resi->tanggal_resi = date_create_from_format('d-M-Y', $data['tgl_resi']);
            $resi->updated_by = $user;
            $resi->updated_at = now();
            if($resi->save()){
                foreach ($data['detail'] as $key => $value) {
                    if(isset($value['is_resi']))
                    {
                        if(isset($value['id_resi_detail']))
                        {
                            $detail = InvoiceResiDetail::where('is_aktif','Y')->find($value['id_resi_detail']);
                            $detail->updated_by = $user;
                            $detail->updated_at = now();
                            $detail->save();
                        }
                        else
                        {
                            $detail = new InvoiceResiDetail();
                            $detail->id_resi = $resi->id;
                            $detail->id_invoice = $value['id_invoice'];
                            $detail->no_invoice = $value['no_invoice'];
                            $detail->jatuh_tempo_lama = $value['jatuh_tempo_lama'];
                            $detail->created_by = $user;
                            $detail->created_at = now();
                            $detail->is_aktif = 'Y';
                            $detail->save();
                        }
                    }
                    else
                    {
                        $detail = InvoiceResiDetail::where('is_aktif','Y')->find($value['id_resi_detail']);
                        $detail->is_aktif = 'N';
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->save();
                    }
                }
            }
            DB::commit();
            return redirect()->route('invoice_resi.index')->with(['status' => 'Success', 'msg' => 'Resi Invoice berhasil diupdate']);
        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('invoice_resi.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvoiceResi  $invoiceResi
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceResi $invoice_resi)
    {
        //
         //
         $user = Auth::user()->id;
         DB::beginTransaction(); 
 
         try {
             $tagihan = InvoiceResi::where('is_aktif', 'Y')->find($invoice_resi->id);
             $tagihan->updated_by = $user;
             $tagihan->updated_at = now();
             $tagihan->is_aktif = 'N';
             if($tagihan->save()){
                InvoiceResiDetail::where('is_aktif', 'Y')->where('id_resi',$invoice_resi->id)->update([
                     'updated_by' => $user,
                     'updated_at' => now(),
                     'is_aktif' => 'N',
                 ]);
             }
             DB::commit();
             return redirect()->route('invoice_resi.index')->with(['status' => 'Success', 'msg' => 'Hapus data Resi Invoice berhasil!']);
 
         } catch (\Throwable $th) {
             //throw $th;
             db::rollBack();
             return redirect()->route('invoice_resi.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
             
         }
    }
}