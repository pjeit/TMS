<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasBank extends Model
{
    use HasFactory;
    protected $table = 'kas_bank';
    protected $primaryKey='id';
    protected $fillable=[
        'nama',
        'no_akun',
        'tipe',
        'saldo_awal',
        'tgl_saldo',
        'no_rek',
        'rek_nama',
        'bank',
        'cabang',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_hapus',
   ];
}
