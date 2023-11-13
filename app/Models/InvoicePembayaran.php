<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaran extends Model
{
    use HasFactory;
    protected $table = 'invoice_pembayaran';
    protected $primaryKey = 'id';

    public function getBillingTo()
    {
         return $this->hasOne(Customer::class, 'id', 'billing_to');
    }
  
    public function getInvoice()
    {
         return $this->hasOne(Invoice::class, 'id', 'id_invoice');
    }

}
