<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helper\VariableHelper;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBank;
use App\Models\Sewa;
use App\Models\UangJalanRiwayat;
use App\Helper\CoaHelper;
class RevisiUangJalanController extends Controller
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

        $data = DB::table('sewa AS s')
                ->where('s.status', 'PROSES DOORING')
                ->where('gt.uang_jalan', '>', DB::raw('s.total_uang_jalan'))
                ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                ->select(
                    's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
                    's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                    'k.nama_panggilan','jod.pick_up',
                    DB::raw('COALESCE(gt.tally, 0) as tally'),
                    DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), 
                    'gt.nama_tujuan', 'gt.uang_jalan as uj_tujuan', 's.total_uang_jalan as uj_sewa',
                    'c.nama as customer',
                    'g.nama_grup as nama_grup'
                )
        ->get();
        // dd($data);

        return view('pages.revisi.revisi_uang_jalan.index',[
            'judul' => "REVISI UANG JALAN",
            'data' => $data,
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
    public function store(Request $request)
    {
        $data = $request->post();
        $uang_jalan = (float)str_replace(',', '', $data['uang_jalan']);
        $tgl_pencairan = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        // dd($data); 
        DB::beginTransaction(); 

        try {
            $sewa_biaya = DB::table('sewa_biaya AS sb')
                ->select('sb.*')
                ->where('sb.is_aktif', 'Y')
                ->where('sb.id_sewa', $data['id_sewa_defaulth'])
                ->get();
            $data_grup_tujuan_biaya = DB::table('grup_tujuan_biaya AS gtb')
                ->select('gtb.*')
                ->where('gtb.is_aktif', 'Y')
                ->where('gtb.grup_tujuan_id', $data['id_tujuan'])
                ->get();
            $old_uang_jalan_riwayat = UangJalanRiwayat::where('is_aktif', '=', "Y")
                                    ->where('sewa_id', $data['id_sewa_defaulth'])
                                    ->first();
            
            if($data['jenis'] == 'tambahan'){
                $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($data['id_sewa_defaulth']);
                $sewa->total_uang_jalan += $uang_jalan;
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                $sewa->save();

                foreach ($data_grup_tujuan_biaya as $item) {
                    $cekAdaData = false;
                    foreach ($sewa_biaya as $item1) {
                        if ($item->deskripsi==$item1->deskripsi && $item->biaya==$item1->biaya ) {
                            $cekAdaData = true;
                            break;
                        }
                    }
                    if (!$cekAdaData ) {
                            DB::table('sewa_biaya')
                            ->insert(array(
                            'id_sewa' => $data['id_sewa_defaulth'],
                            'deskripsi' => $item->deskripsi ,
                            'biaya' => $item->biaya,
                            'catatan' => $item->catatan? $item->catatan:null,
                            'created_at' => now(), 
                            'created_by' => $user,
                            'is_aktif' => "Y",
                            )
                        ); 
                    }   
                }

                $old_uang_jalan_riwayat->total_uang_jalan += $uang_jalan;
                if($data['potong_hutang'] != null){
                    $old_uang_jalan_riwayat->potong_hutang += (float)str_replace(',', '', $data['potong_hutang']);
                }
                $old_uang_jalan_riwayat->updated_by = $user;
                $old_uang_jalan_riwayat->updated_at = now();
                $old_uang_jalan_riwayat->save();
                if($data['potong_hutang'] != null){
                    $potong_hutang = (float)str_replace(',', '', $data['potong_hutang']);
                    $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                    if(!$kh){
                        DB::rollBack();
                        return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Karyawan tidak memiliki hutang!"]);
                    }
                    $kh->total_hutang -= $potong_hutang;
                    $kh->updated_by = $user;
                    $kh->updated_at = now();
                    if($kh->save()){
                        $kht = new KaryawanHutangTransaction();
                        $kht->id_karyawan = $data['id_karyawan'];
                        $kht->refrensi_id = $old_uang_jalan_riwayat->id;
                        $kht->refrensi_keterangan = 
                        '#totalUangJalan:' . (float)str_replace(',', '', $data['uang_jalan']) . 
                        ' #potongHutang:' . (($data['potong_hutang']) ? (float)str_replace(',', '', $data['potong_hutang']) : 0) . 
                        ' #totalDiterima:' . (float)str_replace(',', '', $data['total_diterima']);
                        $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                        $kht->tanggal = $tgl_pencairan;
                        $kht->debit = 0;
                        $kht->kredit = $potong_hutang;
                        $kht->kas_bank_id = $data['pembayaran'];
                        $kht->catatan = $data['catatan'];
                        $kht->created_by = $user;
                        $kht->created_at = now();
                        $kht->is_aktif = 'Y';
                        $kht->save();
                    }
                }
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['pembayaran'], // id kas_bank dr form
                        $tgl_pencairan, //tanggal
                        0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                        (float)str_replace(',', '', $data['total_diterima']), //uang keluar (kredit)
                        CoaHelper::DataCoa(5002), //kode coa uang jalan
                        'rev_tambahan_uang_jalan',
                        'REVISI TAMBAHAN UANG JALAN'.' - '.$data['catatan'].' #'.$data['no_sewa'].' #'.$data['kendaraan'].'('.$data['driver'].')'.' #'.$data['customer'].' #'.$data['tujuan'], //keterangan_transaksi
                        $old_uang_jalan_riwayat->id,//keterangan_kode_transaksi
                        $user, //created_by
                        now(), //created_at
                        $user, //updated_by
                        now(), //updated_at
                        'Y'
                    )
                );

                $saldo = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                if($saldo){
                    $saldo->saldo_sekarang -= $uang_jalan;
                    $saldo->updated_by = $user;
                    $saldo->updated_at = now();
                    $saldo->save();
                }
                DB::commit();
                return redirect()->route('revisi_uang_jalan.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);
            }else{
                $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($data['id_sewa_defaulth']);
                $sewa->total_uang_jalan -= $uang_jalan;
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                $sewa->save();
                // nonaktifkan detail sewa biaya jika ada data yg dihapus di grup_tujuan nya
                foreach ($sewa_biaya as $item) {
                    $cekAdaData = false;
                    foreach ($data_grup_tujuan_biaya as $item1) {
                        if ($item->deskripsi==$item1->deskripsi && $item->biaya==$item1->biaya ) {
                            $cekAdaData = true;
                            break;
                        }
                    }
                    if (!$cekAdaData && $item->deskripsi != 'TL') {
                        DB::table('sewa_biaya')
                            ->where('id_sewa', $data['id_sewa_defaulth'])
                            ->where('id_biaya', $item->id_biaya)
                            ->update(array(
                                'is_aktif' => "N",
                                'updated_at'=> now(),
                                'updated_by'=> $user, // masih hardcode nanti diganti cookies
                            )
                        );
                    }
                        
                }

                $old_uang_jalan_riwayat->total_uang_jalan -= $uang_jalan;
                $old_uang_jalan_riwayat->updated_by = $user;
                $old_uang_jalan_riwayat->updated_at = now();
                $old_uang_jalan_riwayat->save();

                if($data['pembayaran'] != 'HUTANG_KARYAWAN'){

                    $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                    if($kasbank){
                        $kasbank->saldo_sekarang += (float)str_replace(',', '', $data['uang_jalan']);
                        $kasbank->updated_by = $user;
                        $kasbank->updated_at = now();
                        if($kasbank->save()){
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['pembayaran'],// id kas_bank dr form
                                    date_create_from_format('d-M-Y', $data['tanggal_pencairan']),//tanggal
                                    (float)str_replace(',', '', $data['uang_jalan']), //uang masuk (debit)
                                    0,// kredit 0 soalnya kan ini uang masuk
                                    CoaHelper::DataCoa(5002), //kode coa uang jalan
                                    'uang_jalan',
                                    'UANG MASUK #PENGEMBALIAN UANG JALAN '.'#'.$data['no_sewa'].' #'.$data['kendaraan'].'('.$data['driver'].')'.' #'.$data['customer'].' #'.$data['tujuan'].' #'.$data['catatan'], //keterangan_transaksi
                                    $old_uang_jalan_riwayat->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                            DB::commit();
                        }
                    }
                }else{
                    $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();

                    if(isset($kh)){
                        // kalau ada data, update hutang
                        $kh->total_hutang += $uang_jalan; 
                        $kh->updated_by = $user;
                        $kh->updated_at = now();
                        $kh->save();
                    }else{
                        // kalau tidak ada data, buat data hutang baru
                        $kh = new KaryawanHutang();
                        $kh->id_karyawan = $data['id_karyawan'];
                        $kh->total_hutang += $uang_jalan;
                        $kh->created_by = $user;
                        $kh->created_at = now();
                        $kh->is_aktif = 'Y';
                        $kh->save();
                    }

                    $kht = new KaryawanHutangTransaction();
                    $kht->id_karyawan = $data['id_karyawan'];
                    $kht->refrensi_id = $old_uang_jalan_riwayat->id;
                    $kht->refrensi_keterangan = 'PENGEMBALIAN UANG JALAN : '.(float)str_replace(',', '', $data['uang_jalan']);
                    $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                    $kht->tanggal = now();
                    $kht->debit = ($data['uang_jalan']) ? (float)str_replace(',', '', $data['uang_jalan']) : 0;
                    $kht->kredit = 0;
                    $kht->kas_bank_id = $data['pembayaran'];
                    $kht->catatan = $data['catatan'];
                    $kht->created_by = $user;
                    $kht->created_at = now();
                    $kht->is_aktif = 'Y';
                    $kht->save();

                    DB::commit();
                }
                return redirect()->route('revisi_uang_jalan.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);
            }

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('revisi_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
            // return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cairkan($id)
    {
        $sewa = DB::table('sewa AS s')
                ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir', 'kh.total_hutang', 'gt.uang_jalan as uang_jalan_gt')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->leftJoin('karyawan_hutang AS kh', 'kh.id_karyawan', '=', 's.id_karyawan')
                ->where('s.jenis_tujuan', 'FTL')
                ->where('s.status', 'PROSES DOORING')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.id_sewa', '=', $id)
                ->first();
        $sewaBiayaTelukLamong = DB::table('sewa_biaya AS sb')
                ->select('sb.*')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', '=', 'Y')
                ->where('sb.id_sewa', '=', $id)
                ->first();
        // dd($sewa);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.revisi.revisi_uang_jalan.cairkan',[
            'judul' => "Revisi Uang Jalan",
            'sewa'=>$sewa,
            'sewaBiayaTelukLamong'=>$sewaBiayaTelukLamong,
            'dataKas'=>$dataKas
        ]);
    }

    public function kembalikan($id)
    {
        $sewa = DB::table('sewa AS s')
                ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir', 'kh.total_hutang', 'gt.uang_jalan as uang_jalan_gt')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->leftJoin('karyawan_hutang AS kh', 'kh.id_karyawan', '=', 's.id_karyawan')
                ->where('s.jenis_tujuan', 'FTL')
                ->where('s.status', 'PROSES DOORING')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.id_sewa', '=', $id)
                ->first();
        $sewaBiayaTelukLamong = DB::table('sewa_biaya AS sb')
                ->select('sb.*')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', '=', 'Y')
                ->where('sb.id_sewa', '=', $id)
                ->first();
        // dd($sewa);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.revisi.revisi_uang_jalan.kembalikan',[
            'judul' => "Revisi Uang Jalan",
            'sewa'=>$sewa,
            'sewaBiayaTelukLamong'=>$sewaBiayaTelukLamong,
            'dataKas'=>$dataKas
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function load_data($item){
        try {
            $data = DB::table('sewa AS s')
                    ->where('s.status', 'PROSES DOORING')
                    ->whereNull('s.id_supplier')
                    // ->where('s.stack_tl','not like','%tl_teluk_lamong%')
                    // ->where('sb.deskripsi', 'not like', '%TL%')
                    // ->where('s.jenis_tujuan', 'FTL')
                     ->where('s.is_aktif', '=', 'Y')
                    ->where( function($where) use($item){
                        if($item == 'TAMBAHAN UJ'){
                            $where->where('gt.uang_jalan', '>', DB::raw('s.total_uang_jalan'));
                        }else if($item == 'KEMBALIKAN UJ'){
                            $where->where('gt.uang_jalan', '<', DB::raw('s.total_uang_jalan'));
                        }else{
                            //
                        }
                    })
                    ->leftJoin('grup_tujuan AS gt', 'gt.id', '=', 's.id_grup_tujuan')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup AS g', 'g.id', '=', 'gt.grup_id')
                    ->leftJoin('karyawan AS k', 'k.id', '=', 's.id_karyawan')
                    ->leftJoin('job_order_detail AS jod', 'jod.id', '=', 's.id_jo_detail')
                    // ->leftJoin('sewa_biaya AS sb', 's.id_sewa', '=', 'sb.id_sewa')
                    ->select(
                        's.id_sewa', 's.no_sewa', 's.id_jo', 's.id_jo_detail', 's.id_customer', 'c.grup_id',
                        's.id_grup_tujuan', 's.jenis_order', 's.tipe_kontainer', 's.no_polisi as no_polisi',
                        'k.nama_panggilan','jod.pick_up',
                        DB::raw('COALESCE(gt.tally, 0) as tally'),
                        DB::raw('COALESCE(gt.seal_pelayaran, 0) as seal_pelayaran'), 
                        'gt.nama_tujuan', 'gt.uang_jalan as uj_tujuan', 's.total_uang_jalan as uj_sewa',
                        'c.nama as customer',
                        'g.nama_grup as nama_grup'
                    )
            ->get();
            // var_dump($item); die;
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
