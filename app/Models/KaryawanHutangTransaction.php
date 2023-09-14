<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanHutangTransaction extends Model
{
    use HasFactory;
    protected $table = 'karyawan_hutang_transaction';
    protected $primaryKey='id';
}
