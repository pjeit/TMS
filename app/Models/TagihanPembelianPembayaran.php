<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPembelianPembayaran extends Model
{
    use HasFactory;
    protected $table = 'tagihan_pembelian_pembayaran';
    
    public function getSupplier()
    {
         return $this->hasOne(Supplier::class, 'id', 'id_supplier');
    }
    
    public function getPembelian()
    {
     return $this->hasMany(TagihanPembelian::class, 'id_pembayaran', 'id')->where('is_aktif', 'Y')->orderBy('biaya_admin','DESC');
    }

    public function getPembelianDetail()
    {
         return $this->hasMany(TagihanPembelianDetail::class, 'id_tagihan_pembelian', 'id_tagihan_pembelian')->where('is_aktif', 'Y');
    }

    public function get_nota_pembayaran_detail()
    {
         return $this->hasMany(TagihanPembelianPembayaranDetail::class, 'id_tagihan_pembayaran', 'id')
         ->with('get_nota_value')
         ->where('is_aktif','Y')
         ->orderBy('biaya_admin','DESC');
    }
}
