<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class SewaDataHelper
{
    //=================================index================================
    public static function DataSewa()
     {
          // some logic to determine if the publisher is main
        return DB::table('sewa as s')
            ->select('s.*')
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status','like','%MENUNGGU UANG JALAN%')
            ->get();
     }
    //=================================create,edit??================================
    
    public static function DataJO()
     {
          // some logic to determine if the publisher is main
        return DB::table('job_order as jo')
            ->select('jo.*')
            ->where('jo.is_aktif', '=', "Y")
            ->where('jo.status', 'like', "%DALAM PENGIRIMAN%")
            ->get();
     }
     public static function DataCustomer()
     {
        return  DB::table('customer')
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
     public static function DataBooking()
     {
        return DB::table('booking as b')
            ->select('*','b.id as idBooking')
            ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
            ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
            ->where('b.is_aktif', "Y")
            ->orderBy('tgl_booking')
            ->whereNull('b.id_jo_detail')
            ->get();
     }
    public static function DataChassis()
     {
        return DB::table('chassis as c')
            ->select('*')
            ->where('c.is_aktif', "Y")
            ->get();
        ;
     }
     // Your new helper method
     public static function DataKendaraan()
     {
        return DB::table('kendaraan AS k')
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
     }
    //=================================create,edit??================================

    // =========================================API=====================================
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

     

}
?>