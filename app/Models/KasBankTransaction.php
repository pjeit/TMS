<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasBankTransaction extends Model
{
    use HasFactory;
    protected $table = 'kas_bank_transaction';
    protected $primaryKey = 'id';
}
