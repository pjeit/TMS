<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'total_thc',
        'total_lolo',
        'total_apbs',
        'total_cleaning',
        'total_focfee',
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
}
