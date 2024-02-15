<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemutihanInvoice extends Model
{
    use HasFactory;
    protected $table = 'pemutihan_invoice';
    protected $primaryKey='id';
    protected $fillable=[
        'invoice_id',
        'tanggal',
        'nominal_pemutihan',
        'catatan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
   ];

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id','id');
    }
}
