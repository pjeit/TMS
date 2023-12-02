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
class DalamPerjalananController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_DALAM_PERJALANAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_DALAM_PERJALANAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_DALAM_PERJALANAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_DALAM_PERJALANAN', ['only' => ['destroy']]);  
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
                ->get();
        // dd($dataSewa);
    
        return view('pages.order.dalam_perjalanan.index',[
            'judul'=>"Trucking Order",
            'dataSewa' => $dataSewa,
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
            if($dalam_perjalanan->jenis_order=='INBOUND'&&$data['is_kembali']=='Y'){

                $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($data['id_jo_detail_hidden']);
                $JOD->status = 'MENUNGGU INVOICE';
                $JOD->save();

                // //LOGIC BUAT UBAH STATUS JO DARI PROSES DOORING KE MENUNGGU INVOICE
                // $cek_trigger = DB::table('job_order_detail as jod')
                // ->leftJoin('job_order', 'job_order.id', '=', 'jod.id_jo')
                // ->where('job_order.status', 'PROSES DOORING')
                // ->where('job_order.id', $data['id_jo_hidden'])
                // ->where('job_order.is_aktif', '=', "Y")
                // // ->where('jod.id', $dalam_perjalanan->id_jo_detail)
                // ->where('jod.status', 'PROSES DOORING')
                // ->where('jod.is_aktif', 'Y')
                // ->get();

                // if($cek_trigger=='[]')
                // {
                //     DB::table('job_order')
                //             ->where('id', $data['id_jo_hidden'])
                //             ->update([
                //                 'status' => 'MENUNGGU INVOICE',
                //                 'updated_at' => now(),
                //                 'updated_by' => $user,
                //             ]);
                // }
            }
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
                        $SOP->total_operasional = (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                    # code...
                }
            }
            //ini kalo ada data di db
            if(isset($data['data']))
            {
                foreach ($data['data'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));

                    if(isset($value['masuk_db']))
                    {
                         DB::table('sewa_operasional')
                            ->where('id_sewa', $dalam_perjalanan->id_sewa)
                            ->where('id', $value['id_sewa_operasional_data'])
                            ->update([
                                'deskripsi' => $value['deskripsi_data'],
                                'total_operasional' => (float)str_replace(',', '', $value['nominal_data']),
                                'is_ditagihkan' => $value['ditagihkan_data_value'],
                                'is_dipisahkan' => $value['dipisahkan_data_value'],
                                'catatan' => $value['catatan_data'],
                                'updated_at' => now(),
                                'updated_by' => $user,
                            ]);
                      
                    }
                    else
                    {
                       DB::table('sewa_operasional')
                            ->where('id_sewa', $dalam_perjalanan->id_sewa)
                            ->where('id', $value['id_sewa_operasional_data'])
                            ->update([
                                'deskripsi' => $value['deskripsi_data'],
                                'total_operasional' => (float)str_replace(',', '', $value['nominal_data']),
                                'is_ditagihkan' => $value['ditagihkan_data_value'],
                                'is_dipisahkan' => $value['dipisahkan_data_value'],
                                'catatan' => $value['catatan_data'],
                                'updated_at' => now(),
                                'updated_by' => $user,
                            ]); 
                    }
                    # code...
                }
            }
            //ini kalo dicentang data yang ambil dari db tujuan biaya/ jodetail biaya yang S/D/T
            if(isset($data['dataMaster']))
            {
                
                foreach ($data['dataMaster'] as $key => $value) {
                    // dd(isset($value['masuk_db'][1]));

                    if(isset($value['masuk_db']))
                    {
                    
                        $SOP = new SewaOperasional();
                        $SOP->id_sewa = $dalam_perjalanan->id_sewa; 
                        $SOP->deskripsi = $value['deskripsi_data'];
                        $SOP->total_operasional =  (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                    # code...
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
                        $SOP->total_operasional =  (float)str_replace(',', '', $value['nominal_data']);
                        $SOP->is_ditagihkan = $value['ditagihkan_data_value'];
                        $SOP->is_dipisahkan = $value['dipisahkan_data_value'];
                        $SOP->catatan = $value['catatan_data'];
                        $SOP->status = "TAGIHKAN DI INVOICE";
                        $SOP->created_by = $user;
                        $SOP->created_at = now();
                        $SOP->is_aktif = 'Y';
                        $SOP->save();
                    }
                    # code...
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

    public function save_batal_muat(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        $user = Auth::user()->id;
        $tgl_batal_muat_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
        $tgl_kembali = date_create_from_format('d-M-Y', $data['tanggal_kembali']);
        DB::beginTransaction(); 
        // dd($tgl_kembali);
        
        try {
            $sewa->status = 'BATAL MUAT';
            $sewa->total_tarif = floatval(str_replace(',', '', $data['total_tarif_tagih']));
            $sewa->catatan = $data['alasan_cancel'];
            $sewa->no_kontainer = $data['no_kontainer'];
            $sewa->no_surat_jalan = $data['no_surat_jalan'];
            $sewa->is_kembali = 'Y';
            $sewa->tanggal_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            if($sewa->save()){
                if($sewa->id_supplier==null)
                {
                    $uj_kembali = isset($data['total_uang_jalan_kembali'])? floatval(str_replace(',', '', $data['total_uang_jalan_kembali'])):0;
                    $tarif_ditagihkan = isset($data['total_tarif_tagih'])? floatval(str_replace(',', '', $data['total_tarif_tagih'])):0;

                    // DB::table('uang_jalan_riwayat')
                    // ->where('sewa_id', $sewa->id_sewa)
                    // ->where('is_aktif', 'Y')
                    // ->update([
                    //     'catatan' => 'BATAL MUAT',
                    //     'updated_by' => $user,
                    //     'updated_at' => now(),
                    //     'is_aktif' => 'N',
                    // ]);

                    $batal = new SewaBatalCancel();
                    $batal->id_sewa = $sewa->id_sewa;
                    $batal->jenis = 'BATAL';
                    $batal->tgl_batal_muat_cancel = date_format($tgl_batal_muat_cancel, 'Y-m-d H:i:s');
                    $batal->total_tarif_ditagihkan = floatval(str_replace(',', '', $data['total_tarif_tagih']));
                    $batal->total_uang_jalan_kembali = $uj_kembali;
                    if(isset($data['kasbank'])){
                        if($data['kasbank'] != 'HUTANG DRIVER'){
                            $batal->id_kas_bank = $data['kasbank'];
                        }else{
                            $batal->id_karyawan_hutang = $data['id_karyawan'];
                        }
                    }
                    $batal->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    $batal->alasan_batal = $data['alasan_cancel'];
                    $batal->created_by = $user;
                    $batal->created_at = now();
                    $batal->is_aktif = 'Y';
                    if($batal->save()){
                        if(isset($data['kasbank'])){
                            $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();

                            $cek = KasBankTransaction::where('is_aktif', 'Y')
                                                    ->where('id_kas_bank', $data['kasbank'])
                                                    ->where('keterangan_kode_transaksi', $riwayat_uang_jalan->id)
                                                    ->where('jenis', 'uang_jalan')->first();

                            if($cek){
                                if($data['kasbank'] != 'HUTANG DRIVER'){
                                    $kasBankTransaction = new KasBankTransaction ();
                                    $kasBankTransaction->id_kas_bank = $data['kasbank'];
                                    $kasBankTransaction->tanggal = date_format($tgl_batal_muat_cancel, 'Y-m-d H:i:s');
                                    $kasBankTransaction->debit = $uj_kembali; // debit uang masuk
                                    $kasBankTransaction->kredit = 0;
                                    $kasBankTransaction->jenis = 'uang_jalan';
                                    $kasBankTransaction->keterangan_transaksi = '(BATAL MUAT) UANG JALAN KEMBALI - ' . $data['alasan_cancel'] . ' #' . $data['kendaraan'] . ' #' . $data['driver']. $data['customer'].' #' . '('.$data['tujuan'].')' ;
                                    $kasBankTransaction->kode_coa =  CoaHelper::DataCoa(5002); // masih hardcode
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
                                    $kht->refrensi_id = $batal->id;
                                    $kht->refrensi_keterangan = 'BATAL MUAT';
                                    $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = date_format($tgl_batal_muat_cancel, 'Y-m-d H:i:s');
                                    $kht->debit = $uj_kembali;
                                    $kht->kredit = 0;
                                    $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                    $kht->catatan = 'PENGEMBALIAN UANG JALAN SBG HUTANG - ' . $data['alasan_cancel'];
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
                    $batalRekanan->tgl_batal_muat_cancel = date_format($tgl_batal_muat_cancel, 'Y-m-d H:i:s');
                    $batalRekanan->total_tarif_ditagihkan = floatval(str_replace(',', '', $data['total_tarif_tagih']));
                    $batalRekanan->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    $batalRekanan->alasan_batal = $data['alasan_cancel'].'[rekanan batal]';
                    $batalRekanan->created_by = $user;
                    $batalRekanan->created_at = now();
                    $batalRekanan->is_aktif = 'Y';
                    $batalRekanan->save();
                    DB::commit();

                }
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

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

        ]);
    }

    public function save_cancel(Request $request, Sewa $sewa)
    {
        $data = $request->post();
        $tgl_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
        $tgl_kembali = date_create_from_format('d-M-Y', $data['tanggal_kembali']);
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
            $sewa->status = 'CANCEL';
            $sewa->catatan = $data['alasan_cancel'];
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            if($sewa->save()){
                if($sewa->id_supplier==null)
                {
                    $uj_kembali = isset($data['uang_jalan_kembali'])? floatval(str_replace(',', '', $data['uang_jalan_kembali'])):0;
                    $cancel = new SewaBatalCancel();
                    $cancel->id_sewa = $sewa->id_sewa;
                    $cancel->jenis = 'CANCEL';
                    $cancel->tgl_batal_muat_cancel = date_format($tgl_cancel, 'Y-m-d H:i:s');
                    $cancel->total_uang_jalan_kembali = $uj_kembali;
                    if(isset($data['pembayaran'])){
                        if($data['pembayaran'] != 'HUTANG KARYAWAN'){
                            $cancel->id_kas_bank = $data['pembayaran'];
                        }else{
                            $cancel->id_karyawan_hutang = $data['id_karyawan'];
                        }
                    }
                    $cancel->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    $cancel->alasan_batal = $data['alasan_cancel'];
                    $cancel->created_by = $user;
                    $cancel->created_at = now();
                    $cancel->is_aktif = 'Y';
    
                    if($cancel->save()){
                        if(isset($data['pembayaran'])){
                            $riwayat_uang_jalan = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $sewa->id_sewa)->first();

                            $cek = KasBankTransaction::where('is_aktif', 'Y')
                                                    ->where('id_kas_bank', $data['pembayaran'])
                                                    ->where('keterangan_kode_transaksi', $riwayat_uang_jalan->id)
                                                    ->where('jenis', 'uang_jalan')->first();
                          
                            if($cek){
                                if($data['pembayaran'] != 'HUTANG KARYAWAN'){
                                    $kasBankTransaction = new KasBankTransaction ();
                                    $kasBankTransaction->id_kas_bank = $data['pembayaran'];
                                    $kasBankTransaction->tanggal =date_format($tgl_cancel, 'Y-m-d H:i:s');
                                    $kasBankTransaction->debit = $uj_kembali; // debit uang masuk
                                    $kasBankTransaction->kredit = 0;
                                    $kasBankTransaction->jenis = 'uang_jalan';
                                    $kasBankTransaction->keterangan_transaksi = '(CANCEL) UANG JALAN KEMBALI - ' . $data['alasan_cancel'] . ' #' . $data['kendaraan'] . ' #' . $data['driver'].' #' . $data['customer'].' #' . '('.$data['tujuan'].')' ;
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
                                        // if(isset($data['uang_jalan_kembali'])){
                                            DB::table('uang_jalan_riwayat')
                                            ->where('sewa_id', $sewa->id_sewa)
                                            ->where('is_aktif', 'Y')
                                            ->update([
                                                'catatan' => 'CANCEL',
                                                'updated_at' => now(),
                                                'updated_by' => $user,
                                                'is_aktif' => 'N',
                                            ]); 
                                        // }
                                        DB::commit();
                                    }
                                }else{
                                    $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();
                                    if(isset($kh)){
                                        // kalau ada data, hutang ditambah
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
                                    $kht->refrensi_id = $cancel->id;
                                    $kht->refrensi_keterangan = 'CANCEL';
                                    $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                    $kht->tanggal = date_format($tgl_cancel, 'Y-m-d H:i:s');
                                    $kht->debit = $uj_kembali; // HUTANG BARU
                                    $kht->kredit = 0;
                                    $kht->kas_bank_id = NULL; // kalau hutang, kasbank null
                                    $kht->catatan = 'PENGEMBALIAN UANG JALAN SBG HUTANG - ' . $data['alasan_cancel'] . ' #' . $data['kendaraan'] . ' #' . $data['driver'].' #' . $data['customer'].' #' . '('.$data['tujuan'].')';
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
                    $cancel_rekanan = new SewaBatalCancel();
                    $cancel_rekanan->id_sewa = $sewa->id_sewa;
                    $cancel_rekanan->jenis = 'CANCEL';
                    $cancel_rekanan->tgl_batal_muat_cancel = date_format($tgl_cancel, 'Y-m-d H:i:s');
                    $cancel_rekanan->tgl_kembali = date_format($tgl_kembali, 'Y-m-d H:i:s');
                    $cancel_rekanan->alasan_batal = $data['alasan_cancel'].'[rekanan cancel]';
                    $cancel_rekanan->created_by = $user;
                    $cancel_rekanan->created_at = now();
                    $cancel_rekanan->is_aktif = 'Y';
                    $cancel_rekanan->save();
                    DB::commit();

                }
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
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
            
            if($ujr->save()){
                $kht = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                        'id_karyawan' => $sewa->id_karyawan,
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
            $cek_sewa_operasional_TL = DB::table('sewa_operasional as so')
                                    ->select('so.*')
                                    ->where('so.id_sewa',  $id)
                                    ->where('so.is_aktif', 'Y')
                                    ->where('so.deskripsi', 'TL')
                                    ->first();
            //cek kalau misal awalnya tl terus diganti perak kan nyantol di operasional dan biaya
            //kalau ada tl nyantol di ubah jadi N
            if($cek_sewa_operasional_TL )
            {
                DB::table('sewa_operasional')
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
            ->where('k.role_id', 5)
            ->get();

        $dataUangJalanRiwayat= DB::table('uang_jalan_riwayat as ujr')
            ->select('ujr.*')
            ->where('ujr.is_aktif',"Y")
            ->where('ujr.sewa_id', $sewa->id_sewa)
            ->first();

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
                ->whereNotNull('k.driver_id')
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
                return redirect()->route('dalam_perjalanan.ubah_supir', [ $id ])->with(['status' => 'error', 'msg' => "Supir tidak boleh sama dengan sebelumnya jika ingin diubah!"]);
            }else{
                if($sewa->jenis_tujuan == 'FTL'){
                    $tl = isset($ujr->total_tl)?$ujr->total_tl:0;
                    $potong_hutang= isset($ujr->potong_hutang)?$ujr->potong_hutang:0;
                    //pengembalian data yang lama
                    if(isset($ujr)){
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $ujr->kas_bank_id,// id kas_bank 
                                now(),//tanggal
                                ($ujr->total_uang_jalan + $tl) - $potong_hutang,// debit 
                                0, //uang keluar (kredit)
                                2021, //kode coa
                                'uang_jalan',
                                'Pengembalian uang jalan ubah supir '.$sewa->no_sewa.' #'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.' #'.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
                                $ujr->id,//keterangan_kode_transaksi
                                $user,//created_by
                                now(),//created_at
                                $user,//updated_by
                                now(),//updated_at
                                'Y'
                            ) 
                        );
                    }
                    //tambah saldo bank kan ini ngembaliin data lama
                    $kasbank_lama = KasBank::where('is_aktif', 'Y')->find($ujr->kas_bank_id);
                    $kasbank_lama->saldo_sekarang += ($ujr->total_uang_jalan + $tl) - $potong_hutang;
                    $kasbank_lama->updated_by = $user;
                    $kasbank_lama->updated_by = now();
                    //kalo ada hutang karyawan
                    $kht_lama = KaryawanHutangTransaction::where(['is_aktif' => 'Y', 
                                                    'id_karyawan' => $sewa->id_karyawan,
                                                    'refrensi_id' => $ujr->id 
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
                    $kh_baru = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['select_driver'])->first();
                    if(isset($kh_baru)&&isset($data['potong_hutang'])){
                        $kh_baru->total_hutang -= (float)str_replace(',', '', $data['potong_hutang']); 
                        $kh_baru->updated_by = $user;
                        $kh_baru->updated_at = now();
                        $kh_baru->save();

                        $kht_baru = new KaryawanHutangTransaction();
                        $kht_baru->id_karyawan = $data['select_driver'];
                        $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                        $kht_baru->refrensi_keterangan = 'UANG JALAN';
                        $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                        $kht_baru->tanggal = now();
                        $kht_baru->debit = 0;
                        $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                        $kht_baru->kas_bank_id = $ujr->kas_bank_id;
                        $kht_baru->catatan = $data['catatan'] ;
                        $kht_baru->created_by = $user;
                        $kht_baru->created_at = now();
                        $kht_baru->is_aktif = 'Y';
                        $kht_baru->save();
                    }
                    else if(isset($data['potong_hutang']))
                    {
                        $kh_baru = new KaryawanHutang();
                        $kh_baru->id_karyawan = $data['select_driver'];
                        $kh_baru->total_hutang = (float)str_replace(',', '', $data['potong_hutang']);
                        $kh_baru->created_by = $user;
                        $kh_baru->created_at = now();
                        $kh_baru->is_aktif = 'Y';
                        if($kh_baru->save())
                        {
                            $kht_baru = new KaryawanHutangTransaction();
                            $kht_baru->id_karyawan = $data['select_driver'];
                            $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                            $kht_baru->refrensi_keterangan = 'UANG JALAN';
                            $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                            $kht_baru->tanggal = now();
                            $kht_baru->debit = 0;
                            $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                            $kht_baru->kas_bank_id =$ujr->kas_bank_id;
                            $kht_baru->catatan = $data['catatan'] ;
                            $kht_baru->created_by = $user;
                            $kht_baru->created_at = now();
                            $kht_baru->is_aktif = 'Y';
                            $kht_baru->save();
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
                            2021, //kode coa
                            'uang_jalan',
                            'Pencairan Uang jalan dari supir'.' #'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' #'.$data['no_polisi'].'('.$data['driver_nama'].')'.' #'.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
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
                    }
                }elseif($sewa->jenis_tujuan == 'LTL'){
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
                                'Pengembalian uang jalan ubah supir '.$sewa->no_sewa.' #'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.' #'.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
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
                                                                    'refrensi_id' => $ujr->id 
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
                            if(!$kh){
                                $kh = new KaryawanHutang(); // buat baru
                            }
                            $kh->id_karyawan = $data['select_driver'];
                            $kh->total_hutang = (float)str_replace(',', '', $data['potong_hutang']);
                            $kh->created_by = $user;
                            $kh->created_at = now();
                            $kh->is_aktif = 'Y';
                            if($kh->save()){
                                $kht_baru = new KaryawanHutangTransaction();
                                $kht_baru->id_karyawan = $data['select_driver'];
                                $kht_baru->refrensi_id = $ujr->id; // id uang jalan
                                $kht_baru->refrensi_keterangan = 'UANG JALAN';
                                $kht_baru->jenis = 'POTONG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                                $kht_baru->tanggal = now();
                                $kht_baru->debit = 0;
                                $kht_baru->kredit =(float)str_replace(',', '', $data['potong_hutang']);
                                $kht_baru->kas_bank_id =$ujr->kas_bank_id;
                                $kht_baru->catatan = $data['catatan'] ;
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
                                    'Pencairan Uang jalan dari supir'.' #'.$sewa->no_polisi.'('.$sewa->nama_driver.')'.'ke supir -> '.' #'.$data['no_polisi'].'('.$data['driver_nama'].')'.' #'.$data['customer'].'('.$sewa->nama_tujuan.') - '.$data['catatan'], //keterangan_transaksi
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
                }
                
            }
            DB::commit();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil mengubah data supir!"]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'error', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
        }

    }

}
