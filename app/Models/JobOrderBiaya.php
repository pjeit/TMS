<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderBiaya extends Model
{
    use HasFactory;
    protected $table = 'job_order_biaya';
    protected $primaryKey='id';
    protected $fillable=[
    'id',
    'id_jo',
    'deskripsi',
    'biaya',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at',
    'is_aktif',
    ];
}
