<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanHutang extends Model
{
    use HasFactory;
    protected $table = 'karyawan_hutang';
    protected $primaryKey='id';
}
