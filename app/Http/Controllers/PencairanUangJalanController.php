<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Http\Controllers\Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\UangJalanRiwayat;
use App\Models\SewaOperasional;
use Symfony\Component\VarDumper\VarDumper;
use App\Helper\CoaHelper;
class PencairanUangJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sewa = DB::table('sewa AS s')
                ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.jenis_tujuan', 'like', '%FTL%')
                ->whereNull('s.id_supplier')
                ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                ->orderBy('c.id','ASC')
                ->get();

        return view('pages.finance.pencairan_uang_jalan.index',[
            'judul' => "Pencairan Uang Jalan FTL",
            'sewa'=>$sewa,
            'dataJO' => null,
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
    public function form(Request $request)
    {
        $id_sewa_default = $request->input('id_sewa');
        // dd($id_sewa_defaulth);
        // session(['id_sewa' => $id_sewa]);

        $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                    ->where('s.is_aktif', '=', 'Y')
                    ->groupBy('c.id')
                    ->get();
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.finance.pencairan_uang_jalan.form',[
            'judul' => "Pencairan Uang Jalan",
            'sewa'=>$sewa,
            'dataKas'=>$dataKas,
            'id_sewa_defaulth'=>$id_sewa_default,
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        DB::beginTransaction(); 
        try {
            $ujr = new UangJalanRiwayat();
            $ujr->tanggal = now();
            $ujr->tanggal_pencatatan = now();
            $ujr->sewa_id = $data['id_sewa_defaulth'];
            $ujr->total_uang_jalan = (float)str_replace(',', '', $data['uang_jalan']);
            $ujr->total_tl = (isset($data['teluk_lamong'])?(float)str_replace(',', '', $data['teluk_lamong']):0);
            $ujr->potong_hutang = (isset($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0);
            $ujr->kas_bank_id = $data['pembayaran'];
            $ujr->catatan = $data['catatan'];
            $ujr->created_by = $user;
            $ujr->created_at = now();
            $ujr->is_aktif = 'Y';
            if($ujr->save())
            {
                $tl = isset($data['teluk_lamong'])? ($data['teluk_lamong'] != 0? ' #TELUK LAMONG:'.(isset($data['teluk_lamong'])? (float)str_replace(',', '', $data['teluk_lamong']):0):''):""; 
                $pothut = isset($data['potong_hutang'])? ' #POTONG HUTANG: '.(float)str_replace(',', '', $data['potong_hutang']) : ''; 
                $refrensi_keterangan_string = 
                    ' #UANG JALAN: ' . (float)str_replace(',', '', $data['uang_jalan']) . 
                    $tl. $pothut.  
                    ' #TOTAL DITERIMA: ' .(float)str_replace(',', '', $data['total_diterima']);

                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                if(isset($kh)&&isset($data['potong_hutang'])){
                    $saldo_hutang= $kh->total_hutang - (float)str_replace(',', '', $data['potong_hutang']);
                    $kh->total_hutang = $saldo_hutang; 
                    $kh->updated_by = $user;
                    $kh->updated_at = now();
                    $kh->save();

                    $kht = new KaryawanHutangTransaction();
                    $kht->id_karyawan = $data['id_karyawan'];
                    $kht->refrensi_id = $ujr->id; // id uang jalan
                    $kht->refrensi_keterangan = 'UANG JALAN';
                    $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                    $kht->tanggal = now();
                    $kht->debit = 0;
                    $kht->kredit = ($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0;
                    $kht->kas_bank_id = $data['pembayaran'];
                    $kht->catatan = $data['catatan'] . ' '. $refrensi_keterangan_string;
                    $kht->created_by = $user;
                    $kht->created_at = now();
                    $kht->is_aktif = 'Y';
                    $kht->save();
                
                    if( $data['total_diterima']!=0||$data['total_diterima']!="0")
                    {
                        if (isset($data['teluk_lamong'])) {
                            $keterangan_string = 'UANG KELUAR #PEMBAYARAN UANG JALAN + TELUK LAMONG';
                            $SOP = new SewaOperasional();
                            $SOP->id_sewa = $data['id_sewa_defaulth']; 
                            $SOP->deskripsi = 'TL';
                            $SOP->total_operasional = (float)str_replace(',', '', $data['teluk_lamong']);
                            $SOP->total_dicairkan = (float)str_replace(',', '', $data['teluk_lamong']);
                            $SOP->tgl_dicairkan = now();
                            $SOP->is_ditagihkan = 'N';
                            $SOP->is_dipisahkan = 'N';
                            $SOP->status = "SUDAH DICAIRKAN";
                            $SOP->created_by = $user;
                            $SOP->created_at = now();
                            $SOP->is_aktif = 'Y';
                            $SOP->save();
                        } else {
                            $keterangan_string = 'UANG KELUAR #PEMBAYARAN UANG JALAN';
                        }
                        $catatan = isset($data['catatan'])? ' #'.$data['catatan']:' ';
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'],// id kas_bank dr form
                                date_create_from_format('d-M-Y', $data['tanggal_pencairan']),//tanggal
                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                (float)str_replace(',', '', $data['total_diterima']), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                CoaHelper::DataCoa(5002), //kode coa uang jalan
                                'uang_jalan',
                                $keterangan_string.' #'.$data['no_sewa'].' #'.$data['kendaraan'].'('.$data['driver'].')'.' #'.$data['customer'].' #'.$data['tujuan']. $catatan .$refrensi_keterangan_string, //keterangan_transaksi
                                $ujr->id,//keterangan_kode_transaksi
                                $user,//created_by
                                now(),//created_at
                                $user,//updated_by
                                now(),//updated_at
                                'Y'
                            ) 
                        );
                    }
                }
                else
                {
                    if (isset($data['teluk_lamong'])) {
                            // $nominal =(float)str_replace(',', '', $data['total_diterima'])+(float)str_replace(',', '', $data['teluk_lamong']);
                            $keterangan_string='UANG KELUAR #PEMBAYARAN UANG JALAN + TELUK LAMONG';
                            $SOP = new SewaOperasional();
                            $SOP->id_sewa = $data['id_sewa_defaulth']; 
                            $SOP->deskripsi = 'TL';
                            $SOP->total_operasional = (float)str_replace(',', '', $data['teluk_lamong']);
                            $SOP->total_dicairkan = (float)str_replace(',', '', $data['teluk_lamong']);
                            $SOP->tgl_dicairkan = now();
                            $SOP->is_ditagihkan = 'N';
                            $SOP->is_dipisahkan = 'N';
                            $SOP->status = "SUDAH DICAIRKAN";
                            $SOP->created_by = $user;
                            $SOP->created_at = now();
                            $SOP->is_aktif = 'Y';
                            $SOP->save();
                        } else {
                            // $nominal =(float)str_replace(',', '', $data['total_diterima']);
                            $keterangan_string='UANG KELUAR #PEMBAYARAN UANG JALAN';
                        }
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'],// id kas_bank dr form
                                date_create_from_format('d-M-Y', $data['tanggal_pencairan']),//tanggal
                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                (float)str_replace(',', '', $data['total_diterima']), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                CoaHelper::DataCoa(5002), //kode coa uang jalan
                                'uang_jalan',
                                $keterangan_string.' #'.$data['no_sewa'].' #'.$data['kendaraan'].'('.$data['driver'].')'.' #'.$data['customer'].' #'.$data['tujuan'].' #'.$data['catatan'].$refrensi_keterangan_string, //keterangan_transaksi
                                $ujr->id,//keterangan_kode_transaksi
                                $user,//created_by
                                now(),//created_at
                                $user,//updated_by
                                now(),//updated_at
                                'Y'
                            ) 
                        );
                }   
            }
            
            $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($data['id_sewa_defaulth']);
            $sewa->status = 'PROSES DOORING';
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->save();

            if ((float)str_replace(',', '', $data['total_diterima']>0))
            {
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
            }
            DB::commit();
            return redirect()->route('pencairan_uang_jalan.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('pencairan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $pencairan_uang_jalan)
    {
         $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', 'MENUNGGU UANG JALAN')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', '=', $pencairan_uang_jalan->id_sewa)
                    ->groupBy('c.id')
                    ->first();
         $sewaBiayaTelukLamong = DB::table('sewa_biaya AS sb')
                    ->select('sb.*')
                    ->where('sb.deskripsi', 'like', '%TL%')
                    ->where('sb.is_aktif', '=', 'Y')
                    ->where('sb.id_sewa', '=', $pencairan_uang_jalan->id_sewa)
                    ->first();
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
        // dd($sewaBiayaTelukLamong);

        return view('pages.finance.pencairan_uang_jalan.edit',[
            'judul' => "Pencairan Uang Jalan",
            'sewa'=> $sewa,
            'sewaBiayaTelukLamong'=>$sewaBiayaTelukLamong,
            'dataKas'=>$dataKas
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
