<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceResiDetail extends Model
{
    use HasFactory;
    protected $table = 'invoice_resi_detail';
    protected $primaryKey = 'id';
    public function get_invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'id_invoice')->where('is_aktif', 'Y');
    }
}
