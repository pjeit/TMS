<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLain extends Model
{
    use HasFactory;
    protected $table = 'kas_bank_lain';
    protected $primaryKey='id';
    protected $fillable=[
        'tanggal',
        'tanggal_catat',
        'coa_id',
        'kas_bank_id',
        'total',
        'catatan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
