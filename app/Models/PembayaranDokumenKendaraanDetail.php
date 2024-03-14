<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranDokumenKendaraanDetail extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_dokumen_kendaraan_detail';
    protected $primaryKey='id';
}
