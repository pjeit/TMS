<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class SewaOperasional extends Model
{
    use HasFactory;
    protected $table = 'sewa_operasional';
    protected $primaryKey='id';

    public function getSewa()
    {
         return $this->hasOne(Sewa::class, 'id_sewa', 'id_sewa');
    }
    public function getSewas()
    {
        return $this->hasMany(Sewa::class, 'id_sewa', 'id_sewa');
    }

     public function invoiceDetailsAddCostOperasional()
    {
        return $this->hasMany(InvoiceDetailAddcost::class, 'id_sewa_operasional', 'id'); //id target, id sendiri
    } 

    // eloquent
    public function sewa()
    {
        return $this->belongsTo(Sewa::class, 'id_sewa', 'id_sewa');
    }
}
