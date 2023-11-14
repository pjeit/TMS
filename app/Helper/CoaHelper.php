<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\JobOrder;

class SewaCoaHelper
{
    //=================================index================================
    public static function DataCoa($idCoa)
     {
        // some logic to determine if the publisher is main
         $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        return $dataCOA;
     }

}
?>