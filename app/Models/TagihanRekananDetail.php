<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanRekananDetail extends Model
{
    use HasFactory;
    protected $table = 'tagihan_rekanan_detail';


    public function getSewa()
    {
         return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa');
    }


}
