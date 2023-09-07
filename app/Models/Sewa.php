<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    use HasFactory;
    protected $table = 'sewa';
    protected $primaryKey='id_sewa';
    protected $fillable = [
        'id_sewa',
        'id_booking',
        'id_jo',
        'id_jo_detail',
        'status',
        'tanggal_status',
        'tanggal_berangkat',
        'id_customer',
        'idGrup_tujuan',
        'nama_tujuan',
        'alamat_tujuan',
        'jumlah_muatan',
        'kargo',
        'DO',
        'RO',
        'IER',
        'is_bongkar',
        'total_tarif',
        'total_uang_jalan',
        'total_komisi',
        'id_kendaraan',
        'no_pol',
        'id_chassis',
        // 'karoseri_chassis',
        'id_karyawan',
        // 'nama_supir',
        'catatan',
        'is_kembali',
        'tanggal_kembali',
        'no_kontainer',
        'no_surat_jalan',
        'no_segel',
        'no_segel_pje',
        'foto_kontainer',
        'foto_surat_jalan',
        'foto_segel_1',
        'foto_segel_2',
        'foto_segel_pje',
        'total_reimburse_dipisahkan',
        'total_reimburse_tidak_dipisahkan',
        'total_reimburse_aktual',
        'alasan_hapus',
        'is_aktif',
        'created_at', 
        'created_by',
        'updated_at',
        'updated_by',
    ];

}
