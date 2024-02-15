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
        return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa');
    }
    
    public function invoiceDetailsAddCost()
    {
        return $this->hasMany(InvoiceDetailAddcost::class, 'id_invoice_detail', 'id'); //id target, id sendiri
    } 
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id', 'id_invoice');  //id target, id sendiri
    }   

}
