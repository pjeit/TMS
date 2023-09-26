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
class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
    
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
            $sewa->no_sewa = $no_sewa;
            $sewa->id_booking = $data['booking_id'];
            $sewa->id_jo = $data['id_jo'];
            $sewa->id_jo_detail = $data['id_jo_detail'];
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['tujuan_id'];
            $sewa->jenis_tujuan = $data['jenis_tujuan'];
            $sewa->status = 'MENUNGGU UANG JALAN';
            $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->kargo = $data['kargo'];
            $sewa->jenis_order = $data['jenis_order']=='INBOUND'? 'INBOUND':'OUTBOUND';
            $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
            $sewa->total_uang_jalan = $data['uang_jalan'];
            $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
            $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
            $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            $sewa->id_chassis = $data['select_chassis']? $data['select_chassis']:null;
            $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
            $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
            $sewa->catatan = $data['catatan']? $data['catatan']:null;
            $sewa->is_kembali = 'N';
            $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
            $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
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
            
                // EXECUTE TRIGGER BUAT INPUT KE TRIP SUPIR (CEK TRIGGER DI TABEL SEWA)
                
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
    public function edit(Sewa $sewa, $id)
    {
        $data_sewa = Sewa::where('is_aktif', 'Y')->findOrFail($id);
        $dataBooking = DB::table('booking as b')
                ->select('*','b.id as idBooking')
                ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
                ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
                ->where('b.is_aktif', "Y")
                ->where('b.id', $data_sewa['id_booking'])
                ->orderBy('tgl_booking')
                ->whereNull('b.id_jo_detail')
                ->get();

        return view('pages.order.truck_order.edit',[
            'judul' => "Edit Trucking Order",
            'data' => $data_sewa,
            'datajO' => SewaDataHelper::DataJO(),
            'dataCustomer' => SewaDataHelper::DataCustomer(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataBooking' => $dataBooking,
            'dataChassis' => SewaDataHelper::DataChassis()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id; 
        // dd($data);
        try {
            $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($data['sewa_id']);
            $sewa->id_karyawan = $data['select_driver'];
            $sewa->id_kendaraan = $data['kendaraan_id'];
            $sewa->id_chassis = $data['ekor_id'];
            $sewa->no_polisi = $data['no_polisi'];
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->save();
            
            return redirect()->route('truck_order.index')->with('status','Berhasil merubah data Sewa');
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
        var_dump('xxx'); die;
    }
}
