<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanRekananPembayaran extends Model
{
    use HasFactory;
    protected $table = 'tagihan_rekanan_pembayaran';
    
    public function getSupplier()
    {
         return $this->hasOne(Supplier::class, 'id', 'id_supplier');
    }
    
    public function getRekanan()
    {
         return $this->hasMany(TagihanRekanan::class, 'id_pembayaran', 'id')->where('is_aktif', 'Y');
    }

    public function getRekananDetail()
    {
         return $this->hasMany(TagihanRekananDetail::class, 'id_tagihan_rekanan', 'id_tagihan_rekanan')->where('is_aktif', 'Y');
    }
}
