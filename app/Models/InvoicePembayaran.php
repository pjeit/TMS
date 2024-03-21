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
  
//     ga dipake
    public function getInvoices()
    {
         return $this->hasMany(Invoice::class, 'id_pembayaran', 'id');
    }
    public function getInvoices_revisi()
    {
         return $this->hasMany(Invoice::class, 'id_pembayaran', 'id')->orderBy('biaya_admin','DESC');
    }
//     ga dipake
    public function get_pembayaran_detail()
    {
         return $this->hasMany(InvoicePembayaranDetail::class, 'id_pembayaran', 'id')
         ->with('get_invoice_value')
         ->orderBy('biaya_admin','DESC');
    }


}
