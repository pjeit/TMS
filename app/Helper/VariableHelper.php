<?php 
namespace App\Helper;

class VariableHelper
{
     // Your new helper method
     public static function TanggalFormat()
     {
         // some logic to determine if the publisher is main
         return (date("Y-m-d h:i:s")); 
     }

     public static function ShowTanggal()
     {
         // some logic to determine if the publisher is main
         return (date("dd-M-yyyy")); 
     }

}


?>