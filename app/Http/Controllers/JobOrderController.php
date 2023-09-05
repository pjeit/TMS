<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\Booking;
use App\Models\Jaminan;
use App\Models\job_order_detail_biaya;
use App\Models\JobOrderDetail;
use App\Models\JobOrderDetailBiaya;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;

class JobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataJO = DB::table('job_order as jo')
            ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
            ->leftJoin('supplier as s', 's.id', '=', 'jo.id_supplier')
            ->select('jo.*', 'c.kode as kode', 'c.nama as nama_cust', 's.nama as nama_supp')
            ->where('jo.is_aktif', '=', "Y")
            ->OrderBy('created_at', 'ASC')
            ->get();
        
            return view('pages.order.job_order.index',[
                'judul'=>"Job Order",
                'dataJO' => $dataJO,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dataSupplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('jenis_supplier_id', 6) // jenis pelayaran
            ->orderBy('nama')
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', "Y")
            ->orderBy('nama')
            ->get();
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
        // dd($dataPengaturanKeuangan[0]);

        return view('pages.order.job_order.create',[
            'judul'=>"Job Order",
            'dataSupplier' => $dataSupplier,
            'dataCustomer' =>$dataCustomer,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,

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
        try {
            $user = Auth::user()->id;
            $data = $request->post();

            // $currentYear = Carbon::now()->format('y');
            // $currentMonth = Carbon::now()->format('m');

            $kode = DB::table('customer')
                ->select('kode')
                ->where('id', $data['customer'])
                ->first();
            $now = now()->format('ym');
            $t_kode = $kode->kode;

            // "PJE/3DW/2309001"
            //8 PJE/3DW/
            //7 2309001
            //length 15
            // SELECT ifnull(max(substr(job_order.no_jo, -1)), 0) + 1 AS t_no_jo
            // FROM job_order
            // WHERE SUBSTRING(job_order.no_jo, 1, 12) = CONCAT('PJE/', 'CBA/' ,DATE_FORMAT(NOW(), '%y%m'));
            $max_jo = DB::table('job_order')
                ->selectRaw("ifnull(max(substr(no_jo, -3)), 0) + 1 AS t_no_jo")
                ->whereRaw("SUBSTRING(no_jo, 1, 11) = CONCAT('JO/', '$t_kode/','$now')")
                ->value('t_no_jo');

            if (is_null($max_jo) || empty($max_jo)) {
                $no_jo = "JO/$t_kode/" . $now . '001';
            }
            //strpad itu nambah 00 yang awalnya cuman 4 jd 004
            $no_jo = 'JO/'. $kode->kode . '/' . $now . str_pad($max_jo, 3, '0', STR_PAD_LEFT);
            
            // dd($data);
            $newJO = new JobOrder();
            $newJO->no_jo = $no_jo;
            $newJO->id_customer = $data['customer'];
            $newJO->id_supplier = $data['supplier'];
            $newJO->pelabuhan_muat = $data['pelabuhan_muat'];
            $newJO->pelabuhan_bongkar = $data['pelabuhan_bongkar'];
            $newJO->no_bl = $data['no_bl'];
            $newJO->tgl_sandar = isset($data['tgl_sandar'])? date_create_from_format('d-M-Y', $data['tgl_sandar']):NULL; 
            
            // if(isset($data['thc_cekbox'])){
            //     $newJO->total_thc = $data['total_thc'];
            // }
            // if(isset($data['lolo_cekbox'])){
            //     $newJO->total_lolo = $data['total_lolo'];
            // }
            // if(isset($data['apbs_cekbox'])){
            //     $newJO->total_apbs = $data['total_apbs'];
            // }
            // if(isset($data['cleaning_cekbox'])){
            //     $newJO->total_cleaning = $data['total_cleaning'];
            // }
            // if(isset($data['doc_fee_cekbox'])){
            //     $newJO->doc_fee = $data['total_doc_fee'];
            // }
            // $newJO->total_biaya_sebelum_dooring = $data['total_sblm_dooring'];
            $newJO->status = 'WAITING PAYMENT';
            $newJO->created_by = $user;
            $newJO->created_at = now();
            $newJO->is_aktif = 'Y';
            // $newJO->free_time = $data['free_time']; // hapus
            // $newJO->tgl_plan_book = $data['tgl_plan_book']; // hapus
            // $newJO->id_booking = $data['id_booking']; // kosong dulu
            // $newJO->jo_expired = $data['jo_expired']; // kosongkan dulu
            // $newJO->total_storage = $data['total_storage']; // kosongkan dulu
            // $newJO->total_demurage = $data['total_demurage']; // kosongkan dulu
            // $newJO->total_detention = $data['total_detention']; // kosongkan dulu
            // $newJO->total_repair_washing = $data['total_repair_washing']; // kosongkan dulu
            // $newJO->total_biaya_setelah_dooring = $data['total_biaya_setelah_dooring']; // kosongkan dulu

            if($newJO->save()){
                if(isset($data['checkbox']['DOC_FEE'])){
                    // var_dump($data['DOC_FEE']); die;
                    $JOD_biaya = new JobOrderDetailBiaya();
                    $JOD_biaya->id_jo = $newJO->id; 
                    $JOD_biaya->keterangan = 'DOC_FEE'; 
                    $JOD_biaya->nominal = $data['DOC_FEE']; 
                    $JOD_biaya->status = "WAITING PAYMENT"; 
                    $JOD_biaya->created_by = $user;
                    $JOD_biaya->created_at = now();
                    $JOD_biaya->is_aktif = 'Y';

                    $JOD_biaya->save();
                }

                // create JO detail 
                if(isset($data['detail'])){
                    foreach ($data['detail'] as $key => $detail) {
                        // create booking || sementara di offkan dulu
                            // if(isset($detail['tgl_booking'])){
                            //     $booking = new Booking();
                            //     $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                            //     $booking->id_grup_tujuan = $detail['tujuan'];
                            //     $booking->no_kontainer = $detail['no_kontainer'];
                            //     $booking->id_customer = $data['customer'];
                            //     // logic nomer booking
                            //         //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
                            //         //bisa juga substr(no_booking, 8,10)
                            //         $maxBooking = DB::table('booking')
                            //             ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                            //             ->whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$data['kode_cust'],$currentYear, $currentMonth])
                            //             ->value('max_booking');
                                    
                            //         // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                            //         $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

                            //         if (is_null($maxBooking)) {
                            //             $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . '001';
                            //         }
                            //     //
                            //     $booking->no_booking = $newBookingNumber;
                            //     $booking->id_customer = $data['customer'];
                            //     $booking->created_by = $user;
                            //     $booking->created_at = now();
                            //     $booking->save();

                            //     $bookid = $booking->id;
                            // }
                        //
                        $JOD = new JobOrderDetail();
                        $JOD->id_jo = $newJO->id; // get id jo
                        // $JOD->id_booking = isset($detail['tgl_booking'])? $bookid:NULL ; // get id jo
                        $JOD->id_grup_tujuan = $detail['tujuan']; 
                        $JOD->tgl_booking = isset($detail['tgl_booking'])? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL ; // get id jo
                        $JOD->no_kontainer = $detail['no_kontainer'];
                        $JOD->jenis = $detail['jenis']; 
                        $JOD->seal = $detail['seal'];
                        $JOD->tipe_kontainer = $detail['tipe'];
                        $JOD->stripping = $detail['stripping'];
                        // $JOD->thc =  isset($data['thc_cekbox'])?$detail['hargaThc']:0;
                        // $JOD->lolo = isset($data['lolo_cekbox'])?$detail['hargaLolo']:0;
                        // $JOD->apbs = isset($data['apbs_cekbox'])?$detail['hargaApbs']:0;
                        // $JOD->cleaning = isset($data['cleaning_cekbox'])?$detail['hargaCleaning']:0;
                        // $JOD->docfee = isset($data['doc_fee_cekbox'])?$detail['hargaDocFee']:0;
                        $JOD->status = "WAITING PAYMENT";
                        $JOD->created_by = $user;
                        $JOD->created_at = now();
                        $JOD->is_aktif = 'Y';
                        if($JOD->save()){
                            foreach ($data['checkbox'] as $key => $biaya) {
                                // doc fee di skip karna sudah di save duluan di atas
                                if($key != 'DOC_FEE'){
                                    $JOD_biaya = new JobOrderDetailBiaya();
                                    $JOD_biaya->id_jo = $newJO->id; 
                                    $JOD_biaya->id_jo_detail = $JOD->id; 
                                    $JOD_biaya->keterangan = $key; 
                                    $JOD_biaya->nominal = $detail['biaya'][$key]; // get data biaya sesuai key-nya. THC, LOLO, APBS
                                    $JOD_biaya->status = "WAITING PAYMENT"; 
                                    $JOD_biaya->created_by = $user;
                                    $JOD_biaya->created_at = now();
                                    $JOD_biaya->is_aktif = 'Y';
                                    $JOD_biaya->save();
                                }
                            }

                        }
                        // $JOD->save();
                        // $JOD->id_booking = $detail->id_booking; // kosong dulu
                        // $JOD->tgl_berangkat_booking = $detail->tgl_berangkat_booking; // kosong dulu
                        // $JOD->seal_pje = $detail->seal_pje; // kosong dulu
                        // $JOD->id_kendaraan = $detail->id_kendaraan; // kosong dulu
                        // $JOD->nopol_kendaraan = $detail->nopol_kendaraan; // kosong dulu 
                        // $JOD->tgl_dooring = $detail->tgl_dooring; // kosong dulu
                        // $JOD->storage = $detail->storage; // kosong dulu
                        // $JOD->demurage = $detail->demurage; // kosong dulu
                        // $JOD->detention = $detail->detention; // kosong dulu
                        // $JOD->repair_washing = $detail->repair_washing; // kosong dulu
                        // $JOD->do_expaired = $detail->do_expaired; // kosong dulu
                    }
                }

               
                // create jaminan
                if(isset($data['tgl_bayar_jaminan']) || isset($data['total_jaminan'])){
                    $jaminan = new Jaminan();
                    $jaminan->id_job_order = $newJO->id;
                    $jaminan->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar_jaminan']);
                    $jaminan->nominal = floatval(str_replace(',', '', $data['total_jaminan']));
                    $jaminan->catatan = $data['catatan'];
                     // 'MENUNGGU PEMBAYARAN','DIBAYARKAN','KEMBALI'
                    $jaminan->status = 'MENUNGGU PEMBAYARAN';
                    $jaminan->created_by = $user;
                    $jaminan->created_at = now();
                    $jaminan->is_aktif = 'Y';
                    $jaminan->save();
                }

            }
        
            return redirect()->route('job_order.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
    public function edit(JobOrder $jobOrder)
    {
        $data['JO'] = $jobOrder;
        $dataSupplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->get();

        $dataTujuan = DB::table('grup_tujuan')
            ->select('*')
            ->where('grup_id', $jobOrder->getGrupId->grup_id)
            ->where('is_aktif', "Y")
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', '=', "Y")
            ->get();
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();

        $detail = JobOrderDetail::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->get();
        $jaminan = Jaminan::where('id_job_order', $jobOrder->id)->where('is_aktif', 'Y')->first();
        $data['detail'] = json_encode($detail);
        $data['jaminan'] = $jaminan;

        $total_thc = JobOrderDetailBiaya::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->where('keterangan', 'thc')->sum('nominal');
        $total_lolo = JobOrderDetailBiaya::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->where('keterangan', 'lolo')->sum('nominal');
        $total_apbs = JobOrderDetailBiaya::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->where('keterangan', 'apbs')->sum('nominal');
        $total_cleaning = JobOrderDetailBiaya::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->where('keterangan', 'cleaning')->sum('nominal');
        $total_doc_fee = JobOrderDetailBiaya::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->where('keterangan', 'doc_fee')->sum('nominal');
        $data['biaya']['thc'] = $total_thc;
        $data['biaya']['lolo'] = $total_lolo;
        $data['biaya']['apbs'] = $total_apbs;
        $data['biaya']['cleaning'] = $total_cleaning;
        $data['biaya']['doc_fee'] = $total_doc_fee;

        return view('pages.order.job_order.edit',[
            'judul'=>"Job Order",
            'data' => $data,
            'dataSupplier' => $dataSupplier,
            'dataTujuan' => $dataTujuan,
            'dataCustomer' =>$dataCustomer,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobOrder $jobOrder)
    {
        try {
            // var_dump($request->post()); die;

            $user = Auth::user()->id;
            $data = $request->post();
            $currentYear = Carbon::now()->format('y');
            $currentMonth = Carbon::now()->format('m');
            // dd($data); 
            // die;

            if(isset($data['detail'])){
                foreach ($data['detail'] as $key => $detail) {
                    // var_dump($detail['tgl_booking']);
                        // if($detail['id_booking'] != NULL && $detail['tgl_booking'] != NULL){
                            //     $booking = Booking::where('is_aktif', 'Y')->find($detail['id_booking']);
                            //     $booking->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                            //     $booking->id_grup_tujuan = $detail['tujuan'];
                            //     $booking->save();

                            //     $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);
                            //     $JOD->id_booking = $detail['id_booking']; // kosong dulu
                            //     $JOD->id_grup_tujuan = $detail['tujuan'];
                            //     $JOD->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                            //     $JOD->updated_by = $user;
                            //     $JOD->updated_at = now();
                            //     $JOD->save();
                            // }else{
                            // if($detail['tgl_booking'] != null){
                            //     $booking = new Booking();
                            //     $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                            //     $booking->id_grup_tujuan = $detail['tujuan'];
                            //     $booking->no_kontainer = $detail['no_kontainer'];
                            //     $booking->id_customer = $data['customer'];
                            //     // logic nomer booking
                            //         //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
                            //         //bisa juga substr(no_booking, 8,10)
                            //         $maxBooking = DB::table('booking')
                            //             ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                            //             ->whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$jobOrder->getKodeCustomer->kode,$currentYear, $currentMonth])
                            //             ->value('max_booking');
                                    
                            //         // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                            //         $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

                            //         if (is_null($maxBooking)) {
                            //             $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . '001';
                            //         }
                            //     //
                            //     $booking->no_booking = $newBookingNumber;
                            //     $booking->id_customer = $data['customer'];
                            //     $booking->created_by = $user;
                            //     $booking->created_at = now();
                            //     $booking->save();

                            //     $JOD->id_booking = $booking->id; // kosong dulu
                        // }
                    $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);

                    $JOD->id_grup_tujuan = $detail['tujuan'];
                    $JOD->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                    $JOD->updated_by = $user;
                    $JOD->updated_at = now();
                    $JOD->save();

                }
                // die;
            }
            
            if(isset($data['id_jaminan'])){
                $jaminan = Jaminan::where('is_aktif', 'Y')->find($data['id_jaminan']);
                $jaminan->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar_jaminan']);
                $jaminan->nominal = floatval(str_replace(',', '', $data['total_jaminan']));
                $jaminan->catatan = $data['catatan'];
                $jaminan->updated_by = $user;
                $jaminan->updated_at = now();
                $jaminan->save();
            }

        

            return redirect()->route('job_order.index')->with('status','Success!!');
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

    public function printJO(JobOrder $JobOrder)
    {
        //
        $dataSupplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('supplier.id', '=', $JobOrder->id_supplier)
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', '=', "Y")
            ->where('customer.id', '=', $JobOrder->id_customer)
            ->get();
 
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $JobOrder->id)
            ->get();

        $totalThc =  DB::table('job_order_detail_biaya')
            ->where('id_jo', $JobOrder->id)
            ->where('keterangan', 'LIKE', '%THC%')
            ->sum('nominal');
        $totalLolo =  DB::table('job_order_detail_biaya')
            ->where('id_jo', $JobOrder->id)
            ->where('keterangan', 'LIKE', '%LOLO%')
            ->sum('nominal');
        $totalApbs =  DB::table('job_order_detail_biaya')
            ->where('id_jo', $JobOrder->id)
            ->where('keterangan', 'LIKE', '%APBS%')
            ->sum('nominal');
         $totalCleaning =  DB::table('job_order_detail_biaya')
            ->where('id_jo', $JobOrder->id)
            ->where('keterangan', 'LIKE', '%CLEANING%')
            ->sum('nominal');
         $Docfee =  DB::table('job_order_detail_biaya')
            ->select('nominal')
            ->where('id_jo', $JobOrder->id)
            ->where('keterangan', 'LIKE', '%DOC_FEE%')
            ->first();
        $TotalBiaya  = $totalThc+ $totalLolo +$totalApbs+$totalCleaning+$Docfee->nominal;
        // dd($TotalBiaya);


        // dd($dataJoDetail);   
        $pdf = PDF::loadView('pages.order.job_order.print',[
            'judul'=>"Job Order",
            'JobOrder'=>$JobOrder,
            'dataSupplier'=>$dataSupplier,
            'dataCustomer'=>$dataCustomer,
            'dataJaminan'=>$dataJaminan,
            'totalThc'=> $totalThc,
            'totalLolo'=> $totalLolo,
            'totalApbs'=> $totalApbs,
            'totalCleaning'=>$totalCleaning,
            'Docfee'=>$Docfee,
            'TotalBiaya'=>$TotalBiaya
        ]); 
        // dd($JobOrder);
        // $pdf->setPaper('A5', 'landscape');
        $pdf->setPaper('A5', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 180, // Set a high DPI for better resolution
        ]);
        // langsung download
        // return $pdf->download('fileCoba.pdf'); 
        // preview dulu
        return $pdf->stream('fileCoba.pdf'); 

        //  return view('pages.order.job_order.print',[
        //     'judul'=>"Job Order",
        //     'JobOrder'=>$JobOrder,
        //     'dataSupplier'=>$dataSupplier,
        //     'dataCustomer'=>$dataCustomer,
        //     'dataJoDetail'=>$dataJoDetail,
        //     'dataJaminan'=>$dataJaminan,

        // ]);
    }

    public function unloading_plan(){
       
 
        return view('pages.order.job_order.unloading_plan',[
                'judul'=>"Uloading Plan Job Order",
                // 'dataJODetail'=>$dataJODetail
            ]);
    }
     public function unloading_data(Request $request){
        try {
            $data = $request->collect();
             $statusJO  = $data['statusJO'];
            $statusJODetail   =  $data['statusJODetail'];

            // var_dump($statusJO);die;

            $dataJO = DB::table('job_order AS jo')
                    ->select('jo.*','jod.*','jo.status as statusJO','jod.status as statusDetail','c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp')
                    ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
                    ->join('job_order_detail AS jod', function($join) use ($statusJODetail){
                            $join->on('jo.id', '=', 'jod.id_jo') ->where('jod.status','like',"%$statusJODetail%");
                    })
                    ->leftJoin('grup_tujuan AS gt', 'jod.id_grup_tujuan', '=', 'gt.id')
                    ->where('jo.is_aktif', '=', 'Y')
                        ->where('jo.status', 'like', "%$statusJO%")
                    ->groupBy('jod.id')
                    ->get();
            return response()->json(["result" => "success",'data' => $dataJO],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["result" => "error",'message' => $th->getMessage()],500);

        }
       
        // return view('pages.order.job_order.unloading_plan',[
        //         'judul'=>"Uloading Plan Job Order",
        //         'dataJO' => $dataJO,
        //         // 'dataJODetail'=>$dataJODetail
        //     ]);
    }
}
