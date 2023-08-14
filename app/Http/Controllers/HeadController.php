<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use App\Models\Head;
use App\Models\KasBank;
use App\Models\HeadDokumen;
use App\Models\Supplier;
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
            ->where('is_aktif', '=', "Y")
            ->select('*')
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
        $d_chassis = Chassis::leftJoin('m_model_chassis as x', 'chassis.model_id', '=', 'x.id')
                            ->where('chassis.is_aktif', 'Y')
                            ->select('chassis.*', 'x.nama')
                            ->get();

        $d_supplier = Supplier::where('is_aktif', 'Y')->get();

        return view('pages.master.head.create',[
            'judul' => "Head",
            'd_chassis' => $d_chassis,
            'd_supplier' => $d_supplier,
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
            $user = 1;

            $head = new Head();
            $head->no_polisi = $request->no_polisi;
            $head->no_mesin = $request->no_mesin;
            $head->no_rangka = $request->no_rangka;
            $head->merk_model = $request->merk_model;
            $head->tahun_pembuatan = $request->tahun_pembuatan;
            $head->warna = $request->warna;
            $head->driver_id = $request->driver_id;
            $head->chassis_id = $request->chassis_id;
            $head->supplier_id = $request->supplier_id;
            $head->created_by = $user; // manual
            $head->created_at = date("Y-m-d h:i:s");
            $head->is_aktif = "Y";

            if($head->save()){
                if( $request->post()['dokumen'] != '[]'){
                    // isi dokumen kendaraan
                    foreach (json_decode($request->post()['dokumen']) as $key => $docs) {
                        $dokumen = new HeadDokumen();
                        $dokumen->kendaraan_id = $head->id;
                        $dokumen->jenis = $docs->jenis;
                        $dokumen->nomor = $docs->nomor;
                        $dokumen->berlaku_hingga = date("Y-m-d", strtotime($docs->berlaku_hingga));
                        $dokumen->is_reminder = $docs->is_reminder;
                        $dokumen->created_by = $user;
                        $dokumen->created_at = now();
                        $dokumen->is_aktif = 'Y';
                        $dokumen->save();
                    }
                }

            }

            return response()->json(['message' => 'Head berhasil dibuat', 'id' => $head->id]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Terjadi kesalahan']);
            // return redirect()->back()->withErrors($e->errors())->withInput();
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
    public function edit(Head $head)
    {
        $data = Head::where('is_aktif', 'Y')->findOrFail($head->id);

        $d_chassis = Chassis::leftJoin('m_model_chassis as x', 'chassis.model_id', '=', 'x.id')
                ->where('chassis.is_aktif', 'Y')
                ->select('chassis.*', 'x.nama')
                ->get();

        $d_supplier = Supplier::where('is_aktif', 'Y')->get();
        
        $data_berkas = HeadDokumen::where('kendaraan_id', $data['id'])
                ->where('is_aktif', 'Y')
                ->get();

        $data['berkas'] = json_encode($data_berkas);
        // var_dump( $data['berkas']); die;

        return view('pages.master.head.edit',[
            'judul' => "Head",
            'data' => $data,
            'd_chassis' => $d_chassis,
            'd_supplier' => $d_supplier,
        ]);
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
        try {
            $pesanKustom = [
                'no_polisi.required' => 'Nomor Polisi Harus diisi!',
            ];
            
            $request->validate([
                'no_polisi' => 'required',
            ], $pesanKustom);
            $user = 1;

            $edit_head = Head::where('is_aktif', 'Y')->findOrFail($head->id);
            $edit_head->no_polisi = $request->no_polisi;
            $edit_head->no_mesin = $request->no_mesin;
            $edit_head->no_rangka = $request->no_rangka;
            $edit_head->merk_model = $request->merk_model;
            $edit_head->tahun_pembuatan = $request->tahun_pembuatan;
            $edit_head->warna = $request->warna;
            $edit_head->chassis_id = $request->chassis_id;
            $edit_head->driver_id = $request->driver_id;
            $edit_head->supplier_id = $request->supplier_id;
            $edit_head->updated_at = now();
            $edit_head->updated_by = $user;

            if($edit_head->save()){
                $docs = json_decode($request->post()['dokumen']);
                if( $docs != '[]'){
                    $id_disimpan = [];

                    foreach ($docs as $key => $dokumen) {

                        if($dokumen->dokumen_id == ''){                            
                            $new_dok = new HeadDokumen();
                            $new_dok->kendaraan_id = $head->id;
                            $new_dok->jenis = $dokumen->jenis;
                            $new_dok->nomor = $dokumen->nomor;
                            $new_dok->berlaku_hingga = date("Y-m-d", strtotime($dokumen->berlaku_hingga));
                            $new_dok->is_reminder = $dokumen->is_reminder;
                            if($dokumen->is_reminder == 'Y'){
                                $new_dok->reminder_hari = ($dokumen->reminder_hari != "")? $dokumen->reminder_hari:NULL;
                            }else{
                                $new_dok->reminder_hari = NULL;
                            }
                            $new_dok->created_by = $user;
                            $new_dok->created_at = now();
                            $new_dok->is_aktif = 'Y';
                            $new_dok->save();

                            $id_disimpan[] = $new_dok->id;
                        }else{
                            $edit_dok = HeadDokumen::where('id', $dokumen->dokumen_id)
                                    ->where('kendaraan_id', $head->id)
                                    ->where('is_aktif', 'Y')
                                    ->first(); // Menggunakan first() untuk objek tunggal
                            $edit_dok->jenis = $dokumen->jenis;
                            $edit_dok->nomor = $dokumen->nomor;
                            $edit_dok->berlaku_hingga = date("Y-m-d", strtotime($dokumen->berlaku_hingga));
                            $edit_dok->is_reminder = $dokumen->is_reminder;
                            if($dokumen->is_reminder == 'Y'){
                                $edit_dok->reminder_hari = ($dokumen->reminder_hari != "")? $dokumen->reminder_hari:NULL;
                            }else{
                                $edit_dok->reminder_hari = NULL;
                            }
                            $edit_dok->updated_by = $user;
                            $edit_dok->updated_at = now();
                            $edit_dok->is_aktif = 'Y';
                            $edit_dok->save();

                            $id_disimpan[] = $dokumen->dokumen_id;
                        }
                    }
                    
                    $delete_dok = HeadDokumen::where('kendaraan_id', $head->id)
                        ->whereNotIn('id', $id_disimpan)
                        ->update([
                            'is_aktif' => 'N',
                            'updated_by' => $user
                        ]);
                    
                }
            }

            return response()->json(['message' => 'Head berhasil dibuat', 'id' => $head->id]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Terjadi kesalahan']);
            // return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = 1;
        $del_head = Head::where('id', $id)
            ->update([
                'is_aktif' => 'N',
                'updated_by' => $user
            ]);
        if($del_head){
            $del_doc = HeadDokumen::where('kendaraan_id', $id)
                ->update([
                    'is_aktif' => 'N',
                    'updated_by' => $user
                ]);
        }

        return redirect()->route('head.index')->with('status', 'Berhasil menghapus data!');
    }
}
