<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\JobOrder;

class SewaDataHelper
{
    //=================================index================================
    public static function DataSewa()
     {
        // some logic to determine if the publisher is main
        return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->whereNull('id_supplier') 
            ->whereIn('s.status', ['MENUNGGU UANG JALAN', 'CANCEL', 'BATAL MUAT'])
            ->orderBy('created_at', 'DESC')
            ->get();
     }

     public static function getSewaByStatus($status)
     {
        // some logic to determine if the publisher is main
        return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', $status)
            ->orderBy('created_at', 'DESC')
            ->get();
     }
    //=================================create,edit??================================
    
    public static function DataJO()
     {
        // some logic to determine if the publisher is main
        return JobOrder::select('job_order.*')
            ->leftJoin('job_order_detail as jod', 'job_order.id', '=', 'jod.id_jo')
            ->where('jod.status', 'BELUM DOORING')
            ->where('job_order.status', 'PROSES DOORING')
            ->whereNotNull('jod.id_grup_tujuan')
            ->where('job_order.is_aktif', '=', "Y")
            ->with('getCustomer')
            ->with('getSupplier')
            ->groupBy('job_order.id')
            ->get();
     }

     public static function DataCustomer()
     {
        return DB::table('customer')
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
     }
     public static function DataDriver()
     {
        return DB::table('karyawan')
            ->select('*')
            ->where('karyawan.is_aktif', "Y")
            ->where('karyawan.role_id', 5)
            ->orderBy('nama_lengkap')
            ->get();
     }
     public static function DataPengaturanBiaya()
     {
        return DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', "Y")
            ->first();
     }
     public static function DataBooking()
     {
        return DB::table('booking as b')
            ->select('*','b.id as idBooking')
            ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
            ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
            ->where('b.is_aktif', "Y")
            ->where('b.is_sewa', "N")
            ->orderBy('tgl_booking')
            ->whereNull('b.id_jo_detail')
            ->get();
     }
    public static function DataChassis()
     {
        return DB::table('chassis as c')
            ->select('c.*','c.id as idChassis','m.nama as modelChassis')
            ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
            // ->where('m.id', '=', 2)
            ->where('c.is_aktif', "Y")
            ->get();
        ;
     }

