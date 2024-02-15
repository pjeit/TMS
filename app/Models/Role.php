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
        'name',
        'guard_name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
}
