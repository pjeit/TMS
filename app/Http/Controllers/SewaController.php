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
            'dataChassis'=>SewaDataHelper::DataChassis(),
            'dataPengaturanKeuangan'=>SewaDataHelper::DataPengaturanBiaya()
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

            $pengaturan = PengaturanKeuangan::first();
            $romawi = VariableHelper::bulanKeRomawi(date("m"));
            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            // dd($data['stack_tl']);
            //    $sewa->total_uang_jalan = $data['stack_tl'] == 'tl_teluk_lamong'?$data['uang_jalan']+$data['stack_teluk_lamong_hidden']:$data['uang_jalan'];
            // dd($data['uang_jalan']+$data['stack_teluk_lamong_hidden']);
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
            $sewa->id_booking = ($data['booking_id'] == NULL || $data['booking_id'] == 'null')? NULL:$data['booking_id'];
            $sewa->id_jo = $data['id_jo'];
            $sewa->id_jo_detail = $data['id_jo_detail'];
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['tujuan_id'];
            $sewa->jenis_tujuan = $data['jenis_tujuan'];
            $status = 'MENUNGGU UANG JALAN';
            if($data['jenis_tujuan'] == 'LTL'){
                $status = 'PROSES DOORING';
            }
            $sewa->status = $status;
            $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->kargo = $data['kargo'];
            $sewa->jenis_order = $data['jenis_order']=='INBOUND'? 'INBOUND':'OUTBOUND';
            $sewa->total_tarif = $data['jenis_tujuan']=="LTL"? $data['harga_per_kg'] * $data['min_muatan']:$data['tarif'];
            $sewa->total_uang_jalan = $data['uang_jalan'];
            $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
            $sewa->total_komisi_driver = $data['komisi_driver']? $data['komisi_driver']:null;
            $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
            $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            $sewa->id_chassis = $data['select_chassis']? $data['select_chassis']:null;
            $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
            $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
            $sewa->nama_driver = $data['driver_nama']? $data['driver_nama']:null;
            $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:null;
            $sewa->catatan = $data['catatan']? $data['catatan']:null;
            $sewa->is_kembali = 'N';
            $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
            $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
            $sewa->created_by = $user;
            $sewa->created_at = now();
            $sewa->is_aktif = 'Y';
            
            if($sewa->save()){
                if($data['stack_tl'] == 'tl_teluk_lamong'){
                    DB::table('sewa_biaya')
                        ->insert(array(
                        'id_sewa' => $sewa->id_sewa,
                        'deskripsi' => 'TL',
                        'biaya' => $data['stack_teluk_lamong_hidden'],
                        'catatan' => $data['stack_tl'],
                        'created_at' => now(),
                        'created_by' => $user,
                        'is_aktif' => "Y",
                        )
                    ); 
                }

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
                if(isset($data['booking_id'])){
                    DB::table('booking')
                        ->where('id', $data['booking_id'])
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
                            'status'=> 'PROSES DOORING',
                            'updated_at' => now(),
                            'updated_by' => $user,
                        ]);
                    
                }
              
                $arrayBiaya = json_decode($data['biayaDetail'], true);
                if(isset($arrayBiaya))
                {
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

                // EXECUTE TRIGGER BUAT INPUT KE TRIP SUPIR (CEK TRIGGER DI TABEL SEWA)
                
            }

            return redirect()->route('truck_order.index')->with(['status' => 'Success', 'msg' => 'Data berhasil dibuat']);
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
        $data_sewa = Sewa::where('is_aktif', 'Y')
        ->whereNull('id_supplier') 
        ->findOrFail($id);
        $checkTL = SewaBiaya::where('is_aktif', 'Y')
                            ->where('deskripsi', 'TL')
                            ->where('id_sewa', $id)
                            ->first();
        // dd($checkTL);
        // dd($data_sewa);
        $dataJo=JobOrder::select('job_order.*')
            ->leftJoin('job_order_detail as jod', 'job_order.id', '=', 'jod.id_jo')
            ->where('job_order.is_aktif', '=', "Y")
            ->with('getCustomer')
            ->with('getSupplier')
            ->groupBy('job_order.id')
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
        $sewa_biaya_TL = DB::table('sewa_biaya as sb')
                                ->select('sb.*')
                                ->where('sb.id_sewa', $id)
                                ->where('sb.is_aktif', 'Y')
                                ->where('sb.deskripsi', 'TL')
                                ->first();
        // dd($data_sewa);
        return view('pages.order.truck_order.edit',[
            'judul' => "Edit Trucking Order",
            'data' => $data_sewa,
            'checkTL' => $checkTL,
            'datajO' => $dataJo,
            'dataCustomer' => SewaDataHelper::DataCustomer(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataBooking' => $dataBooking,
            'dataChassis' => SewaDataHelper::DataChassis(),
            'dataPengaturanKeuangan'=>SewaDataHelper::DataPengaturanBiaya()

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        // dd($id);
        $data = $request->post();
        $user = Auth::user()->id; 
        try {

            $sewa = Sewa::where('is_aktif', 'Y')
            ->whereNull('id_supplier') 
            ->findOrFail($data['sewa_id']);
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
                   
                // dd($data['uang_jalan']+$data['stack_teluk_lamong_hidden']);
            // kalo tujuan baru ga sama sama tujuan yang lama
            if($data['tujuan_id'] != $sewa->id_grup_tujuan && $sewa->status == "MENUNGGU UANG JALAN" && $sewa->jenis_order == "OUTBOUND"){
                $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
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
                    $sewa->total_uang_jalan = /*$data['stack_tl'] == 'tl_teluk_lamong'?$data['uang_jalan']+$data['stack_teluk_lamong_hidden']:*/$data['uang_jalan'];
                    $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
                    $sewa->total_komisi_driver = $data['komisi_driver']? $data['komisi_driver']:null;
                    $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
                    $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
                    $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
                    $sewa->id_chassis = $data['select_chassis']? $data['select_chassis']:null;
                    $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
                    $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
                    $sewa->nama_driver = $data['driver_nama']? $data['driver_nama']:null;
                    $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:null;
                    $sewa->catatan = $data['catatan']? $data['catatan']:null;
                    $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
                    $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
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
                if(isset($arrayBiaya))
                {
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
                    if($data['stack_tl'] == 'tl_teluk_lamong'){
                        $cek_sewa_biaya_TL = DB::table('sewa_biaya as sb')
                                    ->select('sb.*')
                                    ->where('sb.id_sewa', $data['sewa_id'])
                                    ->where('sb.is_aktif', 'Y')
                                    ->where('sb.deskripsi', 'TL')
                                    ->first();
                        // pengecekan kalau null dan kalau status sewa masih menunggu uang jalan bisa dimasukin tl nya dari edit, 
                        //kalau udah dibayar uang jalan, gabisa masuk detail, harus dari add/return TL
                        if($cek_sewa_biaya_TL ==null && $sewa->status=="MENUNGGU UANG JALAN")
                        {
                            DB::table('sewa_biaya')
                                ->insert(array(
                                'id_sewa' => $sewa->id_sewa,
                                'deskripsi' => 'TL',
                                'biaya' => $data['stack_teluk_lamong_hidden'],
                                'catatan' => $data['stack_tl'],
                                'created_at' => now(),
                                'created_by' => $user,
                                'is_aktif' => "Y",
                                )
                            ); 
                        
                        }
                    }
                }
                
            } else {
                if($sewa->status=="MENUNGGU UANG JALAN"){
                    $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
                    $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
                    $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
                    $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
                    $sewa->id_chassis = $data['select_chassis']? $data['select_chassis']:null;
                    $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
                    $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
                    $sewa->nama_driver = $data['driver_nama']? $data['driver_nama']:null;
                    $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:null;
                    $sewa->total_komisi = $data['komisi']? $data['komisi']:null;
                    $sewa->total_komisi_driver = $data['komisi_driver']? $data['komisi_driver']:null;
                    $sewa->catatan = $data['catatan']? $data['catatan']:null;
                    $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
                    $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
                    $sewa->updated_by = $user;
                    $sewa->updated_at = now();
                    $sewa->save();
                    // dd('masuk sini if');
                    if($data['stack_tl'] == 'tl_teluk_lamong'){
                        $cek_sewa_biaya_TL = DB::table('sewa_biaya as sb')
                                    ->select('sb.*')
                                    ->where('sb.id_sewa', $data['sewa_id'])
                                    ->where('sb.is_aktif', 'Y')
                                    ->where('sb.deskripsi', 'TL')
                                    ->first();
                        // pengecekan kalau null dan kalau status sewa masih menunggu uang jalan bisa dimasukin tl nya dari edit, 
                        //kalau udah dibayar uang jalan, gabisa masuk detail, harus dari add/return TL
                        if($cek_sewa_biaya_TL ==null && $sewa->status=="MENUNGGU UANG JALAN")
                        {
                            DB::table('sewa_biaya')
                                ->insert(array(
                                'id_sewa' => $sewa->id_sewa,
                                'deskripsi' => 'TL',
                                'biaya' => $data['stack_teluk_lamong_hidden'],
                                'catatan' => $data['stack_tl'],
                                'created_at' => now(),
                                'created_by' => $user,
                                'is_aktif' => "Y",
                                )
                            ); 
                            
                        }
                    }
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
                                    'status'=> 'PROSES DOORING',
                                    'updated_at' => now(),
                                    'updated_by' => $user,
                                ]);
                        }
                    }
                } else if($sewa->status == "BATAL MUAT" || $sewa->status == "CANCEL"){ 
                    
                }else {
                    $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:null;
                    $sewa->catatan = $data['catatan']? $data['catatan']:null;
                    $sewa->save();
                    // dd('masuk sini else');
                }
            }

            // if($data['jenis_order']=='OUTBOUND')
            // {
            // }
            // else
            // {
            //     $sewa->tanggal_berangkat = date_format($tgl_berangkat, 'Y-m-d');
            //     $sewa->id_kendaraan = $data['kendaraan_id']? $data['kendaraan_id']:null;
            //     $sewa->no_polisi = $data['no_polisi']? $data['no_polisi']:null;
            //     $sewa->id_chassis = $data['select_chassis']? $data['select_chassis']:null;
            //     $sewa->karoseri = $data['karoseri']? $data['karoseri']:null;
            //     $sewa->id_karyawan = $data['select_driver']? $data['select_driver']:null;
            //     $sewa->nama_driver = $data['driver_nama']? $data['driver_nama']:null;
            //     $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:null;
            //     $sewa->catatan = $data['catatan']? $data['catatan']:null;
            //     $sewa->no_kontainer = $data['no_kontainer']? $data['no_kontainer']:null;
            //     $sewa->tipe_kontainer = $data['tipe_kontainer']? $data['tipe_kontainer']:null;
            //     $sewa->updated_by = $user;
            //     $sewa->updated_at = now();
            //     $sewa->save();
            // }
            // if($data['stack_tl'] == null){
            //     DB::table('sewa_biaya')
            //         ->where('id_sewa', $data['sewa_id'])
            //         ->where('is_aktif', 'Y')
            //         ->where('deskripsi', 'TL')
            //         ->update([
            //             'updated_at' => now(),
            //             'updated_by' => $user,
            //             'is_aktif' => "N",
            //         ]);
            // }else{
            //     $pengaturan = PengaturanKeuangan::first();
            //     DB::table('sewa_biaya')
            //         ->insert(array(
            //         'id_sewa' => $sewa->id_sewa,
            //         'deskripsi' => 'TL',
            //         'biaya' => $pengaturan[$data['stack_tl']],
            //         'catatan' => $data['stack_tl'],
            //         'created_at' => now(),
            //         'created_by' => $user,
            //         'is_aktif' => "Y",
            //         )
            //     ); 
            // }
            if(in_array($sewa->status, ["CANCEL", "BATAL MUAT"])){
                $sewa->status = 'MENUNGGU UANG JALAN';
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                $sewa->save();
            }
            $sewaCek = Sewa::where('is_aktif', 'Y')->findOrFail($id);

            if($sewaCek->status=="MENUNGGU UANG JALAN")
            {
                return redirect()->route('truck_order.index')->with(['status' => 'Success', 'msg' => 'Berhasil merubah data!']);

            }
            else
            {
                return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => 'Berhasil merubah data!']);
            }
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
    public function destroy(Sewa $truck_order)
    {
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try{
            DB::table('sewa')
            ->where('id_sewa', $truck_order->id_sewa)
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );

            DB::table('sewa_biaya')
            ->where('id_sewa', $truck_order->id_sewa)
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );

            DB::table('sewa_operasional')
            ->where('id_sewa', $truck_order->id_sewa)
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );
            return redirect()->route('truck_order.index')->with(['status' => 'Success', 'msg' => 'Berhasil Menghapus data!']);


        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
