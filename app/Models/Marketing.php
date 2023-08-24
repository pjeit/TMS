<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    use HasFactory;
    protected $table = 'grup_member';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'grup_id',
        'role_id',
        'nama',
        'no_rek',
        'atas_nama',
        'bank',
        'cabang',
        'telp1',
        'telp2',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];
}
