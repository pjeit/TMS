<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UangJalanRiwayat extends Model
{
    use HasFactory;
    protected $primaryKey='id';
    protected $table = 'uang_jalan_riwayat';

    protected $fillable=[
        'id',
        'tanggal',
        'tanggal_pencatatan',
        'sewa_id',
        'total_uang_jalan',
        'total_tl',
        // 'total_uang_jalan_tl',
        'potong_hutang',
        // 'total_diterima',
        'kas_bank_id',
        'catatan',   
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];

   public function get_kas_uj()
   {
        return $this->hasOne(KasBank::class, 'id', 'kas_bank_id')->where('is_aktif', 'Y');

   }
}
