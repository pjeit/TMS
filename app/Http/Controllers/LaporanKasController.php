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
    public function index(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        if(strlen($tanggal_awal) && strlen($tanggal_akhir)){
            $tgl_default = '2000-01-01';
            $tgl_awal = date('Y-m-d', strtotime($tanggal_awal)); // date_create_from_format('d-M-Y', $tanggal_awal);
            $tgl_akhir = date('Y-m-d', strtotime($tanggal_akhir)); // date_create_from_format('d-M-Y', $tanggal_awal);
    
            $data = DB::table('kas_bank_transaction')
                ->where('is_aktif', '=', 'Y') 
                ->where('id_kas_bank', '3') 
                ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                ->orderBy('tanggal', 'ASC')
                ->get();
    
            // buat ngitung total, biar ngitungnya ga di frontend
                DB::statement('set @kas_bank_id = 0');
                DB::statement('set @subtotal = 0');
            //
            $data = DB::select("SELECT d.tanggal, d.jenis, d.keterangan_transaksi, d.kode_coa, d.debit, d.kredit, d.id
                ,if(@kas_bank_id <> d.id_kas_bank, 
                    @subtotal := ifnull(d.debit,0) - ifnull(d.kredit,0), 
                    @subtotal := ifnull(@subtotal,0) + ifnull(d.debit,0) - ifnull(d.kredit,0)
                ) as total,
                if(@subtotal >= 0, abs(@subtotal), 0) as subtotal_debit,
                if(@subtotal >= 0, 0, abs(@subtotal)) as subtotal_kredit,
                @kas_bank_id := d.id_kas_bank as xx, d.id_kas_bank as idx
                FROM (
                    SELECT 
                    id, id_kas_bank, CAST('$tgl_awal' AS DATE) AS tanggal, NULL AS jenis, 
                    'Saldo Awal' AS keterangan_transaksi, NULL AS kode_coa, 
                    IF(SUM(debit) - SUM(kredit) >= 0, ABS(SUM(debit) - SUM(kredit)), 0) AS debit,
                    IF(SUM(debit) - SUM(kredit) >= 0, 0, ABS(SUM(debit) - SUM(kredit))) AS kredit
                    FROM kas_bank_transaction 
                    WHERE id_kas_bank = '3'
                    AND CAST(tanggal AS DATE) BETWEEN '$tgl_default' AND '$tgl_awal'
                    AND is_aktif = 'Y'
                    group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit
                    UNION ALL
                    SELECT 
                        id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit
                    FROM kas_bank_transaction 
                    WHERE id_kas_bank = '3'
                    AND CAST(tanggal AS DATE) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    AND is_aktif = 'Y'
                    group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit
                ) AS d 
                ORDER BY CAST(d.tanggal AS DATE)
            ");

            $kas = DB::table('kas_bank')->where('id', '3') ->first();
    
            $sumKredit = DB::table('kas_bank_transaction')
                ->where('is_aktif', '=', 'Y') 
                ->where('id_kas_bank', '3') 
                // ->where('tanggal', '<=', '2023-06-01')
                ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                ->sum('kredit');
    
            $sumDebit = DB::table('kas_bank_transaction')
                ->where('is_aktif', '=', 'Y') 
                ->where('id_kas_bank', '3') 
                // ->where('tanggal', '<=', '2023-06-01')
                ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                ->sum('debit');
    
            return view('pages.laporan.Kas.index',[
                'judul' => "LAPORAN KAS",
                'data' => $data,
                'kas' => $kas,
                'sumKredit' => $sumKredit,
                'sumDebit' => $sumDebit,
            ]);
        }else{
            $data = NULL;
            $kas = NULL;
            $sumKredit = NULL;
            $sumDebit = NULL;
            return view('pages.laporan.Kas.index',[
                'judul' => "LAPORAN KAS",
                'data' => $data,
                'kas' => $kas,
                'sumKredit' => $sumKredit,
                'sumDebit' => $sumDebit,
            ]);
        }
       
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
