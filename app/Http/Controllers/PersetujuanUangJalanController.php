<?php

namespace App\Http\Controllers;

use App\Models\UangJalanRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersetujuanUangJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sewa = DB::table('sewa AS s')
        ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
        ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
        ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
        ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
        ->where('s.is_aktif', '=', 'Y')
        ->where('s.jenis_tujuan', 'like', '%FTL%')
        ->whereNull('s.id_supplier')
        ->where('s.status',  "MENUNGGU PERSETUJUAN")
        ->orderBy('c.id','ASC')
        ->get();

        return view('pages.finance.persetujuan_uang_jalan.index',[
            'judul' => "Persetujuan Uang Jalan",
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function show(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function edit(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function destroy(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }
}
