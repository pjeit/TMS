<?php

namespace App\Http\Controllers;

use App\Helper\CoaHelper;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBank;
use App\Models\UangJalanRiwayat;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PersetujuanUangJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $sewa = DB::table('sewa AS s')
        // ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
        // ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
        // ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
        // ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
        // ->where('s.is_aktif', '=', 'Y')
        // ->where('s.jenis_tujuan', 'like', '%FTL%')
        // ->whereNull('s.id_supplier')
        // ->where('s.status',  "MENUNGGU PERSETUJUAN")
        // ->orderBy('c.id','ASC')
        // ->get();

        $sewa = Sewa::where('is_aktif','Y')
        ->where('sewa.is_aktif', '=', 'Y')
        ->where('sewa.jenis_tujuan', 'FTL')
        ->whereNull('sewa.id_supplier')
        
        ->where('sewa.status', "MENUNGGU PERSETUJUAN")
        ->with('getTujuan')
        ->with('getCustomer')
        ->with('getKaryawan')
        ->with('getUJRiwayat')
        ->get();
        return view('pages.finance.persetujuan_uang_jalan.index',[
            'judul' => "Persetujuan Uang Jalan",
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
    public function store(Request $request)
    {
        $data = $request->post();
        // DD($data);
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        DB::beginTransaction(); 
        $is_sewa_data = true;
        $is_kas = true;
        $is_uj = true;

        try {
            if (isset($data['data'])) {
                foreach ($data['data'] as $key => $value) {
                    $ujr = UangJalanRiwayat::where('is_aktif','Y')->where('is_acc','N')->find($value['id_uj']);
                    if($ujr)
                    {
                        if($value['is_acc']=='Y')
                        {
                            // $ujr->tanggal = date_create_from_format('d-M-Y', $value['tanggal_pencairan']);
                            // $ujr->kas_bank_id ;
                            $ujr->is_acc='Y' ;
                            $ujr->updated_by=$user ;
                            $ujr->updated_at=now() ;
                            if($ujr->save())
                            {
                                $total_diterima = ( $ujr->total_uang_jalan +$ujr->total_tl ) - $ujr->potong_hutang;
                                $tl_string =  $ujr->total_tl > 0? ' >> TELUK LAMONG :'. number_format($ujr->total_tl) :''; 
                                $pothut_string =  $ujr->potong_hutang > 0? ' >> POTONG HUTANG : '.number_format($ujr->potong_hutang) : ''; 
                                $refrensi_keterangan_string = 
                                    ' >> UANG JALAN : ' . number_format($ujr->total_uang_jalan) . 
                                    $tl_string. $pothut_string.  
                                    ' >> TOTAL DITERIMA : ' .number_format($total_diterima) ;
                
                                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $value['id_karyawan'])->first();
                                if(isset($kh)&& $ujr->potong_hutang>0){
                                    $kh->total_hutang -= $ujr->potong_hutang; 
                                    $kh->updated_by = $user;
                                    $kh->updated_at = now();
                                    if( $kh->save())
                                    {
                                        $kht = new KaryawanHutangTransaction();
                                        $kht->id_karyawan = $value['id_karyawan'];
                                        $kht->refrensi_id = $ujr->id; // id uang jalan
                                        $kht->refrensi_keterangan = 'uang_jalan';
                                        $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                        // $kht->tanggal = date_create_from_format('d-M-Y', $value['tanggal_pencairan']);
                                        $kht->tanggal =$ujr->tanggal;
                                        $kht->debit = 0;
                                        $kht->kredit =$ujr->potong_hutang;
                                        $kht->kas_bank_id = $ujr->kas_bank_id;
                                        $kht->catatan = 'Pencairan Uang jalan Potong hutang : '.$value['no_sewa'].' >> '.$value['kendaraan'].'('.$value['driver'].')'.' >> '.$value['customer'].' >> '.$value['tujuan'].' >> '.$ujr->catatan . ' '. $refrensi_keterangan_string;
                                        $kht->created_by = $user;
                                        $kht->created_at = now();
                                        $kht->is_aktif = 'Y';
                                        $kht->save();
                                    
                                        if( $total_diterima>0)
                                        {
                                            if ($ujr->total_tl > 0) {
                                                $keterangan_string = ' PEMBAYARAN UANG JALAN + TELUK LAMONG';
                                            } else {
                                                $keterangan_string = ' PEMBAYARAN UANG JALAN';
                                            }
                                            $catatan = isset($ujr->catatan)? ' >> '.$ujr->catatan:' ';
                                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                array(
                                                    $ujr->kas_bank_id,// id kas_bank dr form
                                                    $ujr->tanggal,//tanggal
                                                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                    $total_diterima , //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                                    CoaHelper::DataCoa(5002), //kode coa uang jalan
                                                    'uang_jalan',
                                                    $keterangan_string.' >> '.$value['no_sewa'].' >> '.$value['kendaraan'].'('.$value['driver'].')'.' >> '.$value['customer'].' >> '.$value['tujuan']. $catatan .$refrensi_keterangan_string, //keterangan_transaksi
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
                                }
                                else
                                {
                                    if ($ujr->total_tl > 0) {
                                        // $nominal =(float)str_replace(',', '', $value['total_diterima'])+(float)str_replace(',', '', $value['teluk_lamong']);
                                        $keterangan_string = ' PEMBAYARAN UANG JALAN + TELUK LAMONG';
                                    } else {
                                        $keterangan_string = ' PEMBAYARAN UANG JALAN';
                                    }
                                    $catatan = isset($ujr->catatan)? ' >> '.$ujr->catatan:' ';
                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                        array(
                                            $ujr->kas_bank_id,// id kas_bank dr form
                                            $ujr->tanggal,//tanggal
                                            0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                            $total_diterima , //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                            CoaHelper::DataCoa(5002), //kode coa uang jalan
                                            'uang_jalan',
                                            $keterangan_string.' >> '.$value['no_sewa'].' >> '.$value['kendaraan'].'('.$value['driver'].')'.' >> '.$value['customer'].' >> '.$value['tujuan'].' >> '.$catatan.$refrensi_keterangan_string, //keterangan_transaksi
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
                            $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($value['id_sewa']);
                            if($sewa)
                            {
                                $sewa->total_uang_jalan += $ujr->total_tl;
                                $sewa->status = 'PROSES DOORING';
                                // $sewa->status = 'MENUNGGU PERSETUJUAN';
                                $sewa->updated_by = $user;
                                $sewa->updated_at = now();
                                if( $sewa->save())
                                {
                                    if ($total_diterima >0)
                                    {
                                        $saldo = KasBank::where('is_aktif', "Y")->find( $ujr->kas_bank_id);
                                        if($saldo)
                                        {
                                            $saldo->saldo_sekarang -= $total_diterima;
                                            $saldo->updated_by = $user;
                                            $saldo->updated_at = now();
                                            $saldo->save();
                                        }
                                        else
                                        {
                                            $is_kas = false;
                                            break;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $is_sewa_data = false;
                                break;
                        
                            }
                        }
                        else
                        {
                            $ujr->is_acc='N' ;
                            $ujr->alasan_tolak = $value['alasan_tolak'] ;
                            $ujr->updated_by=$user ;
                            $ujr->updated_at=now() ;
                            $ujr->is_aktif='N' ;
                            if($ujr->save())
                            {
                                // $teluk_lamong = isset($value['teluk_lamong'])?floatval(str_replace(',', '', $value['teluk_lamong'])):0;
                                $sewa = Sewa::where('is_aktif', 'Y')->findOrFail($value['id_sewa']);
                                if($sewa)
                                {
                                    $sewa->status = 'MENUNGGU UANG JALAN';
                                    $sewa->updated_by = $user;
                                    $sewa->updated_at = now();
                                    $sewa->save();
                                }
                                else
                                {
                                    $is_sewa_data = false;
                                    break;
                                }
                            }
                        }

                    }
                    else
                    {
                        $is_uj = false;
                        break;
                      
                    }
                }
                // $is_sewa_data = true;
                // $is_kas = true;
                // $is_uj = true;
                // dd($is_uj);
                if( $is_sewa_data && $is_kas && $is_uj)
                {
                    DB::commit();
                    return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);

                }
                else if(!$is_sewa_data)
                {
                    db::rollBack();
                    return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Tidak ada data sewa harap, hubungi IT!']);
    
                }
                else if(!$is_kas)
                {
                    db::rollBack();
                    return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Tidak ada data kas bank, harap hubungi IT!']);
    
                }
                else if(!$is_uj)
                {
                    db::rollBack();
                    return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Tidak ada data pencairan uang jalan, harap hubungi IT!']);
                }
                else
                {
                    db::rollBack();
                    return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'error, harap hubungi IT!']);
                }
            }
            else
            {
                db::rollBack();
                return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Tidak ada data pencairan uang jalan!']);
            }
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('persetujuan_uang_jalan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function show(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function edit(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function destroy(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }
}
