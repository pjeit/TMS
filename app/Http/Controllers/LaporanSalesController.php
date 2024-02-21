<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sewa;
use Illuminate\Support\Facades\DB;

class LaporanSalesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:READ_LAPORAN_SALES', ['only' => ['index_laporan_sales']]);
    }
     public function index_laporan_sales()
    {
        return view('pages.laporan.Admin.laporan_sales',[
                'judul'=>"Laporan Sales",
        ]);
    }
    public function load_data_ajax(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $tipe_group     = $request->input('tipe_group');
        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);
        try {
             if($tipe_group=='customer'){
                $order_by='c.nama, c.id, s.tanggal_berangkat, s.id_sewa'; 
            }elseif($tipe_group=='kendaraan'){
                $order_by='k.no_polisi, k.id, s.tanggal_berangkat, s.id_sewa';
            }elseif($tipe_group=='driver'){
                $order_by='kw.nama_panggilan, kw.id, s.tanggal_berangkat, s.id_sewa';
            }
            $dataOps = Sewa::where('sewa.is_aktif', 'Y')
                        ->with('sewaOperasionaSales')
                        ->where('sewa.is_kembali', 'Y')
                        ->whereNotNull('sewa.tanggal_kembali')
                        ->whereBetween('sewa.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                        ->get();
           $data = DB::table('sewa as s')
                    ->select(
                        's.id_sewa',
                        's.id_supplier',
                        's.id_karyawan',
                        's.no_polisi',
                        's.id_customer',
                        's.id_chassis',
                        's.no_sewa',
                        's.nama_tujuan',
                        's.alamat_tujuan',
                        's.total_tarif',
                        's.total_komisi',
                        's.total_komisi_driver',
                        's.catatan',
                        's.no_kontainer',
                        's.no_surat_jalan',
                        'sp.nama as nama_supplier',
                        'i.no_invoice',
                        'c.nama as nama_customer',
                        'chs.kode as nama_ekor',
                        DB::raw("date_format(s.tanggal_berangkat, '%d-%b-%Y') as tanggal_berangkat"),
                        DB::raw("date_format(s.tanggal_kembali, '%d-%b-%Y') as tanggal_kembali"),
                        DB::raw("ifnull(trd.total_tagihan, s.total_uang_jalan) as total_uang_jalan"),
                        DB::raw("SUM(sop.total_operasional) as reimburse"),
                        // 's.total_reimburse_aktual',
                        // DB::raw("ifnull(id.tambahan,0) - ifnull(id.diskon,0) as tambahan_invoice"),
                        // DB::raw(" ifnull(id.diskon,0) as tambahan_invoice"),
                        DB::raw("ifnull(id.tambahan,0) - ifnull(id.diskon,0) as tambahan_invoice"),
                        DB::raw("s.total_tarif - ifnull(trd.subtotal, ifnull(s.total_uang_jalan,0)) - ifnull(s.total_komisi,0) - ifnull(s.total_reimburse_aktual,0) + ifnull(id.tambahan,0) - ifnull(id.diskon,0) as total_profit"),
                        // DB::raw("s.total_tarif - (ifnull(trd.total_tagihan, ifnull(s.total_uang_jalan,0)) - ifnull(s.total_komisi,0) - ifnull(s.total_komisi_driver,0)) - ifnull(id.diskon,0)  as total_profit"),
                        'kw.nama_lengkap as nama_driver',
                        'kw.nama_panggilan as panggilan_driver')
                    ->leftJoin('kendaraan as k', 's.id_kendaraan', '=', 'k.id')
                    ->leftJoin('supplier as sp', 's.id_supplier', '=', 'sp.id')
                    ->leftJoin('chassis as chs', 's.id_chassis', '=', 'chs.id')
                    ->join('customer as c', 's.id_customer', '=', 'c.id')
                    ->join('grup_tujuan as gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan as kw', 's.id_karyawan', '=', 'kw.id')
                    ->leftJoin('sewa_operasional as sop', function ($join) {
                        $join->on('s.id_sewa', '=', 'sop.id_sewa')
                            // ->where(function ($query) {
                            //     $query->where('sop.is_ditagihkan', '=', 'Y')
                            //         ->where('sop.is_dipisahkan', '=', 'N');
                            // })
                            // ->orWhere(function ($query) {
                            //     $query->where('sop.is_ditagihkan', '=', 'Y')
                            //         ->where('sop.is_dipisahkan', '=', 'Y');
                            // })
                            ->where('sop.is_ditagihkan','<>','N') // ini artinya Y N /  Y Y , bakal nyari ditagihkan yang !N sama kayak
                            // ->where('sop.id_sewa', '=', 's.id_sewa')
                            ->where('sop.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('tagihan_rekanan_detail as trd', function ($join) {
                        $join->on('s.id_sewa', '=', 'trd.id_sewa')
                            ->where('trd.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('invoice_detail as id', function ($join) {
                        $join->on('s.id_sewa', '=', 'id.id_sewa')
                            ->where('id.is_aktif', '=', 'Y');
                    })
                    ->leftJoin('invoice as i', function ($join) {
                        $join->on('id.id_invoice', '=', 'i.id')
                            ->where('i.is_aktif', '=', 'Y');
                    })
                    // ->where(function ($query) {
                    //     $query->where(function ($subquery) {
                    //         $subquery->where('s.status',  'MENUNGGU UANG JALAN')
                    //             ->where('s.total_uang_jalan', '=', 0);
                    //     })->orWhere(function ($subquery) {
                    //         $subquery->where('s.status', 'PROSES DOORING')
                    //             ->where('s.total_uang_jalan', '>', 0);
                    //     })
                    //     ->orWhere('s.status', 'MENUNGGU INVOICE')
                    //     ->orWhere('s.status', 'MENUNGGU PEMBAYARAN INVOICE')
                    //     ->orWhere('s.status', 'BATAL MUAT')
                    //     ->orWhere('s.status', 'CANCEL')
                    //     ->orWhere('s.status', 'SELESAI PEMBAYARAN');
                    // })
                    ->where('s.is_aktif', '=', 'Y')
                    ->whereBetween(DB::raw('cast(s.tanggal_berangkat as date)'), [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
                    ->orderByRaw($order_by)
                    ->groupBy('s.id_sewa',
                        // 's.id_supplier',
                        // 's.id_karyawan',
                        // 's.no_polisi',
                        // 's.id_customer',
                        // 's.id_chassis',
                        // 's.no_sewa',
                        // 's.nama_tujuan',
                        // 's.alamat_tujuan',
                        // 's.total_tarif',
                        // 's.total_komisi',
                        // 's.total_komisi_driver',
                        // 's.catatan',
                        // 's.no_kontainer',
                        // 's.no_surat_jalan',
                        // 'sp.nama',
                        // 'i.no_invoice',
                        // 'nama_customer',
                        // 'nama_ekor',
                        // 'tanggal_berangkat',
                        // 'tanggal_kembali',
                        // 'total_uang_jalan',
                        // 'total_profit',
                        // 'nama_driver',
                        // 'panggilan_driver'
                        )
                    ->get();
            return response()->json(["result" => "success", 'data' => $data,'dataOps',$dataOps], 200);
        } catch (\Throwable $th) {
            //throw $th;
        return response()->json(["result" => "error", 'data' =>/*$request->input('tanggal_awal')*/$th->getMessage()], 500);

        }
        
    }
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
            ->where('status_bayar' ,'like','%SELESAI PEMBAYARAN%')
            ->where('jodb.is_aktif', '=', "Y")
            ->get();
        // $dataOpreasional = DB::table('sewa_operasional AS so')
        //             ->select('so.*')
        //             ->where('so.is_aktif', '=', 'Y')
        //             ->where('so.status', 'like', '%SUDAH DICAIRKAN%')
        //             ->orWhere('so.status' ,'like','%TAGIHKAN DI INVOICE%')
        //             ->where('so.id_sewa', '=', $dalam_perjalanan->id_sewa)
        //             ->get();
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
                // dd($dataOpreasionalJO);
        // $cek_trigger = JobOrder::select('job_order.*')
        //         ->leftJoin('job_order_detail as jod', 'job_order.id', '=', 'jod.id_jo')
        //         ->where('jod.status', 'PROSES DOORING')
        //         ->where('job_order.status', 'PROSES DOORING')
        //         ->where('job_order.id', $dalam_perjalanan->id_jo)
        //         // ->where('jod.id', $dalam_perjalanan->id_jo_detail)
        //         ->where('job_order.is_aktif', '=', "Y")
        //         ->with('getCustomer')
        //         ->with('getSupplier')
        //         // ->groupBy('job_order.id')
        //         ->get();
        // $cek_trigger = DB::table('job_order_detail as jod')
        //                 ->leftJoin('job_order', 'job_order.id', '=', 'jod.id_jo')
        //                 ->where('jod.status', 'PROSES DOORING')
        //                 ->where('jod.is_aktif', 'Y')
        //                 ->where('job_order.status', 'PROSES DOORING')
        //                 ->where('job_order.id', $dalam_perjalanan->id_jo)
        //                 // ->where('jod.id', $dalam_perjalanan->id_jo_detail)
        //                 ->where('job_order.is_aktif', '=', "Y")
        //                 ->get();
        //         dd( $cek_trigger );
        // dd($dataOpreasional);
        // dd(strpos($dataOpreasional, 'CLEANING/REPAIR'));

        // $flagCleaning=false;
        // foreach($dataOpreasional as $opersional)
        // {
            
        //     if( $opersional->deskripsi== 'CLEANING/REPAIR' )
        //     {
        //         //hapus array kalau datanya sama 
        //         $flagCleaning = true;
        //         break;
        //     }
        // }
        // dd($flagCleaning);

        $array_inbound = [];
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
                        'deskripsi' => 'DOC_FEE',
                        'biaya' =>$value->doc_fee,
                    ];
                    array_push($array_inbound_parent, $objdoc_fee);
                }
            }
             //yang thc lolo
            foreach($dataOpreasionalJO as $opersional)
            {
                foreach ($array_inbound_parent as $key=> $dataInbound) {
                    # code...
                    if($opersional->deskripsi == $dataInbound['deskripsi'] && 
                    $opersional->total_operasional == $dataInbound['biaya'] &&
                    $opersional->id_jo ==  $dataInbound['id_jo']
                    )
                    {
                        //hapus array kalau datanya sama 
                        unset($array_inbound_parent[$key]);
                    }
                }
            }
        }
       
        //yang storage demurage dkk
        foreach($dataOpreasional as $opersional)
        {
            foreach ($array_inbound as $key=> $dataInbound) {
                # code...
                if($opersional->deskripsi == $dataInbound['deskripsi'] && $opersional->total_operasional == $dataInbound['biaya'] )
                {
                    //hapus array kalau datanya sama 
                    unset($array_inbound[$key]);
                }
            }
        }
        // dd(  $array_inbound);
        // dd($array_inbound_parent);


         $Tujuan = DB::table('grup_tujuan as gt')
            ->select('gt.*')
            ->where('gt.id', '=',  $dalam_perjalanan->id_grup_tujuan)
            ->where('gt.is_aktif', '=', "Y")
            ->get();
        $array_outbond = [];
        foreach ($Tujuan as $item) {
            // if ($item->seal_pelayaran) {
            //     $objSeal = [
            //         'deskripsi' => 'SEAL PELAYARAN',
            //         'biaya' => $item->seal_pelayaran,
            //     ];
            //     array_push($array_outbond, $objSeal);
            //     // array_push($array_inbound, $objSeal); // soalnya di inbound ada biaya seal pelayaran, makanya dimasukin

            // }
    
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
    
            // if ($item->tally) {
            //     $objTally = [
            //         'deskripsi' => 'TALLY',
            //         'biaya' => $item->tally,
            //     ];
            //     array_push($array_outbond, $objTally);
            // }
            
        }
        foreach($dataOpreasional as $opersional)
        {
            foreach ($array_outbond as $key=> $dataOutbond) {
                # code...
                if($opersional->deskripsi == $dataOutbond['deskripsi'] && $opersional->total_operasional == $dataOutbond['biaya'] )
                {
                    //hapus array kalau datanya sama 
                    unset($array_outbond[$key]);
                }
            }
        }
        //sorting array berdasarkan deskripsi
        usort($array_outbond, function ($a, $b) {
            return strcmp($b['deskripsi'],$a['deskripsi']);
        });
        usort($array_inbound, function ($a, $b) {
            return strcmp($b['deskripsi'],$a['deskripsi']);
        });
        // dd($array_outbond);
        return view('pages.laporan.Admin.laporan_sales_detail',[
            'judul' => "Detail Laporan Sales",
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
}
