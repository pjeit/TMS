<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuktiPotongController extends Controller
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

        // Session::flush();
        $data =  DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup', 'ip.no_bukti_potong', 'ip.catatan')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->leftJoin('invoice_pembayaran AS ip', 'i.id_pembayaran', '=', 'ip.id')
                ->where('i.is_aktif', '=', 'Y')
                // ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
                ->orderBy('i.id','ASC')
                ->get();
    
        return view('pages.invoice.bukti_potong.index',[
            'judul' => "Input Bukti Potong",
            'data' => $data,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadData($status) {
        // dd($status);
        $data = null;
        if($status === 'BELUM LUNAS'){
            $data = DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup'/*,'ip.no_bukti_potong'*/, 'i.catatan')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                // ->leftJoin('invoice_pembayaran AS ip', 'i.id', '=', 'ip.id_invoice')
                ->where('i.is_aktif', '=', 'Y')
                ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
                ->orderBy('i.id','ASC')
                ->get();
        }elseif($status === 'LUNAS'){
            $data = DB::table('invoice_pembayaran AS ip')
                ->select('ip.total_diterima','i.no_invoice', 'i.id as id', 'i.total_sisa','i.jatuh_tempo', 'i.tgl_invoice'
                        ,'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup','ip.no_bukti_potong', 'ip.catatan', 'ip.id as id_ip')
                ->leftJoin('invoice AS i', 'i.id_pembayaran', '=', 'ip.id')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->where('i.is_aktif', '=', 'Y')
                ->whereRaw("RIGHT(i.no_invoice, 2) != '/I'") // Add this line to filter based on the last 2 characters
                ->where('ip.no_bukti_potong', NULL)
                ->orderBy('i.id','ASC')
                ->get();
        }
        return $data;
    }
}
