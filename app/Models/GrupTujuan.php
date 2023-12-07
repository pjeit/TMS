<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupTujuan extends Model
{
    use HasFactory;
    protected $table = 'grup_tujuan';
    protected $primaryKey='id';
    protected $fillable=[
       'id',
       'grup_id',
       'marketing_id',
       'nama_tujuan',
       'alamat',
       'jenis_tujuan',
       'harga_per_kg',
       'min_muatan',
       'uang_jalan',
       'tarif',
       'komisi',
       'catatan',

       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
       'is_aktif',
   ];

   public function getGrup()
   {
        return $this->hasOne(Grup::class, 'id', 'grup_id');
   }

   public function getMarketing()
   {
        return $this->hasOne(Marketing::class, 'id', 'marketing_id');
   }
}
