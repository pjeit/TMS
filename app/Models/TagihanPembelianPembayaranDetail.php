<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPembelianPembayaranDetail extends Model
{
    use HasFactory;
    protected $table = 'tagihan_pembelian_pembayaran_detail';

    protected $primaryKey = 'id';
    
    public function get_nota_value()
    {
         return $this->hasOne(TagihanPembelian::class, 'id', 'id_tagihan')->with('getDetails')->where('is_aktif','Y');
    }

    public function get_nota_gabungan_value()
    {
         return $this->hasOne(TagihanPembelian::class, 'id', 'id_tagihan')->with('getDetailsGabungan')->where('is_aktif','Y');
    }
}
