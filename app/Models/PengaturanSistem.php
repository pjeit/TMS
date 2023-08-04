<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_sistem';
    // protected $primaryKey='id';
    protected $fillable=[
       'uang_jajan',
       'reimburse',
       'penerimaan_customer',
       'pembayaran_supplier',
       'pembayaran_gaji',
       'hutang_karyawan',
       'klaim_supir',
       'batas_pemutihan',
       'updated_at',
       'updated_by',
   ];
}
