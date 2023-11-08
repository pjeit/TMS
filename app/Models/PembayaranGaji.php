<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranGaji extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_gaji';
    protected $primaryKey='id';
    protected $fillable=[
        'tanggal',
        'tanggal_catat',
        'tahun_periode',
        'bulan_periode',
        'nama_periode',
        'total',
        'kas_bank_id',
        'catatan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
