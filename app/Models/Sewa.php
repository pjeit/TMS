<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sewa extends Model
{
     use HasFactory;
     protected $table = 'sewa';
     protected $primaryKey='id_sewa';
     protected $fillable = [
          'id_supplier',
          'no_sewa',
          'id_booking',
          'id_jo',
          'id_jo_detail',
          'id_customer',
          'id_grup_tujuan',
          'jenis_tujuan',
          'status',
          'tanggal_berangkat',
          'nama_tujuan',
          'alamat_tujuan',
          'jumlah_muatan',
          'kargo',
          'jenis_order',
          'tipe_kontainer',
          'total_tarif',
          'total_uang_jalan',
          'harga_jual',
          'total_komisi',
          'status_pencairan_customer',
          'tanggal_pencairan_customer',
          'total_komisi_driver',
          'status_pencairan_driver',
          'tanggal_pencairan_driver',
          'id_kendaraan',
          'no_polisi',
          'id_chassis',
          'karoseri',
          'id_karyawan',
          'nama_driver',
          'catatan',
          'is_kembali',
          'tanggal_kembali',
          'no_kontainer',
          'no_surat_jalan',
          'seal_pelayaran',
          'seal_pje',
          'stack_tl',
          'buruh_pje',
          'alasan_hapus',
          'is_tagihan',
          'created_at',
          'created_by',
          'updated_at',
          'updated_by',
          'is_aktif',
          ];


     public function getJOD(): HasOne
     {
          return $this->hasOne(JobOrderDetail::class, 'id', 'id_jo_detail')
                         ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 'job_order_detail.id_grup_tujuan'); // Assuming 'grup_tujuan' is the name of the relation in JobOrderDetail model
     }
     
     public function getTujuan(): HasOne
     {
          return $this->hasOne(GrupTujuan::class, 'id', 'id_grup_tujuan');
     }

     public function getSewaBiaya(): HasOne
     {
          return $this->hasOne(SewaBiaya::class, 'id_sewa', 'id_sewa');
     }

     public function getSewaBiayaAddTL()
     {
          return $this->hasOne(SewaBiaya::class, 'id_sewa', 'id_sewa')->where('deskripsi', 'TL')->where('is_aktif', 'Y');
     }

     public function getSewaBiayaReturnTL()
     {
          return $this->hasOne(SewaBiaya::class, 'id_sewa', 'id_sewa')->where('deskripsi', 'TL')->where('is_aktif', 'Y');
     }

     public function getBatalCancel(): HasOne
     {
          return $this->hasOne(SewaBatalCancel::class, 'id_sewa', 'id_sewa')->where('is_aktif', 'Y')->where('jenis', 'BATAL'); // id db sewa, id db sewadi batal cancel
     }

     public function getCustomer()
     {
          return $this->hasOne(Customer::class, 'id', 'id_customer');
     }

     public function getUJRiwayat()
     {
          return $this->hasMany(UangJalanRiwayat::class, 'sewa_id', 'id_sewa')->where('is_aktif', 'Y')->orderBy('id', 'DESC');
     }

     public function getKaryawan()
     {
          return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
     }

     public function getSupplier()
     {
          return $this->hasOne(Supplier::class, 'id', 'id_supplier');
     }

     // eloquent relation
     public function sewaOperasional()
     {
          return $this->hasMany(SewaOperasional::class, 'id_sewa', 'id_sewa')
          ->where('is_aktif', 'Y');
     //    ->where('is_ditagihkan', 'Y')
     //    ->where('is_dipisahkan', 'N');
          
     }   

     public function sewaOperasionaSales()
     {
          return $this->hasMany(SewaOperasional::class, 'id_sewa', 'id_sewa')
          ->where('is_aktif', 'Y')
          ->where('is_ditagihkan','<>' ,'N');
     }   

     public function sewaOperasionalPisah()
     {
          return $this->hasMany(SewaOperasional::class, 'id_sewa', 'id_sewa')
          ->where('is_aktif', 'Y')
          ->where('is_ditagihkan', 'Y')
          ->where('is_dipisahkan', 'Y');
     }   

     public function customer()
     {
          return $this->belongsTo(Customer::class, 'id', 'id_customer');
     }   

     public function revisiUJ(): HasOne
     {
          return $this->hasOne(GrupTujuan::class, 'id', 'id_grup_tujuan')
                         ->where('is_aktif', 'Y');
     }
}
