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
    
    public function getCustomer()
    {
         return $this->hasOne(Customer::class, 'id', 'id_customer');
    }

    public function getKaryawan()
    {
         return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }


    // eloquent relation
    public function sewaOperasional()
    {
        return $this->hasMany(SewaOperasional::class, 'id_sewa', 'id_sewa');
    }   

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'id_customer');
    }   
}
