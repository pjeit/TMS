<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KasBank;
use Illuminate\Http\Request;

class LaporanInvoiceTruckingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:READ_LAPORAN_INVOICE', ['only' => ['index']]);
		// $this->middleware('permission:CREATE_LAPORAN_INVOICE', ['only' => ['create','store']]);
		// $this->middleware('permission:EDIT_LAPORAN_INVOICE', ['only' => ['edit','update']]);
		// $this->middleware('permission:DELETE_LAPORAN_INVOICE', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $customers = Customer::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();
        
        return view('pages.laporan.invoice_trucking.index',[
            'judul' => "Laporan Invoice Trucking",
            'customers' => $customers,
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
