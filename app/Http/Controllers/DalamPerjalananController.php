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

class DalamPerjalananController extends Controller
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

    $dataSewa =  DB::table('sewa AS s')
                ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                ->where('s.is_aktif', '=', 'Y')
                ->where('s.status', 'PROSES DOORING')
                ->where('s.jenis_tujuan', 'FTL')
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
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','jod.seal as seal_pelayaran_jod','jod.no_kontainer as no_kontainer_jod','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->leftJoin('job_order_detail AS jod', 's.id_jo_detail', '=', 'jod.id')
                    ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    // ->where('s.status', 'PROSES DOORING')
                    // ->whereNull('s.id_supplier')
                    // ->whereNull('s.tanggal_kembali')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', '=', $dalam_perjalanan->id_sewa)
                    ->groupBy('c.id')
                    ->first();
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
                    ->where('so.deskripsi', 'not like', '%OPERASIONAL%')
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
                    ->where('so.deskripsi', 'not like', '%OPERASIONAL%')
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
                $dalam_perjalanan->tanggal_kembali = isset($data['tanggal_kembali'])? date_create_from_format('d-M-Y', $data['tanggal_kembali']):null;
                $dalam_perjalanan->status = $data['is_kembali']=='Y'? 'MENUNGGU INVOICE':'PROSES DOORING';
                $dalam_perjalanan->is_kembali = $data['is_kembali'];
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

    public function save_batal_muat(Request $request, Sewa $sewa){
        $data = $request->post();
        $jenis = $data['jenis'];
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        dd($data);

        try {
            $sewa->status = 'BATAL MUAT';
            $sewa->total_tarif = floatval(str_replace(',', '', $data['total_tarif_tagih']));
            $sewa->catatan = $data['alasan_cancel'];
            $sewa->updated_by = $user;
            $sewa->updated_at = now();
            if($sewa->save()){
                $batal = new SewaBatalCancel();
                $batal->id_sewa = $sewa->id_sewa;
                $batal->jenis = 'BATAL';
                $batal->tgl_batal_muat_cancel = date_create_from_format('d-M-Y', $data['tanggal_cancel']);
                $batal->total_tarif_ditagihkan = floatval(str_replace(',', '', $data['total_tarif_tagih']));
                $batal->total_uang_jalan_kembali = floatval(str_replace(',', '', $data['total_uang_jalan_kembali']));
                $batal->id_kas_bank = $data['kasbank'];
                $batal->tgl_kembali = date_create_from_format('d-M-Y', $data['tanggal_kembali']);
                $batal->alasan_batal = $data['alasan_cancel'];
                $batal->created_by = $user;
                $batal->created_at = now();
                $batal->is_aktif = 'Y';
                if($batal->save()){
                    if($jenis == 'BELUM TRANSFER'){
                        $kasBankTransaction = KasBankTransaction::where('is_aktif', 'Y')->where('keterangan_kode_transaksi', $sewa->id_sewa);
                        if($kasBankTransaction){
                            $kasBankTransaction->tanggal = $data['tanggal_cancel'];
                            $kasBankTransaction->keterangan_transaksi = $data['alasan_cancel'] . ' - ' . $data['kendaraan'] . ' - ' . $data['driver'] ;
                            $kasBankTransaction->updated_at = $user;
                            $kasBankTransaction->updated_by = now();
                            $kasBankTransaction->is_aktif = 'N';
                        }
                    }else{
                        $kasBankTransaction = new KasBankTransaction ();
                        $kasBankTransaction->id_kas_bank = $data['kasbank'];
                        $kasBankTransaction->tanggal = $data['tanggal_cancel'];
                        $kasBankTransaction->debit = floatval(str_replace(',', '', $data['total_uang_jalan_kembali'])); // debit, uang jalan, karena gajadi keluar
                        $kasBankTransaction->kredit = 0;
                        $kasBankTransaction->jenis = 'BATAL MUAT';
                        $kasBankTransaction->keterangan_transaksi = $data['alasan_cancel'] . ' - ' . $data['kendaraan'] . ' - ' . $data['driver'] ;
                        $kasBankTransaction->kode_coa = 1823;
                        $kasBankTransaction->keterangan_kode_transaksi = $sewa->id_sewa;
                        $kasBankTransaction->created_at = $user;
                        $kasBankTransaction->created_by = now();
                        $kasBankTransaction->is_aktif = 'Y';
                    }
                    if($kasBankTransaction->save()){
                        $kasbank = KasBank::where('is_aktif', 'Y')->find($data['kasbank']);
                        $kasbank->saldo_sekarang += floatval(str_replace(',', '', $data['total_uang_jalan_kembali']));
                        $kasbank->updated_by = $user;
                        $kasbank->updated_at = now();
                        $kasbank->save();
                        DB::commit();
                    }
                    
                }
            }

            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil menyimpan data!"]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Terjadi kesalahan! <br>" . $e->getMessage()]);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

    }

    public function batal_muat($id)
    {
        $sewa = Sewa::with('customer')->where('is_aktif', 'Y')->find($id);
        $kasbank = KasBank::where('is_aktif', 'Y')->get();
        $riwayatPotongHutang = UangJalanRiwayat::where('is_aktif', 'Y')->where('sewa_id', $id)->first();

        return view('pages.order.dalam_perjalanan.batal_muat',[
            'judul' => "batal muat",
            'data' => $sewa,
            'kasbank' => $kasbank,
            'riwayatPotongHutang' => $riwayatPotongHutang,
        ]);
    }

    public function cancel($id)
    {
        $sewa = DB::table('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','jod.seal as seal_pelayaran_jod','jod.no_kontainer as no_kontainer_jod','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier','ujr.total_diterima as total_diterima_supir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->leftJoin('job_order_detail AS jod', 's.id_jo_detail', '=', 'jod.id')
                    ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')
                    ->join('uang_jalan_riwayat AS ujr', 's.id_sewa', '=', 'ujr.sewa_id')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    // ->where('s.status', 'PROSES DOORING')
                    // ->whereNull('s.id_supplier')
                    // ->whereNull('s.tanggal_kembali')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', '=', $id)
                    ->groupBy('c.id')
                    ->first();
        // dd($sewa->customer);
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        $dataUJ=DB::table('uang_jalan_riwayat')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        return view('pages.order.dalam_perjalanan.cancel',[
            'judul' => "cancel",
            'data' => $sewa,
            'id_sewa' => $id,
            'dataKas' => $dataKas,

        ]);
    }

    public function insertCancel(Request $request)
    {
        $user = Auth::user()->id;
        $data = $request->post();
        //  dd($data);
        try {
            // $pesanKustom = [
            //     'kode.required' => 'Kode Harus diisi!',
            //     'nama.required' => 'Nama Harus diisi!',
            // ];
            
            // $request->validate([
            //     'kode' => 'required',
            //     'nama' => 'required',
            // ], $pesanKustom);
            
            $datauang_jalan_riwayat = DB::table('uang_jalan_riwayat')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->where('sewa_id', $data['id_sewa_hidden'])
                    ->first();

            // ====================update untuk ngurangin kredit customer sama grup======================
            $sewa = Sewa::where('is_aktif', 'Y')
            ->whereNull('id_supplier') 
            ->findOrFail($data['id_sewa_hidden']);
            $customer_lama = DB::table('customer as c')
                    ->select('c.*')
                    ->where('c.id', '=', $sewa->id_customer)
                    ->where('c.is_aktif', '=', "Y")
                    ->first();
            $grup_lama = DB::table('grup as g')
                ->select('g.*')
                ->where('g.id', '=', $customer_lama->grup_id)
                ->where('g.is_aktif', '=', "Y")
                ->first();
            DB::table('customer')
                ->where('id', $sewa->id_customer)
                ->update([
                    'kredit_sekarang' => (float)$customer_lama->kredit_sekarang-$sewa->total_tarif < 0 ? 0 : $customer_lama->kredit_sekarang-$sewa->total_tarif,
                    'updated_at' => now(),
                    'updated_by' => $user,
            ]);
            DB::table('grup')
                ->where('id', $customer_lama->grup_id)
                ->update([
                    'total_kredit' => (float)$grup_lama->total_kredit-$sewa->total_tarif< 0 ? 0 : $grup_lama->total_kredit-$sewa->total_tarif,
                    'updated_at' => now(),
                    'updated_by' => $user,
            ]);
            // ====================update untuk ngurangin kredit customer sama grup======================


            DB::table('sewa')
            ->where('id_sewa', $data['id_sewa_hidden'])
            ->update([
                'status' =>'CANCEL',
                'tanggal_kembali' =>now(),
                'is_kembali' =>'Y',
                'updated_by' => $user,
                'updated_at' => now(),

            ]); 
            $idHutangKaryawan = 0;
            if(isset($data['nominal_kembali_hutang'])|| 
                $data['nominal_kembali_hutang']!= 0 || 
                (float)str_replace(',', '', $data['nominal_kembali_hutang'])!=0)
            {
                $kh = KaryawanHutang::where('is_aktif', 'Y')->where('id_karyawan', $data['id_karyawan'])->first();

                
                $kh->total_hutang +=  (float)str_replace(',', '', $data['nominal_kembali_hutang']); 
                $kh->updated_by = $user;
                $kh->updated_at = now();
                $kh->save();

                $kht = new KaryawanHutangTransaction();
                $kht->id_karyawan = $data['id_karyawan'];
                $kht->refrensi_id = $datauang_jalan_riwayat->id;
                $kht->refrensi_keterangan = 
                '#totalMasukHutang: ' . (float)str_replace(',', '', $data['nominal_kembali_hutang']);
                $kht->jenis = 'HUTANG'; // ada POTONG(KALAO PENCAIRAN UJ), BAYAR(KALO SUPIR BAYAR), HUTANG(KALAU CANCEL SEWA)
                $kht->tanggal = now();
                $kht->debit = (float)str_replace(',', '', $data['nominal_kembali_hutang']);
                $kht->kredit = 0;
                $kht->kas_bank_id = null;
                $kht->catatan = null;
                $kht->created_by = $user;
                $kht->created_at = now();
                $kht->is_aktif = 'Y';
                $kht->save();

                $idHutangKaryawan = $kht->id;
                if(
                    isset($data['nominal_kembali_kas'])|| 
                    $data['nominal_kembali_kas']!= 0 || 
                    (float)str_replace(',', '', $data['nominal_kembali_kas'])!=0)
                {
                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['pembayaran'],// id kas_bank dr form
                                    now(),//tanggal
                                    (float)str_replace(',', '', $data['nominal_kembali_kas']),// uang masuk (debit)
                                    0, //uang keluar (kredit)
                                    1016, //kode coa
                                    'uang_jalan',
                                    'PENGEMBALIAN UANG JALAN'.'#'.$data['no_sewa'].'#'.$data['kendaraan'].'('.$data['driver'].')'.'#'.$data['customer'].'#'.$data['tujuan'].'#'.$data['catatan'], //keterangan_transaksi
                                    $datauang_jalan_riwayat->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                    $kasbank = KasBank::where('is_aktif', 'Y')->find($data['kasbank']);
                        $kasbank->saldo_sekarang += (float)str_replace(',', '', $data['nominal_kembali_kas']);
                        $kasbank->updated_by = $user;
                        $kasbank->updated_at = now();
                        $kasbank->save();
                }
            }
            else
            {
                   
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['pembayaran'],// id kas_bank dr form
                                    now(),//tanggal
                                    (float)str_replace(',', '', $data['nominal_kembali_kas']),// uang masuk (debit)
                                    0, //uang keluar (kredit)
                                    1016, //kode coa
                                    'uang_jalan',
                                    'PENGEMBALIAN UANG JALAN'.'#'.$data['no_sewa'].'#'.$data['kendaraan'].'('.$data['driver'].')'.'#'.$data['customer'].'#'.$data['tujuan'].'#'.$data['catatan'], //keterangan_transaksi
                                    $datauang_jalan_riwayat->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                $kasbank = KasBank::where('is_aktif', 'Y')->find($data['kasbank']);
                        $kasbank->saldo_sekarang += (float)str_replace(',', '', $data['nominal_kembali_kas']);
                        $kasbank->updated_by = $user;
                        $kasbank->updated_at = now();
                        $kasbank->save();
            }
            $SBC = new SewaBatalCancel();
            $SBC->id_sewa =  $data['id_sewa_hidden']; 
            $SBC->jenis = 'CANCEL';
            $SBC->tgl_batal_muat_cancel = now();
            // $SBC->total_tarif_ditagihkan =  (float)str_replace(',', '', $value['nominal_data']);//gapake soalnya cancel gaada
            $SBC->total_uang_jalan_kembali = $data['nominal_kembali_kas']?(float)str_replace(',', '', $data['nominal_kembali_kas']):0;
            $SBC->id_kas_bank = $data['pembayaran'];
            $SBC->total_uang_jalan_kembali_hutang = $data['nominal_kembali_hutang']?(float)str_replace(',', '', $data['nominal_kembali_hutang']):0;
            $SBC->id_karyawan_hutang = ($idHutangKaryawan!=0)?$idHutangKaryawan:null;
            $SBC->tgl_kembali = now();
            $SBC->alasan_batal = $data['alasan_cancel'];
            $SBC->created_by = $user;
            $SBC->created_at = now();
            $SBC->updated_by = $user;
            $SBC->updated_at = now();
            $SBC->is_aktif = 'Y';
            $SBC->save();

            

            DB::table('uang_jalan_riwayat')
            ->where('sewa_id', $data['id_sewa_hidden'])
            ->update([
                'is_aktif' =>'N',
                'updated_by' => $user,
                'updated_at' => now(),

            ]); 
            
                        

            return redirect()->route('dalam_perjalanan.index')->with(['status' => 'Success', 'msg' => "Berhasil Cancel perjalanan!"]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }
}
