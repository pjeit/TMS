<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaOperasionalPembayaran extends Model
{
    use HasFactory;
    protected $table = 'sewa_operasional_pembayaran';
    protected $primaryKey ='id';

    public function getOperasional()
    {
        return $this->hasMany(SewaOperasional::class, 'id_pembayaran', 'id')->where('is_aktif', 'Y');
    }
    public function getOperasionalDetail()
    {
        return $this->hasMany(SewaOperasionalPembayaranDetail::class, 'id_pembayaran', 'id')->where('is_aktif', 'Y');
    }
}
