<?php

namespace App\Http\Controllers;

use App\Models\PairKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Exception;
class PairKendaraanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_PAIR_KENDARAAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_PAIR_KENDARAAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_PAIR_KENDARAAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_PAIR_KENDARAAN', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        //
         $dataPair = DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    ->where('k.id_kategori', '=', 1) 
                     ->orderBy('cp.nama', 'DESC')
                    ->orderBy('kkm.nama', 'ASC')
                    ->orderBy('k.no_polisi', 'ASC')
                    // ->where('k.cabang_id', '=', 2) 
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                    ->get();
                    // ->get(10);

        // dd($dataPair);
         $dataJenisFilter = DB::table('cabang_pje')
            ->select('*')
            ->where('cabang_pje.is_aktif', '=', "Y")
            ->get();
        return view('pages.master.pair_kendaraan.index', [
            'judul' => "Pair Truck",
            'dataPair' => $dataPair,
            'dataJenisFilter' => $dataJenisFilter

        ]);
    }

     public function filterTruck(Request $request)
    {
        $jenisFilter = $request->input('jenisFilter');
        // dd($jenisFilter);
      
        if($jenisFilter==null)
        {
              $dataPair = DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    ->where('k.id_kategori', '=', 1) 
                     ->orderBy('cp.nama', 'DESC')
                    ->orderBy('kkm.nama', 'ASC')
                    ->orderBy('k.no_polisi', 'ASC')
                    // ->where('k.cabang_id', '=',  $jenisFilter) 
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                    ->get();


        }
        else

        {
           $dataPair = DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    ->where('k.id_kategori', '=', 1) 
                    ->where('k.cabang_id', '=',  $jenisFilter) 
                     ->orderBy('cp.nama', 'DESC')
                    ->orderBy('kkm.nama', 'ASC')
                    ->orderBy('k.no_polisi', 'ASC')
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                    ->get();

        }
        $dataJenisFilter = DB::table('cabang_pje')
            ->select('*')
            ->where('cabang_pje.is_aktif', '=', "Y")
            ->get();

            // dd($dataJenisFilter);
        // return response()->json(['datas' => $data]);

        return view('pages.master.pair_kendaraan.index',[
            'judul' => "Supplier",
            'dataPair' => $dataPair,
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
     * @param  \App\Models\PairKendaraan  $pairKendaraan
     * @return \Illuminate\Http\Response
     */
    public function show(PairKendaraan $pairKendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PairKendaraan  $pairKendaraan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $dataPair = DB::table('kendaraan AS k')
        //         ->select('k.*','pk.*','c.*','m.*' )
        //         ->leftJoin('pair_kendaraan_chassis AS pk', 'k.id', '=', 'pk.kendaraan_id')
        //         ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
        //         ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
        //         ->where('k.id', $id) 
        //         ->where('pk.is_aktif', '=','Y') 
        //         ->get();
        $dataKendaraan =  DB::table('kendaraan')
                ->select('kendaraan.*')
                ->where('kendaraan.is_aktif', '=','Y') 
                ->where('kendaraan.id', $id) 
                ->first();
        $dataChassis = DB::table('chassis')
                ->select('chassis.*','m_model_chassis.nama as namaModel')
                ->join('m_model_chassis','chassis.model_id','m_model_chassis.id')
                ->where('chassis.is_aktif', '=','Y') 
                ->where('chassis.is_dipakai', '=','N') 
                ->where('chassis.cabang_id', '=',$dataKendaraan->cabang_id) 
                ->get();
  
        // $dataChassis = DB::table('chassis')
        //               ->select('chassis.*', 'm_model_chassis.nama as namaModel')
        //                 ->join('m_model_chassis', 'chassis.model_id', 'm_model_chassis.id')
        //                 ->leftJoin('pair_kendaraan_chassis', function ($join) use ($id) {
        //                     $join->on('chassis.id', '=', 'pair_kendaraan_chassis.chassis_id')
        //                         ->where('pair_kendaraan_chassis.kendaraan_id', '=', $id)
        //                         ->where('pair_kendaraan_chassis.is_aktif', '=', 'Y');
        //                 })
        //                 ->where('chassis.is_aktif', '=', 'Y')
        //                 ->where('chassis.cabang_id', '=', $dataKendaraan->cabang_id)
        //                 ->get();
        $dataPaired = DB::table('pair_kendaraan_chassis')
                ->select('pair_kendaraan_chassis.*', 'c.kode as kode', 'c.karoseri as karoseri')
                ->where('pair_kendaraan_chassis.kendaraan_id', $id) 
                ->leftJoin('chassis as c', 'c.id', '=', 'pair_kendaraan_chassis.chassis_id')
                ->where('pair_kendaraan_chassis.is_aktif', '=','Y') 
                ->first();
        $dataDriver = DB::table('karyawan')
                ->where('role_id', VariableHelper::Role_id('Driver')) // 5=driver 
                ->where('is_aktif', '=','Y') 
                ->get();
        $dataCabang = DB::table('cabang_pje')
                ->where('is_aktif', '=','Y') 
                ->where('id', '=',$dataKendaraan->cabang_id) 
                ->first();
        // dd($dataPaired);
        return view('pages.master.pair_kendaraan.edit', [
            'judul' => "Pair Truck",
            // 'dataPair' => $dataPair,
            'dataKendaraan'=>$dataKendaraan,
            'dataChassis'=>$dataChassis,
            'dataPaired'=>$dataPaired,
            'dataDriver'=>$dataDriver,
            'dataCabang'=>$dataCabang
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PairKendaraan  $pairKendaraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idKendaraan)
    {
        //
         $user = Auth::user()->id; 
        // $dataKendaraan =  DB::table('kendaraan')
        //                 ->select('kendaraan.*')
        //                 ->where('kendaraan.is_aktif', '=','Y') 
        //                 ->where('kendaraan.id', $idKendaraan) 
        //                 ->first();
        
        try {
            // $pesanKustom = [
            //     'driver.required' => 'Driver harus di pilih!',
            // ];
            
            // $request->validate([
            //     'driver' => 'required',
            // ], $pesanKustom);
            $data = $request->collect();
            // var_dump($data['idPairedNya']);die;
            if($data['idPairedNya'])
            {
                //  var_dump('masuk if idpaired');die;

                if($data['chasis']==null/*[$i]*/)
                {
                //    var_dump($data);die;
                        DB::table('pair_kendaraan_chassis')
                        ->where('id', $data['idPairedNya']/*[$i]*/)
                        ->where('kendaraan_id', $idKendaraan)
                        ->update(array(
                                'updated_at'=> VariableHelper::TanggalFormat(),
                                'updated_by'=> $user,
                                'is_aktif'=>'N'/*[$i]*/

                            )
                        );
                        DB::table('chassis')
                        ->where('id', $data['idChassis']/*[$i]*/)
                        // ->where('kendaraan_id', $idKendaraan)
                        ->update(array(
                                'is_dipakai' => 'N'/*[$i]*/,
                                'updated_at'=> VariableHelper::TanggalFormat(),
                                'updated_by'=> $user,
                            )
                        );
                        if($data['idDriver'] != $data['driver'])
                        {
                            DB::table('kendaraan')
                            ->where('id', $idKendaraan/*[$i]*/)
                            // ->where('kendaraan_id', $idKendaraan)
                            ->update(array(
                                    'driver_id' => $data['driver']/*[$i]*/,
                                    'updated_at'=> VariableHelper::TanggalFormat(),
                                    'updated_by'=> $user,
                                )
                            );

                        }
                     
                }
                else
                {
                    if($data['chasis'] != $data['idChassis'])
                    {
                        DB::table('chassis')
                            ->where('id', $data['idChassis']/*[$i]*/)
                            // ->where('kendaraan_id', $idKendaraan)
                            ->update(array(
                                    'is_dipakai' => 'N'/*[$i]*/,
                                    'updated_at'=> VariableHelper::TanggalFormat(),
                                    'updated_by'=> $user,
                                )
                            );
                         DB::table('chassis')
                            ->where('id', $data['chasis']/*[$i]*/)
                            // ->where('kendaraan_id', $idKendaraan)
                            ->update(array(
                                    'is_dipakai' => 'Y'/*[$i]*/,
                                    'updated_at'=> VariableHelper::TanggalFormat(),
                                    'updated_by'=> $user,
                                )
                            );
                    }

                    DB::table('chassis')
                            ->where('id', $data['chasis']/*[$i]*/)
                            // ->where('kendaraan_id', $idKendaraan)
                            ->update(array(
                                    'is_dipakai' => 'Y'/*[$i]*/,
                                    'updated_at'=> VariableHelper::TanggalFormat(),
                                    'updated_by'=> $user,
                                )
                            );

                    DB::table('pair_kendaraan_chassis')
                    ->where('id', $data['idPairedNya']/*[$i]*/)
                    ->where('kendaraan_id', $idKendaraan)
                    ->update(array(
                            'driver_id' => $data['driver']/*[$i]*/,
                            'cabang_id' => $data['cabang']/*[$i]*/,
                            'chassis_id' => $data['chasis']/*[$i]*/,
                            'updated_at'=> VariableHelper::TanggalFormat(),
                            'updated_by'=> $user,
                            // 'is_aktif'=>$data['isAktif']/*[$i]*/

                        )
                    );
             
                    if($data['idDriver']  != $data['driver'])
                    {
                        DB::table('kendaraan')
                        ->where('id', $idKendaraan/*[$i]*/)
                        ->update(array(
                                'driver_id' => $data['driver']/*[$i]*/,
                                'updated_at'=> VariableHelper::TanggalFormat(),
                                'updated_by'=> $user,
                            )
                        );

                    }
                }
            }
            else
            {
            // var_dump('masuk else luar');die;
             if($data['chasis']==null/*[$i]*/)
                {
                    return response()->json(['errorPertamaChassis' => 'harap isi chassis apabila pertama kali pairing'], 422);

                }

                DB::table('pair_kendaraan_chassis')->insert(
                    array(
                        'kendaraan_id' => $idKendaraan,
                        'driver_id' => $data['driver']/*[$i]*/,
                        'cabang_id' => $data['cabang']/*[$i]*/,
                        'chassis_id' => $data['chasis']/*[$i]*/,
                        'created_at'=> VariableHelper::TanggalFormat(),
                        'created_by'=> $user,
                        'updated_at'=> VariableHelper::TanggalFormat(),
                        'updated_by'=> $user,
                        'is_aktif'=>'Y'
                    )
                );
                 DB::table('chassis')
                    ->where('id', $data['chasis']/*[$i]*/)
                    // ->where('kendaraan_id', $idKendaraan)
                    ->update(array(
                            'is_dipakai' => 'Y'/*[$i]*/,
                            'updated_at'=> VariableHelper::TanggalFormat(),
                            'updated_by'=> $user,
                        )
                    );

                if( $data['driver'])
                    {
                        DB::table('kendaraan')
                        ->where('id', $idKendaraan/*[$i]*/)
                        ->update(array(
                                'driver_id' => $data['driver']/*[$i]*/,
                                'updated_at'=> VariableHelper::TanggalFormat(),
                                'updated_by'=> $user,
                            )
                        );

                    }
              
                // if($dataChassis->id == $data['chasis'])
                // {
                //     DB::table('chassis')
                //     ->where('id', $data['chasis']/*[$i]*/)
                //     // ->where('kendaraan_id', $idKendaraan)
                //     ->update(array(
                //             'is_dipakai' => 'Y'/*[$i]*/,
                //             'updated_at'=> VariableHelper::TanggalFormat(),
                //             'updated_by'=> $user,
                //         )
                //     );

                // }

            }
     

          
            return response()->json(['message' => 'Sukses mengupdate data truck pairing', 'id' => $idKendaraan]);
        
            // return redirect()->route('pair_kendaraan.index')->with('status','Sukses mengupdate data truck pairing!');
        } /*catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }*/
         catch (ValidationException $e) {
            // cancel input db
            DB::rollBack();
          
            return response()->json(['errorsCatch' => $e->errors()], 422);
        } catch (Exception $ex) {
            // cancel input db
            DB::rollBack();

            return response()->json(['errorServer' => $ex->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PairKendaraan  $pairKendaraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(PairKendaraan $pairKendaraan)
    {
        //
    }
}
