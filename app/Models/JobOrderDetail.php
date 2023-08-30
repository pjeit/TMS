<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderDetail extends Model
{
    use HasFactory;
    protected $table = 'job_order_detail';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'id_jo',
        'id_booking',
        'tgl_booking',
        'no_kontainer',
        'seal',
        'seal_pje',
        'id_kendaraan',
        'nopol_kendaraan',
        'id_grup_tujuan',
        'tgl_dooring',
        'storage',
        'demurage',
        'detention',
        'repair_washing',
        'thc_tipe',
        'thc',
        'lolo',
        'apbs',
        'cleaning',
        'docfee',
        'tipe_kontainer',
        'status',
        'do_expaired',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
