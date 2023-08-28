<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\JobOrderDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
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
        $dataJO = DB::table('job_order')
            ->select('*')
            ->where('is_aktif', 'Y')
            ->paginate(10);

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
            ->get();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', '=', "Y")
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
            // var_dump($request->post()); die;
            // $pesanKustom = [
            //     'no_jo.required' => 'Tanggal JO Harus diisi!',
            //     'tgl_plan_book.required' => 'Tanggal Plan Book Harus diisi!',
            // ];
            
            // $request->validate([
            //     'no_jo' => 'required',
            //     'tgl_plan_book' => 'required',
            // ], $pesanKustom);

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
            // var_dump($data); die;
            $newJO = new JobOrder();
            $newJO->no_jo = $newBookingNumber;
            $newJO->id_customer = $data['customer'];
            $newJO->id_supplier = $data['supplier'];
            $newJO->pelabuhan_muat = $data['pelabuhan_muat'];
            $newJO->pelabuhan_bongkar = $data['pelauhan_bongkar'];
            $newJO->no_bl = $data['no_bl'];
            $newJO->tgl_sandar = date_create_from_format('d-M-Y', $data['tgl_sandar']); 
            
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
            // $newJO->free_time = $data['free_time']; // hapus
            // $newJO->tgl_plan_book = $data['tgl_plan_book']; // hapus
            // $newJO->id_booking = $data['id_booking']; // kosong dulu
            // $newJO->jo_expired = $data['jo_expired']; // kosongkan dulu
            // $newJO->total_storage = $data['total_storage']; // kosongkan dulu
            // $newJO->total_demurage = $data['total_demurage']; // kosongkan dulu
            // $newJO->total_detention = $data['total_detention']; // kosongkan dulu
            // $newJO->total_repair_washing = $data['total_repair_washing']; // kosongkan dulu
            // $newJO->total_biaya_setelah_dooring = $data['total_biaya_setelah_dooring']; // kosongkan dulu
            $newJO->status = 'Waiting Payment';
            $newJO->created_by = $user;
            $newJO->created_at = now();
            $newJO->is_aktif = 'Y';
            
            if($newJO->save()){
                if(isset($data['detail'])){
                    foreach ($data['detail'] as $key => $detail) {
                        $JOD = new JobOrderDetail();
                        $JOD->id_jo = $newJO->id; // get id jo
                        // $JOD->id_booking = $detail->id_booking; // kosong dulu
                        // $JOD->tgl_berangkat_booking = $detail->tgl_berangkat_booking; // kosong dulu
                        $JOD->no_kontainer = $detail['no_kontainer'];
                        $JOD->seal = $detail['seal'];
                        // $JOD->seal_pje = $detail->seal_pje; // kosong dulu
                        // $JOD->id_kendaraan = $detail->id_kendaraan; // kosong dulu
                        // $JOD->nopol_kendaraan = $detail->nopol_kendaraan; // kosong dulu 
                        $JOD->id_grup_tujuan = $detail['tujuan']; 
                        // $JOD->tgl_dooring = $detail->tgl_dooring; // kosong dulu
                        // $JOD->storage = $detail->storage; // kosong dulu
                        // $JOD->demurage = $detail->demurage; // kosong dulu
                        // $JOD->detention = $detail->detention; // kosong dulu
                        // $JOD->repair_washing = $detail->repair_washing; // kosong dulu
                        $JOD->thc = $detail['hargaThc'];
                        $JOD->lolo = $detail['hargaLolo'];
                        $JOD->apbs = $detail['hargaApbs'];
                        $JOD->cleaning = $detail['hargaCleaning'];
                        $JOD->docfee = $detail['hargaDocFee'];
                        $JOD->tipe_kontainer = $detail['tipe'];
                        $JOD->status = "Waiting Payment";
                        // $JOD->do_expaired = $detail->do_expaired; // kosong dulu
                        $JOD->created_by = $user;
                        $JOD->created_at = now();
                        $JOD->is_aktif = 'Y';
                        $JOD->save();
                    }
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
        $data = $jobOrder;
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

        return view('pages.order.job_order.edit',[
            'judul'=>"Job Order",
            'data' => $data,
            'dataSupplier' => $dataSupplier,
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
        //
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
