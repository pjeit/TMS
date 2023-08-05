<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_JenisDokumen extends Model
{
    use HasFactory;
    protected $table = 'm_jenis_dokumen';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'kendaraan_dokumen_id',
        'nama',

        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_hapus',
    ];
}
