<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles'; // role tabel lama, hapus saja
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'grup_id',
        'role_id',
        'nama',
        'no_rek',
        'telp1',
        'telp2',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];
}
