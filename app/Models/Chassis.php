<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chassis extends Model
{
    use HasFactory;
    protected $table = 'Chassis';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'chassis_id',
       'jenis_chassis_id',
       'nomor',
       'berlaku_hingga',
       'berlaku_hingga',
       'is_reminder',
       'reminder_hari',

       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_hapus',
   ];
}
