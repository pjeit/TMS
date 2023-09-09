<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKendaraan extends Model
{
    use HasFactory;
    protected $table = 'mutasi_kendaraan';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'asset_id',
        'cabang_id',
        'jenis',
        'catatan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
