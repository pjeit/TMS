<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaBatalCancel extends Model
{
    use HasFactory;
    protected $table = 'sewa_batal_cancel';
    protected $primaryKey='id';
    protected $fillable=[
        'id_sewa',
        'jenis',
        'tgl_batal_muat_cancel',
        'total_tarif_ditagihkan',
        'total_uang_jalan_kembali',
        'id_kas_bank',
        'total_uang_jalan_kembali_hutang',
        'id_karyawan_hutang',
        'tgl_kembali',
        'alasan_batal',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
}
