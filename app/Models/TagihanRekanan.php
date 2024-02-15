<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanRekanan extends Model
{
    use HasFactory;
    protected $table = 'tagihan_rekanan';

    public function getSupplier()
    {
         return $this->hasOne(Supplier::class, 'id', 'id_supplier');
    }

    public function getDetails()
    {
         return $this->hasMany(TagihanRekananDetail::class, 'id_tagihan_rekanan', 'id')->where('is_aktif', 'Y');
    }
}
