<?php

namespace App\Http\Controllers;

use App\Models\LaporanTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
class LaporanKasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = DB::table('kas_bank_transaction')
            ->where('is_aktif', '=', 'Y') 
            ->where('id_kas_bank', '3') 
            ->whereBetween('tanggal', ['2023-07-01', '2023-07-31'])
            ->orderBy('tanggal', 'ASC')
            ->get();

        $kas = DB::table('kas_bank')
            ->where('id', '3') 
            ->first();

        $sumKredit = DB::table('kas_bank_transaction')
            ->where('is_aktif', '=', 'Y') 
            ->where('id_kas_bank', '3') 
            ->whereBetween('tanggal', ['2023-07-01', '2023-07-31'])
            ->sum('kredit');
        $sumDebit = DB::table('kas_bank_transaction')
            ->where('is_aktif', '=', 'Y') 
            ->where('id_kas_bank', '3') 
            ->whereBetween('tanggal', ['2023-07-01', '2023-07-31'])
            ->sum('debit');

        return view('pages.laporan.Kas.index',[
            'judul' => "LAPORAN KAS",
            'data' => $data,
            'kas' => $kas,
            'sumKredit' => $sumKredit,
            'sumDebit' => $sumDebit,
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
