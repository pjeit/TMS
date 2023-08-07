<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_ModelChassis extends Model
{
    use HasFactory;
    protected $table = 'm_model_chassis';
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
