<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengaturanKeuangan;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\PemutihanInvoice;
use App\Models\InvoiceDetail;
use App\Models\Sewa;
use Exception;
use App\Models\KasBankTransaction;
use App\Models\KasBank;
class PemutihanInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_PEMUTIHAN_INVOICE', ['only' => ['index']]);
		$this->middleware('permission:EDIT_PEMUTIHAN_INVOICE', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataPengaturanKeuangan = PengaturanKeuangan::where('id', 1)->first();

        $dataInvoice =  DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->where('i.is_aktif', '=', 'Y')
                ->where('i.total_sisa','<=',$dataPengaturanKeuangan->batas_pemutihan)
                ->where('i.total_sisa','!=',0)
                ->orderBy('i.id','ASC')
                ->get();
        return view('pages.invoice.pemutihan_invoice.index',[
            'judul'=>"PEMUTIHAN INVOICE",
            'dataInvoice' => $dataInvoice,
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
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $pemutihan_invoice)
    {
        //
        $data_pengaturan = PengaturanKeuangan::where('id', 1)->first();
        $data_customer = Customer::where('is_aktif', 'Y')
                                    ->where('id', $pemutihan_invoice->billing_to)
                                    ->first();

        return view('pages.invoice.pemutihan_invoice.form',[
            'judul'=>"PEMUTIHAN INVOICE",
            'pemutihan_invoice' => $pemutihan_invoice,
            'data_pengaturan' => $data_pengaturan,
            'data_customer' => $data_customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $pemutihan_invoice)
    {
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $pesanKustom = [
                'tanggal_pemutihan.required' => 'Tanggal pemutihan wajib diisi!',
                'jumlah_pemutihan.required' => 'Jumlah pemutihan wajib diisi!',
                // 'catatan_pemutihan.required' => 'Catatan pemutihan wajib diisi',
            ];
            $request->validate([
                'tanggal_pemutihan' => 'required',
                'jumlah_pemutihan' => 'required',
                // 'catatan_pemutihan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            
            $pemutihan = new PemutihanInvoice();
            $pemutihan->invoice_id = $pemutihan_invoice ->id;
            $pemutihan->tanggal = date_create_from_format('Y-m-d', $data['tanggal_pemutihan']);
            $pemutihan->nominal_pemutihan = floatval(str_replace(',', '', $data['jumlah_pemutihan']));
            $pemutihan->catatan = $data['catatan_pemutihan'];
            $pemutihan->created_by = $user;
            $pemutihan->created_at = now();
            $pemutihan->is_aktif = 'Y';
            $pemutihan->save();
            if($pemutihan->save())
            {
                $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($pemutihan_invoice->id);
                
                $curStatus = '';
                $invoice->total_sisa-=floatval(str_replace(',', '', $data['jumlah_pemutihan']));
                $invoice->total_dibayar+=floatval(str_replace(',', '', $data['jumlah_pemutihan']));
                if($invoice->total_sisa == 0){
                    $curStatus = 'SELESAI PEMBAYARAN INVOICE';
                    $invoice->status = $curStatus;
                }
                $invoice->updated_by = $user;
                $invoice->updated_at = now();
                if($invoice->save()){
                    if($curStatus == 'SELESAI PEMBAYARAN INVOICE'){
                        $invoiceDetail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice', $invoice->id)->get();
                        if($invoiceDetail){
                            foreach ($invoiceDetail as $item) {
                                $check = InvoiceDetail::leftJoin('invoice', 'invoice.id', '=', 'invoice_detail.id_invoice')
                                                        ->where('invoice_detail.is_aktif', 'Y')
                                                        ->where('invoice.status', 'MENUNGGU PEMBAYARAN INVOICE')
                                                        ->where('id_sewa', $item->id_sewa)->get();
                                if ($check->isEmpty()) {
                                    $updateSewa = Sewa::where('is_aktif', 'Y')->find($item->id_sewa);
                                    $updateSewa->status = 'SELESAI PEMBAYARAN';
                                    $updateSewa->updated_by = $user;
                                    $updateSewa->updated_at = now();
                                    $updateSewa->save();
                                    // trigger update status jo detail jika semua sewa sudah selesai
                                    // trigger update status jo jika semua jo detail sudah selesai

                                    // logicnya sama kaya di pembayaran invoice
                                    $cust = Customer::where('is_aktif', 'Y')->findOrFail($updateSewa['id_customer']);
                                    if($cust){
                                        $kredit_sekarang = $cust->kredit_sekarang - $updateSewa->total_tarif;
                                        $cust->kredit_sekarang = $kredit_sekarang;
                                        $cust->updated_by = $user;
                                        $cust->updated_at = now();
                                        $cust->save();
                                    }
                                }
                            }
                        }
                    }
                }
                $invoice->updated_by = $user;
                $invoice->updated_at = now();
                $invoice->save();
            }
            DB::commit();
            return redirect()->route('pemutihan_invoice.index')->with(['status' => 'Success', 'msg'  => 'Pemutihan Invoice berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('pemutihan_invoice.index')->with(['status' => 'error', 'msg' => 'Pemutihan Invoice gagal!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('pemutihan_invoice.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
         //
         $user = Auth::user()->id;
         DB::beginTransaction(); 
 
         try {
                $pemutihan = PemutihanInvoice::where('is_aktif', 'Y')->findOrFail($invoice->id);
             
                $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($invoice->id);
                $invoice->total_sisa = $pemutihan->nominal_pemutihan;
                $invoice->total_dibayar -= $pemutihan->nominal_pemutihan;
                $invoice->status = 'MENUNGGU PEMBAYARAN INVOICE';
                $invoice->updated_by = $user;
                $invoice->updated_at = now();
                // $detail->save();
                if($invoice->save())
                {
                    $invoice_detail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice',$invoice->id)->get();
                    foreach ($invoice_detail as  $details) {
                        $sewa = Sewa::where('is_aktif','Y')->where('id_sewa',$details->id_sewa)->first();
                        $sewa->status = 'MENUNGGU PEMBAYARAN INVOICE';
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->save();
                        //buat yang JO ADA DI TRIGGER update_jo_selesai_dooring
                        $cust = Customer::where('is_aktif', 'Y')->findOrFail($sewa->id_customer);
                        if($cust){
                            $cust->kredit_sekarang += $sewa->total_tarif;
                            $cust->updated_by = $user;
                            $cust->updated_at = now();
                            $cust->save();
                        }
                    }
                }
             DB::commit();
             return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'Success', 'msg' => 'Pembayaran Invoice berhasil dihapus!']);
 
         } catch (\Throwable $th) {
             //throw $th;
             db::rollBack();
             return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
             
         }
    }
}
