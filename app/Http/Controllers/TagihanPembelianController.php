<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = TagihanPembelian::from('tagihan_pembelian as tp')
                    ->where('tp.is_aktif', 'Y')
                    ->get();
        // dd($data);
        return view('pages.finance.tagihan_pembelian.index',[
            'judul' => "Tagihan Pembelian",
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dataKas = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->get();

        $supplier = Supplier::where(['is_aktif' => 'Y'])->get();

        return view('pages.finance.tagihan_pembelian.create',[
        'judul' => "Tagihan Pembelian",
        'dataKas' => $dataKas,
        'supplier' => $supplier,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->collect();
        dd($data);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
