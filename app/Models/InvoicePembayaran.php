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
  
    public function getInvoices()
    {
         return $this->hasMany(Invoice::class, 'id_pembayaran', 'id');
    }
    public function getInvoices_revisi()
    {
         return $this->hasMany(Invoice::class, 'id_pembayaran', 'id')->orderBy('biaya_admin','DESC');
    }

}
