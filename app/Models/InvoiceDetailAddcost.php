<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetailAddcost extends Model
{
    use HasFactory;
    protected $table = 'invoice_detail_addcost';
    protected $primaryKey = 'id';

    public function invoiceDetailDariAddcost()
    {
        return $this->belongsTo(InvoiceDetail::class, 'id', 'id_invoice_detail');  //id target, id sendiri
    } 

    public function invoiceAddcost()
    {
        return $this->belongsTo(Invoice::class, 'id', 'id_invoice');  //id target, id sendiri
    } 

    public function sewaOperasionalDetail()
    {
        return $this->belongsTo(InvoiceDetail::class, 'id', 'id_sewa_operasional');  //id target, id sendiri
    } 


}
