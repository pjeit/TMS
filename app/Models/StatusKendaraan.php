<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKendaraan extends Model
{
    use HasFactory;
    protected $table = 'status_kendaraan';
    protected $primaryKey='id';
    protected $fillable=[
        'kendaraan_id',
        'tanggal_mulai',
        'is_selesai',
        'tanggal_selesai',
        'detail_perawatan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
   ];
}
