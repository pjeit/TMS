<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'booking';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'id_jo',
       'id_jo_detail',
       'no_booking',
       'tgl_berangkat',
       'id_customer',
       'id_grup_tujuan',
       'no_kontainer',
       'catatan',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];
}
