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
        'nama',
        'alamat',
        'kota_id',
        'telp',
        'email',
        'npwp',
        'no_rek',
        'rek_nama',
        'bank',
        'cabang',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_hapus',
   ];
}
