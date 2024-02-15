<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDana extends Model
{
    use HasFactory;
     protected $table = 'kas_bank_transfer';
    protected $primaryKey='id';
    protected $fillable=[
        'tanggal',
        'kas_bank_id_dari',
        'kas_bank_id_ke',
        'total',
        'catatan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
