<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKarantinaDetail extends Model
{
    use HasFactory;
    protected $table = 'invoice_karantina_detail';

    public function getKarantina()
    {
        return $this->hasOne(Karantina::class, 'id', 'id_karantina');
    }  

    public function kontainers()
    {
        return $this->hasMany(InvoiceKarantinaDetailKontainer::class, 'id_invoice_k_detail', 'id');
    }  
}
