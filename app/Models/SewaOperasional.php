<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class SewaOperasional extends Eloquent
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

    // eloquent
    public function sewa()
    {
        return $this->belongsTo('sewa');
    }
}
