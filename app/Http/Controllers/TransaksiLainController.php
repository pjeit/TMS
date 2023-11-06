<?php

namespace App\Http\Controllers;

use App\Models\TransaksiLain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiLainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataKasLain= DB::table('kas_bank_lain as ksl')
            ->select('ksl.*','c.nama_jenis')
            ->leftJoin('coa as c', function($join) {
                    $join->on('ksl.coa_id', '=', 'c.id')->where('c.is_aktif', '=', "Y");
                })
            ->where('ksl.is_aktif', '=', "Y")
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();

         $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
         confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.transaksi_lain.index',[
             'judul'=>"Transaksi Lain",
            'dataKasLain' => $dataKasLain,
            'dataKas' => $dataKas,
            'dataCOA' => $dataCOA,
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
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function show(TransaksiLain $transaksiLain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function edit(TransaksiLain $transaksiLain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransaksiLain $transaksiLain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransaksiLain $transaksiLain)
    {
        //
    }
}
