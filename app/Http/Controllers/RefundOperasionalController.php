<?php

namespace App\Http\Controllers;

use App\Models\SewaOperasionalPembayaran;
use Illuminate\Http\Request;
use App\Models\SewaOperasionalPembayaranDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\CoaHelper;
use Exception;
use Carbon\Carbon;

class RefundOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
       
        return view('pages.finance.refund_operasional.index',[
            'judul' => "Refund Operasional",
            'dataKas' => $dataKas,
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
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(SewaOperasionalPembayaran $sewaOperasionalPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(SewaOperasionalPembayaran $refund_biaya_operasional)
    {
        //
        $data = SewaOperasionalPembayaran::where('is_aktif', 'Y')
        ->whereHas('getOperasionalDetail', function ($query){
            $query->where('is_aktif', 'Y');
        })
        ->where('total_refund',0)
        ->where('total_kasbon',0)
        ->where('id',$refund_biaya_operasional->id)
        ->whereNull('total_kembali_stok')
        ->with('getOperasionalDetail')
        ->with('getKas')
        ->with('getOperasionalDetail.getSewaDetail.getCustomer.getGrup')
        ->with('getOperasionalDetail.getSewaDetail.getKaryawan')
        ->with('getOperasionalDetail.getSewaDetail.getSupplier')
        ->first();
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
        // dd($data);
        return view('pages.finance.refund_operasional.refund',[
            'judul' => 'Refund Operasional',
            'data' => $data,
            'dataKas' => $dataKas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SewaOperasionalPembayaran $sewaOperasionalPembayaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SewaOperasionalPembayaran  $sewaOperasionalPembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(SewaOperasionalPembayaran $sewaOperasionalPembayaran)
    {
        //
    }
}
