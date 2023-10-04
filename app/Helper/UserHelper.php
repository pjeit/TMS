<?php 
namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserHelper
{
     // Your new helper method
     public static function getCabang()
     {
        $id_user = Auth::user()->id; 
        $id_role = Auth::user()->role_id; 

        $user = User::where('is_aktif', 'Y')->findOrFail(Auth::user()->id);
        
        if($user){
            return $user->karyawan->cabang_id; 
        }else{
            return "Data not found!";
        }
     }
}

?>