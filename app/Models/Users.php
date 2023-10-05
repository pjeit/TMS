<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Users extends Model
{
    use HasFactory, HasRoles;
    protected $table = 'user';
    protected $primaryKey='id';
    protected $fillable=[
        'role_id',
        'karyawan_id',
        'username',
        'password',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];

    public function getRole(){
        // $role = Role::findOrFail($id);
        $role = 'xx';
        return $role;
    }
}
