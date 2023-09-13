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
         $dataSewa = DB::table('sewa as s')
            ->select('s.*')
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status','like','%MENUNGGU UANG JALAN%')
            ->get();
        
            return view('pages.order.truck_order.index',[
                'judul'=>"Trucking Order",
                'dataSewa' => $dataSewa,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $datajO = DB::table('job_order as jo')
            ->select('jo.*')
            ->where('jo.is_aktif', '=', "Y")
            ->where('jo.status', 'like', "%DALAM PENGIRIMAN%")
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('customer.id as idCustomer',
            'customer.kode as kodeCustomer',
            'customer.nama as namaCustomer',
            'customer.kredit_sekarang as kreditCustomer',
            'g.nama_grup as namaGrup',
            'g.total_max_kredit as maxGrup'
            )
            ->Join('grup AS g', 'customer.grup_id', '=', 'g.id')
            ->where('customer.is_aktif', "Y")
            ->orderBy('customer.nama')
            ->get();
        // dd($datajO[0]->id);
        $dataDriver = DB::table('karyawan')
            ->select('*')
            ->where('karyawan.is_aktif', "Y")
            ->where('karyawan.role_id', 5)
            ->orderBy('nama_lengkap')
            ->get();
        $dataBooking = DB::table('booking as b')
            ->select('*','b.id as idBooking')
            ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
            ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
            ->where('b.is_aktif', "Y")
            ->orderBy('tgl_booking')
            ->whereNull('b.id_jo_detail')
            ->get();
        $dataChassis = DB::table('chassis as c')
            ->select('*')
            ->where('c.is_aktif', "Y")
            ->get();
        // dd($dataBooking);

        $dataKendaraan = DB::table('kendaraan AS k')
                ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi','k.driver_id', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                    $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                })
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                ->Join('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                    ->where(function ($query) {
                        $query->where('k.is_aktif', '=', 'Y')
                            ->where(function ($innerQuery) {
                                $innerQuery->where('k.id_kategori', '=', 1)
                                    ->whereNotNull('c.id');
                            })
                    ->orWhere(function ($innerQuery) {
                        $innerQuery->where('k.id_kategori', '!=', 1);
                    });
                })
                ->whereNotNull('k.driver_id')
                ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                ->get();
            
            // dd($dataKredit);
            return view('pages.order.truck_order.create',[
                'judul'=>"Trucking Order",
                'datajO'=>$datajO,
                'dataCustomer'=>$dataCustomer,
                'dataDriver'=>$dataDriver,
                'dataKendaraan'=>$dataKendaraan,
                'dataBooking'=>$dataBooking,
                'dataChassis'=>$dataChassis
            ]);
    }
    public function getDetailJO($id)
    {
        $datajODetail = DB::table('job_order_detail as jod')
            ->select('jod.*')
            ->where('jod.id_jo', '=', $id)
            ->where('status' ,'like','%BELUM DOORING%')
            ->where('jod.is_aktif', '=', "Y")
            ->whereNotNull('jod.id_grup_tujuan' )
            ->get();
        return response()->json($datajODetail);
        
    }
    public function getDetailJOBiaya($id)
    {
        $datajODetail = DB::table('job_order_detail_biaya as jodb')
            ->select('jodb.*')
            // ->Join('job_order_detail AS job', function($join) {
            //         $join->on('job.id', '=', 'jodb.id_jo_detail')
            //         ->where('job.is_aktif', '=', 'Y')
            //         ->where('status' ,'like','%BELUM DOORING%')
            //         ->whereNotNull('job.id_grup_tujuan');
            //     })
            ->where('jodb.id_jo_detail', '=', $id)
            ->where('status_bayar' ,'like','%SELESAI PEMBAYARAN%')
            ->where('jodb.is_aktif', '=', "Y")
            ->get();
        return response()->json($datajODetail);
        
    }
    public function getTujuanCust($id)
    {
        $cust = Customer::where('id', $id)->first();
        $Tujuan = DB::table('grup_tujuan as gt')
            ->select('gt.*')
            ->where('gt.grup_id', '=',  $cust->grup_id)
            ->where('gt.is_aktif', '=', "Y")
            ->get();
        $dataKredit = DB::table('customer')
            ->select('customer.id as idCustomer',
            'customer.kode as kodeCustomer',
            'customer.nama as namaCustomer',
            'customer.kredit_sekarang as kreditCustomer',
            'g.nama_grup as namaGrup',
            'g.total_max_kredit as maxGrup'
            )
            ->Join('grup AS g', function($join) {
                    $join->on('customer.grup_id', '=', 'g.id')->where('g.is_aktif', '=', 'Y');
                })
            ->where('customer.is_aktif', "Y")
            ->where('customer.id', $cust->id)
            ->first();
        
        // $Tujuan = GrupTujuan::where('grup_id', $cust->grup_id)->where('is_aktif', 'Y')->get();
        return response()->json(['dataTujuan' =>$Tujuan,'dataKredit' => $dataKredit]);
        
    }
    public function getTujuanBiaya($id)
    {
        //TUjuan kan ada id
        $Tujuan = DB::table('grup_tujuan as gt')
            ->select('gt.*')
            ->where('gt.id', '=',  $id)
            ->where('gt.is_aktif', '=', "Y")
            ->first();
        //na biaya ini berdasarkan id tujuannya misa id tujuan 1 punya biaya 1 2 3 dengan id tujuan 1
        $TujuanBiaya = DB::table('grup_tujuan_biaya as gtb')
            ->select('gtb.*')
            ->where('gtb.grup_tujuan_id', '=',  $Tujuan->id)
            ->where('gtb.is_aktif', '=', "Y")
            ->get();

        return response()->json(['dataTujuan' =>$Tujuan,'dataTujuanBiaya' => $TujuanBiaya]);
        
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
            $pesanKustom = [
             
                // 'tanggal_gabung.required' => 'Tanggal gabung Karyawan harap diisi!',
                // 'posisi.required' => 'Posisi karyawan harap diisi!',
                // 'telp1.required' =>'Nomor telpon 1 harap diisi ',
                // 'nama_lengkap.required' => 'Nama lengkap karyawan harap diisi!',
                // 'nama_panggilan.required' => 'Nama panggilan karyawan harap diisi!',
                // // 'foto.required' => 'Foto karyawan harap diisi!',
            ];
             
            
            $request->validate([
                // // 'telp1' =>'required|in:1,2',  // buat radio button
                // 'tanggal_gabung' => 'required',
                // 'posisi' => 'required',
                // 'telp1' =>'required',
                // 'nama_lengkap' => 'required',
                // 'nama_panggilan' => 'required',
                // 'foto' => 'image|mimes:jpg,png,jpeg|max:2048',

            ],$pesanKustom);
           

            $data = $request->collect();
            // encode ubah array jadi json
            //decode ubah json jadi array
            // dd($data);
            // dd(json_decode($data['biayaDetail'], true));
       
            //====== end logic otomatis nik ======
            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tanggal_berangkat']);
            // var_dump($data['status_pegawai']);die;
            $dataBook=$data['select_booking']?explode("-",$data['select_booking']):null;
            // dd($dataBook);
        // dd(json_decode($data['biayaTambahSDT'], true));
            // dd(json_decode($data['biayaTambahTarif'], true));
                //   dd(json_decode($data['biayaDetail'], true));
            $idSewa=DB::table('sewa')
                ->insertGetId(array(
                    'id_booking'=>$dataBook?$dataBook[0]:null, //id booking dr front end di split di combobox
                    'id_jo'=>$data['select_jo']?$data['select_jo']:null,
                    'id_jo_detail'=>$data['select_jo_detail'] ?? null,
                    'jenis_tujuan'=>$data['jenis_tujuan'],
                    'status'=>'MENUNGGU UANG JALAN',
                    'tanggal_status'=>now(),
                    'tanggal_berangkat'=>date_format($tgl_berangkat, 'Y-m-d'),
                    'id_customer'=>$data['customer_id']/*?$data['']:null*/,
                    'idGrup_tujuan'=>$data['tujuan_id']/*?$data['']:null*/,
                    'nama_tujuan'=>$data['nama_tujuan']/*?$data['']:null*/,
                    'alamat_tujuan'=>$data['alamat_tujuan']/*?$data['']:null*/,
                    'jumlah_muatan'=>0,
                    'kargo'=>$data['kargo'],
                    'DO'=>null,
                    'RO'=>null,
                    'IER'=>null,
                    'is_bongkar'=>$data['is_bongkar']=='Y'?'Y':'N',
                    'total_tarif'=>$data['jenis_tujuan']=="LTL"?$data['harga_per_kg'] * $data['min_muatan']:$data['tarif'],
                    'total_uang_jalan'=>$data['uang_jalan'],
                    'total_komisi'=>$data['komisi']?$data['komisi']:null,
                    'id_kendaraan'=>$data['kendaraan_id']?$data['kendaraan_id']:null,
                    'no_pol'=>$data['kendaraan_nopol']?$data['kendaraan_nopol']:null,
                    'id_chassis'=>$data['ekor_id']?$data['ekor_id']:null,
                    // 'karoseri_chassis'=>$data['']?$data['']:null,
                    'id_karyawan'=>$data['select_driver']?$data['select_driver']:null,
                    // 'nama_supir'=>$data['']?$data['']:null,
                    'catatan'=>$data['catatan']?$data['catatan']:null,
                    'is_kembali'=>'N',
                    'tanggal_kembali'=>null,
                    'no_kontainer'=>$data['no_kontainer']?$data['no_kontainer']:null,
                    'no_surat_jalan'=>null,
                    'no_segel'=>null,
                    'no_segel_pje'=>null,
                    'foto_kontainer'=>null,
                    'foto_surat_jalan'=>null,
                    'foto_segel_1'=>null,
                    'foto_segel_2'=>null,
                    'foto_segel_pje'=>null,
                    'total_reimburse_dipisahkan'=>null,
                    'total_reimburse_tidak_dipisahkan'=>null,
                    'total_reimburse_aktual'=>null,
                    'alasan_hapus'=>null,
                    'is_aktif'=>'Y',
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                )
            ); 

            // dd($idSewa['id_jo']);
            
            if($idSewa)
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
                


                if( $dataBook)
                {
                    DB::table('booking')
                        ->where('id', $dataBook[0])
                        ->update([
                            'updated_at' => VariableHelper::TanggalFormat(),
                            'updated_by' => $user,
                            'is_aktif' => "N",
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
                            'id_kendaraan'=>$data['kendaraan_id']?$data['kendaraan_id']:null,
                            'nopol_kendaraan'=>$data['kendaraan_nopol']?$data['kendaraan_nopol']:null,
                            'tgl_dooring'=>date_format($tgl_berangkat, 'Y-m-d'),
                            'status'=>'DALAM PERJALANAN',
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
                            'id_sewa'=>$idSewa,
                            'deskripsi' => $item['deskripsi'] ,
                            'biaya' => $item['biaya'],
                            'catatan' => $item['catatan']?$item['catatan']:null,
                            'is_aktif' => "Y",
                            'created_at'=>VariableHelper::TanggalFormat(), 
                            'created_by'=> $user,
                            'updated_at'=> VariableHelper::TanggalFormat(),
                            'updated_by'=> $user,
                            )
                        ); 
                    }
                }
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

            }
            // var_dump($data['gaji']);
            // var_dump( response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idKaryawan]));

            // return response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idSewa]);

            return redirect()->route('truck_order.index')->with('status','Berhasil menambahkan data Sewa');
        } catch (ValidationException $e) {
            // cancel input db
            DB::rollBack();
                return response()->json(['errorsCatch' => $e->errors()], 422);
        }
       catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return response()->json(['errorServer' => $ex->getMessage()],500);
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
