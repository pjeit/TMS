<?php

namespace App\Http\Controllers;

use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBank;
use App\Models\Sewa;
use App\Models\SewaBiaya;
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
        $data = Sewa::where('is_aktif', 'Y')->where('jenis_tujuan', 'LTL')->groupBy('no_polisi')->get();
        $kas = KasBank::where('is_aktif', 'Y')->get();

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
        DB::beginTransaction(); 
        dd($pot_hut);
        try {
            $sewa = Sewa::where('is_aktif', 'Y')->find($data['key']);

            if($sewa){
                $sewa->total_uang_jalan = floatval(str_replace(',', '', $data['uang_jalan']));
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                if($sewa->save()){
                    $sbiaya = new SewaBiaya();
                    $sbiaya->id_sewa = $sewa->id_sewa;
                    $sbiaya->deskripsi = 'UANG JALAN';
                    $sbiaya->biaya = floatval(str_replace(',', '', $data['uang_jalan']));
                    $sbiaya->catatan = 'LTL: ' . $data['catatan'];
                    $sbiaya->created_by = $user;
                    $sbiaya->created_at = now();
                    if($sbiaya->save()){
                        $kh = KaryawanHutang::where('is_aktif', 'Y')->find($sewa->id_karyawan);
                        if(!$kh){
                            DB::rollBack();
                            return redirect()->route('pencairan_uang_jalan_ltl.index')->with(['status' => 'error', 'msg' => 'Karyawan tidak memiliki hutang!']);    
                        }else{
                            $kh->total_hutang -= floatval(str_replace(',', '', $data['potong_hutang']));
                            $kh->updated_by = $user;
                            $kh->updated_at = now();
                            if($kh->save()){
                                $ujr = new UangJalanRiwayat();
                                $ujr->tanggal = now();
                                $ujr->tanggal_pencatatan = now();
                                $ujr->sewa_id = $sewa->id_sewa;
                                $ujr->total_uang_jalan = floatval(str_replace(',', '', $data['uang_jalan']));
                                $ujr->total_tl = 0;
                                $ujr->potong_hutang = floatval(str_replace(',', '', $data['potong_hutang']));
                                $ujr->kas_bank_id = $data['id_kas'];
                                $ujr->catatan = $data['catatan'];
                                $ujr->created_by = $user;
                                $ujr->created_at = now();
                                if($ujr->is_aktif = 'Y'){
                                    $kht = new KaryawanHutangTransaction();
                                    $kht->id_karyawan = $sewa->id_karyawan;
                                    $kht->refrensi_id = $ujr->id; // id uang jalan
                                    $kht->refrensi_keterangan = 'UANG JALAN LTL';
                                    $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = now();
                                    $kht->debit = 0;
                                    $kht->kredit = $pot_hut;
                                    $kht->kas_bank_id = $data['pembayaran'];
                                    $kht->catatan = $data['catatan'];
                                    $kht->created_by = $user;
                                    $kht->created_at = now();
                                    $kht->is_aktif = 'Y';
                                    $kht->save();

                                }

                            }
                        }

                    }
                }
            }

            DB::commit();
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
        $data = Sewa::where('is_aktif', 'Y')->where('jenis_tujuan', 'LTL')->with('getCustomer')
                      ->where('no_polisi', $item)->where('status', 'PROSES DOORING')->get();

        return response()->json(["result" => "success", 'data' => $data], 200);
        
    }
}
