<?php

namespace App\Http\Controllers;

use App\Models\PembayaranDokumenKendaraan;
use App\Models\PembayaranDokumenKendaraanDetail;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\CoaHelper;
use App\Helper\SewaDataHelper;
use App\Models\PembayaranDokumen;

class PembayaranDokumenKendaraanController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:READ_PEMBAYARAN_GAJI', ['only' => ['index']]);
	// 	$this->middleware('permission:CREATE_PEMBAYARAN_GAJI', ['only' => ['create','store']]);
	// 	$this->middleware('permission:EDIT_PEMBAYARAN_GAJI', ['only' => ['edit','update']]);
	// 	$this->middleware('permission:DELETE_PEMBAYARAN_GAJI', ['only' => ['destroy']]);  
    // }

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
       
        $dataPembayaranKendaraan = PembayaranDokumenKendaraan::where('is_aktif','Y')->with('kas_dokumen_bayar')->get();
        $dataKas = KasBank::where('is_aktif','Y')->get();
        return view('pages.finance.pembayaran_dokumen_kendaraan.index',[
            'judul' => "Pembayaran Dokumen",
            'dataPembayaranKendaraan' => $dataPembayaranKendaraan,
            'dataKendaraan' => SewaDataHelper::DataKendaraanAll(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataKas' => $dataKas,
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
            $pesanKustom = [
                'tanggal_pembayaran.required' => 'Tanggal wajib diisi!',
                'total_nominal.required' => 'nominal wajib diisi!!',
                'select_dokumen.required' => 'Tipe dokumen wajib diisi!!',
            ];
            $request->validate([
                'tanggal_pembayaran' => 'required',
                'total_nominal' => 'required',
                'select_dokumen' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            // dd($data);

            $kendaraan_tampung = '';
            // $catatan_tampung = '';

            $tanggal=date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
            $total_nominal = floatval(str_replace(',', '', $data['total_nominal']));

            $pembayaran_dokumen = new PembayaranDokumenKendaraan();
            $pembayaran_dokumen->id_kas_bank = $data['pembayaran'];
            $pembayaran_dokumen->tanggal_bayar = $tanggal;
            $pembayaran_dokumen->jenis_dokumen = $data['select_dokumen'];
            $pembayaran_dokumen->nominal_bayar = $total_nominal;
            $pembayaran_dokumen->catatan = $data['catatan_pembayaran'];
            $pembayaran_dokumen->created_by = $user;
            $pembayaran_dokumen->created_at = now();
            $pembayaran_dokumen->is_aktif = 'Y';
            if ($pembayaran_dokumen->save()) {
                foreach ($data['kendaraan'] as $value) {
                    $kendaraan_tampung.=' # '.$value['no_polisi'];
                    // if($value['catatan'])
                    // {
                    //     $catatan_tampung.='# '.$value['catatan'];
                    // }
                    $pembayaran_dokumen_detail= new PembayaranDokumenKendaraanDetail();
                    $pembayaran_dokumen_detail->id_pembayaran_kendaraan = $pembayaran_dokumen->id;
                    $pembayaran_dokumen_detail->id_kendaraan = $value['select_kendaraan'];
                    $pembayaran_dokumen_detail->no_pol = $value['no_polisi'];
                    $pembayaran_dokumen_detail->nominal = floatval(str_replace(',', '', $value['nominal']));
                    // $pembayaran_dokumen_detail->catatan = isset($value['catatan'])?$value['catatan']:null;
                    $pembayaran_dokumen_detail->created_by = $user;
                    $pembayaran_dokumen_detail->created_at = now();
                    $pembayaran_dokumen_detail->is_aktif = 'Y';
                    $pembayaran_dokumen_detail->save();
                }
                $kas_bank = KasBank::where('is_aktif', 'Y')
                                ->where('id', $data['pembayaran'])
                                ->first();
                $kas_bank->saldo_sekarang -=  $total_nominal;
                $kas_bank->updated_at = now();
                $kas_bank->updated_by = $user;
                // $kas_bank->save();
                if($kas_bank->save())
                {
                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'],// id kas_bank dr form
                                $tanggal,//tanggal
                                0,// debit 
                                $total_nominal, //kredit
                                CoaHelper::DataCoa(5006), //kode coa stnk dkk
                                'dokumen_kendaraan',
                                'Pembayaran Dokumen Kendaraan'.' - ['.$data['select_dokumen'].'] - '. $kendaraan_tampung.' - '.$data['catatan_pembayaran'], //keterangan_transaksi
                                $pembayaran_dokumen->id,//keterangan_kode_transaksi
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

            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran dokumen kendaraan berhasil!']);

        } catch (ValidationException $e) {
            db::rollBack();

            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->errors())->withInput();

        }   
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PembayaranDokumenKendaraan  $pembayaranDokumenKendaraan
     * @return \Illuminate\Http\Response
     */
    public function show(PembayaranDokumenKendaraan $pembayaranDokumenKendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PembayaranDokumenKendaraan  $pembayaranDokumenKendaraan
     * @return \Illuminate\Http\Response
     */
    public function edit(PembayaranDokumenKendaraan $pembayaran_dokumen_kendaraan)
    {
        //
        $dataKas = KasBank::where('is_aktif','Y')->get();
        $bayar_dokumen=  PembayaranDokumenKendaraan::where('is_aktif','Y')
        ->where('id',$pembayaran_dokumen_kendaraan->id)
        ->with('pembayaran_dokumen_detail')
        ->first();
        $bayar_dokumen_detail=  PembayaranDokumenKendaraanDetail::where('is_aktif','Y')->where('id_pembayaran_kendaraan',$pembayaran_dokumen_kendaraan->id)->get();

        return view('pages.finance.pembayaran_dokumen_kendaraan.edit',[
            'judul'=>"PEMBAYARAN DOKUMEN",
            'pembayaran_dokumen_kendaraan'=> $bayar_dokumen,
            'dataKas'=> $dataKas,
            'dataKendaraan' => SewaDataHelper::DataKendaraanAll(),
            'count_detail'=>count($bayar_dokumen_detail)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PembayaranDokumenKendaraan  $pembayaranDokumenKendaraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PembayaranDokumenKendaraan $pembayaran_dokumen_kendaraan)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $pesanKustom = [
                'tanggal_pembayaran.required' => 'Tanggal wajib diisi!',
                'total_nominal.required' => 'nominal wajib diisi!!',
                'select_dokumen.required' => 'Tipe dokumen wajib diisi!!',
            ];
            $request->validate([
                'tanggal_pembayaran' => 'required',
                'total_nominal' => 'required',
                'select_dokumen' => 'required',
            ], $pesanKustom);

            $data= $request->collect();
            $kas_bank = KasBank::where('is_aktif', 'Y')
             ->where('id', $pembayaran_dokumen_kendaraan->id_kas_bank)
                        ->first();
            $kas_bank->saldo_sekarang +=  floatval(str_replace(',', '', $pembayaran_dokumen_kendaraan->nominal_bayar));
            $kas_bank->updated_at = now();
            $kas_bank->updated_by = $user;
            if($kas_bank->save())
            {
                $kendaraan_tampung = '';
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
                $total_nominal = floatval(str_replace(',', '', $data['total_nominal']));
    
                $bayar_dokumen = PembayaranDokumenKendaraan::where('is_aktif','Y')->where('id',$pembayaran_dokumen_kendaraan->id)->first();
                $bayar_dokumen->id_kas_bank = $data['pembayaran'];
                $bayar_dokumen->tanggal_bayar = $tanggal;
                $bayar_dokumen->jenis_dokumen = $data['select_dokumen'];
                $bayar_dokumen->nominal_bayar = $total_nominal;
                $bayar_dokumen->catatan = $data['catatan_pembayaran'];
                $bayar_dokumen->updated_by = $user;
                $bayar_dokumen->updated_at = now();
                if ($bayar_dokumen->save()) {
                    foreach ($data['kendaraan'] as $value) {
                        if($value['is_aktif']=='Y')
                        {
                            $kendaraan_tampung.=' # '.$value['no_polisi'];
                        }
                        if($value['id_detail']=='baru')
                        {
                            $pembayaran_dokumen_detail_baru= new PembayaranDokumenKendaraanDetail();
                            $pembayaran_dokumen_detail_baru->id_pembayaran_kendaraan = $bayar_dokumen->id;
                            $pembayaran_dokumen_detail_baru->id_kendaraan = $value['select_kendaraan'];
                            $pembayaran_dokumen_detail_baru->no_pol = $value['no_polisi'];
                            $pembayaran_dokumen_detail_baru->nominal = floatval(str_replace(',', '', $value['nominal']));
                            $pembayaran_dokumen_detail_baru->created_by = $user;
                            $pembayaran_dokumen_detail_baru->created_at = now();
                            $pembayaran_dokumen_detail_baru->is_aktif =$value['is_aktif'];
                            $pembayaran_dokumen_detail_baru->save();

                        }
                        else
                        {
                            $bayar_dokumen_detail_lama=  PembayaranDokumenKendaraanDetail::where('is_aktif','Y')->where('id',$value['id_detail'])->first();
                            $bayar_dokumen_detail_lama->id_pembayaran_kendaraan = $bayar_dokumen->id;
                            $bayar_dokumen_detail_lama->id_kendaraan = $value['select_kendaraan'];
                            $bayar_dokumen_detail_lama->no_pol = $value['no_polisi'];
                            if($value['is_aktif']=='Y')
                            {
                                $bayar_dokumen_detail_lama->nominal = floatval(str_replace(',', '', $value['nominal']));
                            }
                            $bayar_dokumen_detail_lama->created_by = $user;
                            $bayar_dokumen_detail_lama->created_at = now();
                            $bayar_dokumen_detail_lama->is_aktif = $value['is_aktif'];
                            $bayar_dokumen_detail_lama->save();
                        }
                    }
                    $kas_bank_transaksi =  KasBankTransaction::where('is_aktif','Y')->where('jenis','dokumen_kendaraan')->where('keterangan_kode_transaksi',$pembayaran_dokumen_kendaraan->id)->first();
                    $kas_bank_transaksi->id_kas_bank = $data['pembayaran'];
                    $kas_bank_transaksi->keterangan_transaksi ='Pembayaran Dokumen Kendaraan'.' - ['.$data['select_dokumen'].'] - '. $kendaraan_tampung.' - '.$data['catatan_pembayaran']; //keterangan_transaksi
                    $kas_bank_transaksi->kredit = $total_nominal;
                    $kas_bank_transaksi->updated_at = now();
                    $kas_bank_transaksi->updated_by = $user;
                    // $kas_bank_transaksi->save();
                    if($kas_bank_transaksi->save())
                    {
                        $kas_bank_baru = KasBank::where('is_aktif', 'Y')
                        ->where('id', $data['pembayaran'])
                                   ->first();
                        $kas_bank_baru->saldo_sekarang -=  $total_nominal;
                        $kas_bank_baru->updated_at = now();
                        $kas_bank_baru->updated_by = $user;
                        $kas_bank_baru->save();
                    }
                }
            }
            DB::commit();

            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran dokumen kendaraan berhasil!']);

        } catch (ValidationException $e) {
            db::rollBack();

            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->errors())->withInput();

        }   
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PembayaranDokumenKendaraan  $pembayaranDokumenKendaraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(PembayaranDokumenKendaraan $pembayaran_dokumen_kendaraan)
    {
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $kas_bank_lama = KasBank::where('is_aktif', 'Y')
             ->where('id', $pembayaran_dokumen_kendaraan->id_kas_bank)
                        ->first();
            $kas_bank_lama->saldo_sekarang +=  floatval(str_replace(',', '', $pembayaran_dokumen_kendaraan->nominal_bayar));
            $kas_bank_lama->updated_at = now();
            $kas_bank_lama->updated_by = $user;
            if($kas_bank_lama->save())
            {
                $bayar_dokumen = PembayaranDokumenKendaraan::where('is_aktif','Y')->where('id',$pembayaran_dokumen_kendaraan->id)->first();
                $bayar_dokumen->updated_by = $user;
                $bayar_dokumen->updated_at = now();
                $bayar_dokumen->is_aktif = 'N';
                if ($bayar_dokumen->save()) {
                    $bayar_dokumen_detail=  PembayaranDokumenKendaraanDetail::where('is_aktif','Y')->where('id_pembayaran_kendaraan',$pembayaran_dokumen_kendaraan->id)->get();
                    foreach ($bayar_dokumen_detail as $value) {
                        $value->updated_by = $user;
                        $value->updated_at = now();
                        $value->is_aktif ='N';
                        $value->save();
                    }
                    $kas_bank_transaksi =  KasBankTransaction::where('is_aktif','Y')->where('jenis','dokumen_kendaraan')->where('keterangan_kode_transaksi',$pembayaran_dokumen_kendaraan->id)->first();
                    $kas_bank_transaksi->updated_at = now();
                    $kas_bank_transaksi->updated_by = $user;
                    $kas_bank_transaksi->is_aktif = 'N';
                    $kas_bank_transaksi->save();
                }
            }
            DB::commit();
            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran dokumen kendaraan berhasil dihapus!']);
        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('pembayaran_dokumen_kendaraan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }
}
