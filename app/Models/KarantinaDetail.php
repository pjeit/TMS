<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KarantinaDetail extends Model
{
    use HasFactory;
    protected $table = 'karantina_detail';

    public function getJOD()
    {
     return $this->hasOne(JobOrderDetail::class, 'id', 'id_jo_detail');
    }
 
}
