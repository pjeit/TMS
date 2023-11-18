<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function Reset()
    {
        try {
            DB::transaction(function () {
                // Add your DELETE statements here
                DB::statement('DELETE FROM tagihan_pembelian');
                DB::statement('DELETE FROM tagihan_pembelian_detail');
                DB::statement('DELETE FROM tagihan_pembelian_pembayaran');
                DB::statement('DELETE FROM tagihan_rekanan');
                DB::statement('DELETE FROM tagihan_rekanan_detail');
                DB::statement('DELETE FROM tagihan_rekanan_pembayaran');
                DB::statement('DELETE FROM pencairan_komisi_detail');
                DB::statement('DELETE FROM pencairan_komisi');
                DB::statement('DELETE FROM trip_supir');
                DB::statement('DELETE FROM sewa_biaya');
                DB::statement('DELETE FROM sewa_operasional');
                DB::statement('DELETE FROM sewa_biaya');
                DB::statement('DELETE FROM sewa');
                DB::statement('DELETE FROM job_order_detail_biaya');
                DB::statement('DELETE FROM job_order_detail');
                DB::statement('DELETE FROM job_order');
                DB::statement('DELETE FROM jaminan');
                DB::statement('DELETE FROM invoice');
                DB::statement('DELETE FROM invoice_detail');
                DB::statement('DELETE FROM invoice_detail_addcost');
                DB::statement('DELETE FROM invoice_pembayaran');
                DB::statement('DELETE FROM karyawan_hutang_transaction');
                DB::statement('DELETE FROM uang_jalan_riwayat');
                DB::statement('DELETE FROM sewa_batal_cancel');
                DB::statement('DELETE FROM tagihan_rekanan');
                DB::statement('DELETE FROM tagihan_rekanan_detail');
                DB::statement('DELETE FROM tagihan_rekanan_pembayaran');
                DB::statement('DELETE FROM karantina');
                DB::statement('DELETE FROM karantina_detail');
                DB::statement('DELETE FROM invoice_karantina');
                DB::statement('DELETE FROM invoice_karantina_pembayaran');

            });
        } catch (\Exception $e) {
            // Handle or log the exception
            Log::error($e->getMessage());
        }
        
        return view('home', [
            'judul'=>'Home'
        ])->with(['status' => 'Success', 'msg' => 'Berhasil reset data']);

    }

    public function index()
    {
        //
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
