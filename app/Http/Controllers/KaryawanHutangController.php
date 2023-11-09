<?php

namespace App\Http\Controllers;

use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
class KaryawanHutangController extends Controller
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
        $dataKaryawanHutang = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.nama as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('role as r', function($join) {
                $join->on('k.role_id', '=', 'r.id')->where('r.is_aktif', '=', "Y");
            })
            ->where('k.is_aktif',  "Y")
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        // dd( $dataKaryawanHutang);
        return view('pages.hrd.karyawan_hutang.index',[
            'judul'=>"Karyawan Hutang",
            'dataKaryawanHutang' => $dataKaryawanHutang,
            'dataKas' => $dataKas,
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
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function show(KaryawanHutang $karyawanHutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function edit(Karyawan $karyawan_hutang)
    {
        //
         $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataKaryawanHutang = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.nama as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('role as r', function($join) {
                $join->on('k.role_id', '=', 'r.id')->where('r.is_aktif', '=', "Y");
            })
            ->where('k.is_aktif',"Y")
            ->where('k.id',$karyawan_hutang->id)
            ->first();
        // dd($dataKaryawanHutang);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        $dataDetailHutang = DB::table('karyawan_hutang_transaction as kht')
        ->select('kht.*', 
        'kht.id as id_kht', 
        'kb.nama as nama_bank', 
        'kh.total_hutang as totalnya',
        DB::raw('kh.total_hutang + IF(kht.debit > 0, -1 * kht.debit, kht.kredit) as total_hutang'),
        DB::raw('if(kht.debit > 0, kht.debit, kht.kredit) as nominal')
        )
        ->leftJoin('kas_bank as kb', function ($join) {
            $join->on('kht.kas_bank_id', '=', 'kb.id')->where('kb.is_aktif', '=', "Y");
        })
        // ->leftJoin('karyawan_hutang as kh', 'kht.id_karyawan', '=', 'kh.id_karyawan')
        ->leftJoin('karyawan_hutang as kh', function ($join) {
            $join->on('kht.id_karyawan', '=', 'kh.id_karyawan')
            ->where('kh.is_aktif', '=', "Y")
            // ->where('kht.id_karyawan', '=', 'kh.id_karyawan')
            ;
        })
        ->where('kht.is_aktif', '=', "Y")
        ->where('kht.id_karyawan',$karyawan_hutang->id)
        ->get();
        // dd($dataDetailHutang);
        return view('pages.hrd.karyawan_hutang.detail',[
            'judul'=>"Karyawan Hutang",
            'dataKaryawanHutang' => $dataKaryawanHutang,
            'dataKas' => $dataKas,
            'dataDetailHutang' => $dataDetailHutang,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KaryawanHutang $karyawanHutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(KaryawanHutang $karyawanHutang)
    {
        //
    }
}
