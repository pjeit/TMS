<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlaimSupirRiawayat extends Model
{
    use HasFactory;
     protected $table = 'klaim_supir_riwayat';
    protected $primaryKey='id';
    protected $fillable=[
        'id_klaim',
        'kas_bank_id',
        'total_pencairan',
        'status_klaim',
        'keterangan_klaim',
        'catatan_pencairan',
        'tanggal_pencairan',
        'kas_bank_id',
        'alasan_tolak',
        'foto_nota',
        'foto_barang',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
