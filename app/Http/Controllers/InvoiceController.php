<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Carbon\Carbon;

class InvoiceController extends Controller
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
        // Session::flush();
        Session::forget(['sewa', 'cust', 'grup']);
        $dataSewa =  DB::table('sewa AS s')
                ->select('s.*','s.id_sewa as idSewanya','c.id AS id_cust','c.nama AS nama_cust','g.nama_grup','g.id as id_grup','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup AS g', 'c.grup_id', '=', 'g.id')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')

                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->where('s.is_aktif', '=', 'Y')
                // ->where('s.jenis_tujuan', 'like', '%FTL%')
                ->where('s.status', 'MENUNGGU INVOICE')
                ->whereNull('s.id_supplier')
                // ->whereNull('s.tanggal_kembali')
                ->orderBy('c.id','ASC')
                ->get();
        // dd($dataSewa);
    
        return view('pages.invoice.belum_invoice.index',[
            'judul'=>"BELUM INVOICE",
            'dataSewa' => $dataSewa,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function setSewaID(Request $request)
    {
        $sewa = session()->get('sewa'); //buat ambil session
        $cust = session()->get('cust'); //buat ambil session
        $grup = session()->get('grup'); //buat ambil session
        Session::forget(['sewa', 'cust', 'grup']);

        $data= $request->collect();
        session()->put('sewa', $data['idSewa']);
        session()->put('cust', $data['idCust']);
        session()->put('grup', $data['idGrup']);
        return $sewa;

        
    }
    public function invoiceKembali(Request $request)
    {
        //
        $user = Auth::user()->id;
        $data= $request->collect();
        // dd(isset($data['idJo']));

        try {
            DB::table('sewa')
                ->where('id_sewa',  $data['idSewa'])
                ->update(array(
                  'status' => 'DALAM PERJALANAN',
                  'is_kembali' => 'N',
                  'tanggal_kembali' => null,
                  'updated_at'=> now(),
                  'updated_by'=>  $user,
                )
            );
            if(isset($data['idJo'])&&isset($data['idJo_detail']))
            {
                DB::table('job_order_detail')
                ->where('id',  $data['idJo_detail'])
                ->update(array(
                  'status' => 'DALAM PERJALANAN',
                  'updated_at'=> now(),
                  'updated_by'=>  $user,
                    )
                ); 
            }
            return redirect()->route('invoice.index')->with('status','Sukses Mengubah mengembalikan data sewa!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
      
    }
    public function create(Request $request)
    {
        $sewa = session()->get('sewa'); //buat ambil session
        $cust = session()->get('cust'); //buat ambil session
        $grup = session()->get('grup'); //buat ambil session
        // dd($cust);
        
        $data = Sewa::whereIn('sewa.id_sewa', $sewa)
                ->where('sewa.status', 'MENUNGGU INVOICE')
                ->get();

        $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
                ->leftJoin('customer as c', 'c.id', 'id_customer')
                ->where('c.grup_id', $grup[0])
                ->where('sewa.status', 'MENUNGGU INVOICE')
                ->select('sewa.*')
                ->get();

        $dataCust = Customer::where('grup_id', $grup[0])
                ->where('is_aktif', 'Y')
                ->get();

        return view('pages.invoice.belum_invoice.form',[
            'judul'=>"BELUM INVOICE",
            'data' => $data,
            'dataSewa' => $dataSewa,
            'dataCust' => $dataCust,
            'grup' => $grup[0],
            'customer' => $cust[0],
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
        $user = Auth::user()->id;
        $data = $request->post();
        // dd($data);
        try {
            // logic nomer booking
                $kode = 'PJE/'.$data['kode_customer'].'/'.date("y").date("m");
                $maxInvoice = DB::table('invoice')
                    ->selectRaw("ifnull(max(substr(no_invoice, -3)), 0) + 1 as max_invoice")
                    ->where('no_invoice', 'like', $kode.'%')
                    ->orderBy('no_invoice', 'DESC')
                    ->value('max_invoice');
                    
                $newInvoiceNumber = 'PJE/' . $data['kode_customer'] . '/' . date("y") . date("m") . str_pad($maxInvoice, 3, '0', STR_PAD_LEFT);

                if (is_null($maxInvoice)) {
                    $newInvoiceNumber = 'PJE/' . $data['kode_customer'] .'/'. date("y") . date("m") . '001';
                }
            //

            $invoice = new Invoice();
            $invoice->id_grup = $data['grup_id'];
            $invoice->no_invoice = $newInvoiceNumber;
            $invoice->tgl_invoice = date_create_from_format('d-M-Y', $data['tanggal_invoice']);
            $invoice->total_tagihan = ($data['total_tagihan'] != '')? floatval(str_replace(',', '', $data['total_tagihan'])):0;
            $invoice->total_sisa = ($data['total_sisa'] != '')? floatval(str_replace(',', '', $data['total_sisa'])):0;
            $invoice->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
            $invoice->catatan = $data['catatan_invoice'];
            $invoice->status = 'MENUNGGU PEMBAYARAN INVOICE';
            $invoice->billing_to = $data['billingTo'];
            $invoice->created_by = $user;
            $invoice->created_at = now();
            $invoice->is_aktif = 'Y';
            if($invoice->save()){
                foreach ($data['detail'] as $key => $value) {
                    $invoice_d = new InvoiceDetail();
                    $invoice_d->id_invoice = $invoice->id;
                    $invoice_d->id_customer = $value['id_customer'];
                    $invoice_d->id_sewa = $key;
                    $invoice_d->tarif = $value['tarif']!=NULL? $value['tarif']:0;
                    $invoice_d->add_cost = $value['addcost']!=NULL? $value['addcost']:0;
                    $invoice_d->diskon = $value['diskon']!=NULL? floatval(str_replace(',', '', $value['diskon'])):0;
                    $invoice_d->sub_total = $value['subtotal']!=NULL? $value['subtotal']:0;
                    $invoice_d->catatan = $value['catatan'];
                    $invoice_d->status = 'MENUNGGU PEMBAYARAN INVOICE DETAIL';
                    $invoice_d->created_by = $user;
                    $invoice_d->created_at = now();
                    $invoice_d->is_aktif = 'Y';
                    if($invoice_d->save()){
                        $dataAddcost = json_decode($value['addcost_details']);
                        foreach ($dataAddcost as $i => $addcost) {
                            $invoice_da = new InvoiceDetailAddcost();
                            $invoice_da->id_invoice = $invoice->id;
                            $invoice_da->id_invoice_detail = $invoice_d->id;
                            $invoice_da->id_sewa_operasional = $addcost->id;
                            $invoice_da->catatan = $addcost->catatan;
                            $invoice_da->created_by = $user;
                            $invoice_da->created_at = now();
                            $invoice_da->is_aktif = 'Y';
                            $invoice_da->save();
                        }
                    }
                }
            }

            return redirect()->route('invoice.index')
                ->with('id_print_invoice', $invoice->id)
                ->with('status', 'Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function print($id)
    {
        $data = Invoice::where('is_aktif', '=', "Y")
            ->where('id', $id)
            ->first();
        // dd($id);
        $TotalBiayaRev = 0;

        // dd($dataJoDetail);   
        $pdf = PDF::loadView('pages.invoice.belum_invoice.print',[
            'judul' => "Invoice",
            'data' => $data,

        ]); 
        $pdf->setPaper('A4', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 180, // Set a high DPI for better resolution
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);

        return $pdf->stream('xxxxx'.'.pdf'); 

    }
}
