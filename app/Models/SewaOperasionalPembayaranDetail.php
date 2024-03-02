<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaOperasionalPembayaranDetail extends Model
{
    use HasFactory;
    protected $table = 'sewa_operasional_pembayaran_detail';
    protected $primaryKey='id';


    public function getSewaDetail()
    {
        return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa');
    }

}
