<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderRiwayatBayar extends Model
{
    use HasFactory;
    protected $table = 'job_order_riwayat_bayar';
    protected $primaryKey='id';
    protected $fillable=[
        "id_jo"	,
        "id_kas",
        "tanggal_bayar",
        "total_thc"	,
        "total_lolo",	
        "total_apbs",
        "total_cleaning",	
        "total_docfee",	
        "total_jaminan",	
        "total_detail_biaya",	
        "total_pembayaran",	
        "created_at",	
        "created_by",	
        "updated_at",	
        "updated_by",	
        "is_aktif"	
    ];
}
