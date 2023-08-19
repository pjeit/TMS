<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use App\Models\GrupMember;
use App\Models\GrupTujuan;
use App\Models\GrupTujuanBiaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class GrupTujuanController extends Controller
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

        return view('pages.master.grup_tujuan.index',[
                'judul' => "Grup Tujuan",
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
        $grups = DB::table('grup')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.master.grup_tujuan.create',[
            'judul' => "Grup",
            'grups' => $grups,
        ]);
    }

    public function getMarketing($groupId)
    {
        $marketingList = GrupMember::where('grup_id', $groupId)->where('role_id', 3)->get();
        return response()->json($marketingList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd('xxx');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GrupTujuan  $grupTujuan
     * @return \Illuminate\Http\Response
     */
    public function show(GrupTujuan $grupTujuan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GrupTujuan  $grupTujuan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Grup::where('is_aktif', 'Y')->findOrFail($id);
        $tujuan = GrupTujuan::where('grup_id', $id)->where('is_aktif', 'Y')->get();
        $item['fields'][NULL] = [];

        foreach ($tujuan as $key => $value) {
            $biaya = GrupTujuanBiaya::where('grup_id', $id)
                                ->where('is_aktif', 'Y')
                                ->where('grup_tujuan_id', $value->id)
                                ->get();
            
            $data['biaya'][$key]=(object)array(
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
        var_dump($data); die;

        return view('pages.master.grup_tujuan.edit',[
            'judul' => "Grup",
            'data' => $data,
            'id' => $id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GrupTujuan  $grupTujuan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $data = $request->post()['data'];
            $user = Auth::user()->id;

            foreach ($data as $key => $value) {
                if($value['id_tujuan'] == null){
                    $tarif = ($value['tarif'] != '')? floatval(str_replace(',', '', $value['tarif'])):0;
                    $komisi = ($value['komisi'] != '')? floatval(str_replace(',', '', $value['komisi'])):0;

                    $new_tuj = new GrupTujuan();
                    $new_tuj->grup_id = $value['grup_hidden'];
                    $new_tuj->nama_tujuan = $value['nama_tujuan'];
                    $new_tuj->alamat = $value['alamat_hidden'];
                    $new_tuj->jenis_tujuan = $value['jenis_tujuan'];
                    $new_tuj->harga_per_kg = $value['harga_per_kg_hidden'];
                    $new_tuj->min_muatan = $value['min_muatan_hidden'];
                    $new_tuj->tarif = $tarif;
                    $new_tuj->komisi = $komisi;
                    $new_tuj->catatan = $value['catatan'];
                    $new_tuj->created_by = $user;
                    $new_tuj->created_at = now();

                    if($new_tuj->save()){
                        $data_biaya = json_decode($value['obj_biaya'], true);
                        foreach ($data_biaya as $key => $item) {
                            $biaya = ($item['biaya'] != '')? floatval(str_replace(',', '', $item['biaya'])):0;

                            $new_biaya = new GrupTujuanBiaya();
                            $new_biaya->grup_id = $value['grup_hidden'];
                            $new_biaya->grup_tujuan_id = $new_tuj->id;
                            $new_biaya->biaya = $biaya;
                            $new_biaya->deskripsi = $item['deskripsi_biaya'];
                            $new_biaya->catatan = $item['catatan_biaya'];
                            $new_tuj->created_by = $user;
                            $new_tuj->created_at = now();
                            $new_biaya->save();
                        }
                    }
                }
            }

            return redirect()->route('grup_tujuan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->route('grup_tujuan.index')->with('status','Gagal!!');
        }
   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GrupTujuan  $grupTujuan
     * @return \Illuminate\Http\Response
     */
    public function destroy(GrupTujuan $grupTujuan)
    {
        //
    }
}
