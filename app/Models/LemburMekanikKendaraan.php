<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LemburMekanikKendaraan extends Model
{
    use HasFactory;
    protected $table = 'lembur_mekanik_kendaraan';
    protected $primaryKey='id';
    protected $fillable=[
        'id_lembur_mekanik',
        'id_kendaraan',
        'no_pol',
        'foto_lembur',
        'keterangan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_aktif',
    ];
    public function kendaraan()
    {
        return $this->hasOne(Head::class, 'id', 'id_kendaraan'); // id dari kendaraan, id_karyawan dr lembur_mekanik
    }
}
