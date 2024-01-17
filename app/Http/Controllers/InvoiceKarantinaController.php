<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceKarantina;
use App\Models\InvoiceKarantinaDetail;
use App\Models\InvoiceKarantinaDetailKontainer;
use App\Models\JobOrder;
use App\Models\Karantina;
use App\Models\KarantinaDetail;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf; // use PDF;

class InvoiceKarantinaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_INVOICE_KARANTINA', ['only' => ['index']]);
		$this->middleware('permission:CREATE_INVOICE_KARANTINA', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_INVOICE_KARANTINA', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_INVOICE_KARANTINA', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        // $customer = Karantina::where('is_aktif', 'Y')->with('getCustomer')->groupBy('id_customer')->get();
        $customer = DB::table('karantina as k')
                        ->leftJoin('karantina_detail as kd', 'k.id', '=', 'kd.id_karantina')
                        ->leftJoin('customer as c', 'c.id', '=', 'k.id_customer')
                        ->selectRaw('k.id_customer, c.nama, COUNT(k.is_invoice) as count_invoice')
                        ->where('k.is_invoice', 'N')
                        ->where(function($query) {
                            $query->where('total_dicairkan', '!=', null)
                                ->orWhere('total_dicairkan', '!=', 0);
                        })
                        ->groupBy('k.id_customer')
                        ->get();
        // dd($customer);

        // dd($customer[0]->getCustomer);
        return view('pages.invoice.invoice_karantina.index',[
            'judul' => "Invoice Karantina",
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $req = $request->collect();
        $cust = $req['customer']; // buat ambil session
        // dd($request->collect());
        
        $data = Karantina::whereIn('id', $req['idKarantina'])
                ->where('is_aktif', 'Y')
                ->get();
        // dd($data);

        $dataCust = Customer::where('grup_id', $data[0]->getCustomer->grup_id)
                ->where('is_aktif', 'Y')
                ->get();
        
        return view('pages.invoice.invoice_karantina.form',[
            'judul' => "Buat Invoice Karantina",
            'data' => $data,
            'dataCust' => $dataCust,
            'grup' => NULL,
            'customer' => $cust,
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
        $data = $request->collect();
        DB::beginTransaction(); 

        try {
            $kode = 'PJE/KRNT/'.$data['kode_customer'].'/'.date("y").date("m");
            $maxInvoice = DB::table('invoice_karantina')
                ->selectRaw("ifnull(max(substr(no_invoice_k, -3)), 0) + 1 as max_invoice")
                ->where('no_invoice_k', 'like', $kode.'%')
                ->orderBy('no_invoice_k', 'DESC')
                ->value('max_invoice');
            
            $newInvoiceNumber = 'PJE/KRNT/' . $data['kode_customer'] . '/' . date("y") . date("m") . str_pad($maxInvoice, 3, '0', STR_PAD_LEFT);

            if (is_null($maxInvoice)) {
                $newInvoiceNumber = 'PJE/KRNT/' . $data['kode_customer'] .'/'. date("y") . date("m") . '001';
            }

            $invoice = new InvoiceKarantina();
            $invoice->id_customer = $data['billingTo'];
            $invoice->no_invoice_k = $newInvoiceNumber;
            $invoice->tgl_invoice = date("Y-m-d", strtotime($data['tanggal_invoice']));
            $invoice->jatuh_tempo = date("Y-m-d", strtotime($data['jatuh_tempo']));
            $invoice->total_tagihan = floatval(str_replace(',', '', $data['total_tagihan']));
            $invoice->sisa_tagihan = floatval(str_replace(',', '', $data['total_tagihan']));
            $invoice->catatan = $data['catatan_invoice'];
            $invoice->status = "MENUNGGU PEMBAYARAN";
            $invoice->created_by = $user;
            $invoice->created_at = now();
            if($invoice->save()){
                foreach ($data['data'] as $key => $value) {
                    $karantina = Karantina::where('is_aktif', 'Y')->find($key);
                    if($karantina){
                        $karantina->is_invoice = 'Y';
                        $karantina->updated_by = $user;
                        $karantina->updated_at = now();
                        if($karantina->save()){
                            $detail = new InvoiceKarantinaDetail();
                            $detail->id_invoice_k = $invoice->id; 
                            $detail->id_karantina = $key; 
                            $detail->created_by   = $user;
                            $detail->created_at   = now();
                            $detail->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('invoice_karantina.index')
                    ->with([
                            'status' => 'Success', 
                            'msg' => 'Invoice berhasil dibuat!',
                            'id_invoice' => $invoice->id, 
                        ]);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('invoice_karantina.index')->with(['status' => 'error', 'msg' => 'Invoice gagal dibuat!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        $invoiceKarantina = InvoiceKarantina::where('invoice_karantina.is_aktif', '=', "Y")
                ->with('getCustomer')
                ->find($id);

        $invoiceKarantinaDetail = InvoiceKarantinaDetail::where('invoice_karantina_detail.is_aktif', '=', "Y")
            ->where('id_invoice_k', $invoiceKarantina->id)
            ->with('getKarantina.details')
            ->get();

        // dd($invoiceKarantinaDetail);

        $qrcode = QrCode::size(150)
        // ->backgroundColor(255, 0, 0, 25)
        ->generate(
            'No. Invoice: ' . '$data->no_invoice' . "\n" .
            'Total tagihan: ' .'Rp.' .'number_format($data->total_tagihan,2) '
        );

        $pdf = Pdf::loadView('pages.invoice.invoice_karantina.print',[
            'judul' => "Invoice",
            'invoiceKarantina' => $invoiceKarantina,
            'invoiceKarantinaDetail' => $invoiceKarantinaDetail,
            'qrcode'=>$qrcode,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 250, // Set a high DPI for better resolution
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);

        return $pdf->stream('invoice_karantina'.'.pdf'); 
    }
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function load_data($id)
    {
        $data = Karantina::where('is_aktif', 'Y')
                ->where('id_customer', $id)
                ->where('is_invoice', 'N')
                ->where('total_dicairkan', '!=', null)
                ->with('getCustomer.getGrup', 'getJO')->get();
        return $data;
    }
}
