<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderDetailBiaya extends Model
{
    use HasFactory;
    protected $table = 'job_order_detail_biaya';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'id_jo',
        'id_jo_detail',
        'keterangan',
        'nominal',
        'status_bayar',
        'tgl_bayar',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
