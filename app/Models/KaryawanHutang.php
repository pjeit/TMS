<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanHutang extends Model
{
    use HasFactory;
    protected $table = 'karyawan_hutang';
    protected $primaryKey='id';
    protected $fillable=[
        'id_karyawan',
        'total_hutang',
        'is_aktif',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
