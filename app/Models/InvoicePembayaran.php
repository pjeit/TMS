<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaran extends Model
{
    use HasFactory;
    protected $table = 'invoice_pembayaran';
    protected $primaryKey = 'id';

}
