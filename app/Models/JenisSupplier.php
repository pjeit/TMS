<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSupplier extends Model
{
    use HasFactory;
    protected $table = 'jenis_supplier';
    protected $primaryKey='id';
    protected $fillable=[
        'id',
        'nama',
        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_hapus',
   ];
}
