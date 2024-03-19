<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceResi extends Model
{
    use HasFactory;
    protected $table = 'invoice_resi';
    protected $primaryKey = 'id';

    public function get_invoice_resi_detail()
    {
        return $this->hasMany(InvoiceResiDetail::class, 'id_resi', 'id')->where('is_aktif', 'Y');
    }
}
