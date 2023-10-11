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
        $dataSewa = DB::table('sewa as s')
        ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap')
        ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
        ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
        ->where('gt.is_aktif', '=', "Y")
        ->where('s.is_aktif', '=', "Y")
        ->whereNotNull('s.id_supplier')
        ->where('s.status', 'MENUNGGU OPERASIONAL')
        ->get();
        return view('pages.order.truck_order_rekanan.index',[
            'judul'=>"Trucking Order Rekanan",
            'dataSewa' =>  $dataSewa 
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
        $supplier = DB::table('supplier as s')
        ->select('s.*')
        ->where('s.is_aktif', '=', "Y")
        ->where('s.jenis_supplier_id', '=', 1)
        ->get();
         return view('pages.order.truck_order_rekanan.create',[
            'judul'=>"Trucking Order Rekanan",
            'datajO'=>SewaDataHelper::DataJO(),
            'dataCustomer'=>SewaDataHelper::DataCustomer(),
            'dataBooking'=>SewaDataHelper::DataBooking(),
            'supplier'=>$supplier,

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
            // dd((float)str_replace(',', '', $data['harga_jual']));
            // dd($data);
            
            $romawi = VariableHelper::bulanKeRomawi(date("m"));

            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            $booking_id = isset($data['booking_id'])? $data['booking_id']:null; 


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
            $sewa->id_supplier = $data['supplier'];
            $sewa->no_sewa = $no_sewa;
            $sewa->id_booking = $booking_id;
            $sewa->id_jo = $data['id_jo'];
            $sewa->id_jo_detail = $data['id_jo_detail'];
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['tujuan_id'];
            $sewa->jenis_tujuan = $data['jenis_tujuan'];
            $sewa->status = 'DALAM PERJALANAN';
            $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->kargo = $data['kargo'];
            $sewa->jenis_order = $data['jenis_order'];
            $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
            $sewa->total_uang_jalan =$data['uang_jalan'];
            $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
            $sewa->harga_jual =(float)str_replace(',', '', $data['harga_jual']);
            $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            $sewa->catatan = $data['catatan']? $data['catatan']:null;
            $sewa->is_kembali = 'N';
            $sewa->no_kontainer = $data['kontainer']? $data['kontainer']:null;
            $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
            $sewa->nama_driver = $data['driver_nama']? $data['driver_nama']:null;
            $sewa->harga_jual = (float)str_replace(',', '', $data['harga_jual']);
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
                            // 'deskripsi' => 'UANG JALAN',
                            // 'biaya' => $data['uang_jalan'],
                            // 'catatan' => null,
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

            return redirect()->route('truck_order_rekanan.index')->with('status','Berhasil menambahkan data Sewa Rekanan');
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
    public function edit(Sewa $truck_order_rekanan)
    {
        //
        $supplier = DB::table('supplier as s')
        ->select('s.*')
        ->where('s.is_aktif', '=', "Y")
        ->where('s.jenis_supplier_id', '=', 6)
        ->get();
        $data_sewa = Sewa::where('is_aktif', 'Y')->whereNotNull('id_supplier') ->where('status', 'MENUNGGU OPERASIONAL')->findOrFail($truck_order_rekanan->id_sewa);
        // dd($data_sewa);
        $dataBooking = DB::table('booking as b')
                ->select('*','b.id as idBooking')
                ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
                ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
                ->where('b.is_aktif', "Y")
                ->where('b.id', $data_sewa['id_booking'])
                ->orderBy('tgl_booking')
                ->whereNull('b.id_jo_detail')
                ->get();

         return view('pages.order.truck_order_rekanan.edit',[
            'judul'=>"Trucking Order Rekanan",
            'datajO'=>SewaDataHelper::DataJO(),
            'dataCustomer'=>SewaDataHelper::DataCustomer(),
            'dataBooking'=>SewaDataHelper::DataBooking(),
            'supplier'=>$supplier,
            'data'=>$data_sewa,
            'dataBooking' => $dataBooking,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $truck_order_rekanan)
    {
        //
        $data = $request->post();
        $user = Auth::user()->id; 
        try {
            $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($truck_order_rekanan->id_sewa);
            $sewa->id_supplier = $data['supplier'];
            $sewa->no_polisi = $data['no_polisi'];
            $sewa->harga_jual = (float)str_replace(',', '', $data['harga_jual']);
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->save();
            
            return redirect()->route('truck_order_rekanan.index')->with('status','Berhasil merubah data sewa rekanan');
        } catch (ValidationException $e) {
            //throw $th;
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage())->withInput();

        }
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
