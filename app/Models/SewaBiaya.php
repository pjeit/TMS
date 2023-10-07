<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaBiaya extends Model
{
    use HasFactory;
    protected $table = 'sewa_biaya';
    protected $primaryKey='id_biaya';
}
