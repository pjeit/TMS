<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\Booking;
use App\Models\Jaminan;
use App\Models\JobOrderDetail;
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

            $currentYear = Carbon::now()->format('y');
            $currentMonth = Carbon::now()->format('m');

            $kode = DB::table('customer')
                ->select('kode')
                ->where('id', $data['customer'])
                ->first();

            //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
            //bisa juga substr(no_booking, 8,10)
            $maxBooking = DB::table('job_order')
                ->selectRaw("ifnull(max(substr(no_jo, -3)), 0) + 1 as max_jo")
                ->whereRaw("substr(no_jo, 1, length(no_jo) - 3) = concat(?, ?, ?)", [$kode->kode,$currentYear, $currentMonth])
                ->value('max_booking');

            if (is_null($maxBooking)) {
                $newBookingNumber = 'PJE/'. $kode->kode . '/' . $currentYear . $currentMonth . '001';
            }else{
                // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                $newBookingNumber = 'PJE/'. $kode->kode . '/' . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);
            }

            $newJO = new JobOrder();
            $newJO->no_jo = $newBookingNumber;
            $newJO->id_customer = $data['customer'];
            $newJO->id_supplier = $data['supplier'];
            $newJO->pelabuhan_muat = $data['pelabuhan_muat'];
            $newJO->pelabuhan_bongkar = $data['pelabuhan_bongkar'];
            $newJO->no_bl = $data['no_bl'];
            $newJO->tgl_sandar = isset($data['tgl_sandar'])? date_create_from_format('d-M-Y', $data['tgl_sandar']):NULL; 
            
            if(isset($data['thc_cekbox'])){
                $newJO->total_thc = $data['total_thc'];
            }
            if(isset($data['lolo_cekbox'])){
                $newJO->total_lolo = $data['total_lolo'];
            }
            if(isset($data['apbs_cekbox'])){
                $newJO->total_apbs = $data['total_apbs'];
            }
            if(isset($data['cleaning_cekbox'])){
                $newJO->total_cleaning = $data['total_cleaning'];
            }
            if(isset($data['doc_fee_cekbox'])){
                $newJO->total_docfee = $data['total_doc_fee'];
            }
            $newJO->total_biaya_sebelum_dooring = $data['total_sblm_dooring'];
            $newJO->status = 'Waiting Payment';
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
                // create JO detail 
                if(isset($data['detail'])){
                    foreach ($data['detail'] as $key => $detail) {
                        $bookid = NULL;
                        // create booking
                        if(isset($detail['tgl_booking'])){
                            $booking = new Booking();
                            $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                            $booking->id_grup_tujuan = $detail['tujuan'];
                            $booking->no_kontainer = $detail['no_kontainer'];
                            $booking->id_customer = $data['customer'];
                            // logic nomer booking
                                //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
                                //bisa juga substr(no_booking, 8,10)
                                $maxBooking = DB::table('booking')
                                    ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                                    ->whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$data['kode_cust'],$currentYear, $currentMonth])
                                    ->value('max_booking');
                                
                                // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                                $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

                                if (is_null($maxBooking)) {
                                    $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . '001';
                                }
                            //
                            $booking->no_booking = $newBookingNumber;
                            $booking->id_customer = $data['customer'];
                            $booking->created_by = $user;
                            $booking->created_at = now();
                            $booking->save();

                            $bookid = $booking->id;
                        }

                        $JOD = new JobOrderDetail();
                        $JOD->id_jo = $newJO->id; // get id jo
                        $JOD->id_booking = isset($detail['tgl_booking'])? $bookid:NULL ; // get id jo
                        $JOD->tgl_booking = isset($detail['tgl_booking'])? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL ; // get id jo
                        $JOD->no_kontainer = $detail['no_kontainer'];
                        $JOD->seal = $detail['seal'];
                        $JOD->id_grup_tujuan = $detail['tujuan']; 
                        $JOD->thc_tipe = $detail['thcLD'];
                        $JOD->thc = $detail['hargaThc'];
                        $JOD->lolo = $detail['hargaLolo'];
                        $JOD->apbs = $detail['hargaApbs'];
                        $JOD->cleaning = $detail['hargaCleaning'];
                        $JOD->docfee = $detail['hargaDocFee'];
                        $JOD->tipe_kontainer = $detail['tipe'];
                        $JOD->status = "Waiting Payment";
                        $JOD->created_by = $user;
                        $JOD->created_at = now();
                        $JOD->is_aktif = 'Y';
                        $JOD->save();
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
                    if($detail['id_booking'] != NULL && $detail['tgl_booking'] != NULL){
                        $booking = Booking::where('is_aktif', 'Y')->find($detail['id_booking']);
                        $booking->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                        $booking->id_grup_tujuan = $detail['tujuan'];
                        $booking->save();

                        $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);
                        $JOD->id_booking = $detail['id_booking']; // kosong dulu
                        $JOD->id_grup_tujuan = $detail['tujuan'];
                        $JOD->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                        $JOD->updated_by = $user;
                        $JOD->updated_at = now();
                        $JOD->save();
                    }else{
                        $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);
                        if($detail['tgl_booking'] != null){
                            $booking = new Booking();
                            $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                            $booking->id_grup_tujuan = $detail['tujuan'];
                            $booking->no_kontainer = $detail['no_kontainer'];
                            $booking->id_customer = $data['customer'];
                            // logic nomer booking
                                //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
                                //bisa juga substr(no_booking, 8,10)
                                $maxBooking = DB::table('booking')
                                    ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                                    ->whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$jobOrder->getKodeCustomer->kode,$currentYear, $currentMonth])
                                    ->value('max_booking');
                                
                                // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
                                $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

                                if (is_null($maxBooking)) {
                                    $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . '001';
                                }
                            //
                            $booking->no_booking = $newBookingNumber;
                            $booking->id_customer = $data['customer'];
                            $booking->created_by = $user;
                            $booking->created_at = now();
                            $booking->save();

                            $JOD->id_booking = $booking->id; // kosong dulu
                        }
                        $JOD->id_grup_tujuan = $detail['tujuan'];
                        $JOD->tgl_booking = $detail['tgl_booking'] != NULL? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                        $JOD->updated_by = $user;
                        $JOD->updated_at = now();
                        $JOD->save();
                    }

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
        $dataJoDetail = DB::table('job_order_detail')
            ->select('*')
            ->where('job_order_detail.is_aktif', '=', "Y")
            ->where('job_order_detail.id_jo', '=', $JobOrder->id)
            ->get();
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $JobOrder->id)
            ->get();
        // dd($dataJoDetail);   
        $pdf = PDF::loadView('pages.order.job_order.print',[
            'judul'=>"Job Order",
            'JobOrder'=>$JobOrder,
            'dataSupplier'=>$dataSupplier,
            'dataCustomer'=>$dataCustomer,
            'dataJoDetail'=>$dataJoDetail,
            'dataJaminan'=>$dataJaminan,
        ]); 
        // dd($JobOrder);
        $pdf->setPaper('A5', 'portrait');
        // Customize the PDF generation process if needed
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif'
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
        //     'dataJoDetail'=>$dataJoDetail

        // ]);
    }
}
