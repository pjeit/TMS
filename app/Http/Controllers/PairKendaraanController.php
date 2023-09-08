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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $dataPair = DB::table('kendaraan AS k')
                    ->select('k.id', 'k.no_polisi', 'kkm.nama as kategoriKendaraan','kt.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                    ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                        $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                    ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                    ->leftJoin('m_kota AS kt', 'k.kota_id', '=', 'kt.id')
                    ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where('k.is_aktif', '=', 'Y') 
                    ->where('k.id_kategori', '=', 1) 
                    ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','kt.nama')
                    ->get();
                    // ->get(10);

        // dd($dataPair);
        return view('pages.master.pair_kendaraan.index', [
            'judul' => "Pair Truck",
            'dataPair' => $dataPair,
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
        $dataPair = DB::table('kendaraan AS k')
                ->select('k.*','pk.*','c.*','m.*' )
                ->leftJoin('pair_kendaraan_chassis AS pk', 'k.id', '=', 'pk.kendaraan_id')
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                ->where('k.id', $id) 
                ->get();
        $dataKendaraan =  DB::table('kendaraan')
                ->select('kendaraan.*')
                ->where('kendaraan.is_aktif', '=','Y') 
                ->where('kendaraan.id', $id) 
                ->get();
        $dataChassis = DB::table('chassis')
                ->select('chassis.*','m_model_chassis.nama as namaModel')
                ->join('m_model_chassis','chassis.model_id','m_model_chassis.id')
                ->where('chassis.is_aktif', '=','Y') 
                ->get();
        $dataPaired = DB::table('pair_kendaraan_chassis')
                ->select('pair_kendaraan_chassis.*')
                ->where('pair_kendaraan_chassis.kendaraan_id', $id) 
                ->where('pair_kendaraan_chassis.is_aktif', '=','Y') 
                ->get();
        $dataDriver = DB::table('karyawan')
                ->where('role_id', '5') // 5=driver 
                ->where('is_aktif', '=','Y') 
                ->get();
        // dd($dataPaired);
        return view('pages.master.pair_kendaraan.edit', [
            'judul' => "Pair Truck",
            'dataPair' => $dataPair,
            'dataKendaraan'=>$dataKendaraan,
            'dataChassis'=>$dataChassis,
            'dataPaired'=>$dataPaired,
            'dataDriver'=>$dataDriver,
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
         $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {
            // $pesanKustom = [
            //     'chasis.*.required' => 'Semua Kolom Chasis harus diisi!',
            // ];
            
            // $request->validate([
            //     'chasis.*' => 'required',
      
            // ], $pesanKustom);
            $data = $request->collect();
            if(!empty($data['idPairedNya']))
            {
                for ($i = 0; $i < count($data['idPairedNya']); $i++) {
                    // semisal id pairednya 
                    //  "idPairedNya" => array:3 [▼
                    //     0 => "4"
                    //     1 => "5"
                    //     2 => "6"
                    //     ] taruh [$i] supaya sesuai array
                    if($data['idPairedNya'][$i])
                    {
                        if($data['isAktif'][$i])
                        {
                                DB::table('pair_kendaraan_chassis')
                                ->where('id', $data['idPairedNya'][$i])
                                ->where('kendaraan_id', $idKendaraan)
                                ->update(array(
                                        'chassis_id' => $data['chasis'][$i],
                                        'updated_at'=> VariableHelper::TanggalFormat(),
                                        'updated_by'=> $user,
                                        'is_aktif'=>$data['isAktif'][$i]
    
                                    )
                                );
                        }
                        DB::table('pair_kendaraan_chassis')
                        ->where('id', $data['idPairedNya'][$i])
                        ->where('kendaraan_id', $idKendaraan)
                        ->update(array(
                                'chassis_id' => $data['chasis'][$i],
                                'updated_at'=> VariableHelper::TanggalFormat(),
                                'updated_by'=> $user,
                            )
                        );
                    }
                    else
                    {
                        DB::table('pair_kendaraan_chassis')->insert(
                        array(
                            'kendaraan_id' => $idKendaraan,
                            'chassis_id' => $data['chasis'][$i],
                            'created_at'=> VariableHelper::TanggalFormat(),
                            'created_by'=> $user,
                            'updated_at'=> VariableHelper::TanggalFormat(),
                            'updated_by'=> $user,
                            'is_aktif'=>'Y'
                        )
                    );
                    }
                    // $cobaPrint.=$data['idPairedNya'][$i];
                }
                
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
