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


    public function getJOD(): HasOne
    {
         return $this->hasOne(JobOrderDetail::class, 'id', 'id_jo_detail')
                     ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 'job_order_detail.id_grup_tujuan'); // Assuming 'grup_tujuan' is the name of the relation in JobOrderDetail model
    }
    public function getTujuan(): HasOne
    {
         return $this->hasOne(GrupTujuan::class, 'id', 'id_grup_tujuan');
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
