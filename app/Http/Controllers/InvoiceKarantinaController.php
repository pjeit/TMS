<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use App\Models\InvoiceKarantina;
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

        $customer = JobOrder::where('is_aktif', 'Y')->with('getCustomer')->groupBy('id_customer')->get();

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
        $data = $request->collect();
        dd($data);
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
        //  $data = InvoiceKarantina::where('is_aktif', '=', "Y")
        //     ->where('id', $id)
        //     ->first();
        
        // dd($data);
        $qrcode = QrCode::size(150)
        // ->backgroundColor(255, 0, 0, 25)
        ->generate(
             'No. Invoice: ' . '$data->no_invoice' . "\n" .
             'Total tagihan: ' .'Rp.' .'number_format($data->total_tagihan,2) '
        );
        $pdf = PDF::loadView('pages.invoice.invoice_karantina.print',[
            'judul' => "Invoice",
            // 'data' => $data,
            'qrcode'=>$qrcode,
            // 'dataOperasional'=>$dataOperasional
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
        $data = JobOrder::where([
                    'is_aktif' => 'Y',
                    'id_customer' => $id,
                ])->with('getDetails.getTujuan')->get();

        return $data;
    }
}
