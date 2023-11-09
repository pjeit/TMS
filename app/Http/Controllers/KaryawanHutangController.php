<?php

namespace App\Http\Controllers;

use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBank;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
class KaryawanHutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataKaryawanHutang = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.nama as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('role as r', function($join) {
                $join->on('k.role_id', '=', 'r.id')->where('r.is_aktif', '=', "Y");
            })
            ->where('k.is_aktif',  "Y")
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        // dd( $dataKaryawanHutang);
        return view('pages.hrd.karyawan_hutang.index',[
            'judul'=>"Karyawan Hutang",
            'dataKaryawanHutang' => $dataKaryawanHutang,
            'dataKas' => $dataKas,
            'dariIndex'=>'Y'
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
        //
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'jenis.required' => 'Jenis transaksi wajib diisi!',
                'tanggal.required' => 'Tanggal Transaksi wajib dipilih!',
                'karyawan_id.required' => 'Karyawan wajib dipilih!',
                'nominal.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Total Nominal wajib diisi!',
                'select_kas_bank.required' => 'Kas Bank wajib diisi!',
            ];
            $request->validate([
                'jenis' => 'required',
                'tanggal' => 'required',
                'karyawan_id' => 'required',
                'nominal' => 'required',
                // 'catatan' => 'required',
                'select_kas_bank' => 'required',

            ], $pesanKustom);
                $data= $request->collect();
                // dd($data);
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal']);
                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['karyawan_id'])->first();

                if ($data['jenis']=='BAYAR') {
                    $kht_b = new KaryawanHutangTransaction();
                    $kht_b->id_karyawan = $data['karyawan_id'];
                    $kht_b->refrensi_id = null; // id ga ada soalnya kan bukan dr uj/sewa
                    $kht_b->refrensi_keterangan = 'BAYAR HUTANG KARYAWAN';
                    $kht_b->jenis = 'BAYAR'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                    $kht_b->tanggal = now();
                    $kht_b->debit = 0;
                    $kht_b->kredit = ($data['nominal']) ? (float)str_replace(',', '', $data['nominal']) : 0;
                    $kht_b->kas_bank_id = $data['select_kas_bank'];
                    $kht_b->catatan = $data['catatan'];
                    $kht_b->created_by = $user;
                    $kht_b->created_at = now();
                    $kht_b->is_aktif = 'Y';
                    // $kht_b->save();
                    if($kht_b->save())
                    {
                        if((float)str_replace(',', '', $data['nominal'])>0)
                        {
                            $kas_bank = KasBank::where('is_aktif', 'Y')
                                        ->where('id', $data['select_kas_bank'])
                                        ->first();
                            $kas_bank->saldo_sekarang +=  floatval(str_replace(',', '', $data['nominal']));
                            $kas_bank->updated_at = now();
                            $kas_bank->updated_by = $user;
                            $kas_bank->save();
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['select_kas_bank'],// id kas_bank dr form
                                        date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                        $data['jenis']=='BAYAR'?(float)str_replace(',', '', $data['nominal']):0,// debit 
                                        0, //kredit
                                        1121, //kode coa
                                        'hutang_karyawan',
                                        $data['catatan'], //keterangan_transaksi
                                        $kht_b->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
                            if(isset($kh)){
                                $kh->total_hutang -= (float)str_replace(',', '', $data['nominal']); 
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                $kh->save();
                            }
                            else
                            {
                                $kht_new = new KaryawanHutang();
                                $kht_new->id_karyawan = $data['karyawan_id'];
                                $kht_new->total_hutang = (float)str_replace(',', '', $data['nominal']); 
                                $kht_new->is_aktif = 'Y';
                                $kht_new->created_at = now();
                                $kht_new->created_by = $user;
                                $kht_new->save();
                            }
                        }

                    }
                }
                else //HUTANG
                {
                    $kht = new KaryawanHutangTransaction();
                    $kht->id_karyawan = $data['karyawan_id'];
                    $kht->refrensi_id = null; // id ga ada soalnya kan bukan dr uj/sewa
                    $kht->refrensi_keterangan = 'TAMBAH HUTANG KARYAWAN';
                    $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                    $kht->tanggal = now();
                    $kht->debit = ($data['nominal']) ? (float)str_replace(',', '', $data['nominal']) : 0; // ARTINYA KAN UANG MASUK KE HUTANG (NAMBAH HUTANG KARYAWAN)
                    $kht->kredit = 0;
                    $kht->kas_bank_id = $data['select_kas_bank'];
                    $kht->catatan = $data['catatan'];
                    $kht->created_by = $user;
                    $kht->created_at = now();
                    $kht->is_aktif = 'Y';
                    // $kht->save();
                    if($kht->save())
                    {
                        if((float)str_replace(',', '', $data['nominal'])>0)
                        {
                            $kas_bank = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_kas_bank'])
                                    ->first();
                            $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['nominal']));
                            $kas_bank->updated_at = now();
                            $kas_bank->updated_by = $user;
                            $kas_bank->save();
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['select_kas_bank'],// id kas_bank dr form
                                        date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                        0,// debit 
                                        $data['jenis']=='HUTANG'?(float)str_replace(',', '', $data['nominal']):0, //kredit
                                        1121, //kode coa
                                        'hutang_karyawan',
                                        $data['catatan'], //keterangan_transaksi
                                        $kht->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
                            if(isset($kh)){ // KALO KASBON KAN BERARTI PJE NGASIK UANG KE KARYAWAN BUAT DI PINJEM ATAU BUAT APA MUNGKIN,MAKAE NAMBAH HUTANGE
                                $kh->total_hutang += (float)str_replace(',', '', $data['nominal']); 
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                $kh->save();
                            }
                            else
                            {
                                $kht_new = new KaryawanHutang();
                                $kht_new->id_karyawan = $data['karyawan_id'];
                                $kht_new->total_hutang = (float)str_replace(',', '', $data['nominal']); 
                                $kht_new->is_aktif = 'Y';
                                $kht_new->created_at = now();
                                $kht_new->created_by = $user;
                                $kht_new->save();
                            }
                        }
                        
                    }
                }
                DB::commit();
                if($data['dariIndex']=='Y')
                {
                    return redirect()->route('karyawan_hutang.index')->with(['status' => 'Success', 'msg'  => 'Berhasil membuat data hutang karyawan!']);
                }
                else
                {
                    return redirect()->route('karyawan_hutang.edit',[$data['karyawan_id']])->with(['status' => 'Success', 'msg'  => 'Berhasil membuat data hutang karyawan!']);

                }
        } catch (ValidationException $e) {
            db::rollBack();
            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }   
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function show(KaryawanHutang $karyawanHutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function edit(Karyawan $karyawan_hutang)
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataKaryawanHutang = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.nama as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('role as r', function($join) {
                $join->on('k.role_id', '=', 'r.id')->where('r.is_aktif', '=', "Y");
            })
            ->where('k.is_aktif',"Y")
            ->where('k.id',$karyawan_hutang->id)
            ->first();
        // dd($dataKaryawanHutang);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        $dataDetailHutang = DB::table('karyawan_hutang_transaction as kht')
        ->select('kht.*', 
        'kht.id as id_kht', 
        'kb.nama as nama_bank', 
        'kh.total_hutang as totalnya',
        DB::raw('kh.total_hutang + IF(kht.debit > 0, -1 * kht.debit, kht.kredit) as total_hutang'),
        DB::raw('if(kht.debit > 0, kht.debit, kht.kredit) as nominal')
        )
        ->leftJoin('kas_bank as kb', function ($join) {
            $join->on('kht.kas_bank_id', '=', 'kb.id')->where('kb.is_aktif', '=', "Y");
        })
        ->leftJoin('karyawan_hutang as kh', function ($join) {
            $join->on('kht.id_karyawan', '=', 'kh.id_karyawan')
            ->where('kh.is_aktif', '=', "Y");
        })
        ->where('kht.is_aktif', '=', "Y")
        ->where('kht.id_karyawan',$karyawan_hutang->id)
        ->get();
        // dd($dataDetailHutang);
        return view('pages.hrd.karyawan_hutang.detail',[
            'judul'=>"Karyawan Hutang",
            'dataKaryawanHutang' => $dataKaryawanHutang,
            'dataKas' => $dataKas,
            'dataDetailHutang' => $dataDetailHutang,
            'dariIndex'=>'N'

        ]);
    }
    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KaryawanHutang $karyawanHutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(KaryawanHutang $karyawanHutang)
    {
        //
    }
}
