<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlaimOperasional extends Model
{
    use HasFactory;
    protected $table = 'klaim_operasional';
    protected $primaryKey='id';
    protected $fillable=[
        'id_sewa',
        'id_karyawan',
        'id_kendaraan',
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
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
    public function sewa_klaim_ops()
    {
        return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa')->with('getCustomer');
    }
    public function kendaraan()
    {
        return $this->hasOne(Head::class, 'id', 'id_kendaraan');
    }

    public function klaimRiwayat()
    {
        return $this->hasOne(KlaimOperasionalRiwayat::class, 'id_klaim_operasional', 'id');
    }
}
