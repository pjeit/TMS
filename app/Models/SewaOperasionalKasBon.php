<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaOperasionalKasBon extends Model
{
    use HasFactory;
    protected $table = 'sewa_operasional_kasbon_transaksi';
    protected $primaryKey='id';
}
