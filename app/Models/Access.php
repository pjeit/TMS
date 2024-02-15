<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;
    protected $table = 'role_has_permissions';
    protected $fillable=[
        'permission_id',
        'role_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];

    public function permission()
    {
        return $this->hasOne(Permissions::class, 'id', 'permission_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
