<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permissions extends Model
{
    use HasFactory;
    protected $table = 'permissions'; 

    public function permissions($menu, $role)
    {
        $data =  DB::table('permissions AS p')
                    ->where('p.is_aktif', 'Y')
                    ->where('p.menu', $menu)
                    ->leftJoin('role_has_permissions as rhp', function($join) use($role){
                        $join->on('rhp.permission_id', '=', 'p.id')
                            ->where('rhp.role_id', $role);
                    })
                    ->orderBy('p.name','ASC')
                    ->get();

        return $data;
    }

    public function isPermission($menu)
    {
        $data = Permissions::where("permissions.is_aktif", 'Y')
                            ->leftJoin('role_has_permissions as rhp', 'rhp.permission_id', '=', 'permissions.id')
                            ->select('*')
                            ->where('menu', $menu)->get();
        return $data;
        // return $this->hasMany(Permissions::class, 'menu', 'menu')->where('is_aktif', 'Y');
    }
}
