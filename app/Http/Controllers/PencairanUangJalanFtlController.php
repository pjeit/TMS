<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Http\Controllers\Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;

class PencairanUangJalanFtlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sewa = DB::table('sewa AS s')
                ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.jenis_tujuan', 'like', '%FTL%')
                ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                ->orderBy('c.id','ASC')
                ->get();

        return view('pages.finance.pembayaran_uang_jalan.index',[
            'judul' => "Pencairan Uang Jalan",
            'sewa'=>$sewa,
            'dataJO' => null,
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
    public function form(Request $request)
    {
        $id_sewa_default = $request->input('id_sewa');
        // dd($id_sewa_defaulth);
        // session(['id_sewa' => $id_sewa]);

        $sewa = DB::table('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                    ->where('s.is_aktif', '=', 'Y')
                    ->groupBy('c.id')
                    ->get();
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.finance.pembayaran_uang_jalan.form',[
            'judul' => "Pencairan Uang Jalan",
            'sewa'=>$sewa,
            'dataKas'=>$dataKas,
            'id_sewa_defaulth'=>$id_sewa_default,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try {
            // dump transaksi
            $dump = DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    now(),//tanggal
                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                    $data['total_diterima'], //uang keluar (kredit)
                    1016, //kode coa
                    'uang_jalan',
                    'UANG KELUAR - PEMBAYARAN UANG JALAN', //keterangan_transaksi
                    $data['select_sewa'],//keterangan_kode_transaksi
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                ) 
            );
            
            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();

            if(isset($kh)){
                $kh->total_hutang = $kh->total_hutang - isset($data['potong_hutang'])? (float)str_replace(',', '', $data['potong_hutang']):0; 
                $kh->updated_by = $user;
                $kh->updated_at = now();
                $kh->save();
            }

            $kht = new KaryawanHutangTransaction();
            $kht->id_karyawan = $data['id_karyawan'];
            $kht->id_sewa = $data['select_sewa'];
            $kht->total_uang_jalan = (float)str_replace(',', '', $data['uang_jalan']);
            $kht->potong_hutang = (float)str_replace(',', '', $data['potong_hutang']);
            $kht->total_diterima = (float)str_replace(',', '', $data['total_diterima']);
            $kht->kas_bank_id = $data['pembayaran'];
            $kht->catatan = $data['catatan'];
            $kht->created_by = $user;
            $kht->created_at = now();
            $kht->is_aktif = 'Y';
            $kht->save();

            $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($data['select_sewa']);
            $sewa->status = 'DALAM PERJALANAN';
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->save();

            $saldo = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->where('kas_bank.id', '=', $data['pembayaran'])
                ->first();
            $saldo_baru = $saldo->saldo_sekarang - (float)str_replace(',', '', $data['total_diterima']);
            DB::table('kas_bank')
                ->where('id', $data['pembayaran'])
                ->update(array(
                    'saldo_sekarang' => $saldo_baru,
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                )
            );

            return redirect()->route('pencairan_uang_jalan_ftl.index')->with('status', "Pembayaran berhasil");
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $sewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
