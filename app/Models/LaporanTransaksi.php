<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanTransaksi extends Model
{
    use HasFactory;
     protected $table = 'kas_bank_transaction';
    protected $primaryKey='id';
    protected $fillable=[
        'id_kas_bank',
        'tanggal',
        'debit',
        'kredit',
        'kode_coa',
        'keterangan_transaksi',
        'keterangan_kode_transaksi',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
   ];
}
