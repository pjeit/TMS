<?php

namespace App\Http\Controllers;

use App\Models\CabangPJE;
use App\Models\Chassis;
use App\Models\Head;
use App\Models\MutasiKendaraan;
use App\Models\PairKendaraan;
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
    public function indexOld()
    {
        $dataKendaraan = DB::table('kendaraan AS k')
            ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
            ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
            })
            ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
            ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
            ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
            ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
            ->where('k.is_aktif', '=', 'Y') 
            ->orderBy('cp.nama', 'DESC')
            ->orderBy('kkm.nama', 'ASC')
            ->orderBy('k.no_polisi', 'ASC')
            ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
            ->get();
        
        $dataJenisFilter = DB::table('cabang_pje')
            ->where('is_aktif', '=', "Y")
            ->get();
            
        return view('pages.master.mutasi_kendaraan.index',[
            'judul' => "Mutasi Kendaraan",
            'dataKendaraan' => $dataKendaraan,
            'dataJenisFilter' => $dataJenisFilter

        ]);
    }

    public function index(Request $request)
    {
        $cabang_tujuan = $request->input('cabang_tujuan');
        $cabang_asal = $request->input('cabang_asal');

        $dataKendaraan = DB::table('mutasi_kendaraan AS mk')
            ->select('mk.*','ca.nama as cabangAsal','cb.nama as cabangBaru', 'k.id_kategori', 
                'k.no_polisi', DB::raw('CONCAT(c.kode, " - ", c.karoseri) AS chassis'),'kk.nama as kategori', 'mk.catatan', 'mk.created_at')
            ->leftJoin('kendaraan as k', function($join) {
                $join->on('k.id', '=', 'mk.kendaraan_id')->where('k.is_aktif', '=', 'Y');
            })
            ->leftJoin('chassis as c', function($join) {
                $join->on('c.id', '=', 'mk.chassis_id')->where('c.is_aktif', '=', 'Y');
            })
            ->leftJoin('kendaraan_kategori as kk', function($join) {
                $join->on('kk.id', '=', 'k.id_kategori')->where('kk.is_aktif', '=', 'Y');
            })
            ->where(function ($query) use($cabang_tujuan, $cabang_asal){
                if(isset($cabang_asal) && isset($cabang_tujuan)){
                    $query->where('cabang_lama', $cabang_asal)
                          ->Where('cabang_baru', $cabang_tujuan);
                }else if(isset($cabang_asal) && !isset($cabang_tujuan)){
                    $query->where('cabang_lama', $cabang_asal);
                }else if(!isset($cabang_asal) && isset($cabang_tujuan)){
                    $query->Where('cabang_baru', $cabang_tujuan);
                }
            })
            ->leftJoin('cabang_pje AS ca', 'mk.cabang_lama', '=', 'ca.id')
            ->leftJoin('cabang_pje AS cb', 'mk.cabang_baru', '=', 'cb.id')
            ->where('mk.is_aktif', 'Y') 
            ->get();

        $dataJenisFilter = DB::table('cabang_pje')
            ->where('is_aktif', '=', "Y")
            ->get();

        $request = $request->all();

        return view('pages.master.mutasi_kendaraan.index',[
            'judul' => "Mutasi Kendaraan",
            'dataKendaraan' => $dataKendaraan,
            'dataJenisFilter' => $dataJenisFilter,
            'request'=> $request, 
        ]);
    }

      public function filterMutasi(Request $request)
    {
        $jenisFilter = $request->input('jenisFilter');
       
        if($jenisFilter==null)
        {
              $dataKendaraan =  DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    // ->where('k.id_kategori', '=', 1) 
                    // ->where('k.cabang_id', '=', 2) 
                    // ->where('k.cabang_id', '=',  $jenisFilter) 
                     ->orderBy('cp.nama', 'DESC')
                    ->orderBy('kkm.nama', 'ASC')
                    ->orderBy('k.no_polisi', 'ASC')
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                    ->get();


        }
        else

        {
            $dataKendaraan =  DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    // ->where('k.id_kategori', '=', 1) 
                    // ->where('k.cabang_id', '=', 2) 
                    ->where('k.cabang_id', '=',  $jenisFilter) 
                     ->orderBy('cp.nama', 'DESC')
                    ->orderBy('kkm.nama', 'ASC')
                    ->orderBy('k.no_polisi', 'ASC')
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                    ->get();

        }

       
        $dataJenisFilter = DB::table('cabang_pje')
            ->where('is_aktif', '=', "Y")
            ->get();

            // dd($dataJenisFilter);
        // return response()->json(['datas' => $data]);

        return view('pages.master.mutasi_kendaraan.index',[
            'judul' => "Mutasi Kendaraan",
            'dataKendaraan' => $dataKendaraan,
            'dataJenisFilter' => $dataJenisFilter
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
                if(isset($value['centang_kendaraan']) && isset($value['centang_chassis'])){
                    // kendaraan + chassis pindah
                    $pair = PairKendaraan::where('is_aktif', 'Y')->where('kendaraan_id', $value['kendaraan'])->first();
                    if($pair){
                        $pair->cabang_id = $data['cabang_tujuan'];
                        $pair->updated_by = $user;
                        $pair->updated_at = now();
                        $pair->save();
                    }

                    $mut = new MutasiKendaraan();
                    $mut->kendaraan_id = isset($value['kendaraan'])? $value['kendaraan']:NULL;
                    $mut->chassis_id = isset($value['chassis'])? $value['chassis']:NULL;
                    $mut->cabang_lama = $data['cabang_asal'];
                    $mut->cabang_baru = $data['cabang_tujuan'];
                    $mut->jenis = 'KENDARAAN';
                    $mut->catatan = $data['catatan'];
                    $mut->created_by = $user;
                    $mut->created_at = now();
                    $mut->is_aktif = 'Y';
                    if($mut->save()){
                        $kendaraan = Head::where('is_aktif', 'Y')->where('id', $value['kendaraan'])->first();
                        if($kendaraan){
                            $kendaraan->cabang_id = $data['cabang_tujuan'];
                            $kendaraan->driver_id = NULL;
                            $kendaraan->updated_by = $user;
                            $kendaraan->updated_at = now();
                            $kendaraan->save();
                        }

                        $chassis = Chassis::where('is_aktif', 'Y')->where('id', $value['chassis'])->first();
                        if($chassis){
                            $chassis->cabang_id = $data['cabang_tujuan'];
                            $chassis->updated_by = $user;
                            $chassis->updated_at = now();
                            $chassis->save();
                        }
                    }
                }else if(isset($value['centang_kendaraan']) && !isset($value['centang_chassis'])){
                    // kalau cuma kendaraan aja 
                    $pair = PairKendaraan::where('is_aktif', 'Y')->where('kendaraan_id', $value['kendaraan'])->first();
                    if($pair){
                        $pair->updated_by = $user;
                        $pair->updated_at = now();
                        $pair->is_aktif = 'N';
                        $pair->save();
                    }

                    $mut = new MutasiKendaraan();
                    $mut->kendaraan_id = isset($value['kendaraan'])? $value['kendaraan']:NULL;
                    $mut->chassis_id = isset($value['chassis'])? $value['chassis']:NULL;
                    $mut->cabang_lama = $data['cabang_asal'];
                    $mut->cabang_baru = $data['cabang_tujuan'];
                    $mut->jenis = 'KENDARAAN';
                    $mut->catatan = $data['catatan'];
                    $mut->created_by = $user;
                    $mut->created_at = now();
                    $mut->is_aktif = 'Y';
                    if($mut->save()){
                        $kendaraan = Head::where('is_aktif', 'Y')->where('id', $value['kendaraan'])->first();
                        if($kendaraan){
                            $kendaraan->cabang_id = $data['cabang_tujuan'];
                            $kendaraan->driver_id = NULL;
                            $kendaraan->updated_by = $user;
                            $kendaraan->updated_at = now();
                            $kendaraan->save();
                        }

                        if(isset($value['chassis'])){
                            $chassis = Chassis::where('is_aktif', 'Y')->where('id', $value['chassis'])->first();
                            if($chassis){
                                $chassis->is_dipakai = 'N';
                                $chassis->updated_by = $user;
                                $chassis->updated_at = now();
                                $chassis->save();
                            }
                        }
                    }
                }else if(!isset($value['centang_kendaraan']) && isset($value['centang_chassis'])){
                    // kalau cuma chassis aja
                    $pair = PairKendaraan::where('is_aktif', 'Y')->where('chassis_id', $value['chassis'])->first();
                    if($pair){
                        $pair->is_aktif = 'N';
                        $pair->updated_by = $user;
                        $pair->updated_at = now();
                        $pair->save();
                    }
                    
                    $mut = new MutasiKendaraan();
                    $mut->kendaraan_id = isset($value['kendaraan'])? $value['kendaraan']:NULL;
                    $mut->chassis_id = isset($value['chassis'])? $value['chassis']:NULL;
                    $mut->cabang_lama = $data['cabang_asal'];
                    $mut->cabang_baru = $data['cabang_tujuan'];
                    $mut->jenis = 'CHASSIS';
                    $mut->catatan = $data['catatan'];
                    $mut->created_by = $user;
                    $mut->created_at = now();
                    $mut->is_aktif = 'Y';
                    if($mut->save()){
                        $chassis = Chassis::where('is_aktif', 'Y')->where('id',$value['chassis'])->first();
                        if($chassis){
                            $chassis->cabang_id = $data['cabang_tujuan'];
                            $chassis->is_dipakai = 'N';
                            $chassis->updated_by = $user;
                            $chassis->updated_at = now();
                            $chassis->save();
                        }
                    }
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
                ->select("k.*", 'kk.nama as kategori', 'cb.nama as cabang', 'pk.chassis_id as chassis_id', 'c.kode as kode', 'c.karoseri as karoseri')
                ->where('k.is_aktif', '=', "Y")
                ->where('k.cabang_id', '=', $id)
                ->leftJoin('kendaraan_kategori as kk','kk.id', '=', "k.id_kategori")
                ->leftJoin('cabang_pje as cb','cb.id', '=', "k.cabang_id")
                ->leftJoin('pair_kendaraan_chassis as pk', function ($join) {
                    $join->on('pk.kendaraan_id', '=', "k.id")
                    ->where('pk.is_aktif', 'Y');
                })
                ->leftJoin('chassis as c', 'c.id', '=', "pk.chassis_id")
                ->orderBy('k.id_kategori', 'ASC')
                ->orderByRaw('ISNULL(c.kode), c.kode ASC')
                ->orderBy('k.no_polisi', 'ASC')
                ->get();

            $dataChassis = DB::table('chassis as c')
                ->leftJoin('m_model_chassis as mc','mc.id', '=', "c.model_id")
                ->select('c.*', 'mc.nama as model')
                ->where('c.is_aktif', "Y")
                ->where('c.cabang_id', $id)
                ->where('c.is_dipakai', 'N')
                ->orderBy('c.kode', 'ASC')
                ->orderBy('c.karoseri', 'ASC')
                ->get();
                    
            return response()->json(["result" => "success",'dataKendaraan' => $dataKendaraan, 'dataChassis' => $dataChassis], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);
        }
    }
}
