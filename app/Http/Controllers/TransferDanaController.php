<?php

namespace App\Http\Controllers;

use App\Models\TransferDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferDanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataKasTransfer= DB::table('kas_bank_transfer as kst')
            ->select('kst.*')
            ->where('kst.is_aktif', '=', "Y")
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
         confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.transfer_dana.index',[
             'judul'=>"Transfer dana",
            'dataKasTransfer' => $dataKasTransfer,
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
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function show(TransferDana $transferDana)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferDana $transferDana)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransferDana $transferDana)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferDana $transferDana)
    {
        //
    }
}
