<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'user';

    protected $fillable = [
        'role_id',
        'karyawan_id',
        'customer_id',
        'username',
        'password',
        'created_by',
        'created_at',
        'updated_at',
        'updated_at',
        'is_aktif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRole(){
        $id = Auth::user()->role_id;
        $role = Role::where('is_aktif', 'Y')->find($id);

        $role = $role->name;
        return $role;
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }
}
