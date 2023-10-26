<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlaimSupir extends Model
{
    use HasFactory;
    protected $table = 'klaim_supir';
    protected $primaryKey='id';
    protected $fillable=[
        'karyawan_id',
        'kendaraan_id',
        'tanggal_klaim',
        'jenis_klaim',
        'total_klaim',
        'total_pencairan',
        'status_klaim',
        'keterangan_klaim',
        'catatan_pencairan',
        'tanggal_pencairan',
        'kas_bank',
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
