<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlaimOperasionalRiwayat extends Model
{
    use HasFactory;
    protected $table = 'klaim_supir_riwayat';
    protected $primaryKey='id';
    protected $fillable=[
        'id_klaim_operasional',
        'id_kas_bank',
        'tanggal_pencairan',
        'total_klaim',
        'total_pencairan',
        'catatan_pencairan',
        'alasan_tolak',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
