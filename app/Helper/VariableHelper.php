<?php 
namespace App\Helper;
use DateTime;
use App\Models\Role;
use App\Models\JenisSupplier;
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
     public static function Role_id($nama_role)
     {
         // some logic to determine if the publisher is main
         try {
            //code...
            $data_role= Role::where('is_aktif','Y')->where('name','like','%'.$nama_role.'%')->first();
            return ($data_role->id); 
         
        } catch (\Throwable $th) {
            //throw $th;
            return ('Tidak ada data role error!'.$th->getMessage()); 
         }
     }
     public static function Jenis_supplier_id($jenis)
     {
         // some logic to determine if the publisher is main
         try {
            //code...
            $data_jenis= JenisSupplier::where('is_aktif','Y')->where('nama','like','%'.$jenis.'%')->first();
            return ($data_jenis->id); 
         
        } catch (\Throwable $th) {
            //throw $th;
            return ('Tidak ada data supplier error!'.$th->getMessage()); 
         }
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