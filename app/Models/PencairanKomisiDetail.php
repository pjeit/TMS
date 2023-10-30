<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencairanKomisiDetail extends Model
{
    use HasFactory;
    protected $table = 'pencairan_komisi_detail';
    protected $primaryKey='id';
    protected $fillable=[
       'id_pencairan_komisi',
       'id_sewa',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
   ];
}
