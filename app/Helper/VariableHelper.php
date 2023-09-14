<?php 
namespace App\Helper;
use DateTime;
class VariableHelper
{
     // Your new helper method
     public static function TanggalFormat()
     {
         // some logic to determine if the publisher is main
         date_default_timezone_set('Asia/Jakarta');
         $now = new DateTime();
         return ( $now->format('Y-m-d H:i:s')); 
     }

     public static function ShowTanggal()
     {
         // some logic to determine if the publisher is main
         return (date("dd-M-yyyy")); 
     }

     function bulanKeRomawi($bulan) {
        $romawi = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Pastikan bulan yang dimasukkan valid (antara 1 hingga 12)
        if ($bulan >= 1 && $bulan <= 12) {
            return $romawi[$bulan];
        } else {
            return '──';
        }
    }

}


?>