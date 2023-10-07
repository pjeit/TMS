<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKeuangan;
use App\Models\M_Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;
use Illuminate\Support\Facades\Auth;

class PengaturanKeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PengaturanKeuangan::where('id', 1)->first();

        $dataMKas = DB::table('m_kas')->get();
        $judul = 'Pengaturan Keuangan';
        return view('pages.master.pengaturan_keuangan.index',[
            'data' => $data,
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
        // DB::table('pengaturan_keuangan')
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
        $user=Auth::user()->id;
        $data = $request->collect();
        // dd($data);
        $update = PengaturanKeuangan::where('id', $id)->first();
        $update->uang_jajan = $data['uang_jalan'];
        $update->reimburse = $data['reimburse'];
        $update->penerimaan_customer = $data['penerimaan_customer'];
        $update->pembayaran_supplier = $data['pembayaran_supplier'];
        $update->pembayaran_gaji = $data['pembayaran_gaji'];
        $update->hutang_karyawan = $data['hutang_karyawan'];
        $update->klaim_supir = $data['klaim_supir'];
        $update->batas_pemutihan = $data['batas_pemutihan'];
        $update->hutang_karyawan = $data['hutang_karyawan'];
        $update->seal_pje = floatval(str_replace(',', '', $data['seal_pje']));
        $update->seal_pelayaran = floatval(str_replace(',', '', $data['seal_pelayaran']));
        $update->tally = floatval(str_replace(',', '', $data['tally']));
        $update->plastik = floatval(str_replace(',', '', $data['plastik']));
        $update->doc_fee = floatval(str_replace(',', '', $data['doc_fee']));
        $update->thc_20ft_luar = floatval(str_replace(',', '', $data['thc_20ft_luar']));
        $update->thc_20ft_dalam = floatval(str_replace(',', '', $data['thc_20ft_dalam']));
        $update->lolo_20ft_luar = floatval(str_replace(',', '', $data['lolo_20ft_luar']));
        $update->lolo_20ft_dalam = floatval(str_replace(',', '', $data['lolo_20ft_dalam']));
        $update->apbs_20ft = floatval(str_replace(',', '', $data['apbs_20ft']));
        $update->cleaning_20ft = floatval(str_replace(',', '', $data['cleaning_20ft']));
        $update->thc_40ft_luar = floatval(str_replace(',', '', $data['thc_40ft_luar']));
        $update->thc_40ft_dalam = floatval(str_replace(',', '', $data['thc_40ft_dalam']));
        $update->lolo_40ft_luar = floatval(str_replace(',', '', $data['lolo_40ft_luar']));
        $update->lolo_40ft_dalam = floatval(str_replace(',', '', $data['lolo_40ft_dalam']));
        $update->apbs_40ft = floatval(str_replace(',', '', $data['apbs_40ft']));
        $update->cleaning_40ft = floatval(str_replace(',', '', $data['cleaning_40ft']));
        $update->tl_perak = floatval(str_replace(',', '', $data['tl_perak']));
        $update->tl_priuk = floatval(str_replace(',', '', $data['tl_priuk']));
        $update->tl_teluk_lamong = floatval(str_replace(',', '', $data['tl_teluk_lamong']));
        $update->updated_at = date("Y-m-d h:i:s");
        $update->updated_by = $user;

        $update->save();

        return redirect()->route('pengaturan_keuangan.index')->with(['status' => 'Success', 'msg' => 'Data berhasil disimpan!']);
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
