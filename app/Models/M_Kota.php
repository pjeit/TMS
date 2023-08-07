<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_Kota extends Model
{
    use HasFactory;
    protected $table = 'm_kota';
    protected $fillable=[
        'nama',
    ];
}
