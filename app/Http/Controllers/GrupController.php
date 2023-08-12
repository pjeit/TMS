<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\GrupTujuan;
use App\Models\GrupTujuanBiaya;
use Symfony\Component\VarDumper\VarDumper;

class GrupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('grup')
            ->where('is_aktif', '=', "Y")
            ->get();
            
        return view('pages.master.grup.index',[
                'judul' => "Grup",
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
        return view('pages.master.grup.create',[
            'judul' => "Grup",
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
                'nama_grup.required' => 'Nama Grup Harus diisi!',
                'nama_pic.required' => 'Nama PIC Harus diisi!',
                'email.required' => 'Email Harus diisi!',
                'telp1.required' => 'Telp 1 Harus diisi!',
                'total_max_kredit.required' => 'Total Max Kredit Harus diisi!',
            ];
            
            $request->validate([
                'nama_grup' => 'required',
                'nama_pic' => 'required',
                'email' => 'required',
                'telp1' => 'required',
                'total_max_kredit' => 'required',
            ], $pesanKustom);
          
            $total_kredit = floatval(str_replace(',', '', $request['total_kredit']));
            $total_max_kredit = floatval(str_replace(',', '', $request['total_max_kredit']));

            $user = 1;
            $grup = new Grup();
            $grup->nama_grup = $request->nama_grup;
            $grup->nama_pic = $request->nama_pic;
            $grup->email = $request->email;
            $grup->telp1 = $request->telp1;
            $grup->telp2 = $request->telp2;
            $grup->total_kredit = $total_kredit;
            $grup->total_max_kredit = $total_max_kredit;

            $grup->created_at = now();
            $grup->created_by = $user; // manual
            $grup->is_aktif = "Y";

            $data = $request->post();
            $data_tujuan = json_decode($data['tujuan'], true);

            if($grup->save()){
                // input tujuan
                foreach ($data_tujuan as $key => $tujuan) {
                    $harga_per_kg = ($tujuan['harga_per_kg'] != '-')? floatval(str_replace(',', '', $tujuan['harga_per_kg'])):0;
                    $uang_jalan = ($tujuan['uang_jalan'] != '-')? floatval(str_replace(',', '', $tujuan['uang_jalan'])):0;
                    $tarif = ($tujuan['tarif'] != '-')? floatval(str_replace(',', '', $tujuan['tarif'])):0;
                    $komisi = ($tujuan['komisi'] != '')? floatval(str_replace(',', '', $tujuan['komisi'])):0;
                    $min_muatan = ($tujuan['min_muatan'] != '-')? $tujuan['min_muatan']:0;

                    $grup_tujuan = GrupTujuan::create([
                        'grup_id' => $grup->id,
                        'nama_tujuan' => $tujuan['nama'],
                        'alamat' => $tujuan['alamat'],
                        'jenis_tujuan' => $tujuan['jenis_tujuan'],
                        'min_muatan' => $min_muatan,
                        'harga_per_kg' => $harga_per_kg,
                        'uang_jalan' => $uang_jalan,
                        'tarif' => $tarif,
                        'komisi' => $komisi,
                        'catatan' => $tujuan['catatan'],
                        'is_aktif' => 'Y',
                        'created_by' => $user,
                        'created_at' => now(),
                    ]);

                    if ($grup_tujuan) {
                        $data_biaya = json_decode($tujuan['detail_uang_jalan'], true);

                        foreach ($data_biaya as $biaya) {
                            $biaya_amount = floatval(str_replace(',', '', $biaya['biaya']));
                
                            $grup_tujuan_biaya = GrupTujuanBiaya::create([
                                'grup_id' => $grup->id,
                                'grup_tujuan_id' => $grup_tujuan->id,
                                'biaya' => $biaya_amount,
                                'deskripsi' => $biaya['deskripsi'],
                                'catatan' => $biaya['catatan'],
                                'is_aktif' => 'Y',
                                'created_by' => $user,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }

                return response()->json(['message' => 'Grup berhasil dibuat', 'id' => $grup->id]);
            }else{
                return response()->json(['message' => 'Gagal menyimpan grup'], 500);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function show(Grup $grup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function edit(Grup $grup)
    {
        $data['grup'] = Grup::where('is_aktif', 'Y')->findOrFail($grup->id);
        $tujuan = GrupTujuan::where('grup_id', $grup->id)->where('is_aktif', 'Y')->get();

        foreach ($tujuan as $key => $value) {
            $biaya = GrupTujuanBiaya::where('grup_id', $data['grup']['id'])
                                ->where('is_aktif', 'Y')
                                ->where('grup_tujuan_id', $value->id)
                                ->get();
            
            $data_tujuan[$key]=(object)array(
                'id'=>$value->id,
                'grup_id'=>$value->grup_id,
                'nama_tujuan'=>$value->nama_tujuan,
                'alamat'=>$value->alamat,
                'jenis_tujuan'=>$value->jenis_tujuan,
                'harga_per_kg'=>$value->harga_per_kg,
                'min_muatan'=>$value->min_muatan,
                'uang_jalan'=>$value->uang_jalan,
                'tarif'=>$value->tarif,
                'komisi'=>$value->komisi,
                'catatan'=>$value->catatan,
                'detail_uang_jalan'=>json_encode($biaya),
                );
        }
        return view('pages.master.grup.edit',[
            'judul' => "Grup",
            'data' => $data,
            'data_tujuan' => !empty($data_tujuan)? $data_tujuan:null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request, Grup $grup)
        {
            try {
                $pesanKustom = [
                    'nama_grup.required' => 'Nama Grup Harus diisi!',
                    'nama_pic.required' => 'Nama PIC Harus diisi!',
                    'email.required' => 'Email Harus diisi!',
                    'telp1.required' => 'Telp 1 Harus diisi!',
                    'total_max_kredit.required' => 'Total Max Kredit Harus diisi!',
                ];
                
                $request->validate([
                    'nama_grup' => 'required',
                    'nama_pic' => 'required',
                    'email' => 'required',
                    'telp1' => 'required',
                    'total_max_kredit' => 'required',
                ], $pesanKustom);
        
                $data = $request->collect();
                $user = 1;
                    
                $total_kredit = ($data['total_kredit'] != '-')? floatval(str_replace(',', '', $data['total_kredit'])):NULL;
                $total_max_kredit = ($data['total_max_kredit'] != '-')? floatval(str_replace(',', '', $data['total_max_kredit'])):NULL;

                $edit_grup = Grup::where('is_aktif', 'Y')->findOrFail($grup->id);
                $edit_grup->nama_grup = $data['nama_grup'];
                $edit_grup->total_kredit = $total_kredit;
                $edit_grup->total_max_kredit = $total_max_kredit;
                $edit_grup->nama_pic = $data['nama_pic'];
                $edit_grup->email = $data['email'];
                $edit_grup->telp1 = $data['telp1'];
                $edit_grup->telp2 = $data['telp2'];
                $edit_grup->updated_at = now();
                $edit_grup->updated_by = $user;

                if($edit_grup->save()){
                    // hapus dulu data yg di hapus
                    $deleted_grup_id = explode(',', $request->post()['deleted_grup_id']);

                    // nested delete grup
                    $delete_grup = GrupTujuan::where('grup_id', $grup->id)
                                ->whereIn('id', ($deleted_grup_id))
                                ->update(
                                    [
                                        'is_aktif' => 'N',
                                        'updated_by' => $user,
                                    ]);

                    if($delete_grup){   
                        $delete_grup = GrupTujuanBiaya::where('grup_id', $grup->id)
                                ->whereIn('grup_tujuan_id', ($deleted_grup_id))
                                ->update(
                                    [
                                        'is_aktif' => 'N',
                                        'updated_by' => $user,
                                    ]);
                    }

                    // kalau sudah hapus2, lanjut
                    $data_tujuan = json_decode($request->post()['tujuan'], true);
                    foreach ($data_tujuan as $key => $tujuan) {

                        if($tujuan['tujuan_id'] != ''){
                            # kalau ada id, di update
                            $edit_tujuan = GrupTujuan::where('grup_id', $grup->id)
                                                    ->where('id', $tujuan['tujuan_id'])
                                                    ->where('is_aktif', 'Y')
                                                    ->first(); // Menggunakan first() untuk objek tunggal
                            if($edit_tujuan){ 
                                // kalau ada datanya lanjut edit
                                $harga_per_kg = ($tujuan['harga_per_kg'] != '-')? floatval(str_replace(',', '', $tujuan['harga_per_kg'])):NULL;
                                $uang_jalan = ($tujuan['uang_jalan'] != '-')? floatval(str_replace(',', '', $tujuan['uang_jalan'])):NULL;
                                $tarif = ($tujuan['tarif'] != '-')? floatval(str_replace(',', '', $tujuan['tarif'])):NULL;
                                $komisi = ($tujuan['komisi'] != '')? floatval(str_replace(',', '', $tujuan['komisi'])):NULL;
                                $min_muatan = ($tujuan['min_muatan'] != '-')? $tujuan['min_muatan']:NULL;

                                $edit_tujuan->nama_tujuan = $tujuan['nama'];
                                $edit_tujuan->alamat = $tujuan['alamat'];
                                $edit_tujuan->jenis_tujuan = $tujuan['jenis_tujuan'];
                                $edit_tujuan->catatan = $tujuan['catatan'];
                                $edit_tujuan->harga_per_kg = $harga_per_kg;
                                $edit_tujuan->min_muatan = $min_muatan;
                                $edit_tujuan->tarif = $tarif;
                                $edit_tujuan->uang_jalan = $uang_jalan;
                                $edit_tujuan->komisi = $komisi;

                                $edit_tujuan->is_aktif = 'Y';
                                $edit_tujuan->updated_by = $user; // nanti di edit
                                $edit_tujuan->updated_at = now(); 
                                
                                if($edit_tujuan->save()){
                                    #kalau disimpan, input detail biaya
                                    $data_biaya = json_decode($tujuan['detail_uang_jalan'], true);
                                    if($data_biaya){

                                        $id_disimpan = [];
                                        foreach ($data_biaya as $key => $biaya) {

                                            if(isset($biaya['id'])){
                                                //ketika data langsung di save
                                                $id_disimpan[] = $biaya['id'];
                                                $edit_biaya = GrupTujuanBiaya::where('grup_id', $grup->id)
                                                                        ->where('grup_tujuan_id', $edit_tujuan['id'])
                                                                        ->where('id', $biaya['id'])
                                                                        ->where('is_aktif', 'Y')
                                                                        ->first(); // Menggunakan first() untuk objek tunggal
                                                                        
                                                if($edit_biaya){
                                                    $biaya_amount = floatval(str_replace(',', '', $biaya['biaya']));

                                                    $edit_biaya->deskripsi = $biaya['deskripsi'];
                                                    $edit_biaya->biaya = $biaya_amount;
                                                    $edit_biaya->catatan = $biaya['catatan'];
                                                    $edit_biaya->updated_by = $user;
                                                    $edit_biaya->updated_at = now();
                                                    $edit_biaya->save();
                                                }

                                                
                                            }else{

                                                if(isset($biaya['biaya_id']) && $biaya['biaya_id'] != ''){
                                                    $id_disimpan[] = $biaya['biaya_id'];
                                                    $edit_biaya = GrupTujuanBiaya::where('grup_id', $grup->id)
                                                                            ->where('grup_tujuan_id', $edit_tujuan['id'])
                                                                            ->where('id', $biaya['biaya_id'])
                                                                            ->where('is_aktif', 'Y')
                                                                            ->first(); // Menggunakan first() untuk objek tunggal
                                                                            
                                                    if($edit_biaya){
                                                        $biaya_amount = floatval(str_replace(',', '', $biaya['biaya']));

                                                        $edit_biaya->deskripsi = $biaya['deskripsi'];
                                                        $edit_biaya->biaya = $biaya_amount;
                                                        $edit_biaya->catatan = $biaya['catatan'];
                                                        $edit_biaya->updated_by = $user;
                                                        $edit_biaya->updated_at = now();
                                                        $edit_biaya->save();
                                                    }
                                                }else{
                                                    $biaya_amount = floatval(str_replace(',', '', $biaya['biaya']));
                                                    $new_biaya = new GrupTujuanBiaya();
                                                    $new_biaya->grup_id = $grup->id;
                                                    $new_biaya->grup_tujuan_id = $tujuan['tujuan_id'];
                                                    $new_biaya->deskripsi = $biaya['deskripsi'];
                                                    $new_biaya->biaya = $biaya_amount;
                                                    $new_biaya->catatan = $biaya['catatan'];
                                                    $new_biaya->created_by = $user;
                                                    $new_biaya->created_at = now();
                                                    $new_biaya->save();

                                                    $id_disimpan[] = $new_biaya->id;
                                                }
                                            }
                                        }
                                        // hapus data
                                        
                                        if($id_disimpan){   
                                            $delete_biaya = GrupTujuanBiaya::where('grup_id', $grup->id)
                                                ->where('grup_tujuan_id', $edit_tujuan['id'])
                                                ->whereNotIn('id', $id_disimpan)
                                                ->update([
                                                    'is_aktif' => 'N',
                                                    'updated_by' => $user
                                                ]);
                                        }
                                    }
                                }
                            }
                        }else{
                            # kalau ga ada id, di create baru
                            $harga_per_kg = ($tujuan['harga_per_kg'] != '-')? floatval(str_replace(',', '', $tujuan['harga_per_kg'])):NULL;
                            $uang_jalan = ($tujuan['uang_jalan'] != '-')? floatval(str_replace(',', '', $tujuan['uang_jalan'])):NULL;
                            $tarif = ($tujuan['tarif'] != '-')? floatval(str_replace(',', '', $tujuan['tarif'])):NULL;
                            $komisi = ($tujuan['komisi'] != '')? floatval(str_replace(',', '', $tujuan['komisi'])):NULL;
                            $min_muatan = ($tujuan['min_muatan'] != '-')? $tujuan['min_muatan']:NULL;

                            $tujuan_baru = new GrupTujuan();
                            $tujuan_baru->grup_id = $grup->id;
                            $tujuan_baru->nama_tujuan = $tujuan['nama'];
                            $tujuan_baru->alamat = $tujuan['alamat'];
                            $tujuan_baru->jenis_tujuan = $tujuan['jenis_tujuan'];
                            $tujuan_baru->catatan = $tujuan['catatan'];
                            $tujuan_baru->harga_per_kg = $harga_per_kg;
                            $tujuan_baru->min_muatan = $min_muatan;
                            $tujuan_baru->tarif = $tarif;
                            $tujuan_baru->uang_jalan = $uang_jalan;
                            $tujuan_baru->komisi = $komisi;

                            $tujuan_baru->is_aktif = 'Y';
                            $tujuan_baru->updated_by = $user;
                            $tujuan_baru->updated_at = now(); // Menggunakan now() untuk waktu saat ini
                            $tujuan_baru->save();
                        }
                    }    
                }
                
                return response()->json(['message' => 'Grup berhasil dirubah', 'id' => $grup->id]);
            } catch (ValidationException $e) {
                return response()->json(['message' => 'Terjadi kesalahan', 'id' => $grup->id]);
                // return redirect()->back()->withErrors($e->errors())->withInput();
            }
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grup $grup)
    {
        $user = 1; // masih hardcode nanti diganti cookies

        $del_grup = DB::table('grup')
                    ->where('id', $grup['id'])
                    ->update(array(
                        'is_aktif' => "N",
                        'updated_by'=> $user, 
                        'updated_at'=> now(),
                    ));
        if($del_grup){

            $del_grup_tuj = DB::table('grup_tujuan')->where('grup_id', $grup['id'])
                            ->update(array(
                                'is_aktif' => "N",
                                'updated_by'=> $user, 
                                'updated_at'=> now(),
                            ));

                if($del_grup_tuj){
                    $del_grup_tuj_biy = DB::table('grup_tujuan_biaya')->where('grup_id', $grup['id'])
                                        ->update(array(
                                            'is_aktif' => "N",
                                            'updated_by'=> $user, 
                                            'updated_at'=> now(),
                                        ));
                }
        }
       
        return redirect()->route('grup.index')->with('status', 'Berhasil menghapus data!');
    }
}
