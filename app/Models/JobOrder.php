<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobOrder extends Model
{
    use HasFactory;
    protected $table = 'job_order';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'no_jo',
        'tgl_book',
        'id_customer',
        'id_supplier',
        'id_booking',
        'id_jaminan',
        'pelabuhan_muat',
        'pelabuhan_bongkar',
        'no_bl',
        'no_bl',
        'tgl_sandar',
        'free_time',
        'jo_expired',
        'thc',
        'lolo',
        'apbs',
        'cleaning',
        'foc_fee',
        'total_biaya_sebelum_dooring',
        'total_storage',
        'total_demurage',
        'total_detention',
        'total_repair_washing',
        'total_biaya_setelah_dooring',
        'status',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];

    //    public function getGrupId(){
    //         $idTuj = Customer::where('is_aktif', 'Y')->select('grup_id')->where('id', $id)->first();

    //         return $idTuj;
    //    }

     public function getDetails()
     {
          return $this->hasMany(JobOrderDetail::class, 'id_jo', 'id');
     }

     public function getGrupId()
     {
          return $this->hasOne(Customer::class, 'id', 'id_customer');
     }

     public function jaminan()
     {
          return $this->hasOne(Jaminan::class, 'id_job_order', 'id');
     }

     public function getCustomer()
     {
          return $this->hasOne(Customer::class, 'id', 'id_customer');
     }

     public function getSupplier()
     {
          return $this->hasOne(Supplier::class, 'id', 'id_supplier');
     }
     
     public function getKodeCustomer()
     {
          return $this->hasOne(Customer::class, 'id', 'id_customer');
     }
     
     public function hasSewa()
     {
          return $this->hasOne(Sewa::class, 'id', 'id_customer');
     }



}
