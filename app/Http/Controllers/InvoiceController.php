<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $dataSewa =  DB::table('sewa AS s')
                ->select('s.*','s.id_sewa as idSewanya','c.id AS id_cust','c.nama AS nama_cust','g.nama_grup','g.id as id_grup','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup AS g', 'c.grup_id', '=', 'g.id')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')

                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->where('s.is_aktif', '=', 'Y')
                // ->where('s.jenis_tujuan', 'like', '%FTL%')
                ->where('s.status', 'like', "%KENDARAAN KEMBALI%")
                ->whereNull('s.id_supplier')
                // ->whereNull('s.tanggal_kembali')
                ->orderBy('c.id','ASC')
                ->get();
        // dd($dataSewa);
    
        return view('pages.invoice.belum_invoice.index',[
            'judul'=>"BELUM INVOICE",
            'dataSewa' => $dataSewa,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function setSewaID(Request $request)
    {
        $sewa = session()->get('sewa'); //buat ambil session
        $cust = session()->get('cust'); //buat ambil session
        $grup = session()->get('grup'); //buat ambil session

        $data= $request->collect();
        session()->put('sewa', $data['idSewa']);
        session()->put('cust', $data['idCust']);
        session()->put('grup', $data['idGrup']);
        return $sewa;

        
    }
    public function create(Request $request)
    {
        $sewa = session()->get('sewa'); //buat ambil session
        $cust = session()->get('cust'); //buat ambil session
        $grup = session()->get('grup'); //buat ambil session
        dd($cust);
        
        $data = Sewa::whereIn('sewa.id_sewa', $sewa)
                ->where('sewa.status', 'KENDARAAN KEMBALI')
                ->get();

        $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
                ->leftJoin('customer as c', 'c.id', 'id_customer')
                ->where('c.grup_id', $grup[0])
                ->where('sewa.status', 'KENDARAAN KEMBALI')
                ->select('sewa.*')
                ->get();
        // dd($dataSewa);

        $dataCust = Customer::where('grup_id', $grup[0])
                ->where('is_aktif', 'Y')
                ->get();

        return view('pages.invoice.belum_invoice.form',[
            'judul'=>"BELUM INVOICE",
            'data' => $data,
            'dataSewa' => $dataSewa,
            'dataCust' => $dataCust,
            'grup' => $grup[0],
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
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
