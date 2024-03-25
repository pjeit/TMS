<?php

namespace App\Http\Controllers;

use App\Models\JobOrderDetail;
use App\Models\Sewa;
use App\Models\SewaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\JobOrder;
use App\Models\KasBank;
use App\Models\SewaBatalCancel;
use App\Models\KaryawanHutang;
use App\Models\KaryawanHutangTransaction;
use App\Models\KasBankTransaction;
use App\Models\UangJalanRiwayat;
use App\Helper\SewaDataHelper;
use App\Helper\CoaHelper;
use App\Models\Customer;
use App\Models\SewaBiaya;
use App\Models\SewaOperasionalPembayaran;
use App\Models\SewaOperasionalRefund;
use App\Models\SewaOperasionalKembaliStok;
use Exception;
use App\Helper\VariableHelper;
use App\Models\Booking;
use App\Models\KlaimOperasional;
use App\Models\SewaOperasionalKasBon;
use App\Models\SewaOperasionalPembayaranDetail;
use App\Models\Supplier;
use Carbon\Carbon;

class DalamPerjalananController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_DALAM_PERJALANAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_DALAM_PERJALANAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_DALAM_PERJALANAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_DALAM_PERJALANAN', ['only' => ['destroy']]);  
        $this->middleware('permission:CANCEL_DALAM_PERJALANAN', ['only' => ['cancel', 'batal_muat', 'cancel_uang_jalan', 'save_cancel', 'save_batal_muat', 'save_cancel_uang_jalan']]);  
    }

    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

    $dataSewa =  DB::table('sewa AS s')
                ->select('s.*','s.jenis_tujuan','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.status', 'PROSES DOORING')
                // ->where('s.jenis_tujuan', 'FTL')
                // ->whereNull('s.id_supplier')
                ->whereNull('s.tanggal_kembali')
                ->orderBy('c.id','ASC')
                ->orderBy('s.tanggal_berangkat','DESC')
                ->get();
    $sewa_operasional = SewaOperasionalPembayaranDetail::where('is_aktif','Y')->get();
        // dd($dataSewa);
    
        return view('pages.order.dalam_perjalanan.index',[
            'judul'=>"Trucking Order",
            'dataSewa' => $dataSewa,
            'sewa_operasional' => $sewa_operasional,
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
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $dalam_perjalanan)
    {
        //
        $sewa = DB::table('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','jod.seal as seal_pelayaran_jod',
                            'jod.no_kontainer as no_kontainer_jod','gt.nama_tujuan','gt.harga_per_kg','gt.min_muatan',
                            'k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->leftJoin('job_order_detail AS jod', 's.id_jo_detail', '=', 'jod.id')
                    ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                    // ->where('s.jenis_tujuan', 'like', '%FTL%')
                    // ->where('s.status', 'PROSES DOORING')
                    // ->whereNull('s.id_supplier')
                    // ->whereNull('s.tanggal_kembali')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', '=', $dalam_perjalanan->id_sewa)
                    ->groupBy('c.id')
                    ->first();
        // dd($sewa);
        $dataJO=DB::table('job_order as jo')
            ->select('jo.*')
            ->where('jo.is_aktif', '=', "Y")
            ->where('jo.id', '=', $dalam_perjalanan->id_jo)
            ->get();
     
        $datajODetailBiaya = DB::table('job_order_detail_biaya as jodb')
            ->select('jodb.*')
            // ->Join('job_order_detail AS job', function($join) {
            //         $join->on('job.id', '=', 'jodb.id_jo_detail')
            //         ->where('job.is_aktif', '=', 'Y')
            //         ->where('status' ,'like','%BELUM DOORING%')
            //         ->whereNotNull('job.id_grup_tujuan');
            //     })
            ->where('jodb.id_jo_detail', '=', $dalam_perjalanan->id_jo_detail)
            // ->where('status_bayar' ,'SELESAI PEMBAYARAN')
            ->whereIn('status_bayar', ['DIBAYAR PENERIMA', 'DIBAYAR CUSTOMER'])
            ->where('jodb.is_aktif', '=', "Y")
            ->get();
      
        $dataOpreasional = DB::table('sewa_operasional AS so')
                    ->select('so.*')
                    ->where('so.is_aktif', '=', 'Y')
                    ->where('so.id_sewa', '=', $dalam_perjalanan->id_sewa)
                    ->where('so.deskripsi', 'not like', '%ALAT%')
                    ->where(function ($query) {
                        $query->where('so.status', 'like', '%SUDAH DICAIRKAN%')
                            ->orWhere('so.status', 'like', '%TAGIHKAN DI INVOICE%');
                    })
                    ->get();
        $data_operasional_pembayaran_detail = SewaOperasionalPembayaranDetail::where(
            [
                'is_aktif'=>'Y',
                'id_sewa'=>$dalam_perjalanan->id_sewa,
            ]
        )
        ->where(function ($query) {
            $query->where('status', 'like', '%SUDAH DICAIRKAN%')
                ->orWhere('status', 'like', '%TAGIHKAN DI INVOICE%');
        })
        ->where('total_operasional', '>', 0)
        ->get();
        $data_klaim_operasional = KlaimOperasional::where(
            [
                'is_aktif'=>'Y',
                'id_sewa'=>$dalam_perjalanan->id_sewa,
            ]
        )
        ->where('status_klaim', '<>', 'PENDING')
        ->get();
        $dataOpreasionalJO = DB::table('sewa_operasional AS so')
                    ->select('so.*','s.id_sewa','jo.id as id_jo')
                    ->leftJoin('sewa AS s', 'so.id_sewa', '=', 's.id_sewa')
                    ->leftJoin('job_order AS jo', 's.id_jo', '=', 'jo.id')
                    ->where('so.is_aktif', '=', 'Y')
                    // ->where('so.id_sewa', '=', $dalam_perjalanan->id_sewa)
                    ->where('s.id_jo', '=', $dalam_perjalanan->id_jo)
                    ->where('so.deskripsi', 'not like', '%ALAT%')
                    ->where(function ($query) {
                        $query->where('so.status', 'like', '%SUDAH DICAIRKAN%')
                            ->orWhere('so.status', 'like', '%TAGIHKAN DI INVOICE%');
                    })
                    ->get();

        
        $data_tl_buruh = DB::table('sewa_biaya AS sb')
                    ->select('sb.*')
                    ->where('sb.is_aktif', '=', 'Y')
                    ->where('sb.id_sewa', '=', $dalam_perjalanan->id_sewa)
                    ->where('sb.deskripsi', 'not like', '%ALAT%')
                    ->where(function ($query) {
                        $query->where('sb.deskripsi',)
                            ->orWhere('sb.deskripsi',);
                    })
                    ->get();
           
        $array_inbound_parent = [];
        if($dalam_perjalanan->jenis_order=="INBOUND")
        {

            $dataJO=DB::table('job_order as jo')
                ->select('jo.*')
                ->where('jo.is_aktif', '=', "Y")
                ->where('jo.id', '=', $dalam_perjalanan->id_jo)
                ->get();
            foreach ($dataJO as $value) {
                if ($value->thc||$value->thc != 0) {
                    $objthc = [
                        'id_jo'=>$value->id,
                        'deskripsi' => 'THC',
                        'biaya' =>$value->thc,
                    ];
                    array_push($array_inbound_parent, $objthc);
                }
                if ($value->lolo||$value->lolo != 0) {
                    $objlolo = [
                        'id_jo'=>$value->id,
                        'deskripsi' => 'LOLO',
                        'biaya' =>$value->lolo,
                    ];
                    array_push($array_inbound_parent, $objlolo);
                }
                if ($value->apbs||$value->apbs != 0) {
                    $objapbs = [
                        'id_jo'=>$value->id,
                        'deskripsi' => 'APBS',
                        'biaya' =>$value->apbs,
                    ];
                    array_push($array_inbound_parent, $objapbs);
                }
                if ($value->cleaning||$value->cleaning != 0) {
                    $objcleaning = [
                        'id_jo'=>$value->id,
                        'deskripsi' => 'CLEANING',
                        'biaya' =>$value->cleaning,
                    ];
                    array_push($array_inbound_parent, $objcleaning);
                }
                if ($value->doc_fee||$value->doc_fee != 0) {
                    $objdoc_fee = [
                        'id_jo'=>$value->id,
                        'deskripsi' => 'DOCFEE',
                        'biaya' =>$value->doc_fee,
                    ];
                    array_push($array_inbound_parent, $objdoc_fee);
                }
            }
             //yang thc lolo
            foreach($dataOpreasionalJO as $opersional)
            {
                foreach ($array_inbound_parent as $key=> $dataInbound) {
                    
                    if($opersional->deskripsi == $dataInbound['deskripsi'] && 
                    $opersional->total_dicairkan == $dataInbound['biaya'] &&
                    $opersional->id_jo ==  $dataInbound['id_jo']
                    )
                    {
                        //hapus array kalau datanya sama 
                        unset($array_inbound_parent[$key]);
                    }
                }
            }
        }

        $array_inbound = [];
        $array_outbond = [];
         // dd($array_inbound);
        // dd($array_inbound_parent);
        $Tujuan = DB::table('grup_tujuan as gt')
            ->select('gt.*')
            ->where('gt.id', '=',  $dalam_perjalanan->id_grup_tujuan)
            ->where('gt.is_aktif', '=', "Y")
            ->get();
        $data_tl_buruh = DB::table('sewa_biaya AS sb')
                    ->select('sb.*')
                    ->where('sb.is_aktif', '=', 'Y')
                    ->where('sb.id_sewa', $dalam_perjalanan->id_sewa)
                    ->where(function ($query) {
                        $query->where('sb.deskripsi','TL')
                            ->orWhere('sb.deskripsi','BURUH');
                    })
                    ->get();
        // dd($data_tl_buruh);
        //buat get data tl sama buruh di sewa biaya
        if(isset($data_tl_buruh))
        {
            foreach ($data_tl_buruh as $item) {
                    if($item->deskripsi=="TL")
                    {
                        $objek = [
                            'deskripsi' => 'TL',
                            'biaya' => 50000,
                        ];
                    }
                    else
                    {
                        $objek = [
                            'deskripsi' => $item->deskripsi,
                            'biaya' => $item->biaya,
                        ];
                    }
                    array_push($array_inbound, $objek);
                    array_push($array_outbond, $objek);
            }
        }
        if(isset($data_operasional_pembayaran_detail))
        {
            foreach ($data_operasional_pembayaran_detail as $item) {
                    
                        $objek = [
                            'id_pembayaran_detail' => $item->id,
                            'deskripsi' => $item->deskripsi,
                            'dicairkan' => $item->total_dicairkan,
                            'biaya' => $item->total_operasional,
                        ];
                    array_push($array_inbound, $objek);
                    array_push($array_outbond, $objek);
            }
        }
        if(isset($data_klaim_operasional))
        {
            foreach ($data_klaim_operasional as $item) {
                    
                        $objek = [
                            'id_klaim_detail' => $item->id,
                            'deskripsi' => $item->jenis_klaim,
                            'dicairkan' => 0,
                            'biaya' => $item->total_klaim,
                        ];
                    array_push($array_inbound, $objek);
                    array_push($array_outbond, $objek);
            }
        }
        
            // dd($array_outbond);
        foreach ($datajODetailBiaya as $item) {
            
            if ($item->storage || $item->storage != 0) {
                $objSTORAGE = [
                    'deskripsi' => 'STORAGE',
                    'biaya' => $item->storage,
                ];
                array_push($array_inbound, $objSTORAGE);
            }
            if ($item->demurage || $item->demurage != 0) {
                $objDEMURAGE = [
                    'deskripsi' => 'DEMURAGE',
                    'biaya' => $item->demurage,
                ];
                array_push($array_inbound, $objDEMURAGE);
            }
            if ($item->detention ||$item->detention != 0) {
                $objDETENTION = [
                    'deskripsi' => 'DETENTION',
                    'biaya' =>$item->detention,
                ];
                array_push($array_inbound, $objDETENTION);
            }
            if ($item->repair ||$item->repair != 0) {
                $objRepair = [
                    'deskripsi' => 'REPAIR',
                    'biaya' =>$item->repair,
                ];
                array_push($array_inbound, $objRepair);
            }
            if ($item->washing ||$item->washing != 0) {
                $objWashing = [
                    'deskripsi' => 'WASHING',
                    'biaya' =>$item->washing,
                ];
                array_push($array_inbound, $objWashing);
            }
        }
        foreach ($Tujuan as $item) {
            if ($item->plastik) {
                $objPlastik = [
                    'deskripsi' => 'SEAL PELAYARAN',
                    'biaya' => $item->seal_pelayaran,
                ];
                array_push($array_outbond, $objPlastik);
            }
            if ($item->plastik) {
                $objPlastik = [
                    'deskripsi' => 'TALLY',
                    'biaya' => $item->tally,
                ];
                array_push($array_outbond, $objPlastik);
                array_push($array_inbound, $objPlastik);

            }
            if ($item->seal_pje) {
                $objSealPje = [
                    'deskripsi' => 'SEAL PJE',
                    'biaya' => $item->seal_pje,
                ];
                array_push($array_outbond, $objSealPje);
            }
    
            if ($item->plastik) {
                $objPlastik = [
                    'deskripsi' => 'PLASTIK',
                    'biaya' => $item->plastik,
                ];
                array_push($array_outbond, $objPlastik);
            }
            
        }
        //yang seal pje sama plastik dari grup tujuan
        foreach($dataOpreasional as $opersional)
        {
            foreach ($array_outbond as $key=> $dataOutbond) {
                
                if($opersional->deskripsi == $dataOutbond['deskripsi'] && $opersional->total_operasional == $dataOutbond['biaya'] )
                {
                    //hapus array kalau datanya sama 
                    unset($array_outbond[$key]);
                }
            }
        }
        //yang storage demurage dkk
        foreach($dataOpreasional as $opersional)
        {
            foreach ($array_inbound as $key=> $dataInbound) {
               
                if($opersional->deskripsi == $dataInbound['deskripsi'] && $opersional->total_operasional == $dataInbound['biaya'] )
                {
                    //hapus array kalau datanya sama 
                    unset($array_inbound[$key]);
                }
            }
        }
        // dd(isset($array_outbond[0]['id_pembayaran_detail']));
        //sorting array berdasarkan deskripsi
        usort($array_outbond, function ($a, $b) {
            return strcmp($b['deskripsi'],$a['deskripsi']);
        });
        usort($array_inbound, function ($a, $b) {
            return strcmp($b['deskripsi'],$a['deskripsi']);
        });
        // dd($array_outbond);
        return view('pages.order.dalam_perjalanan.form',[
            'judul' => "Dalam Perjalanan",
            'sewa'=>$sewa,
            'dataOpreasional'=>$dataOpreasional,
            'dataOpreasionalJO'=>$dataOpreasionalJO,
            'array_inbound'=>$array_inbound,
            'array_outbond'=>$array_outbond,
            'array_inbound_parent'=>$array_inbound_parent
            // 'datajODetail'=>$datajODetail,
            // 'TujuanBiaya'=>$TujuanBiaya
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $dalam_perjalanan)
    {
        //
        $data = $request->post();
        $user = Auth::user()->id; 
        // dd(/*isset(*/$data/*[0]['masuk_db'])*/); 
        // dd($dalam_perjalanan->jenis_order);
        // dd($data);
        try {
            $dalam_perjalanan->catatan = isset($data['catatan'])? $data['catatan']:null;
            $dalam_perjalanan->no_surat_jalan = isset($data['surat_jalan'])? $data['surat_jalan']:null;
            $dalam_perjalanan->seal_pelayaran = isset($data['seal'])? $data['seal']:null;
            $dalam_perjalanan->seal_pje = isset($data['seal_pje'])? $data['seal_pje']:null;
            $dalam_perjalanan->no_kontainer = isset($data['no_kontainer'])? $data['no_kontainer']:null;
            if( $dalam_perjalanan->status == 'PROSES DOORING')
            {
                $tgl_kembali = isset($data['tanggal_kembali'])?date_create_from_format('d-M-Y', $data['tanggal_kembali']):null;

                $dalam_perjalanan->tanggal_kembali = isset($tgl_kembali)? date_format($tgl_kembali, 'Y-m-d H:i:s'):null;
                $dalam_perjalanan->status = $data['is_kembali']=='Y'? 'MENUNGGU INVOICE':'PROSES DOORING';
                $dalam_perjalanan->is_kembali = $data['is_kembali'];
                if ($data['jenis_tujuan']=='LTL') {
                    $dalam_perjalanan->jumlah_muatan = $data['muatan_ltl'];
                    $dalam_perjalanan->total_tarif = floatval(str_replace(',', '', $data['total_harga_ltl']));
                }
            }
            $dalam_perjalanan->updated_by = $user;
            $dalam_perjalanan->updated_at = now();
            $dalam_perjalanan->save();
            // diganti trigger
            // if($dalam_perjalanan->jenis_order=='INBOUND'){

            //     if(isset($data['is_kembali']))
            //     {
            //         if($data['is_kembali']=='Y')
            //         {
            //             $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($data['id_jo_detail_hidden']);
            //             $JOD->status = 'MENUNGGU INVOICE';
            //             $JOD->save();
            //         }
            //     }
                
            // }
            //ini kalo dicentang yang harcode di html
            if(isset($data['data_hardcode']))
            {
                foreach ($data['data_hardcode'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));

                    if(isset($value['masuk_db']))
                    {
                        $SOP = new SewaOperasional();
                        $SOP->id_sewa = $dalam_perjalanan->id_sewa; 
                        $SOP->deskripsi = $value['deskripsi_data'];
                        $SOP->total_operasional = (float)str_replace(',', '', $value['nominal_ditagihkan']);
                        // $SOP->total_dicairkan = (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->total_dicairkan = 0;
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        $SOP->keterangan_internal = "[DATA-HARDCODE-DALAM-PERJALANAN]";
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                    
                }
            }
            //ini kalo ada data di db
            if(isset($data['data']))
            {
                $deskripsi1 = ['STORAGE', 
                                'DEMURAGE', 
                                'DETENTION', 
                                'REPAIR', 
                                'WASHING', 
                                'SEAL PELAYARAN', 
                                'SEAL PJE', 
                                'PLASTIK', 
                                'TALLY', 
                                'TIMBANG', 
                                'BURUH', 
                                'LEMBUR', 'THC', 'LOLO', 'APBS', 'TL', 'DOCFEE','BIAYA DEPO'];
                foreach ($data['data'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));

                    if(isset($value['masuk_db']))
                    {
                        // if (in_array($value['deskripsi_data'], $deskripsi1)) {
                            DB::table('sewa_operasional')
                            ->where('id_sewa', $dalam_perjalanan->id_sewa)
                            ->where('id', $value['id_sewa_operasional_data'])
                            ->update([
                                'is_ditagihkan' => $value['ditagihkan_data_value'],
                                'is_dipisahkan' => $value['dipisahkan_data_value'],
                                'total_operasional' => (float)str_replace(',', '', $value['nominal_ditagihkan']),
                                'catatan' => $value['catatan_data'],
                                'updated_at' => now(),
                                'updated_by' => $user,
                            ]);
                        // } else if(!in_array($value['deskripsi_data'], $deskripsi1)) {
                        //     DB::table('sewa_operasional')
                        //     ->where('id_sewa', $dalam_perjalanan->id_sewa)
                        //     ->where('id', $value['id_sewa_operasional_data'])
                        //     ->update([
                        //         'deskripsi' => $value['deskripsi_data'],
                        //         'total_operasional' => (float)str_replace(',', '', $value['nominal_ditagihkan']),
                        //         'total_dicairkan' => (float)str_replace(',', '', $value['nominal_data']),
                        //         'is_ditagihkan' => $value['ditagihkan_data_value'],
                        //         'is_dipisahkan' => $value['dipisahkan_data_value'],
                        //         'catatan' => $value['catatan_data'],
                        //         'updated_at' => now(),
                        //         'updated_by' => $user,
                        //     ]);
                        // }
                    }
                    else
                    {
                        DB::table('sewa_operasional')
                            ->where('id_sewa', $dalam_perjalanan->id_sewa)
                            ->where('id', $value['id_sewa_operasional_data'])
                            ->update([
                                'deskripsi' => $value['deskripsi_data'],
                                // 'total_operasional' => (float)str_replace(',', '', $value['nominal_data']),
                                'is_ditagihkan' => $value['ditagihkan_data_value'],
                                'is_dipisahkan' => $value['dipisahkan_data_value'],
                                'is_aktif' => 'N',
                                'catatan' => $value['catatan_data'],
                                'updated_at' => now(),
                                'updated_by' => $user,
                            ]); 
                    }
                 
                }
            }
            //ini kalo dicentang data yang ambil dari db tujuan biaya/ jodetail biaya yang S/D/T
            if(isset($data['dataMaster']))
            {
                
                foreach ($data['dataMaster'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));
                    $id_pembayaran_detail= isset($value['id_pembayaran_detail'])?$value['id_pembayaran_detail']:null;
                    $id_klaim_detail= isset($value['id_klaim_detail'])?$value['id_klaim_detail']:null;

                    if(isset($value['masuk_db']))
                    {
                    
                        $SOP = new SewaOperasional();
                        $SOP->id_sewa = $dalam_perjalanan->id_sewa; 
                        $SOP->id_pembayaran_detail = $id_pembayaran_detail;
                        $SOP->id_klaim_detail = $id_klaim_detail;
                        $SOP->deskripsi = $value['deskripsi_data'];
                        $SOP->total_operasional =  (float)str_replace(',', '', $value['nominal_ditagihkan']);
                        // $SOP->total_dicairkan = (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->total_dicairkan = 0;
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        if ($id_pembayaran_detail) {
                            # code...
                            $SOP->keterangan_internal = "[DATA-OPERASIONAL]";
                        }
                        else if($id_klaim_detail)
                        {
                            $SOP->keterangan_internal = "[DATA-KLAIM-OPERASIONAL]";
                        }
                        else
                        {
                            $SOP->keterangan_internal = "[DATA-MASTER-DALAM-PERJALANAN]";
                        }
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                  
                }
            }
            //ini kalo dicentang dan nambah data baru yang user ngetik sendiri
            // dd($data);
            if(isset($data['dataLain']))
            {
                foreach ($data['dataLain'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));

                    if(isset($value['masuk_db']))
                    {
                        $SOP = new SewaOperasional();
                        $SOP->id_sewa = $dalam_perjalanan->id_sewa; 
                        $SOP->deskripsi = $value['deskripsi_data'];
                        $SOP->total_operasional =  (float)str_replace(',', '', $value['nominal_ditagihkan']);
                        // $SOP->total_dicairkan = (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->total_dicairkan = 0;
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        $SOP->keterangan_internal = "[DATA-LAIN-DALAM-PERJALANAN]";
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                   
                }
            }


            // if($dalam_perjalanan->status=='PROSES DOORING')
            // {
                return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data!"]);
            // }
            // else
            // {
            //     return redirect()->route('belum_invoice.create')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data!"]);
            // }
            
        } catch (ValidationException $e) {
            //throw $th;
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage())->withInput();

        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }

   

    public function batal_muat($id)
    {
        $sewa = Sewa::with('customer')
        ->where('is_aktif', 'Y')->find($id);
        // dd($sewa->id_supplier);
        $supplier = DB::table('supplier as s')
            ->select('s.*')
            ->where('s.is_aktif', '=', "Y")
            ->where('s.id', '=', $sewa->id_supplier)
            ->first();
        $kasbank = KasBank::where('is_aktif', 'Y')->get();
        $riwayatPotongHutang = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $id)->first();
        
        return view('pages.order.dalam_perjalanan.batal_muat',[
            'judul' => "batal muat",
            'data' => $sewa,
            'kasbank' => $kasbank,
            'riwayatPotongHutang' => $riwayatPotongHutang,
            'supplier' => $supplier,
            
        ]);
    }

     public function save_batal_muat(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        $tgl_batal_muat_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
        $tgl_kembali = date_create_from_format('d-M-Y', $data['tanggal_kembali']);
        DB::beginTransaction(); 
        // dd($tgl_kembali);
        
        try {
            // $sewa->status = 'BATAL MUAT';
            $sewa->status = 'MENUNGGU INVOICE';
            $sewa->	is_batal_muat = 'Y';
            $customer = Customer::where('is_aktif', '=', "Y")->find($sewa->id_customer);
            if($customer){
                $customer->kredit_sekarang -= $sewa->total_tarif - floatval(str_replace(',', '', $data['total_tarif_tagih']));
                $customer->updated_by = $user;
                $customer->updated_at = now();
                $customer->save();
            }
            $sewa->total_tarif = floatval(str_replace(',', '', $data['total_tarif_tagih']));
            $sewa->alasan_hapus = $data['alasan_cancel'].'[batal_muat]';
            $sewa->no_kontainer = $data['no_kontainer'];
            $sewa->no_surat_jalan = $data['no_surat_jalan'];
            $sewa->is_kembali = 'Y';
            $sewa->tanggal_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            
            // if($sewa->save()){
            if($sewa->id_supplier==null)
            {
                if(isset($data['total_uang_jalan_kembali']))
                {
                    if($sewa->total_uang_jalan>0)
                    {
                        $sewa->total_uang_jalan -= floatval(str_replace(',', '', $data['total_uang_jalan_kembali']));
                    }
                }

                $uj_kembali = isset($data['total_uang_jalan_kembali'])? floatval(str_replace(',', '', $data['total_uang_jalan_kembali'])):0;
        
                $batal = new SewaBatalCancel();
                $batal->id_sewa = $sewa->id_sewa;
                $batal->jenis = 'BATAL';
                $batal->tgl_batal_muat_cancel = $tgl_batal_muat_cancel;
                $batal->total_tarif_ditagihkan = floatval(str_replace(',', '', $data['total_tarif_tagih']));
                $batal->total_uang_jalan_kembali = $uj_kembali;
                if(isset($data['kasbank'])){
                    if($data['kasbank'] != 'HUTANG DRIVER'){
                        $batal->id_kas_bank = $data['kasbank'];
                    }else{
                        $batal->id_karyawan_hutang = $data['id_karyawan'];
                    }
                }
                $batal->tgl_kembali = $tgl_kembali;
                $batal->alasan_batal = $data['alasan_cancel'];
                $batal->created_by = $user;
                $batal->created_at = now();
                $batal->is_aktif = 'Y';
                if($batal->save()){
                    if(isset($data['kasbank'])){
                        $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();

                        // $cek = KasBankTransaction::where('is_aktif', 'Y')
                        //                         ->where('id_kas_bank', $data['kasbank'])
                        //                         ->where('keterangan_kode_transaksi', $riwayat_uang_jalan->id)
                        //                         ->where('jenis', 'uang_jalan')->first();

                        if($riwayat_uang_jalan){
                            if($data['kasbank'] != 'HUTANG DRIVER'){
                                $kasBankTransaction = new KasBankTransaction ();
                                $kasBankTransaction->id_kas_bank = $data['kasbank'];
                                $kasBankTransaction->tanggal = $tgl_batal_muat_cancel;
                                $kasBankTransaction->debit = $uj_kembali; // debit uang masuk
                                $kasBankTransaction->kredit = 0;
                                $kasBankTransaction->jenis = 'uang_jalan';
                                $kasBankTransaction->keterangan_transaksi = '(BATAL MUAT) UANG JALAN KEMBALI - '. '['.$data['no_sewa'] .']' . $data['alasan_cancel'] . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver']. $data['customer'].' >> ' . '('.$data['tujuan'].')' ;
                                $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // kode coa uang jalan
                                $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
                                $kasBankTransaction->created_by = $user;
                                $kasBankTransaction->created_at = now();
                                $kasBankTransaction->is_aktif = 'Y';
                                // dd($kasBankTransaction);
                                if($kasBankTransaction->save()){
                                    $kasbank = KasBank::where('is_aktif', 'Y')->find($data['kasbank']);
                                    $kasbank->saldo_sekarang += $uj_kembali;
                                    $kasbank->updated_by = $user;
                                    $kasbank->updated_at = now();
                                    $kasbank->save();
                                    DB::commit();
                                }
                            }else{
                                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
        
                                if(isset($kh)){
                                    // kalau ada data, update hutang
                                    $kh->total_hutang += $uj_kembali; 
                                    $kh->updated_by = $user;
                                    $kh->updated_at = now();
                                    $kh->save();
                                }else{
                                    // kalau tidak ada data, buat data hutang baru
                                    $kh = new KaryawanHutang();
                                    $kh->id_karyawan = $data['id_karyawan'];
                                    $kh->total_hutang += $uj_kembali;
                                    $kh->created_by = $user;
                                    $kh->created_at = now();
                                    $kh->is_aktif = 'Y';
                                    $kh->save();
                                }
        
                                $kht = new KaryawanHutangTransaction();
                                $kht->id_karyawan = $data['id_karyawan'];
                                $kht->refrensi_id = $sewa->id_sewa;
                                // $kht->refrensi_keterangan = '[BATAL MUAT]-refrensi_nya_id_sewa = '. $sewa->id_sewa;
                                $kht->refrensi_keterangan = 'batal_muat';
                                $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                // $kht->tanggal = date_format($tgl_batal_muat_cancel, 'Y-m-d H:i:s');
                                $kht->tanggal = $tgl_batal_muat_cancel;
                                $kht->debit = $uj_kembali;
                                $kht->kredit = 0;
                                $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                $kht->catatan = 'BATAL MUAT >> - '. '['.$data['no_sewa'] .']' . $data['alasan_cancel'] . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver']. $data['customer'].' >> ' . '('.$data['tujuan'].')';
                                $kht->created_by = $user;
                                $kht->created_at = now();
                                $kht->is_aktif = 'Y';
                                $kht->save();
                                
                                DB::commit();
                            }
                        }
                    }
                }
            }
            else
            {
                $batalRekanan = new SewaBatalCancel();
                $batalRekanan->id_sewa = $sewa->id_sewa;
                $batalRekanan->jenis = 'BATAL';
                $batalRekanan->tgl_batal_muat_cancel =  $tgl_batal_muat_cancel;
                $batalRekanan->total_tarif_ditagihkan = floatval(str_replace(',', '', $data['total_tarif_tagih']));
                $batalRekanan->tgl_kembali = $tgl_kembali;
                $batalRekanan->alasan_batal = $data['alasan_cancel']. '['.$data['no_sewa'] .']'.'[rekanan batal]';
                $batalRekanan->created_by = $user;
                $batalRekanan->created_at = now();
                $batalRekanan->is_aktif = 'Y';
                $batalRekanan->save();
                DB::commit();

            }
            // }
            $sewa->save();
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data batal muat!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }

    }

    public function refund_operasional($id)
    {
        $sewa = Sewa::with('customer')->where('is_aktif', 'Y')->find($id);
        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();
        $supplier = DB::table('supplier as s')
            ->select('s.*')
            ->where('s.is_aktif', '=', "Y")
            ->where('s.id', '=', $sewa->id_supplier)
            ->first();
        $dataOperasional = SewaOperasionalPembayaranDetail::selectRaw('
        sewa_operasional_pembayaran_detail.id_sewa as so_id_sewa, 
        sewa_operasional_pembayaran_detail.id as so_id, 
        s.no_polisi as sewa_kendaraan, 
        s.nama_tujuan as sewa_tujuan, 
        COALESCE(s.nama_driver, CONCAT("DRIVER REKANAN ", sp.nama)) as sewa_driver, 
        s.no_sewa as no_sewa, 
        sewa_operasional_pembayaran_detail.catatan as so_catatan,
        sewa_operasional_pembayaran_detail.id_pembayaran as so_id_pembayaran, 
        sewa_operasional_pembayaran_detail.id_kasbon as so_id_kasbon, 
        sewa_operasional_pembayaran_detail.id_stok as so_id_stok, 
        sewa_operasional_pembayaran_detail.total_dicairkan as so_total_dicairkan, 
        sop.id_kas_bank as id_kas_bank, 
        sewa_operasional_pembayaran_detail.deskripsi as so_deskripsi')
        ->where('sewa_operasional_pembayaran_detail.is_aktif', '=', 'Y')
        ->where('sewa_operasional_pembayaran_detail.id_sewa', '=', $sewa->id_sewa)
        ->leftJoin('sewa AS s', function($join) {
                    $join->on('sewa_operasional_pembayaran_detail.id_sewa', '=', 's.id_sewa')
                    ->where('s.is_aktif', 'Y')
                    ->where('s.status', 'PROSES DOORING')
                    ;
                })
        ->leftJoin('sewa_operasional_pembayaran AS sop', function($join) {
                    $join->on('sop.id', '=', 'sewa_operasional_pembayaran_detail.id_pembayaran')
                    ->where('sop.is_aktif', 'Y')
                    ;
                })
        ->leftJoin('supplier AS sp', function($join) {
                    $join->on('s.id_supplier', '=', 'sp.id')
                    ->where('sp.is_aktif', '=', 'Y');
                })
        ->where(function ($query) {
            $query->where('sewa_operasional_pembayaran_detail.status','SUDAH DICAIRKAN')
                ->orWhere('sewa_operasional_pembayaran_detail.status','TAGIHKAN DI INVOICE')
                ->orWhere('sewa_operasional_pembayaran_detail.status','KASBON');

        })
        ->get();
        // dd($dataOperasional);

        return view('pages.order.dalam_perjalanan.refund_operasional',[
            'judul' => "Refund Operasional",
            'data' => $sewa,
            'id_sewa' => $id,
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'dataOperasional' => $dataOperasional,
        ]);
    }
    public function save_refund_operasional(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // $sewa_biaya = SewaBiaya::where('is_aktif', '=', "Y")->where('id_sewa',$sewa->id_sewa)->get();
        // dd(explode(',' ,$data['data'][0]['id_sewa_operasional_data']));
        // $sdsdsd= SewaOperasional::where('is_aktif', '=', 'Y')
        //                 ->whereIn('id', explode(',' ,$data['data'][1]['id_operasional_data']))
        //                 ->get();
        // dd(isset($data['data'][0]['id_pembayaran_operasional']));
        // dd($data);
        try {
                if(isset($data['data']))
                {
                    foreach ($data['data'] as $value) {
                        // dd($value['kembali']);
                        if(isset($value['is_kembali']))
                        {
                            if($value['is_kembali']=='Y')
                            {
                                if ($value['kembali']=='KEMBALI_STOK') {
                                    $status = 'STOK';
                                    $keterangan_internal = '[REFUND-MASUK-STOK]';
                                }
                                if ($value['kembali']=='kasbon') {
                                    $status = 'KASBON';
                                    $keterangan_internal = '[REFUND-MASUK-KASBON]';
                                }
                                else if($value['kembali']=='DATA_DI_HAPUS')
                                {
                                    $status = 'HAPUS';
                                    $keterangan_internal = '[REFUND-TIDAK-ADA-PENCAIRAN]';
                                }
                                else
                                {
                                    $status = 'HAPUS';
                                    $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                                }
                                $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($value['id_pembayaran_operasional']);
                               
                                if(isset($value['id_pembayaran_operasional']))
                                {
                                    if($value['kembali']!='KEMBALI_STOK'&&$value['kembali']!='DATA_DI_HAPUS'&&$value['kembali']!='kasbon')
                                    {
                                        $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($value['id_pembayaran_operasional']);
                                        if($so_pembayaran){
                                            $so_pembayaran->total_refund += (float)str_replace(',', '', $value['total_dicairkan']);
                                            $so_pembayaran->updated_by = $user;
                                            $so_pembayaran->updated_at = now();
                                            // $so_pembayaran->is_aktif = 'N';
                                            // $so_pembayaran->save();
                                            if ($so_pembayaran->save()) {
            
                                                $so_refund = new SewaOperasionalRefund();
                                                $so_refund ->id_kas_bank = $value['kembali'];
                                                $so_refund ->tanggal_refund = now();
                                                $so_refund ->deskripsi_ops = $value['deskripsi_data'];
                                                $so_refund ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                                                $so_refund->catatan_refund = $value['catatan'];
                                                $so_refund->created_by = $user;
                                                $so_refund->created_at = now();
                                                $so_refund->is_aktif = 'Y';
                                                // $so_refund->save();
                                                if($so_refund->save())
                                                {
                                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                                    ->where('id', $value['id_pembayaran_detail'])
                                                    ->update([
                                                            'is_aktif' => 'N',
                                                            'status' => $status,
                                                            'keterangan_internal'=>$keterangan_internal,
                                                            'id_refund'=>$so_refund->id,
                                                            'total_refund'=>(float)str_replace(',', '', $value['total_dicairkan'])
                                                        ]);
                                                    
                                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                            array(
                                                                $value['kembali'],// id kas_bank dr form
                                                                now(),//tanggal
                                                                (float)str_replace(',', '', $value['total_dicairkan']),// debit 
                                                                0, //uang keluar (kredit)
                                                                CoaHelper::DataCoa(1100), //kode coa piutang usaha
                                                                'operasional_refund',
                                                                $value['rincian'].'>>'.$so_refund->catatan_refund, //keterangan_transaksi
                                                                $so_refund->id,//keterangan_kode_transaksi id refundnya
                                                                $user,//created_by
                                                                now(),//created_at
                                                                $user,//updated_by
                                                                now(),//updated_at
                                                                'Y'
                                                            ) 
                                                        );
                                                        $kas_bank = KasBank::where('is_aktif', 'Y')->find($value['kembali']);
                                                        $kas_bank->saldo_sekarang += (float)str_replace(',', '', $value['total_dicairkan']);
                                                        $kas_bank->updated_by = $user;
                                                        $kas_bank->updated_at = now();
                                                        $kas_bank->save();
                                                }
                                            }
                                        }
                                    }
                                    else if($value['kembali']=='KEMBALI_STOK')
                                    {
                                        $so_pembayaran->total_kembali_stok += 1; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        // $so_pembayaran->save();
                                        if($so_pembayaran->save())
                                        {
                                            $so_stok = new SewaOperasionalKembaliStok();
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
                                        $so_pembayaran->total_kasbon += $total_dicairkan; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        if($so_pembayaran->save())
                                        {
                                            $kasbon_operasional = new SewaOperasionalKasBon();
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
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_kasbon_kembali'=>$kasbon_operasional->id,
                                                    'total_kasbon_kembali'=>$total_dicairkan
                                                ]);
                                        }
                                    }
                                }
                                else
                                {
                                    if($value['kembali']=='KEMBALI_STOK')
                                    {
                                        $so_pembayaran->total_kembali_stok += 1; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        // $so_pembayaran->save();
                                        if($so_pembayaran->save())
                                        {
                                            $so_stok = new SewaOperasionalKembaliStok();
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
                                        $so_pembayaran->total_kasbon += $total_dicairkan; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        if($so_pembayaran->save())
                                        {
                                            $kasbon_operasional = new SewaOperasionalKasBon();
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
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_kasbon_kembali'=>$kasbon_operasional->id,
                                                    'total_kasbon_kembali'=>$total_dicairkan
                                                ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil Refund Operasional!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }

    }
    public function cancel($id)
    {
        $sewa = Sewa::with('customer')->where('is_aktif', 'Y')->find($id);

        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();
        $supplier = DB::table('supplier as s')
            ->select('s.*')
            ->where('s.is_aktif', '=', "Y")
            ->where('s.id', '=', $sewa->id_supplier)
            ->first();

        $dataOperasional = SewaOperasionalPembayaranDetail::selectRaw('
        sewa_operasional_pembayaran_detail.id_sewa as so_id_sewa, 
        sewa_operasional_pembayaran_detail.id as so_id, 
        s.no_polisi as sewa_kendaraan, 
        s.nama_tujuan as sewa_tujuan, 
        COALESCE(s.nama_driver, CONCAT("DRIVER REKANAN ", sp.nama)) as sewa_driver, 
        s.no_sewa as no_sewa, 
        sewa_operasional_pembayaran_detail.catatan as so_catatan,
        sewa_operasional_pembayaran_detail.id_kasbon as so_id_kasbon, 
        sewa_operasional_pembayaran_detail.id_stok as so_id_stok, 
        sewa_operasional_pembayaran_detail.id_pembayaran as so_id_pembayaran, 
        sewa_operasional_pembayaran_detail.total_dicairkan as so_total_dicairkan, 
        sop.id_kas_bank as id_kas_bank, 
        sewa_operasional_pembayaran_detail.deskripsi as so_deskripsi')
        ->where('sewa_operasional_pembayaran_detail.is_aktif', '=', 'Y')
        ->where('sewa_operasional_pembayaran_detail.id_sewa', '=', $sewa->id_sewa)
        ->leftJoin('sewa AS s', function($join) {
                    $join->on('sewa_operasional_pembayaran_detail.id_sewa', '=', 's.id_sewa')
                    ->where('s.is_aktif', 'Y')
                    ->where('s.status', 'PROSES DOORING')
                    ;
                })
        ->leftJoin('sewa_operasional_pembayaran AS sop', function($join) {
                    $join->on('sop.id', '=', 'sewa_operasional_pembayaran_detail.id_pembayaran')
                    ->where('sop.is_aktif', 'Y')
                    ;
                })
        ->leftJoin('supplier AS sp', function($join) {
                    $join->on('s.id_supplier', '=', 'sp.id')
                    ->where('sp.is_aktif', '=', 'Y');
                })
        ->where(function ($query) {
            $query->where('sewa_operasional_pembayaran_detail.status','SUDAH DICAIRKAN')
            ->orWhere('sewa_operasional_pembayaran_detail.status','TAGIHKAN DI INVOICE')
            ->orWhere('sewa_operasional_pembayaran_detail.status','KASBON');
        })
        ->get();
        

        $ujr = UangJalanRiwayat::where([
                                        'is_aktif' => 'Y',
                                        'sewa_id' => $id
                                    ])->first();
        return view('pages.order.dalam_perjalanan.cancel',[
            'judul' => "cancel",
            'data' => $sewa,
            'id_sewa' => $id,
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'ujr' => $ujr,
            'dataOperasional' => $dataOperasional,
        ]);
    }
    public function save_cancel(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        $tgl_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // $sewa_biaya = SewaBiaya::where('is_aktif', '=', "Y")->where('id_sewa',$sewa->id_sewa)->get();
        // dd(explode(',' ,$data['data'][0]['id_sewa_operasional_data']));
        // $sdsdsd= SewaOperasional::where('is_aktif', '=', 'Y')
        //                 ->whereIn('id', explode(',' ,$data['data'][1]['id_operasional_data']))
        //                 ->get();
        // dd(isset($data['data'][0]['id_pembayaran_operasional']));
        // dd($data);

        try {
            // $sewa_cancel = Sewa::where('is_aktif', 'Y')->find($sewa->id_sewa);
            // dd($sewa_cancel);
            $sewa->status = 'CANCEL';
            $customer = Customer::where('is_aktif', '=', "Y")->find($sewa->id_customer);
            if($customer){
                $customer->kredit_sekarang -= $sewa->total_tarif;
                $customer->updated_by = $user;
                $customer->updated_at = now();
                $customer->save();
            }
            $sewa->alasan_hapus = $data['alasan_cancel'].'[cancel]';
            $sewa->updated_at = now();
            $sewa->updated_by = $user;
            $sewa->is_aktif = 'N';
            if($sewa->save()){
                //ubah status jo detail kalo ada, di trigger
                // set is_aktif N buat sewa_biaya dan sewa_operasional DAN KASNYA
                // $sewa_biaya = SewaBiaya::where('is_aktif', '=', "Y")->where($sewa->id_sewa)->get();
                // dd($sewa_biaya);
                SewaBiaya::where('is_aktif', '=', 'Y')
                ->where('id_sewa', $sewa->id_sewa)
                ->update(['is_aktif' => 'N']);
                // if(isset($data['data']))
                // {
                //     foreach ($data['data'] as $value) {
                //         // dd($value['kembali']);
                //         if ($value['kembali']=='KEMBALI_STOK') {
                //             $status = 'STOK';
                //             $keterangan_internal = '[REFUND-MASUK-STOK]';
                //         }
                //         if ($value['kembali']=='kasbon') {
                //             $status = 'KASBON';
                //             $keterangan_internal = '[REFUND-MASUK-KASBON]';
                //         }
                //         else if($value['kembali']=='DATA_DI_HAPUS')
                //         {
                //             $status = 'HAPUS';
                //             $keterangan_internal = '[REFUND-TIDAK-ADA-PENCAIRAN]';
                //         }
                //         else
                //         {
                //             $status = 'HAPUS';
                //             $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                //         }
                //         SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                //         // ->whereIn('id',  explode(',' ,$value['id_operasional_data']))
                //         ->where('id', $value['id_operasional_data'])
                //         ->update([
                //                 'is_aktif' => 'N',
                //                 'status' => $status,
                //                 'keterangan_internal'=>$keterangan_internal

                //             ]);

                //         if(isset($value['id_pembayaran_operasional']))
                //         {
                //             $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($value['id_pembayaran_operasional']);
                //             if($value['kembali']!='KEMBALI_STOK'&&$value['kembali']!='DATA_DI_HAPUS')
                //             {
                //                 // $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($value['id_pembayaran_operasional']);
                //                 if($so_pembayaran){
                //                     $so_pembayaran->total_refund += (float)str_replace(',', '', $value['total_dicairkan']);
                //                     $so_pembayaran->updated_by = $user;
                //                     $so_pembayaran->updated_at = now();
                //                     // $so_pembayaran->is_aktif = 'N';
                //                     // $so_pembayaran->save();
                //                     if ($so_pembayaran->save()) {
    
                //                         $so_refund = new SewaOperasionalRefund();
                //                         $so_refund ->id_kas_bank = $value['kembali'];
                //                         $so_refund ->tanggal_refund = now();
                //                         $so_refund ->id_pembayaran_detail = $value['id_operasional_data'];
                //                         $so_refund ->id_sewa =  $sewa->id_sewa;
                //                         $so_refund ->no_sewa =  $sewa->no_sewa;
                //                         $so_refund ->id_pembayaran = $so_pembayaran->id;
                //                         $so_refund ->deskripsi_ops = $value['deskripsi_data'];
                //                         $so_refund ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                //                         $so_refund->catatan_refund = $value['catatan'];
                //                         $so_refund->created_by = $user;
                //                         $so_refund->created_at = now();
                //                         $so_refund->is_aktif = 'Y';
                //                         // $so_refund->save();
                //                         if($so_refund->save())
                //                         {
                                            
                //                                 DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                //                                     array(
                //                                         $value['kembali'],// id kas_bank dr form
                //                                         $tgl_cancel,//tanggal
                //                                         (float)str_replace(',', '', $value['total_dicairkan']),// debit 
                //                                         0, //uang keluar (kredit)
                //                                         CoaHelper::DataCoa(1100), //kode coa piutang usaha
                //                                         'operasional_refund',
                //                                         $value['rincian'].'>>'.$so_refund->catatan_refund, //keterangan_transaksi
                //                                         $so_refund->id,//keterangan_kode_transaksi id refundnya
                //                                         $user,//created_by
                //                                         now(),//created_at
                //                                         $user,//updated_by
                //                                         now(),//updated_at
                //                                         'Y'
                //                                     ) 
                //                                 );
                //                                 $kas_bank = KasBank::where('is_aktif', 'Y')->find($value['kembali']);
                //                                 $kas_bank->saldo_sekarang += (float)str_replace(',', '', $value['total_dicairkan']);
                //                                 $kas_bank->updated_by = $user;
                //                                 $kas_bank->updated_at = now();
                //                                 $kas_bank->save();
                //                         }
                //                     }
                //                 }
                //             }
                //             else if($value['kembali']=='KEMBALI_STOK')
                //             {
                //                 $so_pembayaran->total_kembali_stok += 1; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                //                 $so_pembayaran->updated_by = $user;
                //                 $so_pembayaran->updated_at = now();
                //                 // $so_pembayaran->save();
                //                 if($so_pembayaran->save())
                //                 {
                //                     $so_stok = new SewaOperasionalKembaliStok();
                //                     $so_stok ->id_pembayaran_detail = $value['id_operasional_data'];
                //                     $so_stok ->id_sewa =  $sewa->id_sewa;
                //                     $so_stok ->no_sewa =  $sewa->no_sewa;
                //                     $so_stok ->id_pembayaran = $so_pembayaran->id;
                //                     $so_stok ->deskripsi_ops = $value['deskripsi_data'];
                //                     $so_stok->tanggal_stok = now();
                //                     $so_stok->stok_masuk = 1;
                //                     $so_stok->stok_keluar = 0;
                //                     $so_stok->catatan_stok = $value['catatan'];
                //                     $so_stok->created_by = $user;
                //                     $so_stok->created_at = now();
                //                     $so_stok->is_aktif = 'Y';
                //                     $so_stok->save();
                //                 }
                //             }
                //             else if($value['kembali']=='kasbon')
                //             {
                //                 $total_dicairkan = (float)str_replace(',', '', $value['total_dicairkan']);
                //                 $so_pembayaran->total_kasbon += $total_dicairkan; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                //                 $so_pembayaran->updated_by = $user;
                //                 $so_pembayaran->updated_at = now();
                //                 if($so_pembayaran->save())
                //                 {
                //                     $kasbon_operasional = new SewaOperasionalKasBon();
                //                     $kasbon_operasional ->id_pembayaran = $so_pembayaran->id;
                //                     $kasbon_operasional ->id_pembayaran_detail = $value['id_operasional_data'];
                //                     $kasbon_operasional->id_sewa =  $sewa->id_sewa;
                //                     $kasbon_operasional->deskripsi_ops = $value['deskripsi_data'];
                //                     $kasbon_operasional->tanggal_transaksi = now();
                //                     $kasbon_operasional->kasbon_masuk = $total_dicairkan;
                //                     $kasbon_operasional->kasbon_keluar =0;
                //                     $kasbon_operasional->catatan_kasbon = $value['catatan'];
                //                     $kasbon_operasional->created_by = $user;
                //                     $kasbon_operasional->created_at = now();
                //                     $kasbon_operasional->is_aktif = 'Y';
                //                     $kasbon_operasional->save();
                //                 }
                //             }
                //         }
                //         else
                //         {
                //             if($value['kembali']=='KEMBALI_STOK')
                //             {
                //                 $so_stok = new SewaOperasionalKembaliStok();
                //                 $so_stok ->id_pembayaran_detail = $value['id_operasional_data'];
                //                 $so_stok ->id_sewa =  $sewa->id_sewa;
                //                 $so_stok ->no_sewa =  $sewa->no_sewa;
                //                 $so_stok ->deskripsi_ops = $value['deskripsi_data'];
                //                 $so_stok->tanggal_stok = now();
                //                 $so_stok->stok_masuk = 1;
                //                 $so_stok->stok_keluar = 0;
                //                 $so_stok->catatan_stok = $value['catatan'];
                //                 $so_stok->created_by = $user;
                //                 $so_stok->created_at = now();
                //                 $so_stok->is_aktif = 'Y';
                //                 $so_stok->save();
                //             }
                //             else if($value['kembali']=='kasbon')
                //             {
                //                 $total_dicairkan = (float)str_replace(',', '', $value['total_dicairkan']);
                //                 $kasbon_operasional = new SewaOperasionalKasBon();
                //                 $kasbon_operasional->id_sewa = $sewa->id_sewa;
                //                 $kasbon_operasional->id_pembayaran_detail = $value['id_operasional_data'];
                //                 $kasbon_operasional->deskripsi_ops = $value['deskripsi_data'];
                //                 $kasbon_operasional->tanggal_transaksi = now();
                //                 $kasbon_operasional->kasbon_masuk = $total_dicairkan;
                //                 $kasbon_operasional->kasbon_keluar =0;
                //                 $kasbon_operasional->catatan_kasbon = $value['catatan'];
                //                 $kasbon_operasional->created_by = $user;
                //                 $kasbon_operasional->created_at = now();
                //                 $kasbon_operasional->is_aktif = 'Y';
                //                 $kasbon_operasional->save();
                //             }
                //         }


                //     }
                // }
                if(isset($data['data']))
                {
                    foreach ($data['data'] as $value) {
                        // dd($value['kembali']);
                                if ($value['kembali']=='KEMBALI_STOK') {
                                    $status = 'STOK';
                                    $keterangan_internal = '[REFUND-MASUK-STOK]';
                                }
                                if ($value['kembali']=='kasbon') {
                                    $status = 'KASBON';
                                    $keterangan_internal = '[REFUND-MASUK-KASBON]';
                                }
                                else if($value['kembali']=='DATA_DI_HAPUS')
                                {
                                    $status = 'HAPUS';
                                    $keterangan_internal = '[REFUND-TIDAK-ADA-PENCAIRAN]';
                                }
                                else
                                {
                                    $status = 'HAPUS';
                                    $keterangan_internal = '[REFUND-UANG-KEMBALI]';
                                }
                                $so_pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($value['id_pembayaran_operasional']);

                                if(isset($value['id_pembayaran_operasional']))
                                {
                                    if($value['kembali']!='KEMBALI_STOK'&&$value['kembali']!='DATA_DI_HAPUS'&&$value['kembali']!='kasbon')
                                    {
                                        if($so_pembayaran){
                                            $so_pembayaran->total_refund += (float)str_replace(',', '', $value['total_dicairkan']);
                                            $so_pembayaran->updated_by = $user;
                                            $so_pembayaran->updated_at = now();
                                            // $so_pembayaran->is_aktif = 'N';
                                            // $so_pembayaran->save();
                                            if ($so_pembayaran->save()) {
            
                                                $so_refund = new SewaOperasionalRefund();
                                                $so_refund ->id_kas_bank = $value['kembali'];
                                                $so_refund ->tanggal_refund = now();
                                                $so_refund ->deskripsi_ops = $value['deskripsi_data'];
                                                $so_refund ->total_refund = (float)str_replace(',', '', $value['total_dicairkan']);
                                                $so_refund->catatan_refund = $value['catatan'];
                                                $so_refund->created_by = $user;
                                                $so_refund->created_at = now();
                                                $so_refund->is_aktif = 'Y';
                                                // $so_refund->save();
                                                if($so_refund->save())
                                                {
                                                    SewaOperasionalPembayaranDetail::where('is_aktif', '=', 'Y')
                                                    ->where('id', $value['id_pembayaran_detail'])
                                                    ->update([
                                                            'is_aktif' => 'N',
                                                            'status' => $status,
                                                            'keterangan_internal'=>$keterangan_internal,
                                                            'id_refund'=>$so_refund->id,
                                                            'total_refund'=>(float)str_replace(',', '', $value['total_dicairkan'])
                                                        ]);
                                                    
                                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                            array(
                                                                $value['kembali'],// id kas_bank dr form
                                                                now(),//tanggal
                                                                (float)str_replace(',', '', $value['total_dicairkan']),// debit 
                                                                0, //uang keluar (kredit)
                                                                CoaHelper::DataCoa(1100), //kode coa piutang usaha
                                                                'operasional_refund',
                                                                $value['rincian'].'>>'.$so_refund->catatan_refund, //keterangan_transaksi
                                                                $so_refund->id,//keterangan_kode_transaksi id refundnya
                                                                $user,//created_by
                                                                now(),//created_at
                                                                $user,//updated_by
                                                                now(),//updated_at
                                                                'Y'
                                                            ) 
                                                        );
                                                        $kas_bank = KasBank::where('is_aktif', 'Y')->find($value['kembali']);
                                                        $kas_bank->saldo_sekarang += (float)str_replace(',', '', $value['total_dicairkan']);
                                                        $kas_bank->updated_by = $user;
                                                        $kas_bank->updated_at = now();
                                                        $kas_bank->save();
                                                }
                                            }
                                        }
                                    }
                                    else if($value['kembali']=='KEMBALI_STOK')
                                    {
                                        $so_pembayaran->total_kembali_stok += 1; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        // $so_pembayaran->save();
                                        if($so_pembayaran->save())
                                        {
                                            $so_stok = new SewaOperasionalKembaliStok();
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
                                        $so_pembayaran->total_kasbon += $total_dicairkan; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        if($so_pembayaran->save())
                                        {
                                            $kasbon_operasional = new SewaOperasionalKasBon();
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
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_kasbon_kembali'=>$kasbon_operasional->id,
                                                    'total_kasbon_kembali'=>$total_dicairkan
                                                ]);
                                        }
                                    }
                                }
                                else
                                {
                                     if($value['kembali']=='KEMBALI_STOK')
                                    {
                                        $so_pembayaran->total_kembali_stok += 1; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        // $so_pembayaran->save();
                                        if($so_pembayaran->save())
                                        {
                                            $so_stok = new SewaOperasionalKembaliStok();
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
                                        $so_pembayaran->total_kasbon += $total_dicairkan; // kenapa 1? karena 1 trailer kan 1 seal doang, gamungkin 2
                                        $so_pembayaran->updated_by = $user;
                                        $so_pembayaran->updated_at = now();
                                        if($so_pembayaran->save())
                                        {
                                            $kasbon_operasional = new SewaOperasionalKasBon();
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
                                            ->where('id', $value['id_pembayaran_detail'])
                                            ->update([
                                                    'is_aktif' => 'N',
                                                    'status' => $status,
                                                    'keterangan_internal'=>$keterangan_internal,
                                                    'id_kasbon_kembali'=>$kasbon_operasional->id,
                                                    'total_kasbon_kembali'=>$total_dicairkan
                                                ]);
                                        }
                                    }
                                }
                    }
                }
                if($sewa->id_supplier==null)
                {
                    $uj_kembali = isset($data['uang_jalan_kembali'])? floatval(str_replace(',', '', $data['uang_jalan_kembali'])):0;
                    // $cancel = new SewaBatalCancel();
                    // $cancel->id_sewa = $sewa->id_sewa;
                    // $cancel->jenis = 'CANCEL';
                    // $cancel->tgl_batal_muat_cancel = date_format($tgl_cancel, 'Y-m-d H:i:s');
                    // $cancel->total_uang_jalan_kembali = $uj_kembali;
                    // if(isset($data['pembayaran'])){
                    //     if($data['pembayaran'] != 'HUTANG KARYAWAN'){
                    //         $cancel->id_kas_bank = $data['pembayaran'];
                    //     }else{
                    //         $cancel->id_karyawan_hutang = $data['id_karyawan'];
                    //     }
                    // }
                    // $cancel->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    // $cancel->alasan_batal = $data['alasan_cancel'];
                    // $cancel->created_by = $user;
                    // $cancel->created_at = now();
                    // $cancel->is_aktif = 'Y';
    
                    // if($cancel->save()){
                        if(isset($data['pembayaran'])){
                            $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();

                            // $cek = KasBankTransaction::where('is_aktif', 'Y')
                            //                         ->where('id_kas_bank', $data['pembayaran'])
                            //                         ->where('keterangan_kode_transaksi', $riwayat_uang_jalan->id)
                            //                         ->where('jenis', 'uang_jalan')->first();
                            
                            if($riwayat_uang_jalan){
                                $kht = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                    'id_karyawan' => $data['id_karyawan'],
                                                    'refrensi_id' => $riwayat_uang_jalan->id,
                                                    'jenis'=>'POTONG',
                                                    'refrensi_keterangan' => 'uang_jalan'
                                                    ])->first();
                                if($data['pembayaran'] != 'HUTANG KARYAWAN'){
                                    $kasBankTransaction = new KasBankTransaction ();
                                    $kasBankTransaction->id_kas_bank = $data['pembayaran'];
                                    $kasBankTransaction->tanggal =$tgl_cancel;
                                    $kasBankTransaction->debit = $uj_kembali; // debit uang masuk
                                    $kasBankTransaction->kredit = 0;
                                    $kasBankTransaction->jenis = 'uang_jalan';
                                    $kasBankTransaction->keterangan_transaksi = '(CANCEL) UANG JALAN KEMBALI - '. '['.$data['no_sewa'] .']'.'-'.$data['alasan_cancel'] . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver'].' >> ' . $data['customer'].' >> ' . '('.$data['tujuan'].')' ;
                                    $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
                                    $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
                                    $kasBankTransaction->created_by = $user;
                                    $kasBankTransaction->created_at = now();
                                    $kasBankTransaction->is_aktif = 'Y';
                                    if($kasBankTransaction->save()){
                                        $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                                        $kasbank->saldo_sekarang += $uj_kembali;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        $kasbank->save();
                                        if($riwayat_uang_jalan->potong_hutang>0)
                                        {
                                            // misal cair 500k potong 30k, trs balik 500k, 30k hutangnya ga jadi
                                            if($kht){
                                                $kht->updated_by = $user;
                                                $kht->updated_at = now();
                                                $kht->is_aktif = 'N';
                                                $kht->save();
                                            }
                                            $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                            if(isset($kh)){
                                                // kalau ada data, hutang ditambah
                                                $kh->total_hutang += $riwayat_uang_jalan->potong_hutang; 
                                                $kh->updated_by = $user;
                                                $kh->updated_at = now();
                                                $kh->save();
                                            }else{
                                                // kalau tidak ada data, buat data hutang baru
                                                $kh = new KaryawanHutang();
                                                $kh->id_karyawan = $data['id_karyawan'];
                                                $kh->total_hutang += $riwayat_uang_jalan->potong_hutang;
                                                $kh->created_by = $user;
                                                $kh->created_at = now();
                                                $kh->is_aktif = 'Y';
                                                $kh->save();
                                            }
                                            // $kht = new KaryawanHutangTransaction();
                                            // $kht->id_karyawan = $data['id_karyawan'];
                                            // $kht->refrensi_id = $cancel->id;
                                            // $kht->refrensi_keterangan = 'CANCEL';
                                            // $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                            // $kht->tanggal = date_format($tgl_cancel, 'Y-m-d H:i:s');
                                            // $kht->debit = $riwayat_uang_jalan->potong_hutang; // ga jadi potong hutang dr uj riwayat
                                            // $kht->kredit = 0;
                                            // $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                            // $kht->catatan = 'CANCEL PERJALANAN - Batal pemotongan hutang uang jalan - ' . $data['alasan_cancel'] . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver'].' >> ' . $data['customer'].' >> ' . '('.$data['tujuan'].')';
                                            // $kht->created_by = $user;
                                            // $kht->created_at = now();
                                            // $kht->is_aktif = 'Y';
                                            // $kht->save();
                                        }
                                        // if(isset($data['uang_jalan_kembali'])){
                                            // DB::table('uang_jalan_riwayat')
                                            // ->where('sewa_id', $sewa->id_sewa)
                                            // ->where('is_aktif', 'Y')
                                            // ->update([
                                            //     'catatan' => 'CANCEL',
                                            //     'updated_at' => now(),
                                            //     'updated_by' => $user,
                                            //     'is_aktif' => 'N',
                                            // ]); 
                                        // }
                                        DB::commit();
                                    }
                                }else{
                                    if($kht){
                                        $kht->updated_by = $user;
                                        $kht->updated_at = now();
                                        $kht->is_aktif = 'N';
                                        $kht->save();
                                    }
                                    $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                    
                                    
                                    //misal uj 500k potong 50k, jadi 450k, terus kembali sbg hutang 450k, kan ada nyantol 50k
                                    //nah 450k itu ditambah sama uj yang nyantol 50k jadi 500k
                                    $total_uj_kembali_dan_hutang_nyantol = $uj_kembali+$riwayat_uang_jalan->potong_hutang;
                                    
                                    if(isset($kh)){
                                        // kalau ada data, hutang ditambah
                                        $kh->total_hutang += $total_uj_kembali_dan_hutang_nyantol; 
                                        $kh->updated_by = $user;
                                        $kh->updated_at = now();
                                        $kh->save();
                                    }else{
                                        // kalau tidak ada data, buat data hutang baru
                                        $kh = new KaryawanHutang();
                                        $kh->id_karyawan = $data['id_karyawan'];
                                        $kh->total_hutang += $total_uj_kembali_dan_hutang_nyantol;
                                        $kh->created_by = $user;
                                        $kh->created_at = now();
                                        $kh->is_aktif = 'Y';
                                        $kh->save();
                                    }
                                    $kht = new KaryawanHutangTransaction();
                                    $kht->id_karyawan = $data['id_karyawan'];
                                    $kht->refrensi_id = $sewa->id_sewa;
                                    // $kht->refrensi_keterangan = '[CANCEL]-refrensi_nya_id_sewa = '. $sewa->id_sewa;
                                    $kht->refrensi_keterangan = 'cancel';
                                    $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = $tgl_cancel;
                                    $kht->debit = $uj_kembali; // HUTANG BARU nah ini 450k nya jadi hutang baru 50k nya gajadi,nanti kalau mau cairin lagi ya 500k potong
                                    $kht->kredit = 0;
                                    $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                    $kht->catatan = 'CANCEL PERJALANAN - ' .'['.$data['no_sewa'] .']'.'-'. $data['alasan_cancel'] . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver'].' >> ' . $data['customer'].' >> ' . '('.$data['tujuan'].')';
                                    $kht->created_by = $user;
                                    $kht->created_at = now();
                                    $kht->is_aktif = 'Y';
                                    $kht->save();
                                    
                                    DB::commit();
                                }
                                // kalau ada data, hutang ditambah
                                $riwayat_uang_jalan->is_aktif = 'N'; 
                                $riwayat_uang_jalan->updated_by = $user;
                                $riwayat_uang_jalan->updated_at = now();
                                $riwayat_uang_jalan->save();
                                // DB::table('uang_jalan_riwayat')
                                //         ->where('sewa_id', $sewa->id_sewa)
                                //         ->where('is_aktif', 'Y')
                                //         ->update([
                                //             'catatan' => 'CANCEL',
                                //             'updated_at' => now(),
                                //             'updated_by' => $user,
                                //             'is_aktif' => 'N',
                                //         ]); 
                            }
                        }
                    // }
                }
                else
                {
                    // $cancel_rekanan = new SewaBatalCancel();
                    // $cancel_rekanan->id_sewa = $sewa->id_sewa;
                    // $cancel_rekanan->jenis = 'CANCEL';
                    // $cancel_rekanan->tgl_batal_muat_cancel = date_format($tgl_cancel, 'Y-m-d H:i:s');
                    // $cancel_rekanan->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    // $cancel_rekanan->alasan_batal = $data['alasan_cancel'].'['.$data['no_sewa'] .']'.'-'.'[rekanan cancel]';
                    // $cancel_rekanan->created_by = $user;
                    // $cancel_rekanan->created_at = now();
                    // $cancel_rekanan->is_aktif = 'Y';
                    // $cancel_rekanan->save();
                    // DB::commit();
                }
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil cancel perjalanan!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }

    }

    public function cancel_uang_jalan($id)
    {
        $sewa = Sewa::with('customer')->where('is_aktif', 'Y')->find($id);

        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();
        $ujr = UangJalanRiwayat::where([
                                        'is_aktif' => 'Y',
                                        'sewa_id' => $id
                                    ])->first();
        return view('pages.order.dalam_perjalanan.cancel_uang_jalan',[
            'judul' => "cancel uang jalan",
            'data' => $sewa,
            'id_sewa' => $id,
            'dataKas' => $dataKas,
            'ujr' => $ujr,
        ]);
    }

    public function save_cancel_uang_jalan(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        $id = $data['id_sewa_hidden'];
        DB::beginTransaction(); 

        // dd($data);

        try {
            $sewa = Sewa::where('is_aktif', 'Y')->where('id_sewa', $id)->first();
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            $sewa->status = 'MENUNGGU UANG JALAN';
            $sewa->save();  
            $ujr = UangJalanRiwayat::where([
                                        'is_aktif' => 'Y',
                                        'sewa_id' => $id
                                    ])->first();
            $ujr->updated_by = $user;
            $ujr->updated_at = now();
            $ujr->is_aktif = 'N';
            
            // if($ujr->potong_hutang>0)
            // {
            //     $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
            //     if(isset($kh)){
            //         // kalau ada data, hutang ditambah
            //         $kh->total_hutang += $ujr->potong_hutang; 
            //         $kh->updated_by = $user;
            //         $kh->updated_at = now();
            //         $kh->save();
            //     }else{
            //         // kalau tidak ada data, buat data hutang baru
            //         $kh = new KaryawanHutang();
            //         $kh->id_karyawan = $data['id_karyawan'];
            //         $kh->total_hutang += $ujr->potong_hutang;
            //         $kh->created_by = $user;
            //         $kh->created_at = now();
            //         $kh->is_aktif = 'Y';
            //         $kh->save();
            //     }
            //     $kht = new KaryawanHutangTransaction();
            //     $kht->id_karyawan = $data['id_karyawan'];
            //     $kht->refrensi_id = $sewa->id;
            //     $kht->refrensi_keterangan = 'CANCEL UANG JALAN';
            //     $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
            //     $kht->tanggal = now();
            //     $kht->debit = $ujr->potong_hutang; // ga jadi potong hutang dr uj riwayat
            //     $kht->kredit = 0;
            //     $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
            //     $kht->catatan = 'CANCEL UANG JALAN - Kembalikan status Uang Jalan - ' . ' >> ' . $data['kendaraan'] . ' >> ' . $data['driver'].' >> ' . $data['customer'].' >> ' . '('.$data['tujuan'].')';
            //     $kht->created_by = $user;
            //     $kht->created_at = now();
            //     $kht->is_aktif = 'Y';
            //     $kht->save();
            // }
            if($ujr->save()){
                $kht = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                        'id_karyawan' => $sewa->id_karyawan,
                                                        'jenis'=>'POTONG',
                                                        'refrensi_keterangan' => 'uang_jalan',
                                                        'refrensi_id' => $ujr->id 
                                                        ])->first();
                if($kht){
                    $kht->updated_by = $user;
                    $kht->updated_at = now();
                    $kht->is_aktif = 'N';
                    if($kht->save()){
                        $kh = KaryawanHutang::where(['is_aktif' => 'Y', 'id_karyawan' => $sewa->id_karyawan])->first();
                        if($kh){
                            $kh->total_hutang += $kht->kredit;
                            $kh->updated_by = $user;
                            $kh->updated_by = now();
                            $kh->save();
                        }
                    }
                }
            }
            $riwayat = KasBankTransaction::where(['is_aktif' => 'Y',
                                                   'jenis' => 'uang_jalan',
                                                   'keterangan_kode_transaksi' => $ujr->id,    
                                                   'tanggal' => $ujr->tanggal    
                                                ])->first();
            // dd($riwayat);
            if($riwayat){
                $kasbank = KasBank::where('is_aktif', 'Y')->find($riwayat->id_kas_bank);
                    $kasbank->saldo_sekarang += $riwayat->kredit;
                    $kasbank->updated_by = $user;
                    $kasbank->updated_by = now();
                if( $kasbank->save()){
                    $riwayat->updated_by = $user;
                    $riwayat->updated_at = now();
                    $riwayat->is_aktif = 'N';
                    $riwayat->save();
                }
            }
            $cek_sewa_operasional_TL = DB::table('sewa_operasional_pembayaran_detail as so')
                                    ->select('so.*')
                                    ->where('so.id_sewa',  $id)
                                    ->where('so.is_aktif', 'Y')
                                    ->where('so.deskripsi', 'TL')
                                    ->first();
            //cek kalau misal awalnya tl terus diganti perak kan nyantol di operasional dan biaya
            //kalau ada tl nyantol di ubah jadi N
            if($cek_sewa_operasional_TL )
            {
                DB::table('sewa_operasional_pembayaran_detail')
                    ->where('id_sewa',  $id)
                    ->where('deskripsi', 'TL')
                    ->update(array(
                        'is_aktif' => "N",
                        'updated_at'=> now(),
                        'updated_by'=> $user, 
                    )
                );
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil Mengembalikan uang jalan!"]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Hubungi IT :'.$th->getMessage()]);
        }

    }

    public function ubah_supir($id)
    {
        $sewa = Sewa::with('customer')
        ->where('is_aktif', 'Y')
        ->find($id);

        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();
                    
        $dataDriver = DB::table('karyawan as k')
            ->select('k.*','k.id as idKaryawan','k.nama_panggilan','kh.total_hutang','ujr.potong_hutang','s.id_sewa')
            ->distinct()
            ->leftJoin('karyawan_hutang as kh', function($join) {
                $join->on('k.id', '=', 'kh.id_karyawan')
                // ->where('kh.is_aktif', '=', "Y")
                ->where('kh.is_aktif', '=', "Y");
            })
            ->leftJoin('sewa as s', function($join)use ($id) {
                $join->on('k.id', '=', 's.id_karyawan')
                ->where(function ($query)use ($id){
                        $query->where(function ($innerQuery)use ($id) {
                            $innerQuery->where('s.id_sewa',$id);
                        })
                        //yang ga ada sewa juga dimunculin (selain driver yang udah jalan)
                        ->orWhere(function ($query) {
                            $query->whereNull('s.id_sewa');
                        });
                })
                ->where('s.is_aktif', '=', "Y");
            })
            ->leftJoin('uang_jalan_riwayat as ujr', function($join) use ($id){
                $join->on('s.id_sewa', '=', 'ujr.sewa_id')
                //cek dulu apakah id sewa di ujr ini sama kaya id sewa yang ada,(driver di sewa sama driver di ujr)
                ->where(function ($query)use ($id){
                        $query->where(function ($innerQuery)use ($id) {
                            $innerQuery->where('s.id_sewa',$id);
                        })
                        //yang ga ada sewa juga dimunculin (selain driver yang udah jalan)
                        ->orWhere(function ($query) {
                            $query->whereNull('s.id_sewa');
                        });
                })
                ->where('ujr.is_aktif', '=', "Y");
            })
            ->where('k.is_aktif',"Y")
            ->where('k.is_keluar',"N")
            ->where('k.role_id', VariableHelper::Role_id('Driver'))
            ->get();

        $dataUangJalanRiwayat= DB::table('uang_jalan_riwayat as ujr')
            ->select('ujr.*')
            ->where('ujr.is_aktif',"Y")
            ->where('ujr.sewa_id', $sewa->id_sewa)
            ->first();
            $dataUjDiterima =0;
        if($dataUangJalanRiwayat)
        {
            $dataUjDiterima = ($dataUangJalanRiwayat->total_uang_jalan+$dataUangJalanRiwayat->total_tl)-$dataUangJalanRiwayat->potong_hutang;
        }
        $dataKendaraan = DB::table('kendaraan AS k')
                ->select('k.id AS kendaraanId', 'c.id as chassisId','k.no_polisi','k.driver_id', 'kkm.nama as kategoriKendaraan','cp.nama as namaKota','mc.nama as tipeKontainerKendaraanDariChassis')
                // ini buat nge get pair kendaraan yang trailer
                ->leftJoin('pair_kendaraan_chassis AS pk', function($join) {
                    $join->on('k.id', '=', 'pk.kendaraan_id')->where('pk.is_aktif', '=', 'Y');
                })
                // get chassis
                ->leftJoin('chassis AS c', 'pk.chassis_id', '=', 'c.id')
                // get model chasis yang 20/40 ft
                ->leftJoin('m_model_chassis AS mc', 'c.model_id', '=', 'mc.id')
                // get cabang jakarta/sby/...
                ->leftJoin('cabang_pje AS cp', 'k.cabang_id', '=', 'cp.id')
                // terus get kategorinya
                ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
                //dikasih pengecekan
                ->where(function ($query) use ($sewa){
                        //kalau dia kategori 1 (trailer)
                        if($sewa->jenis_tujuan=='FTL')
                        {
                            $query->where(function ($innerQuery) use ($sewa) {
                                //syaratnya trailer ada cek tipe chassis trailer 20/40, kemudian pair nya gaboleh null (chassisnya)
                                $innerQuery->where('k.id_kategori', '=', 1)
                                           ->where('mc.nama', 'like', "%$sewa->tipe_kontainer%")
                                           ->whereNotNull('pk.chassis_id');
                            });
                        }
                        else
                        {
                            $query->where(function ($innerQuery) use ($sewa) {
                                //syaratnya trailer ada cek tipe chassis trailer 20/40, kemudian pair nya gaboleh null (chassisnya)
                                $innerQuery->where('k.id_kategori', '!=', 1);
                            });
                        }
                        // ->orWhere(function ($query) {
                        //     $query->where('k.id_kategori', '!=', 1);
                        // });
                })
                ->where('k.is_aktif', '=', 'Y')
                // ->whereNotNull('k.driver_id')
                ->groupBy('k.id', 'k.no_polisi', 'kkm.nama','cp.nama')
                ->get(); 
        $dataChassis=DB::table('chassis as c')
            ->select('c.*','c.id as idChassis','m.nama as modelChassis')
            ->leftJoin('m_model_chassis AS m', 'c.model_id', '=', 'm.id')
            ->where('m.nama', 'like', "%$sewa->tipe_kontainer%")
            ->where('c.is_aktif', "Y")
            ->get();
        return view('pages.order.dalam_perjalanan.ubah_supir',[
            'judul' => "ubah supir",
            'dataDriver'=> $dataDriver,
            'data' => $sewa,
            'dataKas' => $dataKas,
            'dataUangJalanRiwayat' => $dataUangJalanRiwayat,
            'dataKendaraan'=>$dataKendaraan,
            'dataChassis'=>$dataChassis,
            'id_sewa' => $id,
            'dataUjDiterima' => $dataUjDiterima,


        ]);
    }
    
    public function save_ubah_supir(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        $id = $data['id_sewa_hidden'];
        
        DB::beginTransaction(); 
        try {
            $sewa = Sewa::where('is_aktif', 'Y')->where('id_sewa', $id)->first();
            $ujr = UangJalanRiwayat::where([
                                    'is_aktif' => 'Y',
                                    'sewa_id' => $id
                                ])->first();

            // kalo ubah supir tapi sama kayak yang lama ke prevent
            if ($sewa->id_karyawan == $data['select_driver']) {
                $sewa->id_kendaraan = $data['kendaraan_id'];
                $sewa->no_polisi = $data['no_polisi'];
                if ($sewa->jenis_tujuan == 'FTL') {
                    # code...
                    $sewa->id_chassis = $data['select_chassis'];
                    $sewa->karoseri = $data['karoseri'];
                }
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                $sewa->save();
                // return redirect()->route('dalam_perjalanan.ubah_supir', [ $id ])->with(['status' => 'error', 'msg' => "Supir tidak boleh sama dengan sebelumnya jika ingin diubah!"]);
            }else{
                $sewa->id_kendaraan = $data['kendaraan_id'];
                $sewa->no_polisi = $data['no_polisi'];
                if ($sewa->jenis_tujuan == 'FTL') {
                    # code...
                    $sewa->id_chassis = $data['select_chassis'];
                    $sewa->karoseri = $data['karoseri'];
                }
                $sewa->id_karyawan = $data['select_driver'];
                $sewa->nama_driver = $data['driver_nama'];
                $sewa->updated_by = $user;
                $sewa->updated_at = now();
                if($sewa->save())
                {
                    if($sewa->jenis_tujuan == 'FTL' &&isset($ujr)){
                        //pengembalian data yang lama
                        //kalo ada hutang karyawan
                        $kht_lama = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                        'id_karyawan' => $data['id_driver_lama'],
                                                        'refrensi_id' => $ujr->id, 
                                                        'jenis'=>'POTONG',
                                                        'refrensi_keterangan' => 'uang_jalan'
                                                        ])->first();
                        $kh_lama = KaryawanHutang::where(['is_aktif' => 'Y', 'id_karyawan' => $data['id_driver_lama']])->first();
                        
                        if($kht_lama){
                            // terus kalo ganti supir, misal ada hutang yang dipotong,matiin dulu yang lama, kan gajadi
                                $kht_lama->updated_by = $user;
                                $kht_lama->updated_at = now();
                                $kht_lama->is_aktif = 'N';
                                if($kht_lama->save()){
                                    if($kh_lama){
                                        $kh_lama->total_hutang += $kht_lama->kredit;
                                        $kh_lama->updated_by = $user;
                                        $kh_lama->updated_by = now();
                                        $kh_lama->save();
                                    }
                                }
                        }
                        $total_diterima_lama = floatval(str_replace(',', '', $data['total_diterima_lama']));
                        // dd(  $total_diterima_lama );
                        if($total_diterima_lama>0)
                        {
                            if($data['kembali']!='hutang'){
                                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $ujr->kas_bank_id,// id kas_bank 
                                        now(),//tanggal
                                        $total_diterima_lama,// debit 
                                        0, //uang keluar (kredit)
                                        CoaHelper::DataCoa(5002), //kode coa
                                        'uang_jalan',
                                        'Pengembalian uang jalan '.'['.$sewa->no_sewa.']'.' >> '.$data['no_polisi_lama'].'('.$data['driver_nama_lama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                        $ujr->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
                                //tambah saldo bank kan ini ngembaliin data lama
                                $kasbank_lama = KasBank::where('is_aktif', 'Y')->find($ujr->kas_bank_id);
                                $kasbank_lama->saldo_sekarang += $total_diterima_lama;
                                $kasbank_lama->updated_by = $user;
                                $kasbank_lama->updated_by = now();
                                $kasbank_lama->save();
        
                            }
                            else
                            {
                                $kht = new KaryawanHutangTransaction();
                                $kht->id_karyawan =$data['id_driver_lama'];
                                $kht->refrensi_id = $sewa->id_sewa;
                                $kht->refrensi_keterangan = 'ubah_supir';
                                $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                $kht->tanggal =now();
                                $kht->debit = $total_diterima_lama; 
                                $kht->kredit = 0;
                                $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                $kht->catatan =  'Pengembalian uang jalan'.'['.$sewa->no_sewa.']'.' >> '.$data['no_polisi_lama'].'('.$data['driver_nama_lama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'];
                                $kht->created_by = $user;
                                $kht->created_at = now();
                                $kht->is_aktif = 'Y';
                                // $kht->save();
                                if( $kht->save())
                                {
                                    if($kh_lama){
                                        $kh_lama->total_hutang +=  $total_diterima_lama;
                                        $kh_lama->updated_by = $user;
                                        $kh_lama->updated_by = now();
                                        $kh_lama->save();
                                    }
                                }
                            }
                        }
                        $kh_exist = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['select_driver'])->first();
                        if(isset($kh_exist)&&isset($data['potong_hutang'])){
                            $kh_exist->total_hutang -= (float)str_replace(',', '', $data['potong_hutang']); 
                            $kh_exist->updated_by = $user;
                            $kh_exist->updated_at = now();
                            $kh_exist->save();
    
                            $kht_baru = new KaryawanHutangTransaction();
                            $kht_baru->id_karyawan = $data['select_driver'];
                            $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                            $kht_baru->refrensi_keterangan = 'uang_jalan';
                            $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                            $kht_baru->tanggal = now();
                            $kht_baru->debit = 0;
                            $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                            $kht_baru->kas_bank_id = $ujr->kas_bank_id;
                            $kht_baru->catatan = 'Potong hutang Uang jalan'.'['.$sewa->no_sewa.']'.' >> '.$sewa->no_polisi.'('.$sewa->nama_driver.')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'] ;
                            $kht_baru->created_by = $user;
                            $kht_baru->created_at = now();
                            $kht_baru->is_aktif = 'Y';
                            $kht_baru->save();
                        }
                        // gamungkin ga ada data
                        // else if(isset($data['potong_hutang'])&&!isset($kh_exist))
                        // {
                        //     $kh_baru = new KaryawanHutang();
                        //     $kh_baru->id_karyawan = $data['select_driver'];
                        //     $kh_baru->total_hutang = (float)str_replace(',', '', $data['potong_hutang']);
                        //     $kh_baru->created_by = $user;
                        //     $kh_baru->created_at = now();
                        //     $kh_baru->is_aktif = 'Y';
                        //     if($kh_baru->save())
                        //     {
                        //         $kht_baru = new KaryawanHutangTransaction();
                        //         $kht_baru->id_karyawan = $data['select_driver'];
                        //         $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                        //         // $kht_baru->refrensi_keterangan = 'UANG JALAN';
                        //         $kht_baru->refrensi_keterangan = 'uang_jalan';
                        //         $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                        //         $kht_baru->tanggal = now();
                        //         $kht_baru->debit = 0;
                        //         $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                        //         $kht_baru->kas_bank_id =$ujr->kas_bank_id;
                        //         $kht_baru->catatan = 'Potong hutang Uang jalan dari supir'.' >> '.'['.$sewa->no_sewa.']'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' >> '.$data['no_polisi'].'('.$data['driver_nama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'];
                        //         $kht_baru->created_by = $user;
                        //         $kht_baru->created_at = now();
                        //         $kht_baru->is_aktif = 'Y';
                        //         $kht_baru->save();
                        //     }
                        // }
                        $ujr->potong_hutang =floatval(str_replace(',', '', $data['potong_hutang']));
                        $ujr->kas_bank_id = $ujr->kas_bank_id;
                        $ujr->updated_by = $user;
                        $ujr->updated_at = now();
                        $ujr->save();
    
                        if(floatval(str_replace(',', '', $data['total_diterima']))>0){
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                $ujr->kas_bank_id,// id kas_bank 
                                now(),//tanggal
                                0,// debit 
                                floatval(str_replace(',', '', $data['total_diterima'])), //uang keluar (kredit)
                                CoaHelper::DataCoa(5002), //kode coa
                                'uang_jalan',
                                'Pencairan Uang jalan'.' >> '.'['.$sewa->no_sewa.']'.' >> '.$data['no_polisi'].'('.$data['driver_nama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                // 'Pencairan Uang jalan dari supir'.' >> '.'['.$sewa->no_sewa.']'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' >> '.$data['no_polisi'].'('.$data['driver_nama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                $ujr->id,//keterangan_kode_transaksi
                                $user,//created_by
                                now(),//created_at
                                $user,//updated_by
                                now(),//updated_at
                                'Y'
                                ) 
                            );
                            $kasbank = KasBank::where('is_aktif', 'Y')->find($ujr->kas_bank_id);
                            $kasbank->saldo_sekarang -= floatval(str_replace(',', '', $data['total_diterima']));
                            $kasbank->updated_by = $user;
                            $kasbank->updated_by = now();
                            $kasbank->save();
    
                        }
    
                    }/*elseif($sewa->jenis_tujuan == 'LTL'){
                        $tl = isset($ujr->total_tl)? $ujr->total_tl:0;
                        $potong_hutang= isset($ujr->potong_hutang)? $ujr->potong_hutang:0;
    
                        if(isset($ujr)){
                            //pengembalian data yang lama
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $ujr->kas_bank_id,// id kas_bank 
                                    now(),//tanggal
                                    ($ujr->total_uang_jalan + $tl) - $potong_hutang,// debit 
                                    0, //uang keluar (kredit)
                                    CoaHelper::DataCoa(5002), //kode coa
                                    'uang_jalan',
                                    'Pengembalian uang jalan ubah supir '.'['.$sewa->no_sewa.']'.' >> '.$sewa->no_polisi.'('.$sewa->nama_driver.')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                    $ujr->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
    
                            //tambah saldo bank kan ini ngembaliin data lama
                            $kasbank_lama = KasBank::where('is_aktif', 'Y')->find($ujr->kas_bank_id);
                            $kasbank_lama->saldo_sekarang += ($ujr->total_uang_jalan + $tl) - $potong_hutang;
                            $kasbank_lama->updated_by = $user;
                            $kasbank_lama->updated_by = now();
                            
                            //kalo ada hutang karyawan
                            $kht_lama = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                                        'id_karyawan' => $sewa->id_karyawan,
                                                                        'refrensi_id' => $ujr->id, 
                                                                        'jenis'=>'POTONG',
                                                                        'refrensi_keterangan' => 'uang_jalan',
                                                                    ])->first();
                            if($kht_lama){
                                // terus kalo ganti supir, misal ada hutang yang dipotong,matiin dulu yang lama, kan gajadi
                                $kht_lama->updated_by = $user;
                                $kht_lama->updated_at = now();
                                $kht_lama->is_aktif = 'N';
                                if($kht_lama->save()){
                                    $kh_lama = KaryawanHutang::where(['is_aktif' => 'Y', 'id_karyawan' => $sewa->id_karyawan])->first();
                                    if($kh_lama){
                                        $kh_lama->total_hutang += $kht_lama->kredit;
                                        $kh_lama->updated_by = $user;
                                        $kh_lama->updated_by = now();
                                        $kh_lama->save();
                                    }
                                }
                            }
    
                            $ujr->potong_hutang = floatval(str_replace(',', '', $data['potong_hutang']));
                            $ujr->kas_bank_id = $ujr->kas_bank_id;
                            $ujr->updated_by = $user;
                            $ujr->updated_at = now();
                            $ujr->save();
    
                            if($data['potong_hutang'] != 0){
                                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['select_driver'])->first();
                                // if(!$kh){
                                //     $kh = new KaryawanHutang(); // buat baru
                                // }
                                $kh->id_karyawan = $data['select_driver'];
                                $kh->total_hutang -= (float)str_replace(',', '', $data['potong_hutang']);
                                $kh->created_by = $user;
                                $kh->created_at = now();
                                $kh->is_aktif = 'Y';
                                if($kh->save()){
                                    $kht_baru = new KaryawanHutangTransaction();
                                    $kht_baru->id_karyawan = $data['select_driver'];
                                    $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                                    $kht_baru->refrensi_keterangan = 'uang_jalan';
                                    $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht_baru->tanggal = now();
                                    $kht_baru->debit = 0;
                                    $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                                    $kht_baru->kas_bank_id =$ujr->kas_bank_id;
                                    $kht_baru->catatan = 'Potong hutang Uang jalan dari supir'.' >> '.'['.$sewa->no_sewa.']'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' >> '.$data['no_polisi'].'('.$data['driver_nama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'];
                                    $kht_baru->created_by = $user;
                                    $kht_baru->created_at = now();
                                    $kht_baru->is_aktif = 'Y';
                                    $kht_baru->save();
                                }
                            }
                                
                            $current_time = time(); // Get the current timestamp
                            $new_time = $current_time + 5; // Add 5 seconds, biar di transaksi kas bank urutannya benar
                            
                            if(floatval(str_replace(',', '', $data['total_diterima'])) > 0){
                                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $ujr->kas_bank_id,// id kas_bank 
                                        date('Y-m-d H:i:s', $new_time),//tanggal
                                        0,// debit 
                                        floatval(str_replace(',', '', $data['total_diterima'])), //uang keluar (kredit)
                                        CoaHelper::DataCoa(5002), //kode coa
                                        'uang_jalan',
                                        'Pencairan Uang jalan dari supir'.' >> '.'['.$sewa->no_sewa.']'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' >> '.$data['no_polisi'].'('.$data['driver_nama'].')'.' >> '.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                        $ujr->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        date('Y-m-d H:i:s', $new_time),//created_at
                                        $user,//updated_by
                                        date('Y-m-d H:i:s', $new_time),//updated_at
                                        'Y'
                                    ) 
                                );
    
                                $kasbank = KasBank::where('is_aktif', 'Y')->find($ujr->kas_bank_id);
                                $kasbank->saldo_sekarang -= floatval(str_replace(',', '', $data['total_diterima']));
                                $kasbank->updated_by = $user;
                                $kasbank->updated_by = now();
                            }
                        }
    
                        $sewa->id_kendaraan = $data['kendaraan_id'];
                        $sewa->no_polisi = $data['no_polisi'];
                        $sewa->id_chassis = $data['select_chassis'];
                        $sewa->karoseri = $data['karoseri'];
                        $sewa->id_karyawan = $data['select_driver'];
                        $sewa->nama_driver = $data['driver_nama'];
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->save();  
                    }*/
                }
                
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil mengubah data supir!"]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);

        }

    }

    public function ubah_tujuan($id)
    {
        $sewa = Sewa::with('getCustomer')->where('is_aktif', 'Y')->find($id);
        // dd($sewa);
        $dataKas = KasBank::where('is_aktif', '=', "Y")
                    ->select('*')
                    ->get();
        $dataOperasional = SewaOperasionalPembayaranDetail::selectRaw('
        sewa_operasional_pembayaran_detail.id_sewa as so_id_sewa, 
        sewa_operasional_pembayaran_detail.id as so_id, 
        s.no_polisi as sewa_kendaraan, 
        s.nama_tujuan as sewa_tujuan, 
        COALESCE(s.nama_driver, CONCAT("DRIVER REKANAN ", sp.nama)) as sewa_driver, 
        s.no_sewa as no_sewa, 
        sewa_operasional_pembayaran_detail.catatan as so_catatan,
        sewa_operasional_pembayaran_detail.id_pembayaran as so_id_pembayaran, 
        sewa_operasional_pembayaran_detail.total_dicairkan as so_total_dicairkan, 
        sop.id_kas_bank as id_kas_bank, 
        sewa_operasional_pembayaran_detail.deskripsi as so_deskripsi')
        ->where('sewa_operasional_pembayaran_detail.is_aktif', '=', 'Y')
        ->where('sewa_operasional_pembayaran_detail.id_sewa', '=', $sewa->id_sewa)
        ->leftJoin('sewa AS s', function($join) {
                    $join->on('sewa_operasional_pembayaran_detail.id_sewa', '=', 's.id_sewa')
                    ->where('s.is_aktif', 'Y')
                    ->where('s.status', 'PROSES DOORING')
                    ;
                })
        ->leftJoin('sewa_operasional_pembayaran AS sop', function($join) {
                    $join->on('sop.id', '=', 'sewa_operasional_pembayaran_detail.id_pembayaran')
                    ->where('sop.is_aktif', 'Y')
                    ;
                })
        ->leftJoin('supplier AS sp', function($join) {
                    $join->on('s.id_supplier', '=', 'sp.id')
                    ->where('sp.is_aktif', '=', 'Y');
                })
        ->where(function ($query) {
            $query->where('sewa_operasional_pembayaran_detail.status', 'like', '%SUDAH DICAIRKAN%')
                ->orWhere('sewa_operasional_pembayaran_detail.status', 'like', '%TAGIHKAN DI INVOICE%');
        })
        ->get();

        

        $ujr = UangJalanRiwayat::where([
                                        'is_aktif' => 'Y',
                                        'sewa_id' => $id
                                    ])->first();
        $total_uang_jalan_diterima = ($ujr->total_uang_jalan + $ujr->total_tl)-$ujr->potong_hutang;
        $total_uang_jalan = ($ujr->total_uang_jalan + $ujr->total_tl);
        return view('pages.order.dalam_perjalanan.ubah_tujuan',[
            'judul' => "ubah tujuan",
            'data' => $sewa,
            'id_sewa' => $id,
            'dataKas' => $dataKas,
            'ujr' => $ujr,
            'dataOperasional' => $dataOperasional,
            'total_uang_jalan' => $total_uang_jalan,
            'total_uang_jalan_diterima' => $total_uang_jalan_diterima,
            'dataCustomer' => SewaDataHelper::DataCustomer(),
            'dataPengaturanKeuangan'=>SewaDataHelper::DataPengaturanBiaya()
        ]);
    }
    public function save_ubah_tujuan(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        // $tgl_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        try {
           
            $customer = Customer::where('is_aktif', '=', "Y")->find($sewa->id_customer);
            $tarif_baru = isset($data['tarif_baru'])?floatval(str_replace(',', '', $data['tarif_baru'])):0;
            $total_uj_aja = isset($data['uang_jalan'])?floatval(str_replace(',', '', $data['uang_jalan'])):0;

            $total_uj_baru = isset($data['uang_jalan_baru'])?floatval(str_replace(',', '', $data['uang_jalan_baru'])):0;
            $total_tl = isset($data['stack_teluk_lamong_hidden'])?floatval(str_replace(',', '', $data['stack_teluk_lamong_hidden'])):0;
            $potong_hutang = isset($data['potong_hutang'])?floatval(str_replace(',', '', $data['potong_hutang'])):0;

            $total_akhir = isset($data['total_akhir'])?floatval(str_replace(',', '', $data['total_akhir'])):0;
            if($customer){
                $customer->kredit_sekarang -= $sewa->total_tarif;
                $customer->updated_by = $user;
                $customer->updated_at = now();
                $customer->save();
            }
            $sewa->id_customer = $data['customer_id'];
            $sewa->id_grup_tujuan = $data['select_grup_tujuan'];
            $sewa->nama_tujuan = $data['nama_tujuan'];
            $sewa->alamat_tujuan = $data['alamat_tujuan'];
            $sewa->total_tarif = $tarif_baru;
            $sewa->total_uang_jalan = $total_uj_baru;
            $sewa->stack_tl = $data['stack_tl']? $data['stack_tl']:"";
            $sewa->updated_at = now();
            $sewa->updated_by = $user;
            if($sewa->save()){
                SewaBiaya::where('is_aktif', '=', 'Y')
                ->where('id_sewa', $sewa->id_sewa)
                ->update(['is_aktif' => 'N']);
                if($data['stack_tl'] == 'tl_teluk_lamong'){
                    $sewa_biaya = new SewaBiaya();
                    $sewa_biaya->id_sewa = $sewa->id_sewa;
                    $sewa_biaya->deskripsi = 'TL';
                    $sewa_biaya->biaya = $data['stack_teluk_lamong_hidden'];
                    $sewa_biaya->catatan = $data['stack_tl'];
                    $sewa_biaya->created_at = now();
                    $sewa_biaya->created_by = $user;
                    $sewa_biaya->is_aktif = "Y";
                    $sewa_biaya->save();
                }
                if($sewa->id_jo_detail)
                {
                    $jo_detail = JobOrderDetail::where('is_aktif', '=', "Y")->find($sewa->id_jo_detail);
                    $jo_detail->id_grup_tujuan = $data['select_grup_tujuan'];
                    $jo_detail->updated_at = now();
                    $jo_detail->updated_by = $user;
                    $jo_detail->save();
                    if($sewa->id_booking)
                    {
                        $booking = Booking::where('is_aktif', '=', "Y")->find($sewa->id_booking);
                        $booking->id_grup_tujuan = $data['select_grup_tujuan'];
                        $booking->updated_at = now();
                        $booking->updated_by = $user;
                        $booking->save();
                    }
                }
                if($sewa->id_booking )
                {
                    $booking_lama = Booking::where('is_aktif', '=', "Y")->find($sewa->id_booking);
                    if($sewa->id_customer!=$booking_lama->id_customer && $sewa->id_grup_tujuan!=$booking_lama->id_grup_tujuan)
                    {
                        $booking_lama->is_aktif ='N';
                        $booking_lama->updated_at = now();
                        $booking_lama->updated_by = $user;
                        // $booking_lama->save();
                        if( $booking_lama->save())
                        {
                            $currentYear = Carbon::now()->format('y');
                            $currentMonth = Carbon::now()->format('m');
                
                            $maxBooking = Booking::whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$data['customer_kode_baru'],$currentYear, $currentMonth])
                                ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                                ->value('max_booking');
                            
                            // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                            $newBookingNumber = $data['customer_kode_baru'] . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);
        
                            if (is_null($maxBooking)) {
                                $newBookingNumber = $data['customer_kode_baru'] . $currentYear . $currentMonth . '001';
                            }
                            
                            $booking = new Booking();
                            $booking->no_booking =$newBookingNumber;
                            $booking->tgl_booking = $sewa->tanggal_berangkat;
                            $booking->id_customer =$data['customer_id'];
                            $booking->id_grup_tujuan =$data['select_grup_tujuan'];
                            $booking->is_sewa = "Y";
                            $booking->created_at = now();
                            $booking->created_by = $user; // manual
                            $booking->updated_at = now();
                            $booking->updated_by = $user; // manual
                            $booking->is_aktif = "Y";
                            // $booking->save();
                            if($booking->save())
                            {
                                $sewa->id_booking = $booking->id;
                                $sewa->updated_at = now();
                                $sewa->updated_by = $user;
                                $sewa->save();
                            }

                        }
                    }
                    else if ( $sewa->id_grup_tujuan!=$booking_lama->id_grup_tujuan)
                    {
                        $booking_lama->id_grup_tujuan = $data['select_grup_tujuan'];
                        $booking_lama->updated_at = now();
                        $booking_lama->updated_by = $user;
                        $booking_lama->save();
                    }
                }
                
                $arrayBiaya = json_decode($data['biayaDetail'], true);
                if(isset($arrayBiaya))
                {
                    foreach ($arrayBiaya as /*$key =>*/ $item) {
                        $sewa_biaya = new SewaBiaya();
                        $sewa_biaya->id_sewa = $sewa->id_sewa;
                        $sewa_biaya->deskripsi = $item['deskripsi'];
                        $sewa_biaya->biaya = $item['biaya'];
                        $sewa_biaya->catatan = $item['catatan']? $item['catatan']:null;
                        $sewa_biaya->created_at = now();
                        $sewa_biaya->created_by = $user;
                        $sewa_biaya->is_aktif = "Y";
                        $sewa_biaya->save();
                    }
                }
                if(isset($data['pembayaran'])){
                    $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();
                    if($riwayat_uang_jalan){
                        $kht = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                            'id_karyawan' => $data['id_karyawan'],
                                            'refrensi_id' => $riwayat_uang_jalan->id,
                                            'jenis'=>'POTONG',
                                            'refrensi_keterangan' => 'uang_jalan'
                                            ])->first();
                        $kh_lama = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                        if($kht){
                            $kht->updated_by = $user;
                            $kht->updated_at = now();
                            $kht->is_aktif = 'N';
                            // $kht->save();
                            if($kht->save())
                            {
                                if(isset($kh_lama)){
                                    $kh_lama->total_hutang += $riwayat_uang_jalan->potong_hutang; 
                                    $kh_lama->updated_by = $user;
                                    $kh_lama->updated_at = now();
                                    $kh_lama->save();
                                }
                            }
                        }

                        if($data['pembayaran'] != 'HUTANG'&&$data['pembayaran'] != 'TIDAK_ADA_TRANSAKSI'){

                            if($potong_hutang!=0)
                            {
                                // misal cair 500k potong 30k, trs balik 500k, 30k hutangnya ga jadi
                                $kh_baru = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                if(isset($kh_baru)){
                                    $kh_baru->total_hutang -= $potong_hutang; 
                                    $kh_baru->updated_by = $user;
                                    $kh_baru->updated_at = now();
                                    // $kh_baru->save();
                                    if( $kh_baru->save())
                                    {
                                        $kht = new KaryawanHutangTransaction();
                                        $kht->id_karyawan = $data['id_karyawan'];
                                        $kht->refrensi_id = $riwayat_uang_jalan->id; // id uang jalan
                                        $kht->refrensi_keterangan = 'uang_jalan';
                                        $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                        $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                        $kht->debit = 0;
                                        $kht->kredit = $potong_hutang;
                                        $kht->kas_bank_id = $data['pembayaran'];
                                        $kht->catatan = 'Potong hutang revisi order- '. 
                                        '['.$data['no_sewa'] .']'.' >> ' . 
                                        $data['kendaraan'] . ' >> ' . 
                                        $data['driver'].' >> Dari - ' . 
                                        $data['customer_awal'].' >> ' . 
                                        $data['tujuan_awal'] .
                                        '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                        '>> Ke - ' . 
                                        $data['customer_nama_baru'].' >> ' . 
                                        $data['nama_tujuan'] .
                                        '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                        '>> Nominal potong : Rp.'.number_format($potong_hutang).
                                        '>> Nominal diberikan : Rp.'.$data['total_akhir']
                                        ;
                                        $kht->created_by = $user;
                                        $kht->created_at = now();
                                        $kht->is_aktif = 'Y';
                                        // $kht->save();
                                        if($kht->save())
                                        {
                                            if($total_akhir!=0)
                                                {
                                                    
                                                    $kasBankTransaction = new KasBankTransaction ();
                                                    $kasBankTransaction->id_kas_bank = $data['pembayaran'];
                                                    $kasBankTransaction->tanggal =date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                                    $kasBankTransaction->debit = 0; // debit uang masuk
                                                    $kasBankTransaction->kredit =  $total_akhir;
                                                    $kasBankTransaction->jenis = 'uang_jalan';
                                                    $kasBankTransaction->keterangan_transaksi = 'Revisi order - '. 
                                                    '['.$data['no_sewa'] .']'.' >> ' . 
                                                    $data['kendaraan'] . ' >> ' . 
                                                    $data['driver'].' >> Dari - ' . 
                                                    $data['customer_awal'].' >> ' . 
                                                    $data['tujuan_awal'] .
                                                    '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                                    '>> Ke - ' . 
                                                    $data['customer_nama_baru'].' >> ' . 
                                                    $data['nama_tujuan'] .
                                                    '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                                    '>> Nominal potong : Rp.'.number_format($potong_hutang).
                                                    '>> Nominal diberikan : Rp.'.$data['total_akhir']
                                                    ;
                                                    $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
                                                    $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
                                                    $kasBankTransaction->created_at = now();
                                                    $kasBankTransaction->created_by = $user;
                                                    $kasBankTransaction->is_aktif = 'Y';
                                                    if($kasBankTransaction->save()){
                                                        $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                                                        $kasbank->saldo_sekarang -= $total_akhir;
                                                        $kasbank->updated_by = $user;
                                                        $kasbank->updated_at = now();
                                                        $kasbank->save();
                                                    }
                                                }
                                        }

                                    }
                
                                }
                                
                            }
                            else
                            {
                                if($total_akhir!=0)
                                {
                                    
                                    $kasBankTransaction = new KasBankTransaction ();
                                    $kasBankTransaction->id_kas_bank = $data['pembayaran'];
                                    $kasBankTransaction->tanggal =date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                    $kasBankTransaction->debit = 0; // debit uang masuk
                                    $kasBankTransaction->kredit =  $total_akhir;
                                    $kasBankTransaction->jenis = 'uang_jalan';
                                    $kasBankTransaction->keterangan_transaksi = 'Revisi order - '. 
                                    '['.$data['no_sewa'] .']'.' >> ' . 
                                    $data['kendaraan'] . ' >> ' . 
                                    $data['driver'].' >> Dari - ' . 
                                    $data['customer_awal'].' >> ' . 
                                    $data['tujuan_awal'] .
                                    '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                    '>> Ke - ' . 
                                    $data['customer_nama_baru'].' >> ' . 
                                    $data['nama_tujuan'] .
                                    '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                    '>> Nominal diberikan : Rp.'.$data['total_akhir']
                                    ;
                                    $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
                                    $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
                                    $kasBankTransaction->created_by = $user;
                                    $kasBankTransaction->created_at = now();
                                    $kasBankTransaction->is_aktif = 'Y';
                                    if($kasBankTransaction->save()){
                                        $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                                        $kasbank->saldo_sekarang -= $total_akhir;
                                        $kasbank->updated_by = $user;
                                        $kasbank->updated_at = now();
                                        $kasbank->save();
                                    }
                                }
                            }

                        }else{

                            if($potong_hutang!=0)
                            {
                               
                                $kh_lama_hutang = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                if(isset($kh_lama_hutang)){
                                    $kh_lama_hutang->total_hutang += $total_akhir; 
                                    $kh_lama_hutang->updated_by = $user;
                                    $kh_lama_hutang->updated_at = now();
                                    // $kh_lama_hutang->save();
                                    // dd($kh_lama_hutang->total_hutang);
                                    if($kh_lama_hutang->save())
                                    {
                                        $kht = new KaryawanHutangTransaction();
                                        $kht->id_karyawan = $data['id_karyawan'];
                                        $kht->refrensi_id = $sewa->id_sewa;
                                        $kht->refrensi_keterangan = 'uang_jalan';
                                        $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                        $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                        $kht->debit = $total_akhir; // HUTANG BARU nah ini 450k nya jadi hutang baru 50k nya gajadi,nanti kalau mau cairin lagi ya 500k potong
                                        $kht->kredit = 0;
                                        $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                        $kht->catatan = 'revisi order uang jalan lebih- '. 
                                        '['.$data['no_sewa'] .']'.' >> ' . 
                                        $data['kendaraan'] . ' >> ' . 
                                        $data['driver'].' >> Dari - ' . 
                                        $data['customer_awal'].' >> ' . 
                                        $data['tujuan_awal'] .
                                        '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                        '>> Ke - ' . 
                                        $data['customer_nama_baru'].' >> ' . 
                                        $data['nama_tujuan'] .
                                        '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                        '>> Nominal kembali : Rp.'.number_format($total_akhir);
                                        $kht->created_by = $user;
                                        $kht->created_at = now();
                                        $kht->is_aktif = 'Y';
                                        // $kht->save();
                                        if($kht->save())
                                        {
                                            if(isset($kh_lama_hutang)){
                                                $kh_lama_hutang->total_hutang -= $potong_hutang; 
                                                $kh_lama_hutang->updated_by = $user;
                                                $kh_lama_hutang->updated_at = now();
                                                // $kh_lama_hutang->save();
                                                if( $kh_lama_hutang->save())
                                                {

                                                    $kht = new KaryawanHutangTransaction();
                                                    $kht->id_karyawan = $data['id_karyawan'];
                                                    $kht->refrensi_id = $riwayat_uang_jalan->id; // id uang jalan
                                                    $kht->refrensi_keterangan = 'uang_jalan';
                                                    $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                                    $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                                    $kht->debit = 0;
                                                    $kht->kredit = $potong_hutang;
                                                    $kht->kas_bank_id =null; // soalnya potong pas kelebihan uj
                                                    $kht->catatan = 'Potong hutang revisi order uang jalan kembali- '. 
                                                    '['.$data['no_sewa'] .']'.' >> ' . 
                                                    $data['kendaraan'] . ' >> ' . 
                                                    $data['driver'].' >> Dari - ' . 
                                                    $data['customer_awal'].' >> ' . 
                                                    $data['tujuan_awal'] .
                                                    '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                                    '>> Ke - ' . 
                                                    $data['customer_nama_baru'].' >> ' . 
                                                    $data['nama_tujuan'] .
                                                    '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                                    '>> Nominal kembali : Rp.'.number_format($total_akhir).
                                                    '>> Nominal potong : Rp.'.number_format($potong_hutang);
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
                            else
                            {
                                $kh_lama_hutang = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                if(isset($kh_lama_hutang)){
                                    $kh_lama_hutang->total_hutang += $total_akhir; 
                                    $kh_lama_hutang->updated_by = $user;
                                    $kh_lama_hutang->updated_at = now();
                                    // $kh->save();
                                    if($kh_lama_hutang->save())
                                    {
                                        $kht = new KaryawanHutangTransaction();
                                        $kht->id_karyawan = $data['id_karyawan'];
                                        $kht->refrensi_id = $sewa->id_sewa;
                                        $kht->refrensi_keterangan = 'uang_jalan';
                                        $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                        $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                                        $kht->debit = $total_akhir; // HUTANG BARU nah ini 450k nya jadi hutang baru 50k nya gajadi,nanti kalau mau cairin lagi ya 500k potong
                                        $kht->kredit = 0;
                                        $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                        $kht->catatan = 'revisi order uang lebih- '. 
                                        '['.$data['no_sewa'] .']'.' >> ' . 
                                        $data['kendaraan'] . ' >> ' . 
                                        $data['driver'].' >> Dari - ' . 
                                        $data['customer_awal'].' >> ' . 
                                        $data['tujuan_awal'] .
                                        '( UJ lama :'.$data['total_uang_jalan_lama'].')' .
                                        '>> Ke - ' . 
                                        $data['customer_nama_baru'].' >> ' . 
                                        $data['nama_tujuan'] .
                                        '( UJ baru :'.$data['uang_jalan_baru'].')' .
                                        '>> Nominal kembali : Rp.'.number_format($total_akhir);
                                        $kht->created_by = $user;
                                        $kht->created_at = now();
                                        $kht->is_aktif = 'Y';
                                        // $kht->save();
                                    }
                                }
                            }
                        }
                        $riwayat_uang_jalan->total_uang_jalan = $total_uj_aja;
                        $riwayat_uang_jalan->total_tl = $total_tl;
                        $riwayat_uang_jalan->potong_hutang = $potong_hutang;
                        $riwayat_uang_jalan->updated_by = $user;
                        $riwayat_uang_jalan->updated_at = now();
                        // $riwayat_uang_jalan->save();
                        if($riwayat_uang_jalan->save())
                        {
                            if($customer){
                                $customer->kredit_sekarang += $tarif_baru;
                                $customer->updated_by = $user;
                                $customer->updated_at = now();
                                $customer->save();
                            }
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil Ubah tujuan perjalanan!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }




















































































    // public function save_ubah_tujuan_backup(Request $request, Sewa $sewa)
    // {
    //     $data = $request->post();
    //     // $tgl_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
    //     $user = Auth::user()->id;
    //     DB::beginTransaction(); 
    //     dd($data);
    //     try {
           
    //         $customer = Customer::where('is_aktif', '=', "Y")->find($sewa->id_customer);
    //         $total_uj_baru = isset($data['uang_jalan_baru'])?floatval(str_replace(',', '', $data['uang_jalan_baru'])):0;
    //         $total_tl = isset($data['stack_teluk_lamong_hidden'])?floatval(str_replace(',', '', $data['stack_teluk_lamong_hidden'])):0;
    //         $potong_hutang = isset($data['potong_hutang'])?floatval(str_replace(',', '', $data['potong_hutang'])):0;

    //         $total_akhir = isset($data['total_akhir'])?floatval(str_replace(',', '', $data['total_akhir'])):0;
    //         if($customer){
    //             $customer->kredit_sekarang -= $sewa->total_tarif;
    //             $customer->updated_by = $user;
    //             $customer->updated_at = now();
    //             $customer->save();
    //         }
    //         $sewa->updated_at = now();
    //         $sewa->updated_by = $user;
    //         if($sewa->save()){
    //             SewaBiaya::where('is_aktif', '=', 'Y')
    //             ->where('id_sewa', $sewa->id_sewa)
    //             ->update(['is_aktif' => 'N']);
    //             if(isset($data['pembayaran'])){
    //                 $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();
    //                 if($riwayat_uang_jalan){
    //                     $kht = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
    //                                         'id_karyawan' => $data['id_karyawan'],
    //                                         'refrensi_id' => $riwayat_uang_jalan->id,
    //                                         'jenis'=>'POTONG',
    //                                         'refrensi_keterangan' => 'uang_jalan'
    //                                         ])->first();
                    

    //                     if($data['pembayaran'] != 'HUTANG'||$data['pembayaran'] != 'TIDAK_ADA_TRANSAKSI'){

    //                         if($potong_hutang!=0)
    //                         {
    //                             // misal cair 500k potong 30k, trs balik 500k, 30k hutangnya ga jadi
    //                             $kh_lama = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
    //                             $kh_baru = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
    //                             if($kht){
    //                                 $kht->updated_by = $user;
    //                                 $kht->updated_at = now();
    //                                 $kht->is_aktif = 'N';
    //                                 // $kht->save();
    //                                 if($kht->save())
    //                                 {

    //                                     if(isset($kh_lama)){
    //                                         // kalau ada data, hutang ditambah
    //                                         $kh_lama->total_hutang += $riwayat_uang_jalan->potong_hutang; 
    //                                         $kh_lama->updated_by = $user;
    //                                         $kh_lama->updated_at = now();
    //                                         // $kh_lama->save();
    //                                         if($kh_lama->save())
    //                                         {
    //                                             if(isset($kh_baru)){
    //                                                 $kh_baru->total_hutang -= $potong_hutang; 
    //                                                 $kh_baru->updated_by = $user;
    //                                                 $kh_baru->updated_at = now();
    //                                                 // $kh_baru->save();
    //                                                 if( $kh_baru->save())
    //                                                 {
    //                                                     $kht = new KaryawanHutangTransaction();
    //                                                     $kht->id_karyawan = $data['id_karyawan'];
    //                                                     $kht->refrensi_id = $riwayat_uang_jalan->id; // id uang jalan
    //                                                     $kht->refrensi_keterangan = 'uang_jalan';
    //                                                     $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
    //                                                     $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
    //                                                     $kht->debit = 0;
    //                                                     $kht->kredit = $potong_hutang;
    //                                                     $kht->kas_bank_id = $data['pembayaran'];
    //                                                     $kht->catatan = 'Potong hutang revisi uang jalan- '. 
    //                                                     '['.$data['no_sewa'] .']'.' >> ' . 
    //                                                     $data['kendaraan'] . ' >> ' . 
    //                                                     $data['driver'].' >> Dari' . 
    //                                                     $data['customer_awal'].' >> ' . 
    //                                                     '('.$data['tujuan_awal'].')' .
    //                                                     '( UJ lama :'.$data['total_uang_jalan'].')' .
    //                                                     '>> Ke' . 
    //                                                     $data['customer_nama_baru'].' >> ' . 
    //                                                     '('.$data['nama_tujuan'].')' .
    //                                                     '( UJ baru :'.$data['uang_jalan_baru'].')' .
    //                                                     '>> Nominal potong : Rp.'.number_format($potong_hutang).
    //                                                     '>> Nominal diberikan : Rp.'.$data['total_akhir']
    //                                                     ;
    //                                                     $kht->created_by = $user;
    //                                                     $kht->created_at = now();
    //                                                     $kht->is_aktif = 'Y';
    //                                                     $kht->save();

    //                                                 }
                                
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                             else
    //                             {
    //                                 if(isset($kh_baru)){
    //                                     $kh_baru->total_hutang -= $potong_hutang; 
    //                                     $kh_baru->updated_by = $user;
    //                                     $kh_baru->updated_at = now();
    //                                     // $kh_baru->save();
    //                                     if( $kh_baru->save())
    //                                     {
    //                                         $kht = new KaryawanHutangTransaction();
    //                                         $kht->id_karyawan = $data['id_karyawan'];
    //                                         $kht->refrensi_id = $riwayat_uang_jalan->id; // id uang jalan
    //                                         $kht->refrensi_keterangan = 'uang_jalan';
    //                                         $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
    //                                         $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
    //                                         $kht->debit = 0;
    //                                         $kht->kredit = $potong_hutang;
    //                                         $kht->kas_bank_id = $data['pembayaran'];
    //                                         $kht->catatan = 'Potong hutang revisi uang jalan- '. 
    //                                         '['.$data['no_sewa'] .']'.' >> ' . 
    //                                         $data['kendaraan'] . ' >> ' . 
    //                                         $data['driver'].' >> Dari' . 
    //                                         $data['customer_awal'].' >> ' . 
    //                                         '('.$data['tujuan_awal'].')' .
    //                                         '( UJ lama :'.$data['total_uang_jalan'].')' .
    //                                         '>> Ke' . 
    //                                         $data['customer_nama_baru'].' >> ' . 
    //                                         '('.$data['nama_tujuan'].')' .
    //                                         '( UJ baru :'.$data['uang_jalan_baru'].')' .
    //                                         '>> Nominal potong : Rp.'.number_format($potong_hutang).
    //                                         '>> Nominal diberikan : Rp.'.$data['total_akhir']
    //                                         ;
    //                                         $kht->created_by = $user;
    //                                         $kht->created_at = now();
    //                                         $kht->is_aktif = 'Y';
    //                                         $kht->save();

    //                                     }
                    
    //                                 }
    //                             }
    //                             if($total_akhir!=0)
    //                             {
                                    
    //                                 $kasBankTransaction = new KasBankTransaction ();
    //                                 $kasBankTransaction->id_kas_bank = $data['pembayaran'];
    //                                 // $kasBankTransaction->tanggal =date_format($tgl_cancel, 'Y-m-d H:i:s');
    //                                 $kasBankTransaction->debit = 0; // debit uang masuk
    //                                 $kasBankTransaction->kredit =  $total_akhir;
    //                                 $kasBankTransaction->jenis = 'uang_jalan';
    //                                 $kasBankTransaction->keterangan_transaksi = 'Revisi uang jalan - '. 
    //                                 '['.$data['no_sewa'] .']'.' >> ' . 
    //                                 $data['kendaraan'] . ' >> ' . 
    //                                 $data['driver'].' >> Dari' . 
    //                                 $data['customer_awal'].' >> ' . 
    //                                 '('.$data['tujuan_awal'].')' .
    //                                 '( UJ lama :'.$data['total_uang_jalan'].')' .
    //                                 '>> Ke' . 
    //                                 $data['customer_nama_baru'].' >> ' . 
    //                                 '('.$data['nama_tujuan'].')' .
    //                                 '( UJ baru :'.$data['uang_jalan_baru'].')' .
    //                                 '>> Nominal diberikan : Rp.'.$data['total_akhir']
    //                                 ;
    //                                 $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
    //                                 $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
    //                                 $kasBankTransaction->created_at = now();
    //                                 $kasBankTransaction->created_by = $user;
    //                                 $kasBankTransaction->is_aktif = 'Y';
    //                                 if($kasBankTransaction->save()){
    //                                     $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
    //                                     $kasbank->saldo_sekarang -= $total_akhir;
    //                                     $kasbank->updated_by = $user;
    //                                     $kasbank->updated_at = now();
    //                                     $kasbank->save();
    //                                 }
    //                             }
    //                         }
    //                         else
    //                         {
    //                             if($total_akhir!=0)
    //                             {
                                    
    //                                 $kasBankTransaction = new KasBankTransaction ();
    //                                 $kasBankTransaction->id_kas_bank = $data['pembayaran'];
    //                                 // $kasBankTransaction->tanggal =date_format($tgl_cancel, 'Y-m-d H:i:s');
    //                                 $kasBankTransaction->debit = 0; // debit uang masuk
    //                                 $kasBankTransaction->kredit =  $total_akhir;
    //                                 $kasBankTransaction->jenis = 'uang_jalan';
    //                                 $kasBankTransaction->keterangan_transaksi = 'Revisi uang jalan - '. 
    //                                 '['.$data['no_sewa'] .']'.' >> ' . 
    //                                 $data['kendaraan'] . ' >> ' . 
    //                                 $data['driver'].' >> Dari' . 
    //                                 $data['customer_awal'].' >> ' . 
    //                                 '('.$data['tujuan_awal'].')' .
    //                                 '( UJ lama :'.$data['total_uang_jalan'].')' .
    //                                 '>> Ke' . 
    //                                 $data['customer_nama_baru'].' >> ' . 
    //                                 '('.$data['nama_tujuan'].')' .
    //                                 '( UJ baru :'.$data['uang_jalan_baru'].')' .
    //                                 '>> Nominal diberikan : Rp.'.$data['total_akhir']
    //                                 ;
    //                                 $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
    //                                 $kasBankTransaction->keterangan_kode_transaksi = $riwayat_uang_jalan->id;
    //                                 $kasBankTransaction->created_by = $user;
    //                                 $kasBankTransaction->created_at = now();
    //                                 $kasBankTransaction->is_aktif = 'Y';
    //                                 if($kasBankTransaction->save()){
    //                                     $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
    //                                     $kasbank->saldo_sekarang -= $total_akhir;
    //                                     $kasbank->updated_by = $user;
    //                                     $kasbank->updated_at = now();
    //                                     $kasbank->save();
    //                                 }
    //                             }
    //                         }

    //                         DB::commit();
    //                     }else{

    //                         if($potong_hutang!=0)
    //                         {
    //                             if($kht){
    //                                 $kht->updated_by = $user;
    //                                 $kht->updated_at = now();
    //                                 $kht->is_aktif = 'N';
    //                                 // $kht->save();
    //                                 if( $kht->save())
    //                                 {
    //                                     $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
    //                                     $kh_baru = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                        
    //                                     if(isset($kh)){
    //                                         $kh->total_hutang += $total_akhir; 
    //                                         $kh->updated_by = $user;
    //                                         $kh->updated_at = now();
    //                                         // $kh->save();
    //                                         if($kh->save())
    //                                         {
                                                
    //                                             $kht = new KaryawanHutangTransaction();
    //                                             $kht->id_karyawan = $data['id_karyawan'];
    //                                             $kht->refrensi_id = $sewa->id_sewa;
    //                                             $kht->refrensi_keterangan = 'uang_jalan';
    //                                             $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
    //                                             // $kht->tanggal = date_format($tgl_cancel, 'Y-m-d H:i:s');
    //                                             $kht->debit = $total_akhir; // HUTANG BARU nah ini 450k nya jadi hutang baru 50k nya gajadi,nanti kalau mau cairin lagi ya 500k potong
    //                                             $kht->kredit = 0;
    //                                             $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
    //                                             $kht->catatan = 'revisi uang jalan uang lebih- '. 
    //                                             '['.$data['no_sewa'] .']'.' >> ' . 
    //                                             $data['kendaraan'] . ' >> ' . 
    //                                             $data['driver'].' >> Dari' . 
    //                                             $data['customer_awal'].' >> ' . 
    //                                             '('.$data['tujuan_awal'].')' .
    //                                             '>> Ke' . 
    //                                             $data['customer_nama_baru'].' >> ' . 
    //                                             '('.$data['nama_tujuan'].')' .
    //                                             '>> Nominal kembali : Rp.'.number_format($total_akhir);
    //                                             $kht->created_by = $user;
    //                                             $kht->created_at = now();
    //                                             $kht->is_aktif = 'Y';
    //                                             // $kht->save();
    //                                             if($kht->save())
    //                                             {
    //                                                 if(isset($kh_baru)){
    //                                                     $kh_baru->total_hutang -= $potong_hutang; 
    //                                                     $kh_baru->updated_by = $user;
    //                                                     $kh_baru->updated_at = now();
    //                                                     // $kh_baru->save();
    //                                                     if( $kh_baru->save())
    //                                                     {
    //                                                         $kht = new KaryawanHutangTransaction();
    //                                                         $kht->id_karyawan = $data['id_karyawan'];
    //                                                         $kht->refrensi_id = $riwayat_uang_jalan->id; // id uang jalan
    //                                                         $kht->refrensi_keterangan = 'uang_jalan';
    //                                                         $kht->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
    //                                                         $kht->tanggal = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
    //                                                         $kht->debit = 0;
    //                                                         $kht->kredit = $potong_hutang;
    //                                                         $kht->kas_bank_id =null; // soalnya potong pas kelebihan uj
    //                                                         $kht->catatan = 'Potong hutang revisi uang jalan kembali- '. 
    //                                                         '['.$data['no_sewa'] .']'.' >> ' . 
    //                                                         $data['kendaraan'] . ' >> ' . 
    //                                                         $data['driver'].' >> Dari' . 
    //                                                         $data['customer_awal'].' >> ' . 
    //                                                         '('.$data['tujuan_awal'].')' .
    //                                                         '>> Ke' . 
    //                                                         $data['customer_nama_baru'].' >> ' . 
    //                                                         '('.$data['nama_tujuan'].')' .
    //                                                         '>> Nominal uang kembali : Rp.'.$data['total_akhir'].
    //                                                         '>> Nominal potong : Rp.'.number_format($potong_hutang)
    //                                                         ;
    //                                                         $kht->created_by = $user;
    //                                                         $kht->created_at = now();
    //                                                         $kht->is_aktif = 'Y';
    //                                                         $kht->save();
    
    //                                                     }
                                    
    //                                                 }
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                         else
    //                         {
    //                             $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
    //                             if(isset($kh)){
    //                                 $kh->total_hutang += $total_akhir; 
    //                                 $kh->updated_by = $user;
    //                                 $kh->updated_at = now();
    //                                 // $kh->save();
    //                                 if($kh->save())
    //                                 {
    //                                     $kht = new KaryawanHutangTransaction();
    //                                     $kht->id_karyawan = $data['id_karyawan'];
    //                                     $kht->refrensi_id = $sewa->id_sewa;
    //                                     $kht->refrensi_keterangan = 'uang_jalan';
    //                                     $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
    //                                     // $kht->tanggal = date_format($tgl_cancel, 'Y-m-d H:i:s');
    //                                     $kht->debit = $total_akhir; // HUTANG BARU nah ini 450k nya jadi hutang baru 50k nya gajadi,nanti kalau mau cairin lagi ya 500k potong
    //                                     $kht->kredit = 0;
    //                                     $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
    //                                     $kht->catatan = 'revisi uang jalan uang lebih- '. 
    //                                     '['.$data['no_sewa'] .']'.' >> ' . 
    //                                     $data['kendaraan'] . ' >> ' . 
    //                                     $data['driver'].' >> Dari' . 
    //                                     $data['customer_awal'].' >> ' . 
    //                                     '('.$data['tujuan_awal'].')' .
    //                                     '>> Ke' . 
    //                                     $data['customer_nama_baru'].' >> ' . 
    //                                     '('.$data['nama_tujuan'].')' .
    //                                     '>> Nominal kembali : Rp.'.number_format($total_akhir);
    //                                     $kht->created_by = $user;
    //                                     $kht->created_at = now();
    //                                     $kht->is_aktif = 'Y';
    //                                     // $kht->save();
    //                                 }
    //                             }
    //                         }
    //                         DB::commit();
    //                     }

    //                     $riwayat_uang_jalan->total_uang_jalan = $total_uj_baru;
    //                     $riwayat_uang_jalan->total_tl = $total_tl;
    //                     $riwayat_uang_jalan->potong_hutang = $potong_hutang;
    //                     $riwayat_uang_jalan->updated_by = $user;
    //                     $riwayat_uang_jalan->updated_at = now();
    //                     $riwayat_uang_jalan->save();
                        
    //                 }
    //             }
               
    //         }
    //         DB::commit();
    //         return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil Ubah tujuan perjalanan!"]);
    //     } catch (ValidationException $e) {
    //         DB::rollBack();
    //         return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
    //         // return redirect()->back()->withErrors($e->getMessage())->withInput();
    //     }
    //     catch (\Throwable $th) {
    //         db::rollBack();
    //         return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
    //     }
    // }
}
