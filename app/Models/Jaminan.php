<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jaminan extends Model
{
    use HasFactory;
    protected $table = 'jaminan';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'id_job_order',
        'nominal',
        'potongan_jaminan',
        'tgl_bayar',
        'tgl_kembali',
        'nominal_kembali',
        'alasan',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
