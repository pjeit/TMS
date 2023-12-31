<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    use HasFactory;
    protected $table = 'kendaraan';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'id_kategori',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merk_model',
        'tahun_pembuatan',
        'warna',
        'driver_id',
        'supplier_id',
        'kepemilikan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
