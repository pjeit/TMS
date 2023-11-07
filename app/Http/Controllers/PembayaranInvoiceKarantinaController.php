<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceKarantina;
use App\Models\KasBank;
use Illuminate\Http\Request;

class PembayaranInvoiceKarantinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = InvoiceKarantina::where('is_aktif', 'Y')->with('details.kontainers.getJOD')->get();
        // dd($data);

        return view('pages.invoice.pembayaran_invoice_karantina.index',[
            'judul' => "PEMBAYARAN INVOICE",
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bayar(Request $request)
    {
        $collect = $request->collect();
        $data = InvoiceKarantina::whereIn('id', $collect['idInvoice'])->where('is_aktif', 'Y')->get();
        
        $dataCustomers  = Customer::where('grup_id', $data[0]->getCustomer->getGrup->id)
                                ->where('is_aktif', 'Y')->get();
        
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.invoice.pembayaran_invoice_karantina.bayar',[
            'judul' => "Bayar Invoice Karantina",
            'data' => $data,
            'dataCustomers' => $dataCustomers,
            'dataKas' => $dataKas,
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
}
