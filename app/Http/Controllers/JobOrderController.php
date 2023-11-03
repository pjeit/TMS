<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Jaminan;
use App\Models\JobOrderDetail;
use App\Models\JobOrderDetailBiaya;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;
use App\Helper\UserHelper;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class JobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        // $this->middleware('can: create JO');
        // $this->middleware('can: create JO')->only('create');
        // buka UserSeeder buat detailnya
    }

    public function index()
    {
        // $this->authorize('read JO');
        // if(!Gate::allows('read JO')){
        //     abort(403, 'unauthorized');
        // }
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        
        $id_role = Auth::user()->role_id; 
        $cabang = UserHelper::getCabang();

        // dd(User::find(1)->HasRole('Super User'));

        $dataJO = DB::table('job_order as jo')
            ->leftJoin('user as u', 'u.id', '=', 'jo.created_by')
            ->leftJoin('karyawan as k', 'k.id', '=', 'u.karyawan_id')
            ->where(function ($query) use ($id_role, $cabang) {
                if(!in_array($id_role, [1,3])){
                    $query->where('k.cabang_id', $cabang); // selain id [1,3] atau role [superadmin, admin nasional] lock per kota
                }
            })
            // ->select('jo.id as id_jo', 'jo.no_jo', 'u.id as id_user', 'u.karyawan_id as id_karyawan', 'u.username', 'k.cabang_id')
            ->select('jo.*','ja.id as idJaminan', DB::raw('jo.thc + jo.lolo + jo.apbs + jo.cleaning as Jumlah_sblm_dooring'),'c.kode as kode', 'c.nama as nama_cust', 's.nama as nama_supp')
            ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
            ->leftJoin('supplier as s', 's.id', '=', 'jo.id_supplier')
            ->leftJoin('jaminan AS ja', function($join) {
                $join->on('jo.id', '=', 'ja.id_job_order')->where('ja.is_aktif', '=', 'Y');
            })
            ->where('jo.is_aktif', '=', "Y")
            ->OrderBy('c.nama', 'ASC')
            ->OrderBy('jo.status', 'ASC')
            ->OrderBy('jo.created_at', 'ASC')
            ->get();
            // dd($dataJO);
        
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
        // $this->authorize('create JO');

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
            // dd($data);

            $kode = DB::table('customer')
                ->select('kode')
                ->where('id', $data['customer'])
                ->first();
            $now = now()->format('ym');
            $t_kode = $kode->kode;

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
            $newJO->kapal = $data['kapal'];
            $newJO->voyage = $data['voyage'];
            $newJO->tgl_sandar = isset($data['tgl_sandar'])? date_create_from_format('d-M-Y', $data['tgl_sandar']):NULL; 
            $newJO->thc =  isset($data['checkbox_THC'])? floatval(str_replace(',', '', $data['total_thc'])):0;
            $newJO->lolo = isset($data['checkbox_LOLO'])? floatval(str_replace(',', '', $data['total_lolo'])):0;
            $newJO->apbs = isset($data['checkbox_APBS'])? floatval(str_replace(',', '', $data['total_apbs'])):0;
            $newJO->cleaning = isset($data['checkbox_CLEANING'])? floatval(str_replace(',', '', $data['total_cleaning'])):0;
            $newJO->doc_fee = isset($data['checkbox_DOC_FEE'])? floatval(str_replace(',', '', $data['DOC_FEE'])):0;
            $newJO->catatan = $data['catatan'];
            $newJO->created_by = $user;
            $newJO->created_at = now();
            $newJO->is_aktif = 'Y';
       
            if( $newJO->thc == 0 && $newJO->lolo == 0 && $newJO->apbs == 0 && 
                    $newJO->cleaning == 0 && $newJO->doc_fee == 0 && 
                    ($data['tgl_bayar_jaminan'] == null || $data['total_jaminan'] == null) ){

                $newJO->status = 'PROSES DOORING'; // MENUNGGU PEMBAYARAN, PROSES DOORING
            }else{
                $newJO->status = 'MENUNGGU PEMBAYARAN'; // MENUNGGU PEMBAYARAN, PROSES DOORING
            }

            if($newJO->save()){
                // create JO detail 
                if(isset($data['detail'])){
                    foreach ($data['detail'] as $key => $detail) {
                        $JOD = new JobOrderDetail();
                        $JOD->id_jo = $newJO->id; // get id jo
                        $JOD->id_grup_tujuan = $detail['tujuan']; 
                        $JOD->no_kontainer = $detail['no_kontainer'];
                        $JOD->pick_up = $detail['pick_up']; 
                        $JOD->seal = $detail['seal'];
                        $JOD->tipe_kontainer = $detail['tipe'];
                        $JOD->stripping = $detail['stripping'];
                        $JOD->status = "BELUM DOORING";
                        $JOD->created_by = $user;
                        $JOD->created_at = now();
                        $JOD->is_aktif = 'Y';
                        if($JOD->save() && isset($detail['tgl_booking'])){
                            if(isset($detail['tgl_booking'])){
                                $booking = new Booking();
                                $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                                $booking->id_grup_tujuan = $detail['tujuan'];
                                $booking->no_kontainer = $detail['no_kontainer'];
                                $booking->id_customer = $data['customer'];
                                $booking->id_jo_detail = $JOD->id;
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
                                $booking->created_by = $user;
                                $booking->created_at = now();
                                $booking->save();
                            }
                        }
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
        
            // return redirect()->route('job_order.index')->with('status','Success!!');
            $id_print_jo = $newJO->id; // Replace with the actual value of the id

            return redirect()->route('job_order.index')
                ->with('id_print_jo', $id_print_jo)
                ->with(['status' => 'Success', 'msg' => 'Data berhasil tersimpan']);

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
            ->orderBy('nama', 'asc')
            ->get();
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
        $detail = DB::table('job_order_detail as jod')
            ->select('jod.*', 'b.id as id_booking', 'b.tgl_booking as tgl_booking','s.id_sewa as sewa_id')
            ->leftJoin('booking as b', function($leftJoin){
                $leftJoin->on('b.id_jo_detail', '=', "jod.id")->where('b.is_aktif',"Y");
            })
            ->leftJoin('sewa as s', function($leftJoin){
                $leftJoin->on('s.id_jo_detail', '=', "jod.id")->where('s.is_aktif',"Y");
            })
            ->where('jod.id_jo', $jobOrder->id)
            ->where('jod.is_aktif', "Y")
            ->get();

        // $detail = JobOrderDetail::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->get();
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

            if(isset($data['detail'])){
                foreach ($data['detail'] as $key => $detail) {
                    $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);
                    if(isset($detail['tujuan'])){
                        $JOD->id_grup_tujuan = $detail['tujuan'];
                    }
                    if(isset($detail['pick_up'])){
                        $JOD->pick_up = $detail['pick_up'];
                    }
                    $JOD->updated_by = $user;
                    $JOD->updated_at = now();
                    if($JOD->save() && isset($detail['tgl_booking'])){
                        if($detail['id_booking'] != NULL && $detail['tgl_booking'] != NULL){
                            $booking = Booking::where('is_aktif', 'Y')->find($detail['id_booking']);
                            if($booking){
                                $booking->tgl_booking = $detail['tgl_booking'] != NULL ? date_create_from_format('d-M-Y', $detail['tgl_booking']):NULL; // kosong dulu
                                $booking->id_grup_tujuan = $detail['tujuan'];
                                $booking->updated_by = $user;
                                $booking->updated_at = now();
                                $booking->save();
                            }
                        }else{
                            if($detail['tgl_booking'] != NULL && $detail['tujuan'] != NULL){
                                $booking = new Booking();
                                $booking->tgl_booking = date_create_from_format('d-M-Y', $detail['tgl_booking']);
                                $booking->id_grup_tujuan = $detail['tujuan'];
                                $booking->id_jo_detail = $JOD->id;
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
                                    $newBookingNumber = $data['kode_cust'] . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

                                    if (is_null($maxBooking)) {
                                        $newBookingNumber = $data['kode_cust'] . $currentYear . $currentMonth . '001';
                                    }
                                //
                                $booking->no_booking = $newBookingNumber;
                                $booking->id_customer = $data['customer'];
                                $booking->created_by = $user;
                                $booking->created_at = now();
                                $booking->save();
                            }
                        }
                    }

                }
            }
            
            // if(isset($data['id_jaminan'])){
            //     $jaminan = Jaminan::where('is_aktif', 'Y')->find($data['id_jaminan']);
            //     $jaminan->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar_jaminan']);
            //     $jaminan->nominal = floatval(str_replace(',', '', $data['total_jaminan']));
            //     $jaminan->catatan = $data['catatan'];
            //     $jaminan->updated_by = $user;
            //     $jaminan->updated_at = now();
            //     $jaminan->save();
            // }

            return redirect()->route('job_order.index')->with('status','Success');
        } catch (ValidationException $e) {
            return redirect()->route('job_order.index')->with('status','Error');
            // return redirect()->back()->withErrors($e->errors())->withInput();
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
        // dd($jobOrder);
         $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        // try{
            DB::table('job_order')
            ->where('id', $jobOrder['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(),
                'updated_by'=> $user, 
              )
            );
            DB::table('job_order_detail')
            ->where('id_jo', $jobOrder['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(),
                'updated_by'=> $user, 
              )
            );
            $jaminan = Jaminan::where('id_job_order', $jobOrder->id)->where('is_aktif', 'Y')->first();
            if($jaminan)
            {
                DB::table('jaminan')
               ->where('id_job_order', $jobOrder['id'])
               ->update(array(
                   'is_aktif' => "N",
                   'updated_at'=> now(),
                   'updated_by'=> $user, 
                 )
               );
            }

             return redirect()->route('job_order.index')->with('status','Berhasil menghapus data!');

        // }
        // catch (ValidationException $e) {
        //     return redirect()->back()->withErrors($e->errors());
        // }
    }

    public function printJO(JobOrder $JobOrder)
    {
        //
        $dataSupplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('supplier.id', '=', $JobOrder->id_supplier)
            ->first();
        $dataCustomer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', '=', "Y")
            ->where('customer.id', '=', $JobOrder->id_customer)
            ->get();
 
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $JobOrder->id)
            ->first();
        // var_dump(( isset($dataJaminan) ? 'xx':'zzz')); die;
        // $totalThc =  DB::table('job_order_detail_biaya')
        //     ->where('id_jo', $JobOrder->id)
        //     ->where('keterangan', 'LIKE', '%THC%')
        //     ->sum('nominal');
        // $totalLolo =  DB::table('job_order_detail_biaya')
        //     ->where('id_jo', $JobOrder->id)
        //     ->where('keterangan', 'LIKE', '%LOLO%')
        //     ->sum('nominal');
        // $totalApbs =  DB::table('job_order_detail_biaya')
        //     ->where('id_jo', $JobOrder->id)
        //     ->where('keterangan', 'LIKE', '%APBS%')
        //     ->sum('nominal');
        //  $totalCleaning =  DB::table('job_order_detail_biaya')
        //     ->where('id_jo', $JobOrder->id)
        //     ->where('keterangan', 'LIKE', '%CLEANING%')
        //     ->sum('nominal');
        //  $Docfee =  DB::table('job_order_detail_biaya')
        //     ->select('nominal')
        //     ->where('id_jo', $JobOrder->id)
        //     ->where('keterangan', 'LIKE', '%DOC_FEE%')
        //     ->first();
        // $TotalBiaya  = $totalThc+ $totalLolo +$totalApbs+$totalCleaning+$Docfee->nominal;
        // dd($TotalBiaya);

        $TotalBiayaRev = $JobOrder->thc+$JobOrder->lolo+$JobOrder->apbs+$JobOrder->cleaning+$JobOrder->doc_fee;

        // dd($dataJoDetail);   
        $pdf = PDF::loadView('pages.order.job_order.print',[
            'judul'=>"Job Order",
            'JobOrder'=>$JobOrder,
            'dataSupplier'=>$dataSupplier,
            'dataCustomer'=>$dataCustomer,
            'dataJaminan'=>$dataJaminan,
            // 'totalThc'=> $totalThc,
            // 'totalLolo'=> $totalLolo,
            // 'totalApbs'=> $totalApbs,
            // 'totalCleaning'=>$totalCleaning,
            // 'Docfee'=>$Docfee,
            // 'TotalBiaya'=>$TotalBiaya
            'TotalBiayaRev'=>$TotalBiayaRev

        ]); 
        // dd($JobOrder);
        // $pdf->setPaper('A5', 'landscape');
        $pdf->setPaper('A5', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 180, // Set a high DPI for better resolution
            //  'isRemoteEnabled', true
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);
        // langsung download
        // return $pdf->download('fileCoba.pdf'); 
        // preview dulu
        return $pdf->stream($JobOrder->no_jo.'.pdf'); 

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
        $supplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('jenis_supplier_id', 6) // jenis pelayaran
            ->orderBy('nama')
            ->get();
        $customer = DB::table('customer')
            ->select('*')
            ->where('customer.is_aktif', "Y")
            ->orderBy('nama')
            ->get();
        return view('pages.order.job_order.unloading_plan',[
            'judul'=>"Uloading Plan Job Order",
            'supplier'=>$supplier,
            'customer'=>$customer,
            // 'dataJODetail'=>$dataJODetail
        ]);
    }

    public function unloading_data(Request $request){
        try {
            $data = $request->collect();
            $pengirim  = $data['pengirim'];
            $pelayaran   =  $data['pelayaran'];
            $dataJO = DB::table('job_order AS jo')
                    ->select('jo.*','jod.*','jo.status as statusJO','jod.status as statusDetail','c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp')
                    ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('supplier AS s', function($join){
                        $join->on('jo.id_supplier', '=', "s.id")->where('s.is_aktif',"Y");
                    })
                    ->join('job_order_detail AS jod', function($join){
                        $join->on('jo.id', '=', 'jod.id_jo')->where('jod.is_aktif',"Y");
                    })
                    ->leftJoin('grup_tujuan AS gt', 'jod.id_grup_tujuan', '=', 'gt.id')
                    ->where('jo.is_aktif', '=', 'Y')
                    ->where(function ($query) use ($pelayaran) {
                        if(isset($pelayaran)){
                            $query->where('jo.id_supplier', '=', $pelayaran);
                        }
                    })
                    ->where(function ($query) use ($pengirim) {
                        if(isset($pengirim)){
                            $query->where('jo.id_customer', '=', $pengirim);
                        }
                    })
                    ->where('jo.status', 'like', "PROSES DOORING")
                    ->groupBy('jod.id_jo','jod.id')
                    ->get();

            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);

        }
       
        // return view('pages.order.job_order.unloading_plan',[
        //         'judul'=>"Uloading Plan Job Order",
        //         'dataJO' => $dataJO,
        //         // 'dataJODetail'=>$dataJODetail
        //     ]);
    }
}
