<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSistem;
use App\Models\M_Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;

class PengaturanSistemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataPengaturanSistem = PengaturanSistem::where('id', 1)->first();

        $dataMKas = DB::table('m_kas')->get();
        $judul = 'Pengaturan Sistem';
        return view('pages.master.pengaturan_sistem.index',[
            'dataPengaturanSistem' => $dataPengaturanSistem,
            'dataMKas' => $dataMKas,
            'judul' => $judul,
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
       
     
      
        // DB::table('pengaturan_sistem')
        //     ->insert(array(
        //         'uang_jajan' => $data['uang_jalan'],
        //         'reimburse' => $data['reimburse'],
        //         'penerimaan_customer' => $data['penerimaan_customer'],
        //         'pembayaran_supplier' => $data['pembayaran_supplier'],
        //         'pembayaran_gaji' => $data['pembayaran_gaji'],
        //         'hutang_karyawan' => $data['hutang_karyawan'],
        //         'klaim_supir' => $data['klaim_supir'],
        //         'batas_pemutihan' => $data['batas_pemutihan'],
        //         'updated_at'=> date("Y-m-d h:i:s"),
        //         'updated_by'=> 1,// masih hardcode nanti diganti cookies
        //     )
        // ); 
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
        $data = $request->collect();
        $update = PengaturanSistem::where('id', $id)->first();
        $update->uang_jajan = $data['uang_jalan'];
        $update->reimburse = $data['reimburse'];
        $update->penerimaan_customer = $data['penerimaan_customer'];
        $update->pembayaran_supplier = $data['pembayaran_supplier'];
        $update->pembayaran_gaji = $data['pembayaran_gaji'];
        $update->hutang_karyawan = $data['hutang_karyawan'];
        $update->klaim_supir = $data['klaim_supir'];
        $update->batas_pemutihan = $data['batas_pemutihan'];
        $update->hutang_karyawan = $data['hutang_karyawan'];
        $update->updated_at = date("Y-m-d h:i:s");
        $update->updated_by = 1;

        $update->save();

        return redirect()->route('pengaturan_sistem.index')->with('status','Berhasil update data');

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
