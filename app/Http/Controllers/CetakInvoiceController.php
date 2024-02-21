<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\Sewa;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Carbon\Carbon;
class CetakInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_CETAK_INVOICE', ['only' => ['index']]);
    }
    
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        // Session::flush();
        // Session::forget(['sewa', 'cust', 'grup']);
        $dataInvoice =  DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->where('i.is_aktif', '=', 'Y')
                ->where('i.total_sisa', '!=', 0)
                // ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
                ->orderBy('i.id','ASC')
                ->get();
        // dd($dataSewa);
        // dd($dataSewa);
    
        return view('pages.invoice.cetak_invoice.index',[
            'judul'=>"CETAK INVOICE",
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
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $cetak_invoice)
    {
        //
        $user = Auth::user()->id; 
        DB::beginTransaction(); 

        try {
            $invoice = Invoice::where('is_aktif', 'Y')->find($cetak_invoice->id);
            $cek = substr($invoice->no_invoice, -2);
            if($cek != '/I'){
                $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
            }else{
                $invoice = Invoice::where('is_aktif', 'Y')->where('no_invoice', substr($invoice->no_invoice, 0, -2))->first();
                $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
            }

            if ($invoice->total_dibayar > 0 || (isset($reimburse) && $reimburse->total_dibayar > 0)) {
                return redirect()->route('cetak_invoice.index')->with(['status' => 'error', 'msg' => 'Invoice tidak dapat dihapus karena sudah ada pembayaran!']);
            }

            // nonaktifin anak2an invoice (invoice details), intinya logicnya matiin semua dulu baru create ulang
            if($invoice->invoiceDetails != null){
                $invoice->updated_by = $user;
                $invoice->updated_at = now();
                $invoice->is_aktif = 'N';
                $invoice->save();

                foreach ($invoice->invoiceDetails as $key => $value) {
                    $detail = InvoiceDetail::where('is_aktif', 'Y')->find($value->id);
                    $detail->updated_by = $user;
                    $detail->updated_at = now();
                    $detail->is_aktif = 'N';
                    if($detail->save()){
                        // balikin status sewa ke menunggu invoice
                        $sewa = Sewa::where('is_aktif', 'Y')->find($detail->id_sewa);
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->status = 'MENUNGGU INVOICE';
                        $sewa->save();

                        $cust = Customer::where('is_aktif', 'Y')->findOrFail($sewa->id_customer);
                        if($cust){
                            $cust->kredit_sekarang += $sewa->total_tarif;
                            $cust->updated_by = $user;
                            $cust->updated_at = now();
                            $cust->save();
                        }
                    
                        if($detail->invoiceDetailsAddCost != null){
                            foreach ($detail->invoiceDetailsAddCost as $key_cost => $item) {
                                $addcost = InvoiceDetailAddcost::where('is_aktif', 'Y')->find($item->id);
                                $addcost->updated_by = $user;
                                $addcost->updated_at = now();
                                $addcost->is_aktif = 'N';
                                $addcost->save();
                            }
                        }
                    }
                }
            }

            // nonaktifin anak2an reimburse (invoice details)
            if(isset($reimburse) || $reimburse != null){
                $reimburse->updated_by = $user;
                $reimburse->updated_at = now();
                $reimburse->is_aktif = 'N';
                $reimburse->save();

                if($reimburse->invoiceDetails != null){
                    foreach ($reimburse->invoiceDetails as $key => $value) {
                        $detail = InvoiceDetail::where('is_aktif', 'Y')->find($value->id);
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->is_aktif = 'N';
                        if($detail->save()){
                            $sewa = Sewa::where('is_aktif', 'Y')->find($detail->id_sewa);
                            $sewa->updated_by = $user;
                            $sewa->updated_at = now();
                            $sewa->status = 'MENUNGGU INVOICE';
                            $sewa->save();

                            if($detail->invoiceDetailsAddCost != null){
                                foreach ($detail->invoiceDetailsAddCost as $key_cost => $item) {
                                    $addcost = InvoiceDetailAddcost::where('is_aktif', 'Y')->find($item->id);
                                    $addcost->updated_by = $user;
                                    $addcost->updated_at = now();
                                    $addcost->is_aktif = 'N';
                                    $addcost->save();
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('cetak_invoice.index')->with(['status' => 'Succsess', 'msg' => 'Berhasil menghapus data invoice!']);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->route('cetak_invoice.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);

        }
    }
}
