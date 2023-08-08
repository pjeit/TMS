<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChassisDokumen extends Model
{
    use HasFactory;
    protected $table = 'chassis_dokumen';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'chassis_id',
       'jenis_chassis',
       'nomor',
       'berlaku_hingga',
       'is_reminder',
       'reminder_hari',

       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];
}
