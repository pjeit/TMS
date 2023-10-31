<?php

namespace App\Http\Controllers;

use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBank;
use App\Models\Sewa;
use App\Models\SewaBiaya;
use App\Models\SewaOperasional;
use App\Models\UangJalanRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PencairanUangJalanLTLController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sewa::where('is_aktif', 'Y')
            ->where('jenis_tujuan', 'LTL')
            ->groupBy('no_polisi')
            ->get();
        $kas = KasBank::where('is_aktif', 'Y')->get();
        // dd($data);
        return view('pages.finance.pencairan_uang_jalan_ltl.index',[
            'judul' => "Pencairan Uang  LTL",
            'data' => $data,
            'kas' => $kas,
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
        $user = Auth::user()->id;
        $data = $request->post();
        $pot_hut = floatval(str_replace(',', '', $data['potong_hutang']));
        $uj = floatval(str_replace(',', '', $data['uang_jalan']));
        $diterima = floatval(str_replace(',', '', $data['diterima']));
        DB::beginTransaction(); 
        // dd($data);
        try {
            $sewa = Sewa::where('is_aktif', 'Y')->find($data['key']);

            if($sewa){
                $sewa->total_uang_jalan = $uj;
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                if($sewa->save()){
                    $sbiaya = new SewaBiaya();
                    $sbiaya->id_sewa = $sewa->id_sewa;
                    $sbiaya->deskripsi = 'UANG JALAN';
                    $sbiaya->biaya = $uj;
                    $sbiaya->catatan = 'LTL: ' . $data['catatan'];
                    $sbiaya->created_by = $user;
                    $sbiaya->created_at = now();
                    if($sbiaya->save()){
                        $ujr = new UangJalanRiwayat();
                        $ujr->tanggal = now();
                        $ujr->tanggal_pencatatan = now();
                        $ujr->sewa_id = $sewa->id_sewa;
                        $ujr->total_uang_jalan = $diterima;
                        $ujr->potong_hutang = $pot_hut;
                        $ujr->kas_bank_id = $data['id_kas'];
                        $ujr->catatan = 'UANG JALAN LTL - '.$data['catatan'];
                        $ujr->created_by = $user;
                        $ujr->created_at = now();
                        $ujr->is_aktif = 'Y';
                        $ujr->save();

                        if($pot_hut != 0){
                            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan',$sewa->id_karyawan)->first();
                            if($kh){
                                $kh->total_hutang -= $pot_hut;
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                if($kh->save()){
                                    $kht = new KaryawanHutangTransaction();
                                    $kht->id_karyawan = $sewa->id_karyawan;
                                    $kht->refrensi_id = $ujr->id; // id uang jalan
                                    $kht->refrensi_keterangan = 'UANG JALAN LTL';
                                    $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = now();
                                    $kht->debit = 0;
                                    $kht->kredit = $pot_hut;
                                    $kht->kas_bank_id = $data['id_kas'];
                                    $kht->catatan = $data['catatan'];
                                    $kht->created_by = $user;
                                    $kht->created_at = now();
                                    $kht->is_aktif = 'Y';
                                    $kht->save();  
                                }
                            }else{
                                DB::rollBack();
                                return redirect()->route('pencairan_uang_jalan_ltl.index')->with(['status' => 'error', 'msg' => 'Karyawan tidak memiliki hutang!']);    
                            }
                        }

                        $SOP = new SewaOperasional();
                        $SOP->id_sewa = $data['key']; 
                        $SOP->deskripsi = 'UANG JALAN';
                        $SOP->total_operasional = $diterima;
                        $SOP->total_dicairkan = $diterima;
                        $SOP->tgl_dicairkan = now();
                        $SOP->is_ditagihkan = 'N';
                        $SOP->is_dipisahkan = 'N';
                        $SOP->status = "SUDAH DICAIRKAN";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        if($SOP->save()){
                            $saldo = KasBank::where('is_aktif', 'Y')->find($data['id_kas']);
                            $saldo->saldo_sekarang -= $diterima;
                            $saldo->updated_by = $user;
                            $saldo->updated_at = now();
                            $saldo->save();
    
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['id_kas'],// id kas_bank dr form
                                    now(),//tanggal
                                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                    $diterima, //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                    1016, //kode coa
                                    'uang_jalan',
                                    'UJ LTL: #'.$sewa->no_polisi. ' #'.$sewa->nama_driver, //keterangan_transaksi
                                    $ujr->id,//keterangan_kode_transaksi
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
                }
            }

            return redirect()->route('pencairan_uang_jalan_ltl.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);
        } catch (ValidationException $th) {
            db::rollBack();
            return redirect()->route('pencairan_uang_jalan_ltl.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);        }
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
    public function edit($id)
    {
        //
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

    public function get_data($item)
    {
        $data = Sewa::where('is_aktif', 'Y')->where('jenis_tujuan', 'LTL')->with('getCustomer')->with('getKaryawan.getHutang')
                      ->where('no_polisi', $item)->where('status', 'PROSES DOORING')->get();
        if($data[0]['total_uang_jalan'] != 0){
            return response()->json(["result" => "error", 'data' => null], 404);
        }else{
            return response()->json(["result" => "success", 'data' => $data], 200);
        }
        
    }
}
