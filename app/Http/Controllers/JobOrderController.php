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
use Barryvdh\DomPDF\Facade\Pdf; // use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;
use App\Helper\UserHelper;
use App\Models\Customer;
use App\Models\M_Kota;
use App\Models\PengaturanKeuangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
// use Barryvdh\DomPDF\PDF;
use App\Models\JobOrderBiaya;

class JobOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_JO', ['only' => ['index']]);
		$this->middleware('permission:CREATE_JO', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_JO', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_JO', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        
        $id_role = Auth::user()->role_id; 
        // $cabang = UserHelper::getCabang();

        $dataJO = DB::table('job_order as jo')
            ->leftJoin('user as u', 'u.id', '=', 'jo.created_by')
            ->leftJoin('karyawan as k', 'k.id', '=', 'u.karyawan_id')
            // ->where(function ($query) use ($id_role, $cabang) {
            //     if(!in_array($id_role, [1,3])){
            //         $query->where('k.cabang_id', $cabang); // selain id [1,3] atau role [superadmin, admin nasional] lock per kota
            //     }
            // })
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
        $dataSupplier = Supplier::where('supplier.is_aktif', '=', "Y")
                                ->where('jenis_supplier_id', 7) // jenis pelayaran
                                ->orderBy('nama')
                                ->get();
        $dataCustomer = Customer::where('customer.is_aktif', "Y")
                                ->orderBy('kode')
                                ->orderBy('nama')
                                ->get();
        $dataPengaturanKeuangan = PengaturanKeuangan::where('pengaturan_keuangan.is_aktif', '=', "Y")->get();

        $kota = M_Kota::get();

        return view('pages.order.job_order.create',[
            'judul'=>"Job Order",
            'dataSupplier' => $dataSupplier,
            'dataCustomer' =>$dataCustomer,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,
            'kota' => $kota,
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
            DB::beginTransaction();

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
            if($data['no_va'] != null){
                $newJO->no_va = $data['no_va'];
                $newJO->va_nama = $data['va_nama'];
                $newJO->va_bank = $data['va_bank'];
            }
            $newJO->created_by = $user;
            $newJO->created_at = now();
            $newJO->is_aktif = 'Y';
            // dd($data['data_lain']);
            $cek_biaya_lain = false;
            if(isset($data['data_lain']))
            {
                foreach ($data['data_lain'] as $value) {
                    if ($value != null) {
                        $cek_biaya_lain = true;
                    }
                }
                
            }
            // dd($cek_biaya_lain);
            
            if( $newJO->thc == 0 && $newJO->lolo == 0 && $newJO->apbs == 0 && 
                    $newJO->cleaning == 0 && $newJO->doc_fee == 0 && !$cek_biaya_lain &&
                    ($data['tgl_bayar_jaminan'] == null || $data['total_jaminan'] == null) ){

                $newJO->status = 'PROSES DOORING'; // MENUNGGU PEMBAYARAN, PROSES DOORING
            }else{
                $newJO->status = 'MENUNGGU PEMBAYARAN'; // MENUNGGU PEMBAYARAN, PROSES DOORING
            }
            // dd($data);
            if($newJO->save()){
                if ($cek_biaya_lain) {
                    foreach ($data['data_lain']as $value) {
                        if ($value != null) {
                            $jo_biaya = new JobOrderBiaya();
                            $jo_biaya->id_jo = $newJO->id; // get id jo
                            $jo_biaya->deskripsi = $value['deskripsi'];
                            $jo_biaya->biaya = isset($value['biaya'])? floatval(str_replace(',', '', $value['biaya'])):0;
                            $jo_biaya->created_by = $user;
                            $jo_biaya->created_at = now();
                            $jo_biaya->is_aktif = 'Y';
                            $jo_biaya->save();
                        }
                    }
                }
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
                    $jaminan->catatan_jaminan = $data['catatan'];
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

            if( $newJO->thc != 0 || $newJO->lolo != 0 || $newJO->apbs != 0 || 
                    $newJO->cleaning != 0 || $newJO->doc_fee != 0 || 
                    $data['tgl_bayar_jaminan'] != null || $data['total_jaminan'] != null ){

                DB::commit();
                return redirect()->route('job_order.index')
                    ->with('id_print_jo', $id_print_jo)
                    ->with(['status' => 'Success', 'msg' => 'Data berhasil tersimpan']);
            }else{
                DB::commit();
                return redirect()->route('job_order.index')
                ->with(['status' => 'Success', 'msg' => 'Data berhasil tersimpan']);
            }
            

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->with(['status' => 'Error', 'msg' => 'Terjadi kesalahan saat menyimpan']);
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
            ->first();
        // dd($dataPengaturanKeuangan);
        $detail = DB::table('job_order_detail as jod')
            ->select('jod.*', 'b.id as id_booking', 'b.tgl_booking as tgl_booking','s.id_sewa as sewa_id')
            ->leftJoin('booking as b', function($leftJoin){
                $leftJoin->on('b.id_jo_detail', '=', "jod.id")->where('b.is_aktif',"Y");
            })
            // ->leftJoin('sewa as s', function($leftJoin){
            //     $leftJoin->on('s.id_jo_detail', '=', "jod.id")->where('s.is_aktif',"Y");
            // })
            ->leftJoin('sewa AS s', function($join) {
                $join->on('jod.id', '=', 's.id_jo_detail')
                ->where('s.is_aktif', '=', 'Y')
                //where ini buat ambil id sewa yang terakir, kan bisa aja sewanya cancel, trs input lagi
                ->where('s.id_sewa', '=', function ($query) {
                $query->select(DB::raw('MAX(id_sewa)'))
                        ->from('sewa as s')
                        ->where('s.is_aktif', '=', 'Y')
                        ->whereColumn('s.id_jo_detail', 'jod.id');
                });
            })
            ->where('jod.id_jo', $jobOrder->id)
            ->where('jod.is_aktif', "Y")
            ->get();
        // dd(count($detail));
        // $detail = JobOrderDetail::where('id_jo', $jobOrder->id)->where('is_aktif', 'Y')->get();
        $jaminan = Jaminan::where('id_job_order', $jobOrder->id)->where('is_aktif', 'Y')->first();
        $data['detail'] = json_encode($detail);
        $data['jaminan'] = $jaminan;
        $data['count_detail']=count($detail);
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
            $user = Auth::user()->id;
            $data = $request->post();
            $currentYear = Carbon::now()->format('y');
            $currentMonth = Carbon::now()->format('m');
            // dd($data);
            
                // if(isset($data['tgl_sandar'])){
                    // }
                $tgl_sandar = isset($data['tgl_sandar'])?date_create_from_format('d-M-Y', $data['tgl_sandar']):null;
                $jobOrder->tgl_sandar = isset($tgl_sandar)? date_format($tgl_sandar, 'Y-m-d H:i:s'):$jobOrder->tgl_sandar;
                // $jobOrder->tgl_sandar = date_create_from_format('d-M-Y', $data['tgl_sandar']);
                $jobOrder->no_bl = $data['no_bl'];
                $jobOrder->kapal = $data['kapal'];
                $jobOrder->voyage = $data['voyage'];
                if($jobOrder->status == "MENUNGGU PEMBAYARAN")
                {
                    $jobOrder->thc =  isset($data['thc_cekbox'])? floatval(str_replace(',', '', $data['total_thc'])):0;
                    $jobOrder->lolo = isset($data['lolo_cekbox'])? floatval(str_replace(',', '', $data['total_lolo'])):0;
                    $jobOrder->apbs = isset($data['apbs_cekbox'])? floatval(str_replace(',', '', $data['total_apbs'])):0;
                    $jobOrder->cleaning = isset($data['cleaning_cekbox'])? floatval(str_replace(',', '', $data['total_cleaning'])):0;
                    $jobOrder->doc_fee = isset($data['doc_fee_cekbox'])? floatval(str_replace(',', '', $data['total_doc_fee'])):0;
                }
                $jobOrder->updated_by = $user;
                $jobOrder->updated_at = now();
                $jobOrder->save();

            if(isset($data['detail'])){
                foreach ($data['detail'] as $key => $detail) {
                    $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($detail['id_detail']);
                    if(isset($detail['no_kontainer'])){
                        $JOD->no_kontainer = $detail['no_kontainer'];
                    }
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
            if($jobOrder->status == "MENUNGGU PEMBAYARAN")
            {
                if(isset($data['detail_baru'])){
                        foreach ($data['detail_baru'] as $key => $detail) {
                            $JOD = new JobOrderDetail();
                            $JOD->id_jo = $jobOrder->id; // get id jo
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
            }

            
            if(isset($data['id_jaminan']) && isset($data['total_jaminan'])){
                if($data['id_jaminan'] != null){
                    $jaminan = Jaminan::where('is_aktif', 'Y')->find($data['id_jaminan']);
                    $jaminan->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar_jaminan']);
                    $jaminan->nominal = floatval(str_replace(',', '', $data['total_jaminan']));
                    $jaminan->catatan_jaminan = $data['catatan'];
                    $jaminan->updated_by = $user;
                    $jaminan->updated_at = now();
                    $jaminan->save();
                }else{
                    $jaminan = new Jaminan();
                    $jaminan->id_job_order = $jobOrder['id'];
                    $jaminan->nominal = floatval(str_replace(',', '', $data['total_jaminan']));
                    $jaminan->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar_jaminan']);
                    $jaminan->catatan_jaminan = $data['catatan'];
                    $jaminan->status = 'MENUNGGU PEMBAYARAN';
                    $jaminan->created_by = $user;
                    $jaminan->created_at = now();
                    $jaminan->save();
                }
            }

            return redirect()->route('job_order.index')->with(['status' => 'Success', 'msg' => 'Berhasil update data.']);
        } catch (ValidationException $e) {
            return redirect()->route('job_order.index')->with(['status' => 'Error', 'msg' => 'Terjadi kesalahan.']);
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
        DB::beginTransaction();

        try{
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
            if($jaminan){
                DB::table('jaminan')
                ->where('id_job_order', $jobOrder['id'])
                ->update(array(
                    'is_aktif' => "N",
                    'updated_at'=> now(),
                    'updated_by'=> $user, 
                    )
                );
            }

            DB::commit();
            return redirect()->route('job_order.index')->with(['status' => 'Success', 'message' => 'Data berhasil dihapus.']);
        }
        catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->with(['status' => 'Error', 'message' => 'Terjadi kesalahan saat menghapus.']);
        }
    }

    public function printJO(JobOrder $JobOrder)
    {
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

        $TotalBiayaRev = $JobOrder->thc+$JobOrder->lolo+$JobOrder->apbs+$JobOrder->cleaning+$JobOrder->doc_fee;

        $pdf = Pdf::loadView('pages.order.job_order.print',[
            'judul'=>"Job Order",
            'JobOrder'=>$JobOrder,
            'dataSupplier'=>$dataSupplier,
            'dataCustomer'=>$dataCustomer,
            'dataJaminan'=>$dataJaminan,
            'TotalBiayaRev'=>$TotalBiayaRev

        ]); 
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
    }

    public function cetak_job_order(JobOrder $JobOrder)
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
            ->first();
 
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $JobOrder->id)
            ->first();
        $data_kontainer = DB::table('job_order_detail as jod')
            ->select('jod.no_kontainer',
            'jod.seal',
            'gt.nama_tujuan',
            DB::raw('MAX(s.no_polisi) as no_polisi'),
            DB::raw('MAX(s.tanggal_berangkat) as tanggal_berangkat'),
            DB::raw('SUM(jodb.storage) as storage'),
            DB::raw('SUM(jodb.demurage) as demurage'),
            DB::raw('SUM(jodb.detention) as detention'),
            DB::raw('SUM(jodb.repair) as repair'),
            DB::raw('SUM(jodb.washing) as washing'),
            )
            //join grup tujuan buat ngambil nama tujuan
            ->leftJoin('grup_tujuan AS gt', function($join) {
                $join->on('jod.id_grup_tujuan', '=', 'gt.id')->where('gt.is_aktif', '=', 'Y');
            })
            //join sewa buat ngambil nopol sama tgl berangkat
            ->leftJoin('sewa AS s', function($join) {
                $join->on('jod.id', '=', 's.id_jo_detail')
                ->where('s.is_aktif', '=', 'Y')
                //where ini buat ambil id sewa yang terakir, kan bisa aja sewanya cancel, trs input lagi
                ->where('s.id_sewa', '=', function ($query) {
                $query->select(DB::raw('MAX(id_sewa)'))
                        ->from('sewa as s')
                        ->where('s.is_aktif', '=', 'Y')
                        ->whereColumn('s.id_jo_detail', 'jod.id');
                });
            })
            ->leftJoin('job_order_detail_biaya AS jodb', function($join) {
                $join->on('jod.id', '=', 'jodb.id_jo_detail')
                ->where('jodb.status_bayar', 'SELESAI PEMBAYARAN')
                ->where('jodb.is_aktif', '=', 'Y');
            })
            ->where('jod.is_aktif', '=', "Y")
            ->where('jod.id_jo', '=', $JobOrder->id)
            ->groupBy('jod.no_kontainer', 
            'jod.seal', 
            'gt.nama_tujuan', 
            's.no_polisi', 
            's.tanggal_berangkat',
            // 'jodb.storage',
            // 'jodb.demurage',
            // 'jodb.detention',
            // 'jodb.repair',
            // 'jodb.washing',
            )
            ->get();
        // dd($data_kontainer);
        $TotalBiayaRev = $JobOrder->thc+$JobOrder->lolo+$JobOrder->apbs+$JobOrder->cleaning+$JobOrder->doc_fee;

        // dd($dataJoDetail);   
        $pdf = Pdf::loadView('pages.order.job_order.cetak_job_order',[
            'judul'=>"Job Order",
            'JobOrder'=>$JobOrder,
            'dataSupplier'=>$dataSupplier,
            'dataCustomer'=>$dataCustomer,
            'dataJaminan'=>$dataJaminan,
            'TotalBiayaRev'=>$TotalBiayaRev,
            'data_kontainer'=>$data_kontainer
        ]); 
        // dd($JobOrder);
        // $pdf->setPaper('A5', 'landscape');
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 200, // Set a high DPI for better resolution
            //  'isRemoteEnabled', true
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);
        return $pdf->stream($JobOrder->no_jo.'.pdf'); 
        // return view('pages.order.job_order.cetak_job_order',[
        //     'judul'=>"Job Order",
        //     'JobOrder'=>$JobOrder,
        //     'dataSupplier'=>$dataSupplier,
        //     'dataCustomer'=>$dataCustomer,
        //     'dataJaminan'=>$dataJaminan,
        //     'TotalBiayaRev'=>$TotalBiayaRev

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

    public function cetak_si($id){
        $data = JobOrder::with('getSupplier', 'getDetails.getTujuan', 'getCustomer')->where('is_aktif', 'Y')->find($id);
        $user = Auth::user()->username;

        // dd($data);
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection();
        
        // Define styles
        $multiTabStyle = 'multipleTab';
        $phpWord->addParagraphStyle(
            $multiTabStyle,
            [
                'tabs' => [
                    new \PhpOffice\PhpWord\Style\Tab('left', 3200),
                    new \PhpOffice\PhpWord\Style\Tab('left', 3200),
                ],
            ]
        );

        $leftTabStyle = 'leftTab';
        $phpWord->addParagraphStyle($leftTabStyle, ['tabs' => [new \PhpOffice\PhpWord\Style\Tab('left', 5000)]]);
        
        $rightTabStyle = 'rightTab';
        $phpWord->addParagraphStyle($rightTabStyle, ['tabs' => [new \PhpOffice\PhpWord\Style\Tab('right', 9090)]]);

        $styleCenter = 'pStyle';
        $phpWord->addParagraphStyle($styleCenter, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 100]);

        $section->addText(
            'SHIPPING INSTRUCTION',
            array('name' => 'Cambria', 'size' => 10, 'bold' => true, 'underline' => 'single'), $styleCenter
        );

        $section->addText(
            '',
            array('name' => 'Cambria', 'size' => 10)
        );

        $section->addText(
            'Kepada',
            array('name' => 'Cambria', 'size' => 10)
        );

        $kepada = strtoupper($data->getSupplier->nama);
        $section->addText(
            $kepada. ' <w:br/><w:br/>',
            array('name' => 'Cambria', 'size' => 10)
        );

        $section->addText(
            'Dengan ini Kami sampaikan Shipping Instruction ( Instruksi Pengapalan ) sebagai berikut :',
            array('name' => 'Cambria', 'size' => 10)
        );

        // Add listitem elements
        $pengirim = strtoupper($data->getCustomer->nama);
        $containers = $data->getDetails;
        $countContainers = count($containers);
        $containersType = $containers[0]['tipe_kontainer'];
        // dd($containers);
        
        $section->addText("Shipper \t : \t $pengirim", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);
        $section->addText("Consignee \t : \t $pengirim", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);
        $section->addText("Notify Party \t : \t  AS CONSIGNEE", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);
        $section->addText("Quantity \t : \t $countContainers  X $containersType  CONTAINER", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);
        $section->addText("Goods \t : \t -", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);
        $section->addText("Vessel Name \t : \t $data->kapal $data->voyage", array('name' => 'Cambria', 'size' => 10), $multiTabStyle);

        $fancyTableStyleName = 'Table';
        $fancyTableStyle = ['borderSize' => 4, 'borderColor' => '006699', 'cellMargin' => 20, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellSpacing' => 25];
        $fancyTableFirstRowStyle = ['borderBottomSize' => 5, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF'];
        $fancyTableCellStyle = ['valign' => 'center',];
        $fancyTableFontStyle = ['bold' => true];
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);
        $table->addRow(400);
        $table->addCell(2000, $fancyTableCellStyle)->addText('NO. CONTAINER', array('name' => 'Cambria', 'size' => 10) , $styleCenter, $fancyTableFontStyle);
        $table->addCell(2000, $fancyTableCellStyle)->addText('SEAL', array('name' => 'Cambria', 'size' => 10) , $styleCenter, $fancyTableFontStyle);
        $table->addCell(2000, $fancyTableCellStyle)->addText('CARGO', array('name' => 'Cambria', 'size' => 10) , $styleCenter, $fancyTableFontStyle);
        // for ($i = 1; $i <= $countContainers; ++$i) {
        //     $table->addRow();
        //     $table->addCell(2000)->addText("SPNU 310724-5", array('name' => 'Cambria', 'size' => 10), $styleCenter);
        //     $table->addCell(2000)->addText("E22.514192", array('name' => 'Cambria', 'size' => 10), $styleCenter);
        //     $table->addCell(2000)->addText("MAKANAN", array('name' => 'Cambria', 'size' => 10), $styleCenter);
        // }
        foreach ($containers as $key => $value) {
            $kargo = $value->getTujuan->kargo;
            $table->addRow();
            $table->addCell(2000)->addText("$value->no_kontainer", array('name' => 'Cambria', 'size' => 10), $styleCenter);
            $table->addCell(2000)->addText("$value->seal", array('name' => 'Cambria', 'size' => 10), $styleCenter);
            $table->addCell(2000)->addText("$kargo", array('name' => 'Cambria', 'size' => 10), $styleCenter);        }

        $section->addText(
            '<w:br/><w:br/>',
            array('name' => 'Cambria', 'size' => 10)
        );

        $monthNames = [ 1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"];

        $tanggal = date('d', strtotime(now()));
        $bulan = $monthNames[date('m', strtotime(now()))];
        $tahun = date('Y', strtotime(now()));
        $section->addText(
            'Surabaya,  '.$tanggal.' '.$bulan.' '.$tahun,
            array('name' => 'Cambria', 'size' => 10)
        );

        $section->addText(
            '<w:br/><w:br/> Lastri',
            array('name' => 'Cambria', 'size' => 10)
        );

        $filename = $data->no_bl;
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filename);

        header("Content-Disposition: attachment; filename=$filename.docx");
        readfile($filename); // or echo file_get_contents($filename);
        unlink($filename);  // remove temp file

    }
}
