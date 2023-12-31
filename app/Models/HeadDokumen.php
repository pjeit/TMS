<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadDokumen extends Model
{
    use HasFactory;
    protected $table = 'kendaraan_dokumen';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'kendaraan_id',
        'nomor',
        'jenis',
        'berlaku_hingga',
        'is_reminder',
        'reminder_hari',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
