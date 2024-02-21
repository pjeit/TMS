<?php

namespace App\Http\Controllers;

use App\Models\InvoiceKarantinaPembayaran;
use Illuminate\Http\Request;

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
        $data = InvoiceKarantinaPembayaran::where('is_aktif', 'Y')->get();
        return view('pages.revisi.revisi_invoice_karantina.index',[
            'judul' => "Revisi Invoice Trucking",
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
    public function destroy(InvoiceKarantinaPembayaran $invoiceKarantinaPembayaran)
    {
        //
    }
}
