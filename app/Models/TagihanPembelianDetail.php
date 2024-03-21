<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPembelianDetail extends Model
{
    use HasFactory;
    protected $table = 'tagihan_pembelian_detail';
    public function getSewa()
    {
         return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa')->with('getCustomer');
    }
}
