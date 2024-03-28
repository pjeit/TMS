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
use App\Helper\CoaHelper;
use App\Models\KasBankTransaction;

class KaryawanHutangController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_KARYAWAN_HUTANG', ['only' => ['index']]);
		$this->middleware('permission:CREATE_KARYAWAN_HUTANG', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_KARYAWAN_HUTANG', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_KARYAWAN_HUTANG', ['only' => ['destroy']]);  
    }

    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataKaryawanHutang = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.name as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('roles as r', function($join) {
                $join->on('k.role_id', '=', 'r.id')->where('r.is_aktif', '=', "Y");
            })
            ->orderBy('kh.total_hutang','DESC')
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
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $data= $request->collect();
            $pesanKustom = [
                'jenis.required' => 'Jenis transaksi wajib diisi!',
                'tanggal.required' => 'Tanggal Transaksi wajib dipilih!',
                'karyawan_id.required' => 'Karyawan wajib dipilih!',
                'nominal.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Total Nominal wajib diisi!',
                // 'select_kas_bank.required' => 'Kas Bank wajib diisi!',
            ];
            
            $rules = [
                'jenis' => 'required',
                'tanggal' => 'required',
                'karyawan_id' => 'required',
                'nominal' => 'required',
                // 'catatan' => 'required',
                // 'select_kas_bank' => 'required',
            ];
            
            if ($data['jenis'] == 'BAYAR') {
                // Add additional rules for 'BAYAR' if needed
                $rules['select_kas_bank'] = 'required';
                $pesanKustom['select_kas_bank.required'] = 'Kas Bank wajib diisi!';
            }
            
            $request->validate($rules, $pesanKustom);
                
            $tanggal=date_create_from_format('d-M-Y', $data['tanggal']);
            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['karyawan_id'])->first();
            $karyawan = Karyawan::where('is_aktif', 'Y')->where('id', $data['karyawan_id'])->first();
            // dd($data);
        
            if ($data['jenis']=='BAYAR'&&isset($data['select_kas_bank'])) {
                if((float)str_replace(',', '', $data['nominal'])>(float)str_replace(',', '', $data['total_hutang']))
                {
                    if($data['dariIndex']=='Y')
                    {
                        return redirect()->route('karyawan_hutang.index')->with(['status' => 'error', 'msg'  => 'Pembayaran nominal hutang tidak boleh melebihi jumlah hutang karyawan!']);
                    }
                    else
                    {
                        return redirect()->route('karyawan_hutang.edit',[$data['karyawan_id']])->with(['status' => 'error', 'msg'  => 'Pembayaran nominal hutang tidak boleh melebihi jumlah hutang karyawan!']);
                    }
                }
                $kht_b = new KaryawanHutangTransaction();
                $kht_b->id_karyawan = $data['karyawan_id'];
                $kht_b->refrensi_id = null; // id ga ada soalnya kan bukan dr uj/sewa
                $kht_b->refrensi_keterangan = 'bayar_hutang_karyawan';
                $kht_b->jenis = 'BAYAR'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                $kht_b->tanggal =  $tanggal;
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
                                    $tanggal,//tanggal
                                    $data['jenis']=='BAYAR'?(float)str_replace(',', '', $data['nominal']):0,// debit 
                                    0, //kredit
                                    CoaHelper::DataCoa(1151), //kode coa piutang karyawan
                                    'hutang_karyawan',
                                    'Pembayaran Hutang Karyawan:'.'['.$karyawan->nama_panggilan.'] - '.$data['catatan'], //keterangan_transaksi
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
                $kht->refrensi_keterangan = 'tambah_hutang_karyawan';
                $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                $kht->tanggal =  $tanggal;
                $kht->debit = ($data['nominal']) ? (float)str_replace(',', '', $data['nominal']) : 0; // ARTINYA KAN UANG MASUK KE HUTANG (NAMBAH HUTANG KARYAWAN)
                $kht->kredit = 0;
                $kht->kas_bank_id = isset($data['select_kas_bank'])?$data['select_kas_bank']:null;
                $kht->catatan = $data['catatan'];
                $kht->created_by = $user;
                $kht->created_at = now();
                $kht->is_aktif = 'Y';
                // $kht->save();
                if($kht->save())
                {
                    if((float)str_replace(',', '', $data['nominal'])>0)
                    {
                        if(isset($data['select_kas_bank']))
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
                                        $tanggal,//tanggal
                                        0,// debit 
                                        $data['jenis']=='HUTANG'?(float)str_replace(',', '', $data['nominal']):0, //kredit
                                        CoaHelper::DataCoa(1151), //kode coa piutang karyawan
                                        'hutang_karyawan',
                                        'Kasbon Karyawan :'.'['.$karyawan->nama_panggilan.'] - '.$data['catatan'], //keterangan_transaksi
                                        $kht->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
                        }
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
            ->select('k.*','k.id as idKaryawan','k.telp1','k.nama_panggilan','r.name as namaPosisi','k.tgl_mulai_kontrak as tanggalBergabung','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('roles as r', function($join) {
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
        DB::raw('GREATEST(kh.total_hutang + IF(kht.debit > 0, -1 * kht.debit, kht.kredit), 0) as total_hutang'),
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
        ->where(function ($query) {
            $query->where('kht.debit', '!=', 0)
                ->orWhere('kht.kredit', '!=', 0);
        })
        ->orderByDesc('kht.tanggal')
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
         //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $data= $request->collect();
            $pesanKustom = [
                'jenis_edit.required' => 'Jenis transaksi wajib diisi!',
                'tanggal_edit.required' => 'Tanggal Transaksi wajib dipilih!',
                'karyawan_id_edit.required' => 'Karyawan wajib dipilih!',
                'nominal_edit.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Total Nominal wajib diisi!',
                // 'select_kas_bank.required' => 'Kas Bank wajib diisi!',
            ];
            
            $rules = [
                'jenis_edit' => 'required',
                'tanggal_edit' => 'required',
                'karyawan_id_edit' => 'required',
                'nominal_edit' => 'required',
                // 'catatan' => 'required',
                // 'select_kas_bank' => 'required',
            ];
            
            if ($data['jenis_edit'] == 'BAYAR') {
                // Add additional rules for 'BAYAR' if needed
                $rules['select_kas_bank_edit'] = 'required';
                $pesanKustom['select_kas_bank_edit.required'] = 'Kas Bank wajib diisi!';
            }
            
            $request->validate($rules, $pesanKustom);

                // dd($data);
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_edit']);
                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['karyawan_id_edit'])->first();
                $karyawan = Karyawan::where('is_aktif', 'Y')->where('id', $data['karyawan_id_edit'])->first();

                $kht_b = KaryawanHutangTransaction::where('is_aktif', 'Y')
                ->where('id', $data['key'])
                ->where('id_karyawan', $data['karyawan_id_edit'])
                ->first();

                if($kht_b)
                {
                    if ($data['jenis_edit']=='BAYAR') { 
                        if((float)str_replace(',', '', $data['nominal_edit'])>(float)str_replace(',', '', $data['total_hutang_edit']))
                        {
                                return redirect()->route('karyawan_hutang.edit',[$data['karyawan_id_edit']])->with(['status' => 'error', 'msg'  => 'Pembayaran nominal hutang tidak boleh melebihi jumlah hutang karyawan!']);
                        }
                    }
                    // ini untuk cek case sensitif sama aja kaya $kht_b->jenis=== "HUTANG" tapi semua huruf hutang
                    if(strcasecmp($kht_b->jenis, "HUTANG") === 0)
                    {
                        //ini update data yang lama ditambah dulu (karenakan kalau hutang pje ngeluarin duit buat kasih karyawan)
                        if(isset($kht_b->kas_bank_id))
                        {
                            $kas_bank_b_old = KasBank::where('is_aktif', 'Y')
                                            ->where('id', $kht_b->kas_bank_id)
                                            ->first();
                            $kas_bank_b_old->saldo_sekarang +=  floatval(str_replace(',', '', $kht_b->debit));
                            $kas_bank_b_old->updated_at = now();
                            $kas_bank_b_old->updated_by = $user;
                            $kas_bank_b_old->save();
                        }
                        if(isset($kh)){ // 
                            $kh->total_hutang -= (float)str_replace(',', '', $kht_b->debit); 
                            $kh->updated_by = $user;
                            $kh->updated_at = now();
                            $kh->save();
                        }
                    }
                    else if(strcasecmp($kht_b->jenis, "BAYAR") === 0)
                    {
                        //ini update data yang lama dikurangin dulu (karenakan kalau BAYAR pje dapet duit dari karyawan bayar)
                        if(isset($kht_b->kas_bank_id))
                        {
                            $kas_bank_b_old = KasBank::where('is_aktif', 'Y')
                                            ->where('id', $kht_b->kas_bank_id)
                                            ->first();
                            $kas_bank_b_old->saldo_sekarang -=  floatval(str_replace(',', '', $kht_b->kredit));
                            $kas_bank_b_old->updated_at = now();
                            $kas_bank_b_old->updated_by = $user;
                            $kas_bank_b_old->save();
                        }
                        if(isset($kh)){ // 
                            $kh->total_hutang += (float)str_replace(',', '', $kht_b->kredit); 
                            $kh->updated_by = $user;
                            $kh->updated_at = now();
                            $kh->save();
                        }
                    }
                    // $kht_b->refrensi_id = null; // id ga ada soalnya kan bukan dr uj/sewa
                    $kht_b->refrensi_keterangan =  ($data['jenis_edit']=='BAYAR') ? 'BAYAR HUTANG KARYAWAN':'TAMBAH HUTANG KARYAWAN';
                    $kht_b->jenis = $data['jenis_edit']=='BAYAR'?'BAYAR':'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                    $kht_b->tanggal =$tanggal;
                    $kht_b->debit = ($data['jenis_edit']=='HUTANG') ? (float)str_replace(',', '', $data['nominal_edit']) : 0;
                    $kht_b->kredit = ($data['jenis_edit']=='BAYAR') ? (float)str_replace(',', '', $data['nominal_edit']) : 0;
                    $kht_b->kas_bank_id = isset($data['select_kas_bank_edit'])?$data['select_kas_bank_edit']:null;
                    $kht_b->catatan = $data['catatan_edit'];
                    $kht_b->updated_by = $user;
                    $kht_b->updated_at = now();
                    // $kht_b->save();
                    if($kht_b->save())
                    {
                        if((float)str_replace(',', '', $data['nominal_edit'])>0 && isset($data['select_kas_bank_edit']))
                        {
                            $kas_bank = KasBank::where('is_aktif', 'Y')
                                        ->where('id', $data['select_kas_bank_edit'])
                                        ->first();
                            if($data['jenis_edit']=='HUTANG')
                            {
                                $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['nominal_edit']));
                            }
                            else if($data['jenis_edit']=='BAYAR')
                            {
                                $kas_bank->saldo_sekarang +=  floatval(str_replace(',', '', $data['nominal_edit']));
                            }
                            $kas_bank->updated_at = now();
                            $kas_bank->updated_by = $user;
                            $kas_bank->save();
                                DB::table('kas_bank_transaction')
                                    ->where('keterangan_kode_transaksi', $kht_b->id)
                                    ->where('jenis', 'hutang_karyawan')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'id_kas_bank'=>$data['select_kas_bank_edit'],
                                        'debit'=>($data['jenis_edit']=='BAYAR') ? (float)str_replace(',', '', $data['nominal_edit']) : 0,
                                        'kredit'=>($data['jenis_edit']=='HUTANG') ? (float)str_replace(',', '', $data['nominal_edit']) : 0,
                                        'kode_coa' => ($data['jenis_edit'] == 'BAYAR') ? 1121 : (($data['jenis_edit'] == 'HUTANG') ? 1122 : 1132), // masih hardcode
                                        'keterangan_transaksi'=> ($data['jenis_edit'] == 'BAYAR') ? 'Pembayaran Hutang Karyawan:'.'['.$karyawan->nama_panggilan.'] - '.$data['catatan_edit'] : (($data['jenis_edit'] == 'HUTANG') ?'Kasbon Karyawan:'.'['.$karyawan->nama_panggilan.'] - '.$data['catatan_edit']  : $data['catatan_edit']), // masih hardcode
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                            
                        }
                        if(isset($kh)){
                            if($data['jenis_edit']=='HUTANG')
                            {
                                $kh->total_hutang += (float)str_replace(',', '', $data['nominal_edit']); 
                            }
                            else if($data['jenis_edit']=='BAYAR')
                            {
                                $kh->total_hutang -= (float)str_replace(',', '', $data['nominal_edit']); 
                            }
                            $kh->updated_by = $user;
                            $kh->updated_at = now();
                            $kh->save();
                        }
                    }
                    DB::commit();
                    return redirect()->route('karyawan_hutang.edit',[$data['karyawan_id_edit']])->with(['status' => 'Success', 'msg'  => 'Berhasil mengubah data hutang karyawan!']);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KaryawanHutang  $karyawanHutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(KaryawanHutangTransaction $karyawan_hutang)
    {
        //
        // dd($karyawan_hutang);
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
                // dd($data);
                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $karyawan_hutang->id_karyawan)->first();
                $kht_b = KaryawanHutangTransaction::where('is_aktif', 'Y')
                ->where('id', $karyawan_hutang->id)
                ->where('id_karyawan', $karyawan_hutang->id_karyawan)
                ->first();
                if($kht_b )
                {
                    $kht_b->updated_by = $user;
                    $kht_b->updated_at = now();
                    $kht_b->is_aktif = 'N';
                    // $kht_b->save();
                    if($kht_b->save())
                    {
                        // ini untuk cek case sensitif sama aja kaya $kht_b->jenis=== "HUTANG" tapi semua huruf hutang
                        if(strcasecmp($kht_b->jenis, "HUTANG") === 0)
                        {
                            //ini update data yang lama ditambah dulu (karenakan kalau hutang pje ngeluarin duit buat kasih karyawan)
                            if(isset($kht_b->kas_bank_id))
                            {
                                $kas_bank_b_old = KasBank::where('is_aktif', 'Y')
                                                ->where('id', $kht_b->kas_bank_id)
                                                ->first();
                                $kas_bank_b_old->saldo_sekarang +=  floatval(str_replace(',', '', $kht_b->debit));
                                $kas_bank_b_old->updated_at = now();
                                $kas_bank_b_old->updated_by = $user;
                                // $kas_bank_b_old->save();
                                if($kas_bank_b_old->save())
                                {
                                    $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                    ->where('keterangan_kode_transaksi', $kht_b->id)
                                    // ->where('tanggal', $kht_b->tanggal)
                                    ->where('jenis', 'hutang_karyawan')
                                    ->where('id_kas_bank', $kht_b->kas_bank_id)
                                    ->first();
                                    if($kas_bank_transaksi)
                                    {
                                        $kas_bank_transaksi->updated_at=now();
                                        $kas_bank_transaksi->updated_by=$user;
                                        $kas_bank_transaksi->is_aktif='N';
                                        $kas_bank_transaksi->save();
                                    }
                                    else
                                    {
                                        db::rollBack();
                                        return redirect()->back()->with(['status' => 'error', 'msg'  => 'Data riwayat kas bank tidak ditemukan']);

                                    }
                                }
                            }
                            if(isset($kh)){ // 
                                $kh->total_hutang -= (float)str_replace(',', '', $kht_b->debit); 
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                $kh->save();
                            }
                        }
                        else if(strcasecmp($kht_b->jenis, "BAYAR") === 0)
                        {
                            //ini update data yang lama dikurangin dulu (karenakan kalau BAYAR pje dapet duit dari karyawan bayar)
                            if(isset($kht_b->kas_bank_id))
                            {
                                $kas_bank_b_old = KasBank::where('is_aktif', 'Y')
                                                ->where('id', $kht_b->kas_bank_id)
                                                ->first();
                                $kas_bank_b_old->saldo_sekarang -=  floatval(str_replace(',', '', $kht_b->kredit));
                                $kas_bank_b_old->updated_at = now();
                                $kas_bank_b_old->updated_by = $user;
                                // $kas_bank_b_old->save();
                                if($kas_bank_b_old->save())
                                {
                                    $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                    ->where('keterangan_kode_transaksi', $kht_b->id)
                                    // ->where('tanggal', $kht_b->tanggal)
                                    ->where('jenis', 'hutang_karyawan')
                                    ->where('id_kas_bank', $kht_b->kas_bank_id)
                                    ->first();
                                    if($kas_bank_transaksi)
                                    {
                                        $kas_bank_transaksi->updated_at=now();
                                        $kas_bank_transaksi->updated_by=$user;
                                        $kas_bank_transaksi->is_aktif='N';
                                        $kas_bank_transaksi->save();
                                    }
                                    else
                                    {
                                        db::rollBack();
                                        return redirect()->back()->with(['status' => 'error', 'msg'  => 'Data riwayat kas bank tidak ditemukan']);

                                    }
                                }
                            }
                            if(isset($kh)){ // 
                                $kh->total_hutang += (float)str_replace(',', '', $kht_b->kredit); 
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                $kh->save();
                            }
                        }
                    }
                    DB::commit();
                    return redirect()->back()->with(['status' => 'Success', 'msg'  => 'Berhasil menghapus data hutang karyawan!']);
                }

        } catch (ValidationException $e) {
            db::rollBack();
            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->back()->with(['status' => 'error', 'msg'  => 'Terjadi kesalahan, harap hubungi IT!'.$e->errors()]);
        }   
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            // return redirect()->back()->withErrors($ex->getMessage())->withInput();
            return redirect()->back()->with(['status' => 'error', 'msg'  => 'Terjadi kesalahan, harap hubungi IT!'.$ex->getMessage()]);
        }
    }
}
