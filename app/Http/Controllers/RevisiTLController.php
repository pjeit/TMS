<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\Sewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Exception;
use App\Models\PengaturanKeuangan;
use App\Helper\SewaDataHelper;
use App\Models\SewaBiaya;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\SewaOperasional;
use App\Models\UangJalanRiwayat;

class RevisiTLController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('pages.revisi.revisi_tl.index',[
            'judul' => "ADD RETURN TL",
            'data' => $data,
        ]);
    }

    public function cair($id)
    {
        $pengaturan = PengaturanKeuangan::first();

        $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir', 'kh.total_hutang')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->leftJoin('karyawan_hutang AS kh', 'kh.id_karyawan', '=', 's.id_karyawan')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', "PROSES DOORING")
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', $id)
                    ->groupBy('c.id')
                    ->first();
                    
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.revisi.revisi_tl.cair',[
            'judul' => "Pencairan TL",
            'sewa' => $sewa,
            'jumlah' => $pengaturan[$sewa['stack_tl']],
            'dataKas' => $dataKas,
            'id_sewa' => $id,
        ]);
    }

    public function refund($id)
    {
        $pengaturan = PengaturanKeuangan::first();

        $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', "PROSES DOORING")
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', $id)
                    ->groupBy('c.id')
                    ->first();
        
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        $checkTL = SewaBiaya::where('is_aktif', 'Y')
                            ->where('deskripsi', 'TL')
                            ->where('id_sewa', $id)
                            ->first();

        return view('pages.revisi.revisi_tl.refund',[
            'judul' => "Pengembalian TL",
            'sewa' => $sewa,
            'jumlah' => $pengaturan[$sewa['stack_tl']],
            'dataKas' => $dataKas,
            'id_sewa' => $id,
            'checkTL'=>$checkTL,
            'id'=>$id
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save_cair(Request $request)
    {
        $user = Auth::user()->id;
        $data = $request->post();
        try {
            DB::table('sewa_biaya')
                ->insert(array(
                        'id_sewa' =>  $data['id_sewa'],
                        'deskripsi' => 'TL',
                        'biaya' => (float)str_replace(',', '', $data['jumlah']),
                        'catatan' => $data['stack_tl_hidden_value'],//value combobox
                        'created_at' => now(),
                        'created_by' => $user,
                        'is_aktif' => "Y",
                    )
                ); 
            $SOP = new SewaOperasional();
            $SOP->id_sewa = $data['id_sewa']; 
            $SOP->deskripsi = 'TL';
            $SOP->total_operasional = (float)str_replace(',', '', $data['jumlah']);
            $SOP->total_dicairkan = (float)str_replace(',', '', $data['total_diterima']);
            $SOP->tgl_dicairkan = now();
            $SOP->is_ditagihkan = 'N';
            $SOP->is_dipisahkan = 'N';
            $SOP->status = "SUDAH DICAIRKAN";
            $SOP->created_by = $user;
            $SOP->created_at = now();
            $SOP->is_aktif = 'Y';
            $SOP->save();

            $uang_jalan_riwayat = UangJalanRiwayat::where('is_aktif', 'Y')
                                    ->where('sewa_id', $data['id_sewa'])
                                    ->first();
            $uang_jalan_riwayat->total_tl += (float)str_replace(',', '', $data['jumlah']); 
            $uang_jalan_riwayat->potong_hutang += (isset($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0);
            $uang_jalan_riwayat->updated_at = now();
            $uang_jalan_riwayat->updated_by = $user;
            $uang_jalan_riwayat->save();

            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
            if(isset($kh) && isset($data['potong_hutang'])){
                $kh->total_hutang -=(float)str_replace(',', '', $data['potong_hutang']); 
                $kh->updated_by = $user;
                $kh->updated_at = now();
                $kh->save();

                $kht = new KaryawanHutangTransaction();
                $kht->id_karyawan = $data['id_karyawan'];
                $kht->refrensi_id = $uang_jalan_riwayat->id;
                $kht->refrensi_keterangan = "UANG JALAN RIWAYAT";
                $kht->catatan = 
                $data['catatan'] . ' #totalTL:' . (float)str_replace(',', '', $data['jumlah']) . 
                ' #potongHutang:' . (($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0) . 
                ' #totalDiterima:' . (float)str_replace(',', '', $data['total_diterima']);
                $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                $kht->tanggal = now();
                $kht->debit = 0;
                $kht->kredit = ($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0;
                $kht->kas_bank_id = NULL;
                $kht->created_by = $user;
                $kht->created_at = now();
                $kht->is_aktif = 'Y';
                $kht->save();
            }
                $catatan = isset($data['catatan'])? ' '.$data['catatan']:'';
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    date_create_from_format('d-M-Y', $data['tanggal_pencairan']),//tanggal
                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                    (float)str_replace(',', '', $data['total_diterima']), //uang keluar (kredit)
                    1016, //kode coa
                    'teluk_lamong',
                    'PENAMBAHAN TELUK LAMONG:'.$catatan.' #'.$data['no_sewa'].' #'.$data['kendaraan'].' #'.$data['driver'].' #'.$data['customer'].' #'.$data['tujuan'], //keterangan_transaksi
                    $uang_jalan_riwayat->id,//keterangan_kode_transaksi
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                ));
                
                $saldo = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->where('kas_bank.id', '=', $data['pembayaran'])
                    ->first();
    
                $saldo_baru = $saldo->saldo_sekarang - (float)str_replace(',', '', $data['total_diterima']);
                
                DB::table('kas_bank')
                    ->where('id', $data['pembayaran'])
                    ->update(array(
                        'saldo_sekarang' => $saldo_baru,
                        'updated_at'=> now(),
                        'updated_by'=> $user,
                    )
                );
        
            return redirect()->route('revisi_tl.index')->with(['status' => 'Success', 'msg' => 'Sukses Menambah Biaya TL']);
                    
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors($th->getMessage())->withInput();
            db::rollBack();
        }
          
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save_refund(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        try {
             $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
             DB::table('sewa_biaya')
                ->where('id_biaya', $data['id_sewa_biaya'])
                ->update(array(
                    'updated_at' => now(),
                    'updated_by' => $user,
                    'is_aktif' => "N",
                )
            );
            DB::table('sewa_operasional')
                ->where('id_sewa', $data['id_sewa'])
                ->where('deskripsi', 'TL')
                ->update(array(
                    'updated_at' => now(),
                    'updated_by' => $user,
                    'is_aktif' => "N",
                )
            );
            $datauang_jalan_riwayat = DB::table('uang_jalan_riwayat')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->where('sewa_id', $data['id_sewa'])
                    ->first();
            DB::table('uang_jalan_riwayat')
                ->where('sewa_id', $data['id_sewa'])
                ->where('is_aktif', 'Y')
                ->update(array(
                    'total_tl'=> $datauang_jalan_riwayat->total_tl-= (float)str_replace(',', '', $data['jumlah']),
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                )
            );
            if($data['pembayaran']=='hutang_karyawan'){
                $kht = new KaryawanHutangTransaction();
                $kht->id_karyawan = $data['id_karyawan'];
                $kht->refrensi_id =  $datauang_jalan_riwayat->id;
                $kht->refrensi_keterangan = 
                '#totalTL:' . (float)str_replace(',', '', $data['jumlah']) . 
                ' #potongHutang:0' . 
                ' #totalTLMasukHutang:'. (($data['jumlah']) ? (float)str_replace(',', '', $data['jumlah']) : 0);
                $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                $kht->tanggal = now();
                $kht->debit = ($data['jumlah']) ? (float)str_replace(',', '', $data['jumlah']) : 0;
                $kht->kredit = 0;
                $kht->kas_bank_id = NULL;
                $kht->catatan = $data['catatan'];
                $kht->created_by = $user;
                $kht->created_at = now();
                $kht->is_aktif = 'Y';
                if($kht->save())
                {
                    if(isset($kh)){
                        // kalau ada data, update hutang
                         $kh->total_hutang +=(float)str_replace(',', '', $data['jumlah']); 
                         $kh->updated_by = $user;
                         $kh->updated_at = now();
                         $kh->save();
                    }else{
                        // kalau tidak ada data, buat data hutang baru
                        $kh = new KaryawanHutang();
                        $kh->id_karyawan = $data['id_karyawan'];
                        $kh->total_hutang +=(float)str_replace(',', '', $data['jumlah']);
                        $kh->createad_by = $user;
                        $kh->createad_at = now();
                        $kh->is_aktif = 'Y';
                        $kh->save();
                    }

                   
                }
            }else{
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    date_create_from_format('d-M-Y', $data['tanggal_pengembalian']),//tanggal
                    (float)str_replace(',', '', $data['jumlah']),// debit uang masuk
                    0, //uang keluar (kredit) 0 soalnya kan ini uang MASUK, ga ada uang KELUAR
                    1016, //kode coa
                    'teluk_lamong',
                    'PENGEMBALIAN TELUK LAMONG'.'#'.$data['no_sewa'].'#'.$data['kendaraan'].'('.$data['driver'].')'.'#'.$data['customer'].'#'.$data['tujuan'].'#'.$data['catatan'], //keterangan_transaksi
                     $datauang_jalan_riwayat->id,//keterangan_kode_transaksi
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                ));
                $saldo = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->where('kas_bank.id', '=', $data['pembayaran'])
                    ->first();
    
                $saldo_baru = $saldo->saldo_sekarang + (float)str_replace(',', '', $data['jumlah']);
                
                DB::table('kas_bank')
                    ->where('id', $data['pembayaran'])
                    ->update(array(
                        'saldo_sekarang' => $saldo_baru,
                        'updated_at'=> now(),
                        'updated_by'=> $user,
                    )
                );

            }
            return redirect()->route('revisi_tl.index')->with(['status' => 'Success', 'msg' => 'Sukses Mengembalikan Biaya TL']);

        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->withErrors($th->getMessage())->withInput();
            db::rollBack();

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getData($status)
    {
        // some logic to determine if the publisher is main
        
        if($status == 'Return TL'){
            return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer'
                    ,'s.stack_tl','sb.id_biaya', 'sb.deskripsi as isTL', 'sb.catatan as jenisTL', 'sb.is_aktif as TLAktif')
            ->leftJoin('sewa_biaya as sb', function($join){
                $join->on('sb.id_sewa', '=', 's.id_sewa')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', 'Y')
                ->whereNotIn('s.stack_tl',['tl_teluk_lamong']);

            })
            ->whereNotIn('s.stack_tl',['tl_teluk_lamong'])
            ->whereNotNull('sb.id_biaya')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->whereNull('s.id_supplier')
            ->orderBy('created_at', 'DESC')
            ->get();
        }else if($status == 'Add TL'){
            return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer'
                    ,'s.stack_tl','sb.id_biaya', 'sb.deskripsi as isTL', 'sb.catatan as jenisTL', 'sb.is_aktif as TLAktif')
            ->leftJoin('sewa_biaya as sb', function($join){
                $join->on('sb.id_sewa', '=', 's.id_sewa')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', 'Y')
                ->where('s.stack_tl','like','%tl_teluk_lamong%');

            })
            ->where('s.stack_tl','like','%tl_teluk_lamong%')
            ->whereNull('sb.id_biaya')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->whereNull('s.id_supplier')
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();
        }else{
            return null;
        }

    }
}
