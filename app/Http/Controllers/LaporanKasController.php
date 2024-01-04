<?php

namespace App\Http\Controllers;

use App\Models\LaporanTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Models\KasBankTransaction;

class LaporanKasController extends Controller
{
  public function __construct()
  {
      $this->middleware('permission:READ_LAPORAN_KAS', ['only' => ['index']]);
  }

  public function index(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        // $tipe           = $request->input('tipe');

        if(strlen($tanggal_awal) && strlen($tanggal_akhir)){
            $tgl_default = '2000-01-01';
            $tgl_awal = date('Y-m-d', strtotime($tanggal_awal)); // date_create_from_format('d-M-Y', $tanggal_awal);
            $tgl_akhir = date('Y-m-d', strtotime($tanggal_akhir)); // date_create_from_format('d-M-Y', $tanggal_awal);
    
            $data = DB::table('kas_bank_transaction')
                ->where('is_aktif', '=', 'Y') 
                ->where('id_kas_bank', 2) 
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
                @kas_bank_id := d.id_kas_bank,
                case
                  when d.jenis = 'saldo_awal' then
                    'Saldo Awal'
                  when d.jenis = 'uang_jalan' then
                    'Uang Jalan'
                  when d.jenis = 'reimburse' then
                    'Reimburse'
                  when d.jenis = 'invoice_customer' then
                    'Invoice'
                  when d.jenis = 'tagihan_supplier' then
                    'Pembelian'
                  when d.jenis = 'hutang_karyawan' then
                    'Hutang'
                  when d.jenis = 'gaji' then
                    'Gaji'
                  when d.jenis = 'transfer_dana' then
                    'Pindah Dana'
                  when d.jenis = 'lainnya' then
                    'Lainnya'
                when d.jenis = 'biaya_admin' then
                    'Biaya_admin'
                when d.jenis = 'uang_klaim_supir' then
                    'Klaim Supir'
                  end jenis_deskripsi
                FROM (
                    -- SELECT 
                    -- id, id_kas_bank, CAST(DATE_ADD('$tgl_akhir', interval 1 day) AS DATE) AS tanggal, NULL AS jenis, 
                    -- 'Saldo Awal' AS keterangan_transaksi, NULL AS kode_coa, 
                    -- IF(SUM(debit) - SUM(kredit) >= 0, ABS(SUM(debit) - SUM(kredit)), 0) AS debit,
                    -- IF(SUM(debit) - SUM(kredit) >= 0, 0, ABS(SUM(debit) - SUM(kredit))) AS kredit,keterangan_kode_transaksi
                    -- FROM kas_bank_transaction 
                    -- WHERE id_kas_bank = 2
                    -- AND CAST(tanggal AS DATE) BETWEEN date_add('$tgl_default', interval 1 day) 
                    -- AND date_add('$tgl_awal', interval -1 day)
                    -- AND is_aktif = 'Y'
                    -- --  group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                    -- UNION ALL
                    SELECT 
                        id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                    FROM kas_bank_transaction 
                    WHERE id_kas_bank = 2
                    AND CAST(tanggal AS DATE) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    AND is_aktif = 'Y'
                    --  group by id, id_kas_bank, tanggal, jenis, keterangan_transaksi, kode_coa, debit, kredit,keterangan_kode_transaksi
                ) AS d 
                ORDER BY cast(tanggal as datetime) asc,id     
            ");

            $kas = DB::table('kas_bank')->find(2);
            $transaction = KasBankTransaction::where('is_aktif', 'Y')
                                            ->where('id_kas_bank', 2)
                                            // ->whereBetween('tanggal', [$tgl_default, $tgl_awal])
                                            ->where('tanggal','<', $tgl_awal)
                                            ->get();
            $sumKredit  = $transaction->sum('kredit');
            $sumDebit   = $transaction->sum('debit');
            $kasBank = DB::table('kas_bank')->where('is_aktif', 'Y')->orderBy('nama', 'asc')->get();
    
            return view('pages.laporan.Kas.index',[
                'judul' => "LAPORAN KAS",
                'data' => $data,
                'kas' => $kas,
                'request' => $request,
                'kasBank' => $kasBank,
                'sumKredit' => $sumKredit,
                'sumDebit' => $sumDebit,
            ]);
        }else{
            $kasBank = DB::table('kas_bank')->where('is_aktif', 'Y')->orderBy('nama', 'asc')->get();
            $data = NULL;
            $kas = NULL;
            return view('pages.laporan.Kas.index',[
                'judul' => "LAPORAN KAS",
                'data' => $data,
                'request' => $request,
                'kas' => $kas,
                'kasBank' => $kasBank,
                'sumKredit' => NULL,
                'sumDebit' => NULL,

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
