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
      'role_id',
      'nik',
      'nama_lengkap',
      'nama_panggilan',
      'jenis_kelamin',
      'status_menikah',
      'jumlah_anak',
      'tempat_lahir',
      'tanggal_lahir',
      'agama',
      'cabang_id',
      'alamat_ktp',
      'alamat_domisili',
      'kota_ktp',
      'telp1',
      'telp2',
      'email',
      'norek',
      'rek_nama',
      'bank',
      'cabang_bank',
      'tgl_gabung',
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
      'kota_domisili',
      'ptkp_id',
      'status_pegawai',
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

  public function cabang()
  {
       return $this->hasOne(CabangPJE::class, 'id', 'cabang_id');
  }
}
