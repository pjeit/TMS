<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;

class PaymentJobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = DB::table('job_order')
        ->select('job_order.id','job_order.no_jo','customer.nama as namaCustomer','supplier.nama as namaSupplier','job_order.pelabuhan_muat','job_order.pelabuhan_bongkar','job_order.tgl_sandar','job_order.status')
        ->Join('supplier', 'job_order.id_supplier', '=', 'supplier.id')
        ->Join('customer', 'job_order.id_customer', '=', 'customer.id')
        ->where('job_order.is_aktif', '=', 'Y') 
        ->paginate(5);

        // dd($data);
        


        //  $data = JobOrder::where('is_aktif', 'Y')->paginate(5);

        return view('pages.finance.pembayaran_order.index',[
            'judul' => "Pembayaran Job Order",
            'data' => $data,
            'dataJO' => null,
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

        return view('pages.finance.pembayaran_order.create',[
            'judul'=>"Pembayaran Job Order",
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
        //
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
    public function edit(JobOrder $pembayaran_jo)
    {
        //
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
        $dataJoDetail = DB::table('job_order_detail')
            ->select('*')
            ->where('job_order_detail.is_aktif', '=', "Y")
            ->where('job_order_detail.id_jo', '=', $pembayaran_jo->id)
            ->get();
        $dataJaminan = DB::table('jaminan')
            ->select('*')
            ->where('jaminan.is_aktif', '=', "Y")
            ->where('jaminan.id_job_order', '=', $pembayaran_jo->id)
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
            // kalau bug misal data ga ada, ganti variable sama nama routing sama
        // dd($dataJaminan[0]);
        // dd($pembayaran_jo->no_jo);

        return view('pages.finance.pembayaran_order.edit',[
            'judul'=>"Pembayaran Job Order",
            'pembayaran_jo'=>$pembayaran_jo,
            'dataSupplier' => $dataSupplier,
            'dataCustomer' =>$dataCustomer,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,
            'dataJoDetail' =>$dataJoDetail,
            'dataJaminan' =>$dataJaminan,
            'dataKas'=>$dataKas
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobOrder  $jobOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobOrder $pembayaran_jo)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

           try {
            // $pesanKustom = [
             
            //     'pembayaran.required' => 'Pembayaran harus dipilih',
            // ];
            
            // $request->validate([
            //     'pembayaran' => 'required',
            // ], $pesanKustom);
            $data = $request->collect();
            // dd($data);
            // 'FINANCE_PENDING','FINANCE_APPROVED'
            DB::table('job_order')
            ->where('id', $pembayaran_jo['id'])
            ->update(array(
                //    'nama' => strtoupper($data['nama']),
                    'status' => 'LUNAS',
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            );
            return redirect()->route('pembayaran_jo.index')->with('status', "Pembayaran Job Order Dengan Kode $pembayaran_jo->no_jo berhasil");
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
}
