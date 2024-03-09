<?php

namespace App\Http\Controllers;

use App\Models\SewaOperasionalPembayaran;
use Illuminate\Http\Request;
use App\Models\SewaOperasionalPembayaranDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\CoaHelper;
use App\Models\KasBank;
use App\Models\SewaOperasionalKasBon;
use App\Models\SewaOperasionalKembaliStok;
use App\Models\SewaOperasionalRefund;
use App\Models\SewaOperasionalRefundDetail;
use Exception;
use Carbon\Carbon;

class RefundOperasionalController extends Controller
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
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
       
        return view('pages.finance.refund_operasional.index',[
            'judul' => "Refund Operasional",
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(SewaOperasionalPembayaran $sewaOperasionalPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(SewaOperasionalPembayaran $refund_biaya_operasional)
    {
        //
        $data = SewaOperasionalPembayaran::where('is_aktif', 'Y')
        ->whereHas('getOperasionalDetail'/*, function ($query){
            $query->where('is_aktif', 'Y');
        }*/)
        // ->where('total_refund',0)
        // ->where('total_kasbon',0)
        ->where('id',$refund_biaya_operasional->id)
        ->whereNull('total_kembali_stok')
        ->with('getOperasionalDetail')
        ->with('getKas')
        ->with('getOperasionalDetail.getSewaDetail.getCustomer.getGrup')
        ->with('getOperasionalDetail.getSewaDetail.getKaryawan')
        ->with('getOperasionalDetail.getSewaDetail.getSupplier')
        ->first();
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
        // dd($data);
        return view('pages.finance.refund_operasional.refund',[
            'judul' => 'Refund Operasional',
            'data' => $data,
            'dataKas' => $dataKas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SewaOperasionalPembayaran $refund_biaya_operasional)
    {
        //
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);
        // try {
            $count = 0;
            $total_stok = 0;
            $total_kasbon = 0;
            $total_refund = 0;
            $customer ='';
            $tujuan='';
            $no_pol_driver='';
            if(isset($data['cekbox_semua']))
            {
                if($data['cekbox_semua']=="Y")
                {
                    // dd('masuk');
                    $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($refund_biaya_operasional->id);
                    if($data['kembali']!='KEMBALI_STOK'&&$data['kembali']!='DATA_DI_HAPUS'&&$data['kembali']!='kasbon')
                    {
    
                        $so_refund = new SewaOperasionalRefund();
                        $so_refund ->id_kas_bank = $data['kembali'];
                        $so_refund ->tanggal_refund = now();
                        $so_refund ->id_pembayaran = $refund_biaya_operasional->id;
                        $so_refund ->deskripsi_ops =$refund_biaya_operasional->deskripsi;
                        $so_refund ->total_refund = (float)str_replace(',', '', $data['total_dicairkan']);
                        $so_refund->catatan_refund = $data['catatan'];
                        $so_refund->created_by = $user;
                        $so_refund->created_at = now();
                        $so_refund->is_aktif = 'Y';
                        if($so_refund->save())
                        {
                        //  dd($data);
                            // dd($so_pembayaran);
                            if($so_pembayaran){
                                $so_pembayaran->total_refund += (float)str_replace(',', '', $data['total_dicairkan']);
                                $so_pembayaran->updated_by = $user;
                                $so_pembayaran->updated_at = now();
                                $so_pembayaran->is_aktif = 'N';
                                $so_pembayaran->save();
                                if( $so_pembayaran->save());
                                {
                                    foreach ($data['data'] as $value) {
                                        $status = 'HAPUS';
                                        $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                                        $customer = $value['customer'];
                                        $tujuan = $value['tujuan'];
                                        $no_pol_driver .= '# '.$value['no_pol'];
                                        $total_refund+=(float)str_replace(',', '', $value['total_dicairkan']);
                                        SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                        // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                        ->where('id', $value['id_pembayaran_detail'])
                                        ->update([
                                                'is_aktif' => 'N',
                                                'status' => $status,
                                                'keterangan_internal'=>$keterangan_internal,
                                                'id_refund'=>$so_refund->id,
                                                'total_refund'=>(float)str_replace(',', '', $value['total_dicairkan'])
                                            ]);
                                        // $so_refund_detail = new SewaOperasionalRefundDetail();
                                        // $so_refund_detail ->id_refund = $so_refund->id;
                                        // $so_refund_detail ->id_pembayaran = $refund_biaya_operasional->id;
                                        // $so_refund_detail ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                        // $so_refund_detail ->id_sewa = $value['id_sewa'];
                                        // $so_refund_detail ->no_sewa = $value['no_sewa'];
                                        // $so_refund_detail ->deskripsi_ops =$refund_biaya_operasional->deskripsi;
                                        // $so_refund_detail ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                                        // $so_refund_detail->created_by = $user;
                                        // $so_refund_detail->created_at = now();
                                        // $so_refund_detail->is_aktif = 'Y';
                                        // $so_refund_detail->save();
                                        $count++;
                                    }
                                }
                            }
    
                          
                        }
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['kembali'],// id kas_bank dr form
                                    now(),//tanggal
                                    (float)str_replace(',', '', $data['total_dicairkan']),// debit 
                                    0, //uang keluar (kredit)
                                    CoaHelper::DataCoa(1100), //kode coa piutang usaha
                                    'operasional_refund',
                                    'Pengembalian Operasional : '.$refund_biaya_operasional->deskripsi.' '.$count.' X >>'.'('.$customer.') - >'.$tujuan. $no_pol_driver.'Catatan :'.$so_refund->catatan_refund, //keterangan_transaksi
                                    $so_refund->id,//keterangan_kode_transaksi id refundnya
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                            $kas_bank = KasBank::where('is_aktif', 'Y')->find($data['kembali']);
                            $kas_bank->saldo_sekarang += (float)str_replace(',', '', $data['total_dicairkan']);
                            $kas_bank->updated_by = $user;
                            $kas_bank->updated_at = now();
                            $kas_bank->save();
                         DB::commit();
    
                    }
                    else
                    {
                        // dd($data);
                      
                        foreach ($data['data'] as $value) {
                            if ($data['kembali']=='KEMBALI_STOK') {
                                $status = 'STOK';
                                $total_stok+=1;
                                $keterangan_internal = '[REFUND-MASUK-STOK]';
                            }
                            else if ($data['kembali']=='kasbon') {
                                $status = 'KASBON';
                                $total_kasbon += (float)str_replace(',', '', $value['total_dicairkan']);
                                $keterangan_internal = '[REFUND-MASUK-KASBON]';
                            }
                           
                            if($value['kembali']=='KEMBALI_STOK')
                            {
                                
                                $so_pembayaran->total_kembali_stok =  $total_stok; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                $so_pembayaran->updated_by = $user;
                                $so_pembayaran->updated_at = now();
                                // $so_pembayaran->save();
                                if($so_pembayaran->save())
                                {
                                    $so_stok = new SewaOperasionalKembaliStok();
                                    $so_stok ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                    $so_stok ->id_sewa =  $value['id_sewa'];
                                    $so_stok ->no_sewa =  $value['no_sewa'];
                                    $so_stok ->id_pembayaran = $so_pembayaran->id;
                                    $so_stok ->deskripsi_ops = $value['deskripsi_data'];
                                    $so_stok->tanggal_stok = now();
                                    $so_stok->stok_masuk = 1;
                                    $so_stok->stok_keluar = 0;
                                    $so_stok->catatan_stok = $value['catatan'];
                                    $so_stok->created_by = $user;
                                    $so_stok->created_at = now();
                                    $so_stok->is_aktif = 'Y';
                                    $so_stok->save();
                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                    // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                    ->where('id', $value['id_pembayaran_detail'])
                                    ->update([
                                            'is_aktif' => 'N',
                                            'status' => $status,
                                            'keterangan_internal'=>$keterangan_internal,
                                            'id_stok_kembali'=>$so_stok->id
                                        ]);
                                }
                            }
                            else if($value['kembali']=='kasbon')
                            {
                                $total_dicairkan = (float)str_replace(',', '', $value['total_dicairkan']);
                                $so_pembayaran->total_kasbon = $total_kasbon; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                $so_pembayaran->updated_by = $user;
                                $so_pembayaran->updated_at = now();
                                if($so_pembayaran->save())
                                {
                                    $kasbon_operasional = new SewaOperasionalKasBon();
                                    $kasbon_operasional ->id_pembayaran = $so_pembayaran->id;
                                    $kasbon_operasional ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                    $kasbon_operasional->id_sewa =  $value['id_sewa'];
                                    $kasbon_operasional->deskripsi_ops = $value['deskripsi_data'];
                                    $kasbon_operasional->tanggal_transaksi = now();
                                    $kasbon_operasional->kasbon_masuk = $total_dicairkan;
                                    $kasbon_operasional->kasbon_keluar =0;
                                    $kasbon_operasional->catatan_kasbon = $value['catatan'];
                                    $kasbon_operasional->created_by = $user;
                                    $kasbon_operasional->created_at = now();
                                    $kasbon_operasional->is_aktif = 'Y';
                                    $kasbon_operasional->save();
                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                    // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                    ->where('id', $value['id_pembayaran_detail'])
                                    ->update([
                                            'is_aktif' => 'N',
                                            'status' => $status,
                                            'keterangan_internal'=>$keterangan_internal,
                                            'id_kasbon_kembali'=>$kasbon_operasional->id
                                        ]);
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                foreach ($data['data'] as $value) {
                    // dd($value['kembali']);
                    if(isset($value['is_kembali']))
                    {
                        if($value['is_kembali']=='Y')
                        {
                            $customer = $value['customer'];
                            $tujuan = $value['tujuan'];
                            $no_pol_driver = '# '.$value['no_pol'];
                            $total_kembali = (float)str_replace(',', '', $value['total_dicairkan']);

                            if ($data['kembali']=='KEMBALI_STOK') {
                                $status = 'STOK';
                                $total_stok+=1;
                                
                                $keterangan_internal = '[REFUND-MASUK-STOK]';
                            }
                            else if ($data['kembali']=='kasbon') {
                                $status = 'KASBON';
                                $total_kasbon+=$total_kembali;
                                $keterangan_internal = '[REFUND-MASUK-KASBON]';
                            }
                            else
                            {
                                $status = 'HAPUS';
                                $total_refund+=$total_kembali;
                                $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                            }
                           
                            $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($refund_biaya_operasional->id);
                            if($data['kembali']!='KEMBALI_STOK'&&$data['kembali']!='DATA_DI_HAPUS'&&$data['kembali']!='kasbon')
                            {
                                if($so_pembayaran){
                                    $so_pembayaran->total_refund = $total_refund;
                                    $so_pembayaran->updated_by = $user;
                                    $so_pembayaran->updated_at = now();
                                    // $so_pembayaran->is_aktif = 'N';
                                    // $so_pembayaran->save();
                                    if ($so_pembayaran->save()) {
                                        $so_refund = new SewaOperasionalRefund();
                                        $so_refund ->id_kas_bank = $data['kembali'];
                                        $so_refund ->tanggal_refund = now();
                                        $so_refund ->id_pembayaran = $refund_biaya_operasional->id;
                                        $so_refund ->deskripsi_ops =$refund_biaya_operasional->deskripsi;
                                        $so_refund ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                                        $so_refund->catatan_refund = $data['catatan'];
                                        $so_refund->created_by = $user;
                                        $so_refund->created_at = now();
                                        $so_refund->is_aktif = 'Y';
                                        if($so_refund->save())
                                        {
                                            SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                            // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_refund'=>$so_refund->id,
                                                    'total_refund'=>(float)str_replace(',', '', $value['total_dicairkan'])
                                                ]);
                                            // $so_refund_detail = new SewaOperasionalRefundDetail();
                                            // $so_refund_detail ->id_refund = $so_refund->id;
                                            // $so_refund_detail ->id_pembayaran = $refund_biaya_operasional->id;
                                            // $so_refund_detail ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                            // $so_refund_detail ->id_sewa = $value['id_sewa'];
                                            // $so_refund_detail ->no_sewa = $value['no_sewa'];
                                            // $so_refund_detail ->deskripsi_ops =$refund_biaya_operasional->deskripsi;
                                            // $so_refund_detail ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                                            // $so_refund_detail->created_by = $user;
                                            // $so_refund_detail->created_at = now();
                                            // $so_refund_detail->is_aktif = 'Y';
                                            // $so_refund_detail->save();
                                            // if($so_refund_detail->save())
                                            // {
                                                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                    array(
                                                        $data['kembali'],// id kas_bank dr form
                                                        now(),//tanggal
                                                        (float)str_replace(',', '', $value['total_dicairkan']),// debit 
                                                        0, //uang keluar (kredit)
                                                        CoaHelper::DataCoa(1100), //kode coa piutang usaha
                                                        'operasional_refund',
                                                        'Pengembalian Operasional : '.$refund_biaya_operasional->deskripsi.'1 X >>'.'('.$customer.') - >'.$tujuan. $no_pol_driver .$so_refund->catatan_refund, //keterangan_transaksi
                                                        $so_refund->id,//keterangan_kode_transaksi id refundnya
                                                        $user,//created_by
                                                        now(),//created_at
                                                        $user,//updated_by
                                                        now(),//updated_at
                                                        'Y'
                                                    ) 
                                                );
                                                $kas_bank = KasBank::where('is_aktif', 'Y')->find($data['kembali']);
                                                $kas_bank->saldo_sekarang += (float)str_replace(',', '', $value['total_dicairkan']);
                                                $kas_bank->updated_by = $user;
                                                $kas_bank->updated_at = now();
                                                $kas_bank->save();
                                            // }
                                        }
                                    }
                                }
                            }
                            else if($data['kembali']=='KEMBALI_STOK')
                            {
                                

                                $so_pembayaran->total_kembali_stok = $total_stok; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                $so_pembayaran->updated_by = $user;
                                $so_pembayaran->updated_at = now();
                                // $so_pembayaran->save();
                                if($so_pembayaran->save())
                                {
                                    $so_stok = new SewaOperasionalKembaliStok();
                                    $so_stok ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                    $so_stok ->id_sewa =  $value['id_sewa'];
                                    $so_stok ->no_sewa =  $value['no_sewa'];
                                    $so_stok ->id_pembayaran = $so_pembayaran->id;
                                    $so_stok ->deskripsi_ops = $value['deskripsi_data'];
                                    $so_stok->tanggal_stok = now();
                                    $so_stok->stok_masuk = 1;
                                    $so_stok->stok_keluar = 0;
                                    $so_stok->catatan_stok = $value['catatan'];
                                    $so_stok->created_by = $user;
                                    $so_stok->created_at = now();
                                    $so_stok->is_aktif = 'Y';
                                    $so_stok->save();
                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                            // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_stok_kembali'=>$so_stok->id
                                                ]);
                                }
                            }
                            else if($data['kembali']=='kasbon')
                            {
                                $total_dicairkan = (float)str_replace(',', '', $value['total_dicairkan']);
                                $so_pembayaran->total_kasbon = $total_kembali; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                $so_pembayaran->updated_by = $user;
                                $so_pembayaran->updated_at = now();
                                if($so_pembayaran->save())
                                {
                                    $kasbon_operasional = new SewaOperasionalKasBon();
                                    $kasbon_operasional ->id_pembayaran = $so_pembayaran->id;
                                    $kasbon_operasional ->id_pembayaran_detail = $value['id_pembayaran_detail'];
                                    $kasbon_operasional->id_sewa =  $value['id_sewa'];
                                    $kasbon_operasional->deskripsi_ops = $value['deskripsi_data'];
                                    $kasbon_operasional->tanggal_transaksi = now();
                                    $kasbon_operasional->kasbon_masuk = $total_dicairkan;
                                    $kasbon_operasional->kasbon_keluar =0;
                                    $kasbon_operasional->catatan_kasbon = $value['catatan'];
                                    $kasbon_operasional->created_by = $user;
                                    $kasbon_operasional->created_at = now();
                                    $kasbon_operasional->is_aktif = 'Y';
                                    $kasbon_operasional->save();
                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                            // ->whereIn('id',  explode(',' ,$value['id_pembayaran_detail']))
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_kasbon_kembali'=>$kasbon_operasional->id,
                                                    'total_kasbon_kembali'=>$total_kembali
                                                ]);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('refund_biaya_operasional.index')->with(['status' => 'Success', 'msg'  => 'Refund Operasional berhasil!']);

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     db::rollBack();
        // return redirect()->route('refund_biaya_operasional.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);

        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(SewaOperasionalPembayaran $sewaOperasionalPembayaran)
    {
        //
    }
}
