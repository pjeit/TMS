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
use App\Models\PengaturanKeuangan;
use App\Models\SewaBiaya;
use App\Models\SewaOperasional;
use App\Models\JobOrder;
use Illuminate\Support\Carbon;
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
        ->where('s.status', 'PROSES DOORING')
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
            $sewa->status = 'PROSES DOORING';
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
                            'status'=> 'PROSES DOORING',
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
        $data_sewa = Sewa::where('is_aktif', 'Y')
        ->whereNotNull('id_supplier') 
        ->where('status', 'PROSES DOORING')
        ->findOrFail($truck_order_rekanan->id_sewa);
        $supplier = DB::table('supplier as s')
        ->select('s.*')
        ->where('s.is_aktif', '=', "Y")
        ->where('s.jenis_supplier_id', '=', 1)
        ->get();
        $dataBooking = DB::table('booking as b')
                ->select('*','b.id as idBooking')
                ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
                ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
                ->where('b.is_aktif', "Y")
                ->where('b.id', $data_sewa['id_booking'])
                ->orderBy('tgl_booking')
                ->whereNull('b.id_jo_detail')
                ->get();
         $dataJo=JobOrder::select('job_order.*')
            ->leftJoin('job_order_detail as jod', 'job_order.id', '=', 'jod.id_jo')
            ->where('job_order.is_aktif', '=', "Y")
            ->with('getCustomer')
            ->with('getSupplier')
            ->groupBy('job_order.id')
            ->get();
        // dd($data_sewa);
         return view('pages.order.truck_order_rekanan.edit',[
            'judul'=>"Trucking Order Rekanan",
            'datajO'=>$dataJo,
            'dataCustomer'=>SewaDataHelper::DataCustomer(),
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
            // dd($data);

        try {
            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            $sewa = Sewa::where('is_aktif', 'Y')
            ->whereNotNull('id_supplier') 
            ->findOrFail($truck_order_rekanan->id_sewa);
            // $sewa->jenis_order = $data['jenis_order']=='INBOUND'? 'INBOUND':'OUTBOUND';
            $customer_lama = DB::table('customer as c')
                    ->select('c.*')
                    ->where('c.id', '=', $sewa->id_customer)
                    ->where('c.is_aktif', '=', "Y")
                    ->first();
            $grup_lama = DB::table('grup as g')
                ->select('g.*')
                ->where('g.id', '=', $customer_lama->grup_id)
                ->where('g.is_aktif', '=', "Y")
                ->first();
            // kalo tujuan baru ga sama sama tujuan yang lama
            if($data['tujuan_id']!=$sewa->id_grup_tujuan &&$sewa->jenis_order == "OUTBOUND")
            {
                // dd('masuk sini');
                //KURANGI DULU KREDIT YANG LAMA,SOALNYA KAN TARIFNYA BEDA PER TUJUAN
                DB::table('customer')
                    ->where('id', $sewa->id_customer)
                    ->update([
                        'kredit_sekarang' => (float)$customer_lama->kredit_sekarang-$sewa->total_tarif < 0 ? 0 : $customer_lama->kredit_sekarang-$sewa->total_tarif,
                        'updated_at' => now(),
                        'updated_by' => $user,
                ]);
                DB::table('grup')
                    ->where('id', $customer_lama->grup_id)
                    ->update([
                        'total_kredit' => (float)$grup_lama->total_kredit-$sewa->total_tarif< 0 ? 0 : $grup_lama->total_kredit-$sewa->total_tarif,
                        'updated_at' => now(),
                        'updated_by' => $user,
                ]);
                    $sewa->id_customer = $data['customer_id'];
                    $sewa->id_grup_tujuan = $data['tujuan_id'];
                    $sewa->jenis_tujuan = $data['jenis_tujuan'];
                    $sewa->nama_tujuan = $data['nama_tujuan'];
                    $sewa->alamat_tujuan = $data['alamat_tujuan'];
                    $sewa->kargo = $data['kargo'];
                    $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
                    $sewa->total_uang_jalan = $data['uang_jalan'];
                    $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
                    $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
                    $sewa->id_supplier = $data['supplier'];
                    $sewa->no_polisi = $data['no_polisi'];
                    $sewa->harga_jual = (float)str_replace(',', '', $data['harga_jual']);
                    $sewa->updated_by = $user;
                    $sewa->updated_at = now();
                    $sewa->save();

                if ($data['jenis_tujuan'] === "LTL") {
                    $harga = (float)$data['harga_per_kg'] * $data['min_muatan'];
                } else {
                    $harga = (float)$data['tarif'];
                }
            
                // update kredit grup + customer
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
                $arrayBiaya = json_decode($data['biayaDetail'], true);
                //   dd(isset($arrayBiaya));

                if(isset($arrayBiaya))
                {
                //   dd($sewa->id_sewa);

                    DB::table('sewa_biaya')
                    ->where('id_sewa', $sewa->id_sewa)
                    ->update(array(
                        'is_aktif' => "N",
                        'updated_at'=> now(),
                        'updated_by'=> $user, // masih hardcode nanti diganti cookies
                    )
                    );

                    foreach ($arrayBiaya as /*$key =>*/ $item) {
                        DB::table('sewa_biaya')
                            ->insert(array(
                            'id_sewa' => $sewa->id_sewa,
                            'deskripsi' => $item['deskripsi'] ,
                            'biaya' => $item['biaya'],
                            'catatan' => $item['catatan']? $item['catatan']:null,
                            'created_at' => now(), 
                            'created_by' => $user,
                            'is_aktif' => "Y",
                            )
                        ); 
                    }
                }
            }
            else
            {
                $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
                $sewa->id_supplier = $data['supplier'];
                $sewa->no_polisi = $data['no_polisi'];
                $sewa->harga_jual = (float)str_replace(',', '', $data['harga_jual']);
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                $sewa->save();

                // dd(isset($data['id_jo_detail']));
                if(isset($data['id_jo_detail']))
                {
                    $ganti_tgl_dooring_jod = DB::table('job_order_detail as jod')
                            ->select('jod.*')
                            ->where('jod.id', $data['id_jo_detail'])
                            ->where('jod.is_aktif', 'Y')
                            ->first();
                    // dd($ganti_tgl_dooring_jod);
                    // dd(Carbon::parse($ganti_tgl_dooring_jod->tgl_dooring)->format('Y-m-d')!=date_format($tgl_berangkat, 'Y-m-d'));
                    // dd(date_format($ganti_tgl_dooring_jod['tgl_dooring'], 'Y-m-d')!=date_format($tgl_berangkat, 'Y-m-d'));
                    if(Carbon::parse($ganti_tgl_dooring_jod->tgl_dooring)->format('Y-m-d')!=date_format($tgl_berangkat, 'Y-m-d'))
                    {
                        DB::table('job_order_detail')
                            ->where('id', $data['id_jo_detail'])
                            ->update([
                                'tgl_dooring' => date_format($tgl_berangkat, 'Y-m-d'),
                                // 'status'=> 'PROSES DOORING',
                                'updated_at' => now(),
                                'updated_by' => $user,
                            ]);
                    }
                    
                }
            }
            
            
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
