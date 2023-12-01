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
        'status_klaim',
        'keterangan_klaim',
        'foto_nota',
        'foto_barang',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }

    public function kendaraan()
    {
        return $this->hasOne(Head::class, 'id', 'kendaraan_id');
    }

    public function klaimRiwayat()
    {
        return $this->hasOne(KlaimSupirRiawayat::class, 'id_klaim', 'id');
    }
}
