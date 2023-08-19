<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PairKendaraan extends Model
{
    use HasFactory;
    protected $table = 'pair_kendaraan_chassis';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'kendaraan_id',
        'chassis_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
   ];
}
