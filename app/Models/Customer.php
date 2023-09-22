<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Customer extends Eloquent
{
    use HasFactory;
    protected $table = 'customer';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'grup_id',
        'kode',
        'nama',
        'npwp',
        'alamat',
        'kota_id',
        'telp1',
        'telp2',
        'email',
        'catatan',
        'kredit_sekarang',
        'max_kredit',
        'ketentuan_bayar',
        'nama_pic',
        'email_pic',
        'telp1_pic',
        'telp2_pic',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];

    public function getGrup()
    {
         return $this->hasOne(Grup::class, 'id', 'grup_id');
    }

    // eloquent
    public function sewa()
    {
        return $this->belongsTo(Sewa::class);
    }
}
