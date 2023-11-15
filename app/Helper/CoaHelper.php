<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\JobOrder;

class CoaHelper
{
    //=================================index================================
    public static function DataCoa($jenisCoa)
     {
        // some logic to determine if the publisher is main
         $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();

         //kalo komisi customer/ driver,coanya tergantung kasbanknya apa yang dikeluarin
         $coaTampungan = [
            'coa_pembayaran_jo' => 'I', //5003 Beban Operasional Pelayaran
            'coa_pembayaran_sdt' => 'II', //5003 Beban Operasional Pelayaran
            'coa_pembayaran_gaji' => 'III', //5021  Beban Gaji Pegawai
            'coa_pembayaran_invoice' => 'IV', // 1100 piutang usaha
            'coa_pemutihan_invoice' => 'IV', // 7004 selisih pembulatan
            'coa_pencairan_komisi_customer' => 'V', // kalo komisi customer/ driver,coanya tergantung kasbanknya apa yang dikeluarin
            'coa_pencairan_komisi_driver' => 'VI', // kalo komisi customer/ driver,coanya tergantung kasbanknya apa yang dikeluarin
            'coa_biaya_operasional_karantina' => 'VII', //5003 Beban Operasional Pelayaran
            'coa_biaya_operasional_alat_tally_buruh' => 'VII', // 5007 Biaya Alat, Krani (tally) dan Buruh
            'coa_pencairan_uj' => 'VII',//5002  Biaya Sopir ( Uang Sangu )
        ];
        return $coaTampungan[$jenisCoa];
         //aksesnya misal
         
     }

}
?>