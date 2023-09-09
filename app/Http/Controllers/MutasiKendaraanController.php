<?php

namespace App\Http\Controllers;

use App\Models\CabangPJE;
use App\Models\Chassis;
use App\Models\Head;
use App\Models\MutasiKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class MutasiKendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('cabang_pje')
            ->where('is_aktif', '=', "Y")
            ->get();

        $dataKendaraan = DB::table('kendaraan as k')
            ->where('k.is_aktif', '=', "Y")
            ->select("k.*", 'kk.nama as kategori', 'cb.nama as cabang')
            ->leftJoin('kendaraan_kategori as kk','kk.id', '=', "k.id_kategori")
            ->leftJoin('cabang_pje as cb','cb.id', '=', "k.cabang_id")
            ->orderBy('cb.nama', 'DESC')
            ->orderBy('kk.nama', 'ASC')
            ->orderBy('k.no_polisi', 'ASC')
            ->get();
        
        return view('pages.master.mutasi_kendaraan.index',[
            'judul' => "Mutasi Kendaraan",
            'data' => $data,
            'dataKendaraan' => $dataKendaraan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cabang = CabangPJE::get();
        $dataKendaraan = DB::table('kendaraan as k')
            ->where('k.is_aktif', '=', "Y")
            ->select("k.*", 'kk.nama as kategori', 'cb.nama as cabang', 'pk.chassis_id as chassis_id', 'c.kode as kode', 'c.karoseri as karoseri')
            ->leftJoin('kendaraan_kategori as kk','kk.id', '=', "k.id_kategori")
            ->leftJoin('cabang_pje as cb','cb.id', '=', "k.cabang_id")
            ->leftJoin('pair_kendaraan_chassis as pk', 'pk.kendaraan_id', '=', "k.id")
            ->leftJoin('chassis as c', 'c.id', '=', "pk.chassis_id")
            ->orderBy('k.id_kategori', 'ASC')
            ->orderBy('k.no_polisi', 'ASC')
            ->get();

        return view('pages.master.mutasi_kendaraan.create',[
            'judul' => "Mutasi Kendaraan",
            'cabang' => $cabang,
            'dataKendaraan' => $dataKendaraan,
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
        $data = $request->post();
        $user = Auth::user()->id;
        try {
            foreach ($data['data'] as $key => $value) {
                if(isset($value['centang_kendaraan'])){
                    $mut = new MutasiKendaraan();
                    $mut->asset_id = $value['kendaraan'];
                    $mut->cabang_id = $data['cabang_tujuan'];
                    $mut->jenis = 'KENDARAAN';
                    $mut->catatan = $data['catatan'];
                    $mut->created_by = $user;
                    $mut->created_at = now();
                    $mut->is_aktif = 'Y';
                    if($mut->save()){
                        $kendaraan = Head::where('is_aktif', 'Y')->find($value['kendaraan']);
                        $kendaraan->cabang_id = $data['cabang_tujuan'];
                        $kendaraan->updated_by = $user;
                        $kendaraan->updated_at = now();
                        $kendaraan->save();
                    }
                }

                if(isset($value['centang_chassis'])){
                    $chassis = Chassis::where('is_aktif', 'Y')->find($value['chassis']);
                    $chassis->cabang_id = $data['cabang_tujuan'];
                    $chassis->updated_by = $user;
                    $chassis->updated_at = now();
                    $chassis->save();
                }
             }
            return redirect()->route('mutasi_kendaraan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MutasiKendaraan  $mutasiKendaraan
     * @return \Illuminate\Http\Response
     */
    public function show(MutasiKendaraan $mutasiKendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MutasiKendaraan  $mutasiKendaraan
     * @return \Illuminate\Http\Response
     */
    public function edit(MutasiKendaraan $mutasiKendaraan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MutasiKendaraan  $mutasiKendaraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MutasiKendaraan $mutasiKendaraan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MutasiKendaraan  $mutasiKendaraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(MutasiKendaraan $mutasiKendaraan)
    {
        //
    }

    public function get_data($id){
        try {
            $dataKendaraan = DB::table('kendaraan as k')
                ->where('k.is_aktif', '=', "Y")
                ->where('k.cabang_id', '=', $id)
                ->select("k.*", 'kk.nama as kategori', 'cb.nama as cabang', 'pk.chassis_id as chassis_id', 'c.kode as kode', 'c.karoseri as karoseri')
                ->leftJoin('kendaraan_kategori as kk','kk.id', '=', "k.id_kategori")
                ->leftJoin('cabang_pje as cb','cb.id', '=', "k.cabang_id")
                ->leftJoin('pair_kendaraan_chassis as pk', 'pk.kendaraan_id', '=', "k.id")
                ->leftJoin('chassis as c', 'c.id', '=', "pk.chassis_id")
                ->orderBy('k.id_kategori', 'ASC')
                ->orderBy('k.no_polisi', 'ASC')
                ->get();

            return response()->json(["result" => "success",'data' => $dataKendaraan], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);
        }
    }
}
