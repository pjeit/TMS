<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderPembayaran extends Model
{
    use HasFactory;
    protected $table = 'job_order_pembayaran';
    protected $primaryKey='id';
    protected $fillable=[
        "id_jo"	,
        "id_kas",
        "tanggal_bayar",
        "total_storage"	,
        "total_demurage",	
        "total_detention",
        "total_repair",	
        "total_washing",	
        "total_pembayaran",	
        "created_at",	
        "created_by",	
        "updated_at",	
        "updated_by",	
        "is_aktif"	
    ];
}
