<?php

namespace App\Http\Controllers;

use App\Models\LaporanTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Models\KasBank;
use App\Models\KasBankTransaction;

class LaporanBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_BANK', ['only' => ['index']]);
    }
    
    public function index(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $tipe           = $request->input('tipe');

        if(strlen($tanggal_awal) && strlen($tanggal_akhir)){
            $tgl_default = '2000-01-01';
            $tgl_awal = date('Y-m-d', strtotime($tanggal_awal)); // date_create_from_format('d-M-Y', $tanggal_awal);
            $tgl_akhir = date('Y-m-d', strtotime($tanggal_akhir)); // date_create_from_format('d-M-Y', $tanggal_awal);
    
            $data = DB::table('kas_bank_transaction')
                ->where('is_aktif', '=', 'Y') 
                ->where('id_kas_bank', "$tipe") 
                ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                ->orderBy('tanggal', 'ASC')
                ->get();
            // buat ngitung total, biar ngitungnya ga di frontend
                // ini sama kaya deklarasi $jumlah di php
                DB::statement('set @kas_bank_id = 0');
                DB::statement('set @subtotal = 0');
            //
            $data = DB::select("SELECT d.tanggal, d.jenis, d.keterangan_transaksi, d.kode_coa, d.debit, d.kredit, d.id
                ,if(@kas_bank_id <> d.id_kas_bank, 
                    -- ini mirip deklarasi di php, contoh: 
                    @subtotal := ifnull(d.debit,0) - ifnull(d.kredit,0), 
                    @subtotal := ifnull(@subtotal,0) + ifnull(d.debit,0) - ifnull(d.kredit,0)
                ) as total,
                if(@subtotal >= 0, abs(@subtotal), 0) as subtotal_debit,
                if(@subtotal >= 0, 0, abs(@subtotal)) as subtotal_kredit,
                @kas_bank_id := d.id_kas_bank, d.id_kas_bank as id_kas_bank,
                -- ini kayak switch statement di php
                case
                    when d.jenis = 'saldo_awal' 
                        then 'Saldo Awal'
                    when d.jenis = 'uang_jalan' 
                        then 'Uang Jalan'
                    when d.jenis = 'reimburse' 
                        then 'Reimburse'
                    when d.jenis = 'invoice_customer' 
                        then 'Invoice'
                    when d.jenis = 'tagihan_supplier' 
                        then 'Pembelian'
                    when d.jenis = 'hutang_karyawan' 
                        then 'Hutang'
                    when d.jenis = 'gaji' 
                        then 'Gaji'
                    when d.jenis = 'transfer_dana' 
                        then 'Pindah Dana'
                    when d.jenis = 'lainnya' 
                        then 'Lainnya'
                    when d.jenis = 'biaya_admin' 
                        then 'Biaya_admin'
                    when d.jenis = 'klaim_supir' 
                        then 'Klaim Supir'
                    when d.jenis = 'pencairan_operasional' 
                        then 'Pencairan Operasional'
                    when d.jenis = 'pembayaran_invoice' 
                        then 'Pembayaran Invoice'
                    when d.jenis = 'tagihan_pembelian' 
                        then 'Pembayaran Nota'
                    when d.jenis = 'karantina' 
                        then 'Karantina'
                    when d.jenis = 'bonus_supir' 
                        then 'Bonus Supir'
                    when d.jenis = 'operasional_refund' 
                        then 'Operasional Kembali'
                    when d.jenis = 'biaya_pelayaran' 
                        then 'Biaya Pelayaran'
                    when d.jenis = 'biaya_sdt' 
                        then 'Biaya S/D/T'
                    when d.jenis = 'pembayaran_invoice_karantina' 
                        then 'Pembayaran Invoice Karantina'
                    when d.jenis = 'komisi_customer' 
                        then 'Pencairan Komisi Customer'
                    when d.jenis = 'komisi_driver' 
                        then 'Pencairan Komisi Driver'
                    when d.jenis = 'pengembalian_jaminan' 
                        then 'Pengembalian Jaminan'
                    when d.jenis = 'teluk_lamong ' 
                        then 'Revisi Teluk Lamong'
                    when d.jenis = 'rev_tambahan_uang_jalan ' 
                        then 'Revisi Uang Jalan'
                    when d.jenis = 'tagihan_rekanan' 
                        then 'Pembayaran Gabungan'
                    when d.jenis = 'lembur_mekanik' 
                        then 'Lembur Mekanik'
                    when d.jenis = 'klaim_operasional' 
                        then 'Klaim Operasional'
                end jenis_deskripsi
                FROM (
                    -- SELECT 
                    -- id, id_kas_bank, CAST(DATE_ADD('$tgl_akhir', interval 1 day) AS DATE) AS tanggal, NULL AS jenis, 
                    -- 'Saldo Awal' AS keterangan_transaksi, NULL AS kode_coa, 
                    -- IF(SUM(debit) - SUM(kredit) >= 0, ABS(SUM(debit) - SUM(kredit)), 0) AS debit,
                    -- IF(SUM(debit) - SUM(kredit) >= 0, 0, ABS(SUM(debit) - SUM(kredit))) AS kredit, keterangan_kode_transaksi
                    -- FROM kas_bank_transaction 
                    -- WHERE id_kas_bank = '$tipe'
                    -- AND CAST(tanggal AS DATE) BETWEEN date_add('$tgl_default', interval 1 day) 
                    -- AND date_add('$tgl_awal', interval -1 day)
                    -- AND is_aktif = 'Y'
                    -- group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                    -- UNION ALL

                    -- ini select sub query, dulu di union, makanya pake subquery, tp sekarang uda ngga pake union, aslinya ga perlu sub query juga bisa
                    SELECT 
                        id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                    FROM kas_bank_transaction 
                    WHERE id_kas_bank = '$tipe'
                    AND CAST(tanggal AS DATE) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    AND is_aktif = 'Y'
                    --  group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                ) AS d 
                ORDER BY cast(tanggal as datetime) asc,id     
            ");
            
            $kas = DB::table('kas_bank')->find($tipe);

            // ini ngeget seluruh data dari awal record sampai dengan tanggal awal yg diinput buat dapetin saldo awal
            $transaction = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('id_kas_bank', $tipe)
                                            // ->whereBetween('tanggal', [$tgl_default, $tgl_awal]) 
                                            ->where('tanggal','<', $tgl_awal)
                                            ->get();
            
            $sumKredit  = $transaction->sum('kredit'); // ini hitung sum kreditnya
            $sumDebit   = $transaction->sum('debit');
            // di front end, $saldo_sekarang = $kas->saldo_sekarang + $sumDebit - $sumKredit; ini buat nentuin saldo awal

            $kasBank = KasBank::where('is_aktif', 'Y')->where('tipe', 'Bank')->orderBy('nama', 'asc')->get();
    
            return view('pages.laporan.Bank.index',[
                'judul' => "LAPORAN BANK",
                'data' => $data,
                'kas' => $kas,
                'sumKredit' => $sumKredit,
                'sumDebit' => $sumDebit,
                'request' => $request,
                'kasBank' => $kasBank,
            ]);
        }else{
            $kasBank = DB::table('kas_bank')->where('is_aktif', 'Y')->where('tipe','like' ,'%Bank%')->orderBy('nama', 'asc')->get();
            $data = NULL;
            $kas = NULL;
            return view('pages.laporan.Bank.index',[
                'judul' => "LAPORAN BANK",
                'data' => $data,
                'request' => $request,
                'kas' => $kas,
                'sumKredit' => NULL,
                'sumDebit' => NULL,
                'kasBank' => $kasBank,
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
