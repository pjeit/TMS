<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanHutangTransaction extends Model
{
    use HasFactory;
    protected $table = 'karyawan_hutang_transaction';
    protected $primaryKey='id';
    protected $fillable=[
        'id_karyawan',
        'refrensi_id',
        'refrensi_keterangan',
        'jenis',
        'tanggal',
        'debit',
        'kredit',
        'kas_bank_id',
        'catatan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
