<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LemburMekanik extends Model
{
    use HasFactory;

    protected $table = 'lembur_mekanik';
    protected $primaryKey='id';
    protected $fillable=[
        'id_karyawan',
        'id_kendaraan',
        'tanggal_lembur',
        'jam_mulai_lembur',
        'jam_akhir_lembur',
        'nominal_lembur',
        'jenis_lembur',
        'status',
        'keterangan',
        'foto_lembur',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan'); // id dari karyawan, id_karyawan dr lembur_mekanik
    }
    public function kendaraan()
    {
        return $this->hasOne(Head::class, 'id', 'id_kendaraan'); // id dari kendaraan, id_karyawan dr lembur_mekanik
    }
    public function lemburRiwayat()
    {
        return $this->hasOne(LemburMekanikRiwayat::class, 'id_lembur_mekanik', 'id'); // id dari karyawan, id_karyawan dr lembur_mekanik
    }
}
