<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    protected $primaryKey = 'id';

    public function getBillingTo()
    {
         return $this->hasOne(Customer::class, 'id', 'billing_to');
    }

    // eloquent relation
    public function invoiceDetails()
    {
    return $this->hasMany(invoiceDetail::class, 'id_invoice', 'id');
    }   

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'id_customer');
    }   

}
