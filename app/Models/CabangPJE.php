<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabangPJE extends Model
{
    use HasFactory;
    protected $table = 'cabang_pje';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'nama',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];
}
