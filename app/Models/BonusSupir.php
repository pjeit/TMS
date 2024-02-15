<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusSupir extends Model
{
    use HasFactory;
    protected $table = 'bonus_supir';
    protected $primaryKey='id';
    protected $fillable=[
        'id_karyawan',
        'tanggal_pencairan',
        'total_pencairan',
        'id_kas_bank',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
    public function karyawanIndex()
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
}
