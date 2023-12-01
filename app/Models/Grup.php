<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    use HasFactory;
    protected $table = 'grup';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'nama_grup',
        'nama_pic',
        'email',
        'telp1',
        'telp2',
        'total_kredit',
        'total_max_kredit',

        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_aktif',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'grup_id', 'id');
    }
}
