<?php

namespace App\Http\Controllers;

use App\Models\KlaimSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class KlaimSupirController extends Controller
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
            ->select('*')
            ->leftJoin('karyawan as k', function($join) {
                    $join->on('ks.karyawan_id', '=', 'k.id')->where('k.is_aktif', '=', "Y");
                })
            ->where('ks.is_aktif', '=', "Y")
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        return view('pages.finance.klaim_supir.index',[
                'judul'=>"Klaim Supir",
            'dataKlaimSupir' => $dataKlaimSupir,
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
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function show(KlaimSupir $klaimSupir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function edit(KlaimSupir $klaimSupir)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KlaimSupir $klaimSupir)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function destroy(KlaimSupir $klaimSupir)
    {
        //
    }
}
