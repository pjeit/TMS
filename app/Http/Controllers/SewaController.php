<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Customer;
// use App\Models\GrupTujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use Buglinjo\LaravelWebp\Webp;
use App\Helper\SewaDataHelper;
class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('pages.order.truck_order.index',[
            'judul'=>"Trucking Order",
            'dataSewa' => SewaDataHelper::DataSewa(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.order.truck_order.create',[
            'judul'=>"Trucking Order",
            'datajO'=>SewaDataHelper::DataJO(),
            'dataCustomer'=>SewaDataHelper::DataCustomer(),
            'dataDriver'=>SewaDataHelper::DataDriver(),
            'dataKendaraan'=>SewaDataHelper::DataKendaraan(),
            'dataBooking'=>SewaDataHelper::DataBooking(),
            'dataChassis'=>SewaDataHelper::DataChassis()
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
        $user = Auth::user()->id; 
        
        try {
            $data = $request->collect();
            // dd($data);
       
            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            $dataBook = $data['select_booking']? explode("-",$data['select_booking']):null;
            
            $sewa = new Sewa();
            $sewa->id_booking = $dataBook? $dataBook[0]:null;
            $sewa->id_jo = $data['select_jo']?? null;
            $sewa->id_jo_detail = $data['select_jo_detail']?? null;
            $sewa->jenis_tujuan = $data['jenis_tujuan'];
            $sewa->status = 'MENUNGGU UANG JALAN';
            $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['tujuan_id'];
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->kargo = $data['kargo'];
            $sewa->jenis_order = $data['jenis_order']=='INBOUND'? 'INBOUND':'OUTBOND';
            $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
            $sewa->total_uang_jalan = $data['uang_jalan'];
            $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
            $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
            $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            $sewa->id_chassis = $data['ekor_id']? $data['ekor_id']:null;
            $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
            $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
            $sewa->catatan = $data['catatan']? $data['catatan']:null;
            $sewa->is_kembali = 'N';
            $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
            $sewa->created_by = $user;
            $sewa->created_at = now();
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->is_aktif = 'Y';

            if($sewa->save())
            {
                $customer = DB::table('customer as c')
                    ->select('c.*')
                    ->where('c.id', '=', $data['customer_id'])
                    ->where('c.is_aktif', '=', "Y")
                    ->first();
                $grup = DB::table('grup as g')
                    ->select('g.*')
                    ->where('g.id', '=', $customer->grup_id)
                    ->where('g.is_aktif', '=', "Y")
                    ->first();

                if ($data['jenis_tujuan'] === "LTL") {
                    $harga = (float)$data['harga_per_kg'] * $data['min_muatan'];
                } else {
                    $harga = (float)$data['tarif'];
                }

                DB::table('customer')
                    ->where('id', $data['customer_id'])
                    ->update([
                        'kredit_sekarang'=> (float)$customer->kredit_sekarang+$harga,
                        'updated_at' => VariableHelper::TanggalFormat(),
                        'updated_by' => $user,
                ]);

                DB::table('grup')
                    ->where('id', $customer->grup_id)
                    ->update([
                        'total_kredit'=>(float)$grup->total_kredit+$harga,
                        'updated_at' => VariableHelper::TanggalFormat(),
                        'updated_by' => $user,
                ]);
                
                if($dataBook){
                    DB::table('booking')
                        ->where('id', $dataBook[0])
                        ->update([
                            'updated_at' => VariableHelper::TanggalFormat(),
                            'updated_by' => $user,
                            'is_sewa' => "Y", // berarti sudah masuk sewa
                    ]);
                }
    
                if($data['select_jo'] && $data['select_jo_detail'])
                {
                    // DB::table('job_order')
                    //     ->where('id', $data['select_jo'])
                    //     ->update([
                    //         'status'=>'masih gatau',
                    //         'updated_at' => VariableHelper::TanggalFormat(),
                    //         'updated_by' => $user,
                    //     ]);
    
                    DB::table('job_order_detail')
                        ->where('id', $data['select_jo_detail'])
                        ->update([
                            'id_kendaraan' => $data['kendaraan_id']? $data['kendaraan_id']:null,
                            'nopol_kendaraan' => $data['no_polisi']? $data['no_polisi']:null,
                            'tgl_dooring' => date_format($tgl_berangkat, 'Y-m-d'),
                            'status'=> 'DALAM PERJALANAN',
                            'updated_at' => VariableHelper::TanggalFormat(),
                            'updated_by' => $user,
                        ]);
                    
                }
              
                $arrayBiaya = json_decode($data['biayaDetail'], true);
                
                // sama trip supir
                if( $arrayBiaya)
                {
                    foreach ($arrayBiaya as /*$key =>*/ $item) {
                        DB::table('sewa_biaya')
                            ->insert(array(
                            'id_sewa' => $sewa->id_sewa,
                            'deskripsi' => $item['deskripsi'] ,
                            'biaya' => $item['biaya'],
                            'catatan' => $item['catatan']?$item['catatan']:null,
                            'is_aktif' => "Y",
                            'created_at' => VariableHelper::TanggalFormat(), 
                            'created_by' => $user,
                            'updated_at' => VariableHelper::TanggalFormat(),
                            'updated_by' => $user,
                            )
                        ); 
                    }
                }
                ///
                    // $biayaTambahTarif = json_decode($data['biayaTambahTarif'], true);
                    // if($biayaTambahTarif)
                    // {
                    //       foreach ($biayaTambahTarif as /*$key =>*/ $item) {
                    //         DB::table('sewa_operasional')
                    //             ->insert(array(
                    //             'id_sewa'=>$idSewa,
                    //             'deskripsi' => $item['deskripsi'] ,
                    //             'total_operasional' => $item['biaya'],
                    //             'is_ditagihkan' => null,
                    //             'is_dipisahkan' => null,
                    //             'catatan' => null,
                    //             'is_aktif' => "Y",
                    //             'created_at'=>VariableHelper::TanggalFormat(), 
                    //             'created_by'=> $user,
                    //             'updated_at'=> VariableHelper::TanggalFormat(),
                    //             'updated_by'=> $user,
                    //             )
                    //         ); 
                    
                    //     }
                    // }
                    // $biayaTambahSDT = json_decode($data['biayaTambahSDT'], true);
                    // if($biayaTambahSDT)
                    // {
                    //     foreach ($biayaTambahSDT as /*$key =>*/ $item) {
                    //       DB::table('sewa_operasional')
                    //           ->insert(array(
                    //           'id_sewa'=>$idSewa,
                    //           'deskripsi' => $item['deskripsi'] ,
                    //           'total_operasional' => $item['biaya'],
                    //           'is_ditagihkan' => null,
                    //           'is_dipisahkan' => null,
                    //           'catatan' => null,
                    //           'is_aktif' => "Y",
                    //           'created_at'=>VariableHelper::TanggalFormat(), 
                    //           'created_by'=> $user,
                    //           'updated_at'=> VariableHelper::TanggalFormat(),
                    //           'updated_by'=> $user,
                    //           )
                    //       ); 
                    //   }
                    // }
                ///
            }

            return redirect()->route('truck_order.index')->with('status','Berhasil menambahkan data Sewa');
        } catch (ValidationException $e) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();

            // return response()->json(['errorsCatch' => $e->errors()], 422);
        }
       catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();

            // return response()->json(['errorServer' => $ex->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $sewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
