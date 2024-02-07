<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Helper\UserHelper;
use App\Helper\CoaHelper;
use Exception;

class PaymentJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_PEMBAYARAN_JO', ['only' => ['index']]);
		$this->middleware('permission:CREATE_PEMBAYARAN_JO', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_PEMBAYARAN_JO', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_PEMBAYARAN_JO', ['only' => ['destroy']]);  
    }

    public function index()
    {
        // use App\Helper\UserHelper;
        $id_role = Auth::user()->role_id; 
        // $cabang = UserHelper::getCabang();

        $data = DB::table('job_order')
                ->leftJoin('user as u', 'u.id', '=', 'job_order.created_by')
                ->leftJoin('karyawan as k', 'k.id', '=', 'u.karyawan_id')
                // ->where(function ($query) use ($id_role, $cabang) {
                //     if(!in_array($id_role, [1,3])){
                //         $query->where('k.cabang_id', $cabang); // selain id [1,3] atau role [superadmin, admin nasional] lock per kota
                //     }
                // })
                ->select('job_order.id','job_order.no_jo','customer.nama as namaCustomer','supplier.nama as namaSupplier','job_order.pelabuhan_muat','job_order.pelabuhan_bongkar','job_order.tgl_sandar','job_order.status')
                ->Join('supplier', 'job_order.id_supplier', '=', 'supplier.id')
                ->Join('customer', 'job_order.id_customer', '=', 'customer.id')
                // ->join('jaminan', 'job_order.id', '=', 'jaminan.id_job_order')
                ->where('job_order.is_aktif', '=', 'Y') 
                ->where('job_order.status', 'MENUNGGU PEMBAYARAN') 
                ->get();

        //  $data = JobOrder::where('is_aktif', 'Y')->paginate(5);

        return view('pages.finance.pembayaran_order.index',[
            'judul' => "Pembayaran Job Order",
            'data' => $data,
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
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function show(JobOrder $jobOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(JobOrder $pembayaran_jo)
    {
        $dataSupplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', '=', "Y")
            ->get();
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
     
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $pembayaran_jo->id)
            ->first();
            // dd($dataJaminan?$dataJaminan->nominal:0);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->where('kas_bank.id', '=', 2)
            ->get();
            // kalau bug misal data ga ada, ganti variable sama nama routing sama
            // dd($dataJaminan);
            // dd($pembayaran_jo->no_jo);
            // dd($dataKas[0]->saldo_awal);
            
            // $totalThc =  DB::table('job_order_detail_biaya')
            //     ->where('id_jo', $pembayaran_jo->id)
            //     ->where('keterangan', 'LIKE', '%THC%')
            //     ->sum('nominal');
            // $totalLolo =  DB::table('job_order_detail_biaya')
            //     ->where('id_jo', $pembayaran_jo->id)
            //     ->where('keterangan', 'LIKE', '%LOLO%')
            //     ->sum('nominal');
            // $totalApbs =  DB::table('job_order_detail_biaya')
            //     ->where('id_jo', $pembayaran_jo->id)
            //     ->where('keterangan', 'LIKE', '%APBS%')
            //     ->sum('nominal');
            //  $totalCleaning =  DB::table('job_order_detail_biaya')
            //     ->where('id_jo', $pembayaran_jo->id)
            //     ->where('keterangan', 'LIKE', '%CLEANING%')
            //     ->sum('nominal');
            //  $Docfee =  DB::table('job_order_detail_biaya')
            //     ->select('nominal')
            //     ->where('id_jo', $pembayaran_jo->id)
            //     ->where('keterangan', 'LIKE', '%DOC_FEE%')
            //     ->first();
            // $TotalBiaya  = $totalThc+ $totalLolo +$totalApbs+$totalCleaning+$Docfee->nominal;
        $TotalBiayaRev = $pembayaran_jo->thc+$pembayaran_jo->lolo+$pembayaran_jo->apbs+$pembayaran_jo->cleaning+$pembayaran_jo->doc_fee;

        return view('pages.finance.pembayaran_order.edit',[
            'judul'=>"Pembayaran Job Order",
            'pembayaran_jo'=>$pembayaran_jo,
            'dataSupplier' => $dataSupplier,
            'dataCustomer' =>$dataCustomer,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,
            'dataJaminan' =>$dataJaminan,
            'dataKas'=>$dataKas,
            'TotalBiayaRev'=>$TotalBiayaRev
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobOrder $pembayaran_jo)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

           try {
            $pesanKustom = [
                'pembayaran.required' => 'Pembayaran harus dipilih',
            ];
            $request->validate([
                'pembayaran' => 'required',
            ], $pesanKustom);
            $data = $request->collect();

            // 'MENUNGGU PEMBAYARAN','DALAM PENGIRIMAN'
            $data_saldo_kas_sekarang = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->where('kas_bank.id', '=', $data['pembayaran'])
                ->first();
            $dataJaminan = DB::table('jaminan')
                ->select('*')
                ->where('jaminan.is_aktif', '=', "Y")
                ->where('jaminan.id_job_order', '=', $pembayaran_jo->id)
                ->first();
            $coaJaminan = DB::table('coa')
                ->select('*')
                ->where('coa.is_aktif', '=', "Y")
                ->where('coa.no_akun', '=', 1205)
                ->first();
            $coaPelayaran = DB::table('coa')
                ->select('*')
                ->where('coa.is_aktif', '=', "Y")
                ->where('coa.no_akun', '=', 5003)
                ->first();

            // dd($pembayaran_jo->total_biaya_sebelum_dooring);
            DB::table('job_order')
                ->where('id', $pembayaran_jo['id'])
                ->update(array(
                        'status' => 'PROSES DOORING',
                        'updated_at'=> VariableHelper::TanggalFormat(),
                        'updated_by'=> $user,
                        'is_aktif' => "Y",
                    )
                );

            if($dataJaminan){
                $perhitunganSaldo = $data_saldo_kas_sekarang->saldo_sekarang - (($data['total_sblm_dooring']+$dataJaminan->nominal));
                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['pembayaran'],// id kas_bank dr form
                        now(),//tanggal
                        0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                        $data['total_sblm_dooring']+$dataJaminan->nominal, //uang keluar (kredit)
                        CoaHelper::DataCoa(5003), //kode coa pelayaran
                        'biaya_pelayaran',
                        'UANG KELUAR # BIAYA PELAYARAN + UANG JAMINAN # '.$pembayaran_jo->no_jo.
                        '# PELABUHAN MUAT : '.$pembayaran_jo->pelabuhan_muat.
                        '# PELABUHAN BONGKAR : '.$pembayaran_jo->pelabuhan_bongkar.
                        '# BIAYA SEBELUM DOORING : '.number_format( $data['total_sblm_dooring']).
                        '# BIAYA JAMINAN : '.number_format( $dataJaminan->nominal).
                        '# TOTAL BIAYA : '.number_format( $data['total_sblm_dooring']+$dataJaminan->nominal), //keterangan_transaksi
                        $pembayaran_jo->id,//keterangan_kode_transaksi
                        $user,//created_by
                        now(),//created_at
                        $user,//updated_by
                        now(),//updated_at
                        'Y'
                        ) 
                    );
                    DB::table('jaminan')
                    ->where('id_job_order', $pembayaran_jo['id'])
                    ->where('is_aktif', 'Y')
                    ->update(array(
                        //    'nama' => strtoupper($data['nama']),
                            'status' => 'DIBAYARKAN',
                            'updated_at'=> VariableHelper::TanggalFormat(),
                            'updated_by'=> $user,
                        )
                    );
            }else{
                $perhitunganSaldo = $data_saldo_kas_sekarang->saldo_sekarang - ($data['total_sblm_dooring']);
                 DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    now(),//tanggal
                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                    $data['total_sblm_dooring'], //uang keluar (kredit)
                    CoaHelper::DataCoa(5003), //kode coa pelayaran
                    'biaya_pelayaran',
                    'UANG KELUAR # BIAYA PELAYARAN # '.$pembayaran_jo->no_jo.
                    '# PELABUHAN MUAT : '.$pembayaran_jo->pelabuhan_muat.
                    '# PELABUHAN BONGKAR : '.$pembayaran_jo->pelabuhan_bongkar.
                    '# BIAYA SEBELUM DOORING : '.number_format( $data['total_sblm_dooring']).
                    // '# BIAYA JAMINAN : 0'.
                    '# TOTAL BIAYA : '.number_format( $data['total_sblm_dooring']), //keterangan_transaksi
                    $pembayaran_jo->id,//keterangan_kode_transaksi
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                    ) 
                );
            }

            // dd( $perhitunganSaldo );
            DB::table('kas_bank')
            ->where('id', $data['pembayaran'])
            ->update(array(
                //    'nama' => strtoupper($data['nama']),
                    'saldo_sekarang' => $perhitunganSaldo,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                )
            );
            // dd( $coaJaminan[0]->no_akun );
            // id_kas_bank,1
            //tanggal,2
            // debit,3
            // kredit, 4
            // kode_coa,5
            // keterangan_transaksi,6
            // keterangan_kode_transaksi,7
            // created_by,8
            // created_at,9
            // updated_by,10
            // updated_at 11
            // is_aktif 12
            // DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
            // array(
            //     $data['pembayaran'],// id kas_bank dr form
            //     now(),//tanggal
            //     0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
            //     $data['total_sblm_dooring'], //uang keluar (kredit)
            //     $coaPelayaran->no_akun, //kode coa
            //     'biaya_pelayaran',
            //     'UANG KELUAR - BIAYA PELAYARAN - JO', //keterangan_transaksi
            //     $pembayaran_jo->no_jo,//keterangan_kode_transaksi
            //     $user,//created_by
            //     now(),//created_at
            //     $user,//updated_by
            //     now(),//updated_at
            //     'Y'
            //     ) 
            // );

            // if($dataJaminan)
            // {
            //         // 'MENUNGGU PEMBAYARAN','DIBAYARKAN','KEMBALI'
            //     DB::table('jaminan')
            //     ->where('id_job_order', $pembayaran_jo['id'])
            //     ->where('is_aktif', 'Y')
            //     ->update(array(
            //         //    'nama' => strtoupper($data['nama']),
            //             'status' => 'DIBAYARKAN',
            //             'updated_at'=> VariableHelper::TanggalFormat(),
            //             'updated_by'=> $user,
            //         )
            //     );
            //     DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
            //     array(
            //         $data['pembayaran'],// id kas_bank dr form
            //         now(),//tanggal
            //         0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
            //         $dataJaminan->nominal, //uang keluar (kredit)
            //         $coaJaminan->no_akun, //kode coa
            //         'uang_jaminan',
            //         'UANG KELUAR - UANG JAMINAN - JO', //keterangan_transaksi
            //         $pembayaran_jo->no_jo,//keterangan_kode_transaksi
            //         $user,//created_by
            //         now(),//created_at
            //         $user,//updated_by
            //         now(),//updated_at
            //         'Y'
            //         ) 
            //     );
            // }
            return redirect()->route('pembayaran_jo.index')->with(['status' => 'Success', 'msg' => "Pembayaran Job Order dengan kode $pembayaran_jo->no_jo berhasil"]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobOrder $jobOrder)
    {
        //
    }
}
