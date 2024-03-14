<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranDokumenKendaraan extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_dokumen_kendaraan';
    protected $primaryKey='id';

    public function kas_dokumen_bayar()
    {
         return $this->hasOne(KasBank::class,  'id','id_kas_bank');
    }
    public function pembayaran_dokumen_detail()
    {
         return $this->hasMany(PembayaranDokumenKendaraanDetail::class,  'id_pembayaran_kendaraan','id');
    }
    
}
