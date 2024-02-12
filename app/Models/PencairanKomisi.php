<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencairanKomisi extends Model
{
    use HasFactory;
    protected $table = 'pencairan_komisi';
    protected $primaryKey='id';
    protected $fillable=[
       'id_kas',
       'tanggal',
       'jenis_pencairan',
       'id_driver',
       'id_customer',
       'total_komisi',
       'total_pencairan',
       'created_by',
       'created_at',
       'updated_by',
       'updated_at',
   ];
}
