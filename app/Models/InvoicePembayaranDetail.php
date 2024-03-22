<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranDetail extends Model
{
    use HasFactory;
    protected $table = 'invoice_pembayaran_detail';
    protected $primaryKey = 'id';
    
    public function get_invoice_value()
    {
         return $this->hasOne(Invoice::class, 'id', 'id_invoice')->with('invoiceDetails')->where('is_aktif','Y');
    }
}
