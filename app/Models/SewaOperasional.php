<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaOperasional extends Model
{
    use HasFactory;
    protected $table = 'sewa_operasional';
    protected $primaryKey='id';

    public function getSewa()
    {
         return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa');
    }
}