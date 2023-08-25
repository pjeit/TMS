<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\JobOrderDetail;
use Illuminate\Support\Facades\Auth;

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
            $pesanKustom = [
                'no_jo.required' => 'Tanggal JO Harus diisi!',
                'tgl_plan_book.required' => 'Tanggal Plan Book Harus diisi!',
            ];
            
            $request->validate([
                'no_jo' => 'required',
                'tgl_plan_book' => 'required',
            ], $pesanKustom);

            $user = Auth::user()->id;
            $data = $request->post();

            $newJO = new JobOrder();
            $newJO->no_jo = $data['no_jo'];
            $newJO->tgl_plan_book = $data['tgl_plan_book'];
            $newJO->id_customer = $data['id_customer'];
            $newJO->id_supplier = $data['id_supplier'];
            $newJO->id_booking = $data['id_booking'];
            $newJO->pelabuhan_muat = $data['pelabuhan_muat'];
            $newJO->pelabuhan_bongkar = $data['pelabuhan_bongkar'];
            $newJO->no_bl = $data['no_bl'];
            $newJO->tgl_sandar = $data['tgl_sandar'];
            $newJO->free_time = $data['free_time'];
            $newJO->jo_expired = $data['jo_expired'];
            $newJO->total_thc = $data['total_thc'];
            $newJO->total_lolo = $data['total_lolo'];
            $newJO->total_apbs = $data['total_apbs'];
            $newJO->total_cleaning = $data['total_cleaning'];
            $newJO->total_docfee = $data['total_docfee'];
            $newJO->total_biaya_sebelum_dooring = $data['total_biaya_sebelum_dooring'];
            $newJO->total_storage = $data['total_storage'];
            $newJO->total_demurage = $data['total_demurage'];
            $newJO->total_detention = $data['total_detention'];
            $newJO->total_repair_washing = $data['total_repair_washing'];
            $newJO->total_biaya_setelah_dooring = $data['total_biaya_setelah_dooring'];
            $newJO->status = $data['status'];
            $newJO->createad_by = $user;
            $newJO->createad_at = now();
            
            if($newJO->save()){
                if(isset($data['detail'])){
                    foreach ($data['detail'] as $key => $detail) {
                        $JOD = new JobOrderDetail();
                        $JOD->id_jo = $newJO->id; // get id jo
                        $JOD->no_kontainer = $detail->no_kontainer;
                        $JOD->seal = $detail->seal;
                        $JOD->seal_pje = $detail->seal_pje;
                        $JOD->seal_pje = $detail->seal_pje;
                        $JOD->id_kendaraan = $detail->id_kendaraan;
                        $JOD->nopol_kendaraan = $detail->nopol_kendaraan;
                        $JOD->id_grup_tujuan = $detail->id_grup_tujuan;
                        $JOD->tgl_dooring = $detail->tgl_dooring;
                        $JOD->storage = $detail->storage;
                        $JOD->demurage = $detail->demurage;
                        $JOD->detention = $detail->detention;
                        $JOD->repair_washing = $detail->repair_washing;
                        $JOD->thc = $detail->thc;
                        $JOD->lolo = $detail->lolo;
                        $JOD->apbs = $detail->apbs;
                        $JOD->cleaning = $detail->cleaning;
                        $JOD->docfee = $detail->docfee;
                        $JOD->tipe_kontainer = $detail->tipe_kontainer;
                        $JOD->status = $detail->status;
                        $JOD->do_expaired = $detail->do_expaired;
                        $JOD->created_by = $user;
                        $JOD->created_at = now();
                        $JOD->is_aktif = 'Y';
                        $JOD->save();
                    }
                }

            }
        

            return redirect()->route('customer.index')->with('status','Success!!');
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
        //
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
}
