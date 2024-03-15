<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class Head extends Model
{
    use HasFactory;
    protected $table = 'kendaraan';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'id_kategori',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merk_model',
        'tahun_pembuatan',
        'warna',
        'driver_id',
        'supplier_id',
        'kepemilikan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
   public function get_sewa_dashboard()
   {
        return $this->hasMany(Sewa::class, 'id_kendaraan', 'id');
   }
   public function get_driver_dashboard()
   {
        return $this->hasOne(Karyawan::class, 'id', 'driver_id');
   }
   public function get_maintenance_dashboard()
   {
        return $this->hasOne(StatusKendaraan::class, 'kendaraan_id', 'id');
   }
}
