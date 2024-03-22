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
    public function get_invoice_resi()
    {
        return $this->hasOne(InvoiceResiDetail::class, 'id_invoice', 'id')->where('is_aktif', 'Y');
    }
    // eloquent relation
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'id_invoice', 'id')->where('is_aktif', 'Y'); //id target, id sendiri
    }   

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'id_customer');
    }   

    public function getGroup()
    {
        return $this->hasOne(Grup::class, 'id', 'id_grup');
    }   

    //buat pengecekan revisi invoice
    public function get_invoice_pembayaran_detail()
    {
        return $this->hasOne(InvoicePembayaranDetail::class, 'id_invoice', 'id');
    }   

}
