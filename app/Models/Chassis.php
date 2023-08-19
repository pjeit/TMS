<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chassis extends Model
{
    use HasFactory;
    protected $table = 'chassis';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'kode',
       'karoseri',
       'model_id',
       'taun_buat',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];
}
