<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Helper\SewaDataHelper;
class SewaRekananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
         return view('pages.order.truck_order_rekanan.create',[
            'judul'=>"Trucking Order Rekanan",
            'datajO'=>SewaDataHelper::DataJO(),
            'dataCustomer'=>SewaDataHelper::DataCustomer(),
            'dataBooking'=>SewaDataHelper::DataBooking(),
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
        //
         $user = Auth::user()->id; 
        
        try {
            $data = $request->collect();
            // dd($data);
            
            $romawi = VariableHelper::bulanKeRomawi(date("m"));

            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            $booking_id = $data['booking_id']; 

            $lastNoSewa = Sewa::where('is_aktif', 'Y')
                        ->where('no_sewa', 'like', '%'.date("Y").'/CUST/'.$romawi.'%')
                        ->orderBy('no_sewa', 'DESC')
                        ->first();
            if(isset($lastNoSewa)){
                $last3Chars = substr($lastNoSewa['no_sewa'], -3);
                // Convert it to an integer and increment by 1
                $last3CharsInt = (int)$last3Chars + 1;
                // Format the result with leading zeros (assuming a maximum of 3 digits)
                $newValue = sprintf("%03d", $last3CharsInt);
                // Replace the original last 3 characters with the new value
                $newString = preg_replace('/\d{3}$/', $newValue, $lastNoSewa['no_sewa']);
            }

            $no_sewa = isset($lastNoSewa) ? $newString : date("Y").'/CUST/'.$romawi.'/'.'001';

            
            $sewa = new Sewa();
            $sewa->no_sewa = $no_sewa;
            $sewa->id_booking = $booking_id;
            $sewa->id_jo = $data['id_jo'];
            $sewa->id_jo_detail = $data['id_jo_detail'];
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['tujuan_id'];
            $sewa->jenis_tujuan = $data['jenis_tujuan'];
            $sewa->status = 'MENUNGGU OPERASIONAL';
            $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->kargo = $data['kargo'];
            $sewa->jenis_order = $data['jenis_order']=='INBOUND'? 'INBOUND':'OUTBOND';
            $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
            $sewa->total_uang_jalan = $data['uang_jalan'];
            $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
            $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            $sewa->catatan = $data['catatan']? $data['catatan']:null;
            $sewa->is_kembali = 'N';
            $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
            $sewa->created_by = $user;
            $sewa->created_at = now();
            $sewa->is_aktif = 'Y';
            
            if($sewa->save()){

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

                // update kredit grup + customer
                    DB::table('customer')
                        ->where('id', $data['customer_id'])
                        ->update([
                            'kredit_sekarang' => (float)$customer->kredit_sekarang+$harga,
                            'updated_at' => now(),
                            'updated_by' => $user,
                    ]);

                    DB::table('grup')
                        ->where('id', $customer->grup_id)
                        ->update([
                            'total_kredit' => (float)$grup->total_kredit+$harga,
                            'updated_at' => now(),
                            'updated_by' => $user,
                    ]);
                ///
                
                if(isset($booking_id)){
                    DB::table('booking')
                        ->where('id', $booking_id)
                        ->update([
                            'is_sewa' => "Y", // berarti sudah masuk sewa
                            'updated_at' => now(),
                            'updated_by' => $user,
                    ]);
                }
    
                if(isset($data['select_jo']) && isset($data['id_jo_detail']))
                {
                    // DB::table('job_order')
                    //     ->where('id', $data['select_jo'])
                    //     ->update([
                    //         'status'=>'masih gatau',
                    //         'updated_at' => now(),
                    //         'updated_by' => $user,
                    //     ]);
                    DB::table('job_order_detail')
                        ->where('id', $data['id_jo_detail'])
                        ->update([
                            'id_kendaraan' => $data['kendaraan_id']? $data['kendaraan_id']:null,
                            'nopol_kendaraan' => $data['no_polisi']? $data['no_polisi']:null,
                            'tgl_dooring' => date_format($tgl_berangkat, 'Y-m-d'),
                            'status'=> 'DALAM PERJALANAN',
                            'updated_at' => now(),
                            'updated_by' => $user,
                        ]);
                    
                }
              
                $arrayBiaya = json_decode($data['biayaDetail'], true);
                
                // sama trip supir
                if( isset($arrayBiaya))
                {
                    foreach ($arrayBiaya as /*$key =>*/ $item) {
                        DB::table('sewa_biaya')
                            ->insert(array(
                            'id_sewa' => $sewa->id_sewa,
                            'deskripsi' => $item['deskripsi'] ,
                            'biaya' => $item['biaya'],
                            'catatan' => $item['catatan']?$item['catatan']:null,
                            'is_aktif' => "Y",
                            'created_at' => now(), 
                            'created_by' => $user,
                            'updated_at' => now(),
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
                    //             'created_at'=>now(), 
                    //             'created_by'=> $user,
                    //             'updated_at'=> now(),
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
                    //           'created_at'=>now(), 
                    //           'created_by'=> $user,
                    //           'updated_at'=> now(),
                    //           'updated_by'=> $user,
                    //           )
                    //       ); 
                    //   }
                    // }
                ///
            }

            return redirect()->route('truck_order.index')->with('status','Berhasil menambahkan data Sewa Rekanan');
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
