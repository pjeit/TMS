<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'supplier';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'jenis_supplier_id',
        'nama',
        'alamat',
        'kota_id',
        'telp',
        'email',
        'npwp',
        'no_rek',
        'is_virtual_acc',
        'rek_nama',
        'bank',
        'cabang',
        'catatan',   
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
