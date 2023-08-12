<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupTujuanBiaya extends Model
{
    use HasFactory;
    protected $table = 'grup_tujuan_biaya';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'grup_id',
       'grup_tujuan_id',
       'deskripsi',
       'biaya',
       'catatan',

       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];
}
