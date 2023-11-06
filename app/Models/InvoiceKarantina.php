<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKarantina extends Model
{
    use HasFactory;
    protected $table = 'invoice_karantina';

    public function details()
    {
        return $this->hasMany(InvoiceKarantinaDetail::class, 'id_invoice_k', 'id');
    }   

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, 'id', 'id_customer');
    }   
}
