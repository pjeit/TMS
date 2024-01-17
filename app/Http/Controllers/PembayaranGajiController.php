<?php

namespace App\Http\Controllers;

use App\Models\PembayaranGaji;
use App\Models\PembayaranGajiDetail;
use App\Models\KasBank;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\CoaHelper;
class PembayaranGajiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_PEMBAYARAN_GAJI', ['only' => ['index']]);
		$this->middleware('permission:CREATE_PEMBAYARAN_GAJI', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_PEMBAYARAN_GAJI', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_PEMBAYARAN_GAJI', ['only' => ['destroy']]);  
    }

    public function index()
    {
        //
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
        $dataPembayaranGaji = DB::table('pembayaran_gaji as pg')
            ->select('pg.*')
            ->where('pg.is_aktif', '=', "Y")
            ->orderBy('pg.id', 'ASC')
            ->get();
        return view('pages.hrd.pembayaran_gaji.index',[
            'judul' => "Pembayaran Gaji",
            'dataKas' => $dataKas,
            'dataPembayaranGaji' => $dataPembayaranGaji,
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
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
        $dataKaryawan = DB::table('karyawan as k')
            ->select('k.id as idKaryawan','k.nama_panggilan','k.nama_lengkap','k.gaji','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                    $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
                })
            ->where('k.is_aktif', '=', "Y")
            ->where('k.is_keluar', '=', "N")
            ->orderBy('k.nama_lengkap', 'ASC')
            ->get();
        return view('pages.hrd.pembayaran_gaji.create',[
            'judul' => "Pembayaran Gaji",
            'dataKas' => $dataKas,
            'dataKaryawan' => $dataKaryawan,
        ]);
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
                'tanggal.required' => 'Tanggal wajib diisi!',
                'tahun_periode.required' => 'Periode wajib diisi!!',
                'nama_periode.required' => 'Nama Periode wajib diisi!!',
            ];
            $request->validate([
                'tanggal' => 'required',
                'tahun_periode' => 'required',
                'nama_periode' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            // dd(floatval(str_replace(',', '', $data['detail'][0]['potongan_hutang']))!=0);
            // dd($data);
            // dd($data['detail'][0]['potongan_hutang']!=null);

            $tanggal=date_create_from_format('d-M-Y', $data['tanggal']);
            // dd(date_format($tanggal, 'Y-m-d h:i:s'));
            $bayar_gaji = new PembayaranGaji();
            $bayar_gaji->tanggal = date_format($tanggal, 'Y-m-d h:i:s');
            $bayar_gaji->tanggal_catat = now();
            $bayar_gaji->tahun_periode = $data['tahun_periode'];
            $bayar_gaji->bulan_periode = $data['select_bulan'];
            $bayar_gaji->nama_periode = $data['nama_periode'];
            $bayar_gaji->total = floatval(str_replace(',', '', $data['total']));
            $bayar_gaji->kas_bank_id = $data['kas'];
            $bayar_gaji->catatan = $data['catatan'];
            $bayar_gaji->created_by = $user;
            $bayar_gaji->created_at = now();
            $bayar_gaji->is_aktif = 'Y';
            // $bayar_gaji->save();
            if ($bayar_gaji->save()) {
                foreach ($data['detail'] as $value) {
                    $gaji_detail= new PembayaranGajiDetail();
                    $gaji_detail->pembayaran_gaji_id = $bayar_gaji->id;
                    $gaji_detail->karyawan_id = $value['karyawan_id'];
                    $gaji_detail->total_gaji = floatval(str_replace(',', '', $value['total_gaji']));
                    $gaji_detail->potong_hutang = floatval(str_replace(',', '', $value['potongan_hutang']));
                    $gaji_detail->pendapatan_lain = floatval(str_replace(',', '', $value['pendapatan_lain']));
                    $gaji_detail->potongan_lain = floatval(str_replace(',', '', $value['potongan_lain']));
                    $gaji_detail->total_diterima = floatval(str_replace(',', '', $value['total_diterima']));
                    $gaji_detail->catatan = $value['catatan_detail'];
                    $gaji_detail->created_by = $user;
                    $gaji_detail->created_at = now();
                    $gaji_detail->is_aktif = 'Y';
                    // $gaji_detail->save();
                    if($gaji_detail->save())
                    {
                        if($gaji_detail->potong_hutang!=0 || $gaji_detail->potong_hutang!=null)
                        {
                            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $gaji_detail->karyawan_id)->first();
                            if(isset($kh)&&isset($gaji_detail->potong_hutang)){
                                // dd($gaji_detail->potong_hutang);

                                $kh->total_hutang -= $gaji_detail->potong_hutang; 
                                $kh->updated_by = $user;
                                $kh->updated_at = now();
                                // $kh->save();
                                if($kh->save())
                                {
                                    $kht = new KaryawanHutangTransaction();
                                    $kht->id_karyawan = $gaji_detail->karyawan_id;
                                    $kht->refrensi_id = $gaji_detail->id; // id uang jalan
                                    $kht->refrensi_keterangan = 'potong_gaji';
                                    $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = now();
                                    $kht->debit = 0;
                                    $kht->kredit = $gaji_detail->potong_hutang;
                                    $kht->kas_bank_id = NULL;
                                    $kht->catatan = $gaji_detail->catatan ;
                                    $kht->created_by = $user;
                                    $kht->created_at = now();
                                    $kht->is_aktif = 'Y';
                                    $kht->save();
                                }
                            }
                        }
                    }
                }
                $kas_bank = KasBank::where('is_aktif', 'Y')
                                ->where('id', $data['kas'])
                                ->first();
                $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                $kas_bank->updated_at = now();
                $kas_bank->updated_by = $user;
                // $kas_bank->save();
                if($kas_bank->save())
                {
                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['kas'],// id kas_bank dr form
                                date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                0,// debit 
                                (float)str_replace(',', '', $data['total']), //kredit
                                CoaHelper::DataCoa(5021), //kode coa gaji
                                'gaji',
                                'Pembayaran Gaji'.' - '.$data['catatan'].' - '.$data['tahun_periode'].' - '.$data['nama_periode'], //keterangan_transaksi
                                $bayar_gaji->id,//keterangan_kode_transaksi
                                $user,//created_by
                                now(),//created_at
                                $user,//updated_by
                                now(),//updated_at
                                'Y'
                            ) 
                        );
                }
                
            }
            DB::commit();

            return redirect()->route('pembayaran_gaji.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran Gaji berhasil!']);

        } catch (ValidationException $e) {
            db::rollBack();

            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();

        }   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PembayaranGaji  $pembayaranGaji
     * @return \Illuminate\Http\Response
     */
    public function show(PembayaranGaji $pembayaranGaji)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PembayaranGaji  $pembayaranGaji
     * @return \Illuminate\Http\Response
     */
    public function edit(PembayaranGaji $pembayaran_gaji)
    {
        //
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
        $dataKaryawan = DB::table('karyawan as k')
            ->select('k.id as idKaryawan','k.nama_panggilan','k.nama_lengkap','k.gaji','kh.total_hutang')
            ->leftJoin('karyawan_hutang as kh', function($join) {
                    $join->on('k.id', '=', 'kh.id_karyawan')->where('kh.is_aktif', '=', "Y");
                })
            ->where('k.is_aktif', '=', "Y")
            ->where('k.is_keluar', '=', "N")
            ->orderBy('k.nama_lengkap', 'ASC')
            ->get();
        $pembayaran_gaji_detail= DB::table('pembayaran_gaji_detail')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->where('pembayaran_gaji_id', $pembayaran_gaji->id)
            ->get();
        return view('pages.hrd.pembayaran_gaji.edit',[
            'judul' => "Pembayaran Gaji",
            'dataKas' => $dataKas,
            'dataKaryawan' => $dataKaryawan,
            'pembayaran_gaji'=>$pembayaran_gaji,
            'pembayaran_gaji_detail'=>$pembayaran_gaji_detail

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PembayaranGaji  $pembayaranGaji
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PembayaranGaji $pembayaran_gaji)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PembayaranGaji  $pembayaranGaji
     * @return \Illuminate\Http\Response
     */
    public function destroy(PembayaranGaji $pembayaranGaji)
    {
        //
    }
}
