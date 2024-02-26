<?php

namespace App\Http\Controllers;

use App\Models\UangJalanRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // use PDF;

class CetakUangJalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_BUKTI_POTONG', ['only' => ['index']]);
        $this->middleware('permission:EDIT_BUKTI_POTONG', ['only' => ['index']]);
    }
    
    public function index()
    {
        //
        $data_uang_jalan = DB::table('uang_jalan_riwayat as uj')
            ->select('uj.*','uj.id as idUj','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','s.no_polisi','s.no_sewa')
            ->leftJoin('sewa as s', 'uj.sewa_id', '=', 's.id_sewa')
            ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
            ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
            ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
            ->where('uj.is_aktif', '=', "Y")
            ->whereNull('s.id_supplier')
            ->where('s.status','PROSES DOORING')
            ->get();
        // dd($data_uang_jalan);
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        return view('pages.finance.cetak_uang_jalan.index',[
                'judul'=>"cetak uang jalan",
            'data_uang_jalan' => $data_uang_jalan,
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
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function show(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function edit(UangJalanRiwayat $cetak_uang_jalan)
    {
        //
         $data_uang_jalan = DB::table('uang_jalan_riwayat as uj')
            ->select('uj.*','uj.id as idUj','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','s.no_polisi','s.no_sewa')
            ->leftJoin('sewa as s', 'uj.sewa_id', '=', 's.id_sewa')
            ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
            ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
            ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
            ->where('uj.is_aktif', '=', "Y")
            ->where('uj.id', '=', $cetak_uang_jalan->id)
            ->whereNull('s.id_supplier')
            ->first();
        $data_sewa_biaya=DB::table('sewa_biaya as sb')
            ->select('sb.*')
            ->where('sb.is_aktif', '=', "Y")
            ->where('sb.id_sewa', '=',  $data_uang_jalan->sewa_id)
            ->get();
        // dd($data_sewa_biaya);

         $pdf = Pdf::loadView('pages.finance.cetak_uang_jalan.cetak',[
            'judul' => "cetak uang jalan",
            'data_uang_jalan' => $data_uang_jalan,
            'data_sewa_biaya' => $data_sewa_biaya,

        ]);
        
        $pdf->setPaper('A4', 'portrait');
 
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 200, // Set a high DPI for better resolution
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);

        return $pdf->stream($data_uang_jalan->no_sewa.'.pdf'); 
        // return view('pages.finance.cetak_uang_jalan.cetak',[
        //         'judul'=>"cetak uang jalan",
        //     'data_uang_jalan' => $data_uang_jalan,
        //     'data_sewa_biaya' => $data_sewa_biaya,

        // ]);
         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UangJalanRiwayat  $uangJalanRiwayat
     * @return \Illuminate\Http\Response
     */
    public function destroy(UangJalanRiwayat $uangJalanRiwayat)
    {
        //
    }
}
