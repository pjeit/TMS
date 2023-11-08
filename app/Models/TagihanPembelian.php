<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPembelian extends Model
{
    use HasFactory;
    protected $table = 'tagihan_pembelian';

    public function getSupplier()
    {
         return $this->hasOne(Supplier::class, 'id', 'id_supplier');
    }

    public function getDetails()
    {
         return $this->hasMany(TagihanPembelianDetail::class, 'id_tagihan_pembelian', 'id')->where('is_aktif', 'Y');
    }
}