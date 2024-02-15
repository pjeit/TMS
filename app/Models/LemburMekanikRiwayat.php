<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LemburMekanikRiwayat extends Model
{
    use HasFactory;
    protected $table = 'lembur_mekanik_riwayat';
    protected $primaryKey='id';
    protected $fillable=[
        'id_lembur_mekanik',
        'id_kas_bank',
        'tanggal_pencairan',
        'total_lembur',
        'total_pencairan',
        'catatan_pencairan',
        'alasan_tolak',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
}
