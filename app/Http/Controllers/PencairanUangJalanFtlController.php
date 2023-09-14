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
                        // ->groupBy('c.id')
                        ->orderBy('c.id','ASC')
                        ->get();
            //   dd($sewa);
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
            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    now(),//tanggal
                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                    $data['total_diterima'], //uang keluar (kredit)
                    1016, //kode coa
                    'UANG_JALAN',
                    'UANG KELUAR - BAYAR UANG JALAN', //keterangan_transaksi
                    $data['select_sewa'],//keterangan_kode_transaksi
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                ) 
            );
            dd($data);
        } catch (ValidationException $error) {
            //throw $th;
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
