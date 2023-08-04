<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_Kas extends Model
{
    use HasFactory;
    protected $table = 'm_kas';
    // protected $primaryKey='id';
    protected $fillable=[
        'nama',
    ];
}
