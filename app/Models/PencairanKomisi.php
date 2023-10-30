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
       'jenis_pencairan',
       'id_driver',
       'id_customer',
       'total_komisi',
       'total_pencairan',
       'created_at',
       'created_at',
       'updated_at',
       'updated_by',
   ];
}
