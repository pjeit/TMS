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
        //
      
        $datajO = DB::table('job_order as jo')
            ->select('jo.*')
            ->where('jo.is_aktif', '=', "Y")
            ->where('jo.status', 'like', "%DALAM PENGIRIMAN%")
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', "Y")
            ->orderBy('nama')
            ->get();
        // dd($datajO[0]->id);
        $dataDriver = DB::table('karyawan')
            ->select('*')
            ->where('karyawan.is_aktif', "Y")
            ->where('karyawan.posisi_id', 5)
            ->orderBy('nama_lengkap')
            ->get();
        $dataBooking = DB::table('booking as b')
            ->select('*','b.id as idBooking')
            ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
            ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
            ->where('b.is_aktif', "Y")
            ->orderBy('tgl_booking')
            ->get();
        $dataChassis = DB::table('chassis as c')
            ->select('*')
            ->where('c.is_aktif', "Y")
            ->get();
        // dd($dataBooking);

        $dataKendaraan = DB::table('kendaraan AS k')
                ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi', 'kkm.nama as kategoriKendaraan','kt.nama as namaKota', DB::raw('GROUP_CONCAT(CONCAT(c.kode, " (", m.nama, ")") SEPARATOR ", ") AS chassis_model'))
                ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                    $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                })
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
                ->leftJoin('m_kota AS kt', 'k.kota_id', '=', 'kt.id')
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
                ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','kt.nama')
                ->get();
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
            ->where('jod.is_aktif', '=', "Y")
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
        
        // $Tujuan = GrupTujuan::where('grup_id', $cust->grup_id)->where('is_aktif', 'Y')->get();
        return response()->json($Tujuan);
        
    }
    public function getTujuanBiaya($id)
    {
        $Tujuan = DB::table('grup_tujuan as gt')
            ->select('gt.*')
            ->where('gt.id', '=',  $id)
            ->where('gt.is_aktif', '=', "Y")
            ->first();
        
        $TujuanBiaya = DB::table('grup_tujuan_biaya as gtb')
            ->select('gtb.*')
            ->where('gtb.grup_tujuan_id', '=',  $id)
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
       
            //====== end logic otomatis nik ======
            $tgl_berangkat = date_create_from_format('d-M-Y', $data['tgl_berangkat']);
            $telp1= $data['telp1'];
         
            // var_dump($data['status_pegawai']);die;
            $idSewa=DB::table('sewa')
                ->insertGetId(array(
                    
                    // end data status Karyawan
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            ); 
            
            $arrayDokumen = json_decode($data['identitas'], true);
            foreach ($arrayDokumen as $key => $item) {
                DB::table('karayawan_identitas')
                    ->insert(array(
                    'karyawan_id'=>$idSewa,
                    'm_jenis_identitas_id' => $item['m_jenis_identitas_id'] ,
                    'nomor' => $item['nomor'],
                    'catatan' => $item['catatan'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                    )
                ); 
        
            }
          
            // var_dump($data['gaji']);
            // var_dump( response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idKaryawan]));

            return response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idSewa]);

            // return redirect()->route('karyawan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            
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
