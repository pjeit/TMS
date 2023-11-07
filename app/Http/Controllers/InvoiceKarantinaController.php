<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceKarantina;
use App\Models\InvoiceKarantinaDetail;
use App\Models\InvoiceKarantinaDetailKontainer;
use App\Models\JobOrder;
use App\Models\Karantina;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;

class InvoiceKarantinaController extends Controller
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

        $customer = Karantina::where('is_aktif', 'Y')->with('getCustomer')->groupBy('id_customer')->get();

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
        // dd($data);

        try {
            $kode = 'PJE/KRNT/'.$data['kode'].'/'.date("y").date("m");
            $maxInvoice = DB::table('invoice_karantina')
                ->selectRaw("ifnull(max(substr(no_invoice_k, -3)), 0) + 1 as max_invoice")
                ->where('no_invoice_k', 'like', $kode.'%')
                ->orderBy('no_invoice_k', 'DESC')
                ->value('max_invoice');
            
            $newInvoiceNumber = 'PJE/KRNT/' . $data['kode'] . '/' . date("y") . date("m") . str_pad($maxInvoice, 3, '0', STR_PAD_LEFT);

            if (is_null($maxInvoice)) {
                $newInvoiceNumber = 'PJE/KRNT/' . $data['kode'] .'/'. date("y") . date("m") . '001';
            }

            $invoice = new InvoiceKarantina();
            $invoice->id_customer = $data['customer'];
            $invoice->no_invoice_k = $newInvoiceNumber;
            $invoice->tgl_invoice = date("Y-m-d", strtotime($data['tanggal_invoice']));
            $invoice->total_tagihan = $data['total_nominal'];
            $invoice->sisa_tagihan = $data['total_nominal'];
            $invoice->status = "MENUNGGU PEMBAYARAN";
            $invoice->created_by = $user;
            $invoice->created_at = now();
            if($invoice->save()){
                foreach ($data['data'] as $key => $value) {
                    $detail = new InvoiceKarantinaDetail();
                    $detail->id_invoice_k       = $invoice->id; 
                    $detail->id_jo              = $key; 
                    $detail->tarif_karantina    = floatval(str_replace(',', '', $value['nominal']));
                    $detail->created_by         = $user;
                    $detail->created_at         = now();
                    if($detail->save()){
                        if(isset($value['idJOD']) && $value['nominal'] != null){
                            foreach ($value['idJOD'] as $item) {
                                $kontainer = new InvoiceKarantinaDetailKontainer();
                                $kontainer->id_invoice_k = $invoice->id; 
                                $kontainer->id_invoice_k_detail = $detail->id; 
                                $kontainer->id_jo_detail = $item; 
                                $kontainer->created_by = $user;
                                $kontainer->created_at = now();
                                $kontainer->save();
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('invoice_karantina.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('invoice_karantina.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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
        //
         $dataInvoiceKarantina = InvoiceKarantina::where('invoice_karantina.is_aktif', '=', "Y")
            ->select('invoice_karantina.*','c.nama as nama_customer')
            ->leftJoin('customer AS c', 'invoice_karantina.id_customer', '=', 'c.id')
            ->where('invoice_karantina.id', $id)
            ->first();

        $InvoiceKarantinaDetail = InvoiceKarantinaDetail::where('invoice_karantina_detail.is_aktif', '=', "Y")
            ->select('invoice_karantina_detail.*','jo.kapal','jo.no_bl')
            ->leftJoin('job_order AS jo', 'invoice_karantina_detail.id_jo', '=', 'jo.id')
            ->where('invoice_karantina_detail.id_invoice_k', $dataInvoiceKarantina->id)
            ->get();
        $InvoiceKarantinaDetailKontainer = InvoiceKarantinaDetailKontainer::where('invoice_karantina_detail_kontainer.is_aktif', '=', "Y")
            ->select('invoice_karantina_detail_kontainer.*','jod.no_kontainer','jod.seal','jod.tipe_kontainer')
            ->leftJoin('job_order_detail AS jod', 'invoice_karantina_detail_kontainer.id_jo_detail', '=', 'jod.id')
            ->where('invoice_karantina_detail_kontainer.id_invoice_k', $dataInvoiceKarantina->id)
            ->get();

        // dd($dataInvoiceKarantina);
        $qrcode = QrCode::size(150)
        // ->backgroundColor(255, 0, 0, 25)
        ->generate(
             'No. Invoice: ' . '$data->no_invoice' . "\n" .
             'Total tagihan: ' .'Rp.' .'number_format($data->total_tagihan,2) '
        );
        $pdf = PDF::loadView('pages.invoice.invoice_karantina.print',[
            'judul' => "Invoice",
            'dataInvoiceKarantina' => $dataInvoiceKarantina,
            'InvoiceKarantinaDetail' => $InvoiceKarantinaDetail,
            'InvoiceKarantinaDetailKontainer' => $InvoiceKarantinaDetailKontainer,
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
        // return view('pages.invoice.invoice_karantina.print',[
        //     'judul'=>"Invoice",

        // ]);
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
        $data = Karantina::where('is_aktif', 'Y')->where('id_customer', $id)->with('getCustomer.getGrup', 'getJO')->get();

        return $data;
    }
}
