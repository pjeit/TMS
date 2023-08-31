<?php

namespace App\Http\Controllers;

use App\Models\LaporanTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
class LaporanBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $data = DB::table('job_order')
        ->select('job_order.id','job_order.no_jo','customer.nama as namaCustomer','supplier.nama as namaSupplier','job_order.pelabuhan_muat','job_order.pelabuhan_bongkar','job_order.tgl_sandar','job_order.status')
        ->Join('supplier', 'job_order.id_supplier', '=', 'supplier.id')
        ->Join('customer', 'job_order.id_customer', '=', 'customer.id')
        ->where('job_order.is_aktif', '=', 'Y') 
        ->where('job_order.status', 'like', 'FINANCE PENDING') 

        ->paginate(5);

        // dd($data);
        


        //  $data = JobOrder::where('is_aktif', 'Y')->paginate(5);

        return view('pages.laporan.Bank.index',[
            'judul' => "LAPORAN BANK",
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
     * @param  \App\Models\LaporanTransaksi  $laporanTransaksi
     * @return \Illuminate\Http\Response
     */
    public function show(LaporanTransaksi $laporanTransaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LaporanTransaksi  $laporanTransaksi
     * @return \Illuminate\Http\Response
     */
    public function edit(LaporanTransaksi $laporanTransaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LaporanTransaksi  $laporanTransaksi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaporanTransaksi $laporanTransaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LaporanTransaksi  $laporanTransaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(LaporanTransaksi $laporanTransaksi)
    {
        //
    }
}
