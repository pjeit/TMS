<?php

namespace App\Http\Controllers;

use App\Models\PairKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
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
            ->select('k.id', 'k.no_polisi', DB::raw('GROUP_CONCAT(c.kode SEPARATOR ", ") AS chassis'), DB::raw('GROUP_CONCAT(m.nama SEPARATOR ", ") AS "model_chassis"'))
            ->leftJoin('pair_kendaraan_chassis AS pk', 'k.id', '=', 'pk.kendaraan_id')
            ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
            ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
            ->where('k.is_aktif', '=','Y') 
            ->groupBy('k.id', 'k.no_polisi')
            // ->paginate(5);
            ->get();

        return view('pages.master.pair_kendaraan.index', [
            'judul' => "Pair Kendaraan",
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
        //
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
        // dd($dataKendaraan[0]->id);
        return view('pages.master.pair_kendaraan.edit', [
            'judul' => "Pair Kendaraan",
            'dataPair' => $dataPair,
            'dataKendaraan'=>$dataKendaraan,
            'dataChassis'=>$dataChassis,
            'dataPaired'=>$dataPaired
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
             
            //     'nama.required' => 'Nama kas Harus diisi!',
            //     // 'no_akun.required' => 'Nomor kas akun Harus diisi!',
            //     'tipe.required' =>'Tipe kas harap dipilih salah satu!',
            //     'saldo_awal.required' => 'Saldo awal Harus diisi!',
            //     'tgl_saldo.required' => 'Tanngal saldo awal Harus diisi!',
      
            // ];
            
            // $request->validate([
            //     'nama' => 'required',
            //     // 'no_akun' => 'required',
            //     'tipe' =>'required|in:1,2',
            //     'saldo_awal' => 'required',
            //     'tgl_saldo' => 'required'
            //     // 'catatan' => 'required',
            // ], $pesanKustom);
    
            $data = $request->collect();
            // dd($data['idPairedNya']);
            // dd($idKendaraan);

            // $cobaPrint="";
            
            for ($i = 0; $i < count($data['idPairedNya']); $i++) {

                if($data['idPairedNya'][$i])
                {
                    DB::table('pair_kendaraan_chassis')
                    ->where('id', $data['idPairedNya'])
                    ->where('kendaraan_id', $idKendaraan)
                    ->update(array(
                            'chassis_id' => $data['chassis_id'][$i],
                            'updated_at'=> VariableHelper::TanggalFormat()[$i],
                            'updated_by'=> $user[$i],
                        )
                    );

                }
                else
                {

                }
                // $cobaPrint.=$data['idPairedNya'][$i];

                // DB::table('purchase_request_detail')->insert(
                //     array(
                //         // 'idPurchaseRequest' => $purchaseRequest->id,
                //         'jumlah' => $data['itemTotal'][$i],
                //         'ItemID' => $data['itemId'][$i],
                //         'harga' => $data['itemHarga'][$i],
                //         'keterangan_jasa' => $data['itemKeterangan'][$i],
                //     )
                // );
            }
            // dd($cobaPrint);

          
        
            return redirect()->route('pair_kendaraan.index')->with('status','Sukses mengupdate data kas!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
