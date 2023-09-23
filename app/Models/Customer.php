<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';

    public function getGrup()
    {
         return $this->hasOne(Grup::class, 'id', 'grup_id');
    }

    // eloquent
    public function sewa()
    {
        return $this->hasMany(Sewa::class, 'id_customer', 'id');
    }
}
