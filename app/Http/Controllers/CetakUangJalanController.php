<?php

namespace App\Http\Controllers;

use App\Models\UangJalanRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CetakUangJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataKlaimSupir = DB::table('klaim_supir as ks')
            ->select('ks.*','ks.id as id_klaim','k.nama_panggilan as nama_supir','k.telp1 as telp')
            ->leftJoin('karyawan as k', function($join) {
                    $join->on('ks.karyawan_id', '=', 'k.id')->where('k.is_aktif', '=', "Y");
                })
            ->where('ks.is_aktif', '=', "Y")
            // ->where('ks.status_klaim','like',"%PENDING%")
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        return view('pages.finance.cetak_uang_jalan.index',[
                'judul'=>"cetak uang jalan",
            // 'dataKlaimSupir' => $dataKlaimSupir,


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
