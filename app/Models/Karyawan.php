<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $primaryKey='id';
    protected $fillable = [
      'nik',
      'nama_lengkap',
      'nama_panggilan',
      'jenis_kelamin',
      'status_menikah',
      'jumlah_anak',
      'tempat_lahir',
      'tanggal_lahir',
      'agama',
      'm_kota_id',
      'alamat_ktp',
      'alamat_domisili',
      'id_ptkp',
      'status_pegawai',
      'telp1',
      'telp2',
      'email',
      'norek',
      'rek_nama',
      'bank',
      'tgl_gabung',
      'role_id',
      'gaji',
      'is_keluar',
      'tgl_keluar',
      'tgl_mulai_kontrak',
      'tgl_selesai_kontrak',
      'foto',
      'nama_kontak_darurat',
      'hubungan_kontak_darurat',
      'nomor_kontak_darurat',
      'alamat_kontak_darurat',
      'saldo_cuti',
      'created_at',
      'created_by',
      'updated_at',
      'updated_by',
      'is_aktif',
  ];

  
  public function getHutang()
  {
       return $this->hasOne(KaryawanHutang::class, 'id_karyawan', 'id');
  }
}
