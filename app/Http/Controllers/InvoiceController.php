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
use Illuminate\Support\Facades\Session;
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
        // Session::flush();
        Session::forget(['sewa', 'cust', 'grup']);
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
        Session::forget(['sewa', 'cust', 'grup']);

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
        // dd($cust);
        
        $data = Sewa::whereIn('sewa.id_sewa', $sewa)
                ->where('sewa.status', 'KENDARAAN KEMBALI')
                ->get();

        $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
                ->leftJoin('customer as c', 'c.id', 'id_customer')
                ->where('c.grup_id', $grup[0])
                ->where('sewa.status', 'KENDARAAN KEMBALI')
                ->select('sewa.*')
                ->get();

        $dataCust = Customer::where('grup_id', $grup[0])
                ->where('is_aktif', 'Y')
                ->get();

        return view('pages.invoice.belum_invoice.form',[
            'judul'=>"BELUM INVOICE",
            'data' => $data,
            'dataSewa' => $dataSewa,
            'dataCust' => $dataCust,
            'grup' => $grup[0],
            'customer' => $cust[0],
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
        $user = Auth::user()->id;
        $data = $request->post();
        try {
            // logic nomer booking
                $kode = 'PJE/'.$data['kode_customer'].'/'.date("y").date("m");
                $maxInvoice = DB::table('invoice')
                    ->selectRaw("ifnull(max(substr(no_invoice, -3)), 0) + 1 as max_invoice")
                    ->where('no_invoice', 'like', $kode.'%')
                    ->orderBy('no_invoice', 'DESC')
                    ->value('max_invoice');
                    
                $newInvoiceNumber = 'PJE/' . $data['kode_customer'] . '/' . date("y") . date("m") . str_pad($maxInvoice, 3, '0', STR_PAD_LEFT);

                if (is_null($maxInvoice)) {
                    $newInvoiceNumber = 'PJE/' . $data['kode_customer'] .'/'. date("y") . date("m") . '001';
                }
            //

            $invoice = new Invoice();
            $invoice->id_grup = $data['grup_id'];
            $invoice->no_invoice = $newInvoiceNumber;
            $invoice->tgl_invoice = date_create_from_format('d-M-Y', $data['tanggal_invoice']);
            $invoice->total_tagihan = ($data['total_tagihan'] != '')? floatval(str_replace(',', '', $data['total_tagihan'])):0;
            $invoice->total_sisa = ($data['total_sisa'] != '')? floatval(str_replace(',', '', $data['total_sisa'])):0;
            $invoice->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
            $invoice->catatan = $data['catatan_invoice'];
            $invoice->status = 'MENUNGGU PEMBAYARAN INVOICE';
            $invoice->billing_to = $data['billingTo'];
            $invoice->created_by = $user;
            $invoice->created_at = now();
            $invoice->is_aktif = 'Y';
            $invoice->save();
            // dd($data);

            return redirect()->route('invoice.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
