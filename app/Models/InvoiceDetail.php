<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    protected $table = 'invoice_detail';
    protected $primaryKey = 'id';

    public function sewa()
    {
         return $this->hasOne(sewa::class, 'id_sewa', 'id_sewa');
    }

}
