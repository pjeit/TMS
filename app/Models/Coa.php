<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;
    protected $table = 'coa';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'no_akun',
       'nama_jenis',
       'tipe',
       'jenis_laporan_keuangan',
       'catatan',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_hapus',
   ];
}
