<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKarantinaPembayaran extends Model
{
    use HasFactory;
    protected $table = 'invoice_karantina_pembayaran';

    public function detail_invoice()
    {
        return $this->hasMany(InvoiceKarantina::class, 'id_pembayaran', 'id');
    }  
    public function billing_to_pembayaran()
    {
        return $this->hasOne(Customer::class, 'id', 'billing_to');
    }    

}
