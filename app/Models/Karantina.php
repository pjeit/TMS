<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karantina extends Model
{
    use HasFactory;
    protected $table = 'karantina';

    public function details(){
        return $this->hasMany(KarantinaDetail::class, 'id_karantina', 'id');
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, 'id', 'id_customer');
    }

    public function getJO()
    {
        return $this->hasOne(JobOrder::class, 'id', 'id_jo');
    }   
}
