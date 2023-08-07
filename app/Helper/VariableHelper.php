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
        $now->format('Y-m-d H:i:s');
         return (date("Y-m-d h:i:s")); 
     }

     public static function ShowTanggal()
     {
         // some logic to determine if the publisher is main
         return (date("dd-M-yyyy")); 
     }

}


?>