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
        "id_jo"	,
        "id_jo_detail"	,
        "storage"	,
        "demurage",	
        "detention"	,
        "repair",	
        "washing",	
        "status_bayar",	
        "catatan"	,
        "tgl_bayar",	
        "created_by",	
        "created_at",	
        "updated_by",	
        "updated_at",	
        "is_aktif"	
   ];

   	

}
