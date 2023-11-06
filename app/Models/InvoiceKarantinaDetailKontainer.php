<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKarantinaDetailKontainer extends Model
{
    use HasFactory;
    protected $table = 'invoice_karantina_detail_kontainer';

    public function getJOD()
    {
        return $this->hasOne(JobOrderDetail::class, 'id', 'id_jo_detail');
    }
}
