<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranGajiDetail extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_gaji_detail';
    protected $primaryKey='id';
    protected $fillable=[
        'pembayaran_gaji_id',
        'karyawan_id',
        'total_gaji',
        'potong_hutang',
        'pendapatan_lain',
        'potongan_lain',
        'total_diterima',
        'catatan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
   ];
}
