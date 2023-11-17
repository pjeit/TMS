<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\JobOrder;

class CoaHelper
{
    //=================================index================================
  
   public static function DataCoa($no_akun_variable)
   {
      // some logic to determine if the publisher is main
      // $dataCOA = DB::table('coa')
      //    // ->paginate(10);
      //    ->select('coa.*')
      //    ->where('coa.is_aktif', '=', "Y")
      //    // ->paginate(10);
      //    ->get();

      // //kalo komisi customer/ driver,coanya tergantung kasbanknya apa yang dikeluarin
      // $coaTampungan = [
      //    'coa_operasional_pelayaran' => Co5003)/*$dataCOA[81]->no_akun*/, //5003 (untuk pembayaran jo,sdt,karantina)
      //    'coa_pembayaran_gaji' => $dataCOA[92]->no_akun, //5021  Beban Gaji Pegawai
      //    'coa_pembayaran_invoice' => $dataCOA[8]->no_akun, // 1100 piutang usaha
      //    'coa_pemutihan_invoice' => $dataCOA[132]->no_akun, // 7004 selisih pembulatan
      //    'coa_biaya_operasional_alat_tally_buruh' => $dataCOA[85]->no_akun, // 5007 Biaya Alat, Krani (tally) dan Buruh
      //    'coa_pencairan_uj' => $dataCOA[80]->no_akun,//5002  Biaya Sopir ( Uang Sangu )
      // ];
      // coa tagihan (yang kita bayar itu (tagihan pembelian, sama tagihan pembayaran)),
      //  kalo notanya bulan ini dibayar bulan ini maka coanya biaya (yang 5000 an), 
      //  kalo dibayar bulan depan ya yag 2010 utang usaha

      $dataCOA = DB::table('coa')
         // ->paginate(10);
         ->select('coa.*')
         ->where('coa.is_aktif', '=', "Y")
         // ->paginate(10);
         ->where('coa.no_akun', $no_akun_variable)
         ->first();
      if ($dataCOA) {
            return $dataCOA->no_akun;
      } else {
            return 0000;
      }
      //aksesnya misal
      
   }
   public static function DataCoaBank($idBank)
   {
      $dataCOA = DB::table('coa')
         // ->paginate(10);
         ->select('coa.*')
         ->where('coa.is_aktif', '=', "Y")
         // ->paginate(10);
         ->get();

      //kalo komisi customer/ driver,coanya tergantung kasbanknya apa yang dikeluarin
      $coaBank= [
         1 => $dataCOA[81]->no_akun, // KAS BESAR[BCA]
         2 => $dataCOA[81]->no_akun, // KAS KECIL
         3 => $dataCOA[81]->no_akun, // BANK MAYAPADA
         4 => $dataCOA[81]->no_akun, // KAS BESAR[MANDIRI]
      ];
      return $coaBank[$idBank];
      //aksesnya misal
      
   }

}
?>