     public static function DataKendaraan()
     {
        return DB::table('kendaraan AS k')
                ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi','k.driver_id', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota','mc.nama as tipeKontainerKendaraanDariChassis')
                // ini buat nge get pair kendaraan yang trailer
                ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                    $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                })
                // get chassis
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                // get model chasis yang 20/40 ft
                ->leftJoin('m_model_chassis AS mc', 'c.model_id', '=', 'mc.id')
                // get cabang jakarta/sby/...
                ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                // terus get kategorinya
                ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                //dikasih pengecekan
                ->where(function ($query) {
                        //kalau dia kategori 1 (trailer)
                        $query->where(function ($innerQuery) {
                            //syaratnya trailer ada cek tipe chassis trailer 20/40, kemudian pair nya gaboleh null (chassisnya)
                            $innerQuery->where('k.id_kategori', '=', 1)
                                    //    ->where('mc.id', '=', 1)
                                       ->whereNotNull('pk.chassis_id');
                        })
                        ->orWhere(function ($query) {
                            $query->where('k.id_kategori', '!=', 1);
                        });
                })
                ->where('k.is_aktif', '=', 'Y')
                ->whereNotNull('k.driver_id')
                ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                ->get(); 
     }
    //=================================create,edit??================================

    // =========================================API=====================================
    public function getJoDetail($id)
    {
        $datajODetail = DB::table('job_order_detail as jod')
            ->select('jod.*', 'b.id as booking_id')
            ->leftJoin('booking as b', function($join){
                $join->on('jod.id', '=', 'b.id_jo_detail')
                        ->where('b.is_sewa', 'N')
                        ->where('b.is_aktif', "Y");
            })
            ->where('jod.id_jo', '=', $id)
            ->where('status', 'BELUM DOORING')
            ->where('jod.is_aktif', '=', "Y")
            ->orderBy('jod.id', 'ASC')
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
        //Tujuan kan ada id
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
    public function getDataBooking($id)
    {
        $booking = DB::table('booking as b')
            ->select('b.*')
            ->where('b.id_jo_detail', '=',  $id)
            ->where('b.is_aktif', '=', "Y")
            ->first();
        return response()->json($booking);
    }

    //buat get data pembayaran uang jalan
    public function getDatasewaDetail($id)
    {
        $sewaDetail = DB::table('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', '=', $id)
                    ->groupBy('c.id')
                    ->first();
        $sewa_biaya_TL = DB::table('sewa_biaya as sb')
                                ->select('sb.*')
                                ->where('sb.id_sewa', $id)
                                ->where('sb.is_aktif', 'Y')
                                ->where('sb.deskripsi', 'TL')
                                ->first();
        $hutangKaryawan = DB::table('karyawan_hutang AS k')
                    ->select('k.*')
                    ->where('k.is_aktif', '=', 'Y')
                    ->where('k.id_karyawan', '=', $sewaDetail->id_karyawan)
                    ->first();
        return response()->json(['sewaDetail'=>$sewaDetail,'hutangKaryawan'=>$hutangKaryawan,'SewaBiayaTL'=>$sewa_biaya_TL]);
    }

     public static function getDataChassisByModel($model)
     {
        // $data='';
        // if($model)
        // {
            $data=DB::table('chassis as c')
            ->select('c.*','c.id as idChassis','m.nama as modelChassis')
            ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
            ->where('m.nama', 'like', "%$model%")
            ->where('c.is_aktif', "Y")
            ->get();
        // }
        // else
        // {
        //     $data=DB::table('chassis as c')
        //     ->select('c.*','c.id as idChassis','m.nama as modelChassis')
        //     ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
        //     ->where('c.is_aktif', "Y")
        //     ->get();
        // }
        return $data;
     }
     // Your new helper method
     public static function getDataKendaraanByModel($model)
     {
        // $data='';
        // if($model)
        // {
            $data= DB::table('kendaraan AS k')
                ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi','k.driver_id', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota','mc.nama as tipeKontainerKendaraanDariChassis')
                // ini buat nge get pair kendaraan yang trailer
                ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                    $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                })
                // get chassis
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                // get model chasis yang 20/40 ft
                ->leftJoin('m_model_chassis AS mc', 'c.model_id', '=', 'mc.id')
                // get cabang jakarta/sby/...
                ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                // terus get kategorinya
                ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                //dikasih pengecekan
                ->where(function ($query) use ($model){
                        //kalau dia kategori 1 (trailer)
                        $query->where(function ($innerQuery) use ($model) {
                            //syaratnya trailer ada cek tipe chassis trailer 20/40, kemudian pair nya gaboleh null (chassisnya)
                            $innerQuery->where('k.id_kategori', '=', 1)
                                       ->where('mc.nama', 'like', "%$model%")
                                       ->whereNotNull('pk.chassis_id');
                        });
                        // ->orWhere(function ($query) {
                        //     $query->where('k.id_kategori', '!=', 1);
                        // });
                })
                ->where('k.is_aktif', '=', 'Y')
                ->whereNotNull('k.driver_id')
                ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                ->get(); 
        // }
        // else
        // {
        //     $data=DB::table('kendaraan AS k')
        //         ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi','k.driver_id', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota')
        //         // ini buat nge get pair kendaraan yang trailer
        //         ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
        //             $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
        //         })
        //         // get chassis
        //         ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
        //         // get model chasis yang 20/40 ft
        //         ->leftJoin('m_model_chassis AS mc', 'c.model_id', '=', 'mc.id')
        //         // get cabang jakarta/sby/...
        //         ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
        //         // terus get kategorinya
        //         ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
        //         //dikasih pengecekan
        //         ->where(function ($query) {
        //                 //kalau dia kategori 1 (trailer)
        //                 $query->where(function ($innerQuery)  {
        //                     //syaratnya trailer ada cek tipe chassis trailer 20/40, kemudian pair nya gaboleh null (chassisnya)
        //                     $innerQuery->where('k.id_kategori', '=', 1)
        //                                ->whereNotNull('pk.chassis_id');
        //                 })
        //                 ->orWhere(function ($query) {
        //                     $query->where('k.id_kategori', '!=', 1);
        //                 });
        //         })
        //         ->where('k.is_aktif', '=', 'Y')
        //         ->whereNotNull('k.driver_id')
        //         ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
        //         ->get(); 
        // }
        return $data;
     }
}
?>