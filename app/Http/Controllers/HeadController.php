<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\KasBank;
use App\Models\HeadDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class HeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('kendaraan')
            ->select('*')
            ->where('is_hapus', '=', "N")
            ->get();

            return view('pages.master.head.index',[
            'judul'=>"Head",
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
        return view('pages.master.head.create',[
            'judul'=>"Head",
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
        try {
            $pesanKustom = [
                'no_polisi.required' => 'Nomor Polisi Harus diisi!',
            ];
            
            $request->validate([
                'no_polisi' => 'required',
            ], $pesanKustom);
    
            $head = new Head();
            $head->no_polisi = $request->no_polisi;
            $head->no_mesin = $request->no_mesin;
            $head->no_rangka = $request->no_rangka;
            $head->merk_model = $request->merk_model;
            $head->tahun_pembuatan = $request->tahun_pembuatan;
            $head->warna = $request->warna;
            $head->driver_id = $request->driver_id;
            $head->supplier_id = $request->supplier_id;
            $head->created_at = date("Y-m-d h:i:s");
            $head->created_by = 1; // manual
            $head->updated_at = date("Y-m-d h:i:s");
            $head->updated_by = 1; // manual
            $head->is_hapus = "N";
            $head->save();

            // if(isset($data['kendaraan_dokumen'])){
                // isi dokumen kendaraan
            // }
            return redirect()->route('head.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
    public function update(Request $request, Head $head)
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
