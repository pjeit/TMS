<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengaturanKeuangan;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\PemutihanInvoice;
class PemutihanInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //
         $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        // Session::flush();
        // Session::forget(['sewa', 'cust', 'grup']);
        $dataInvoice =  DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->where('i.is_aktif', '=', 'Y')
                ->where('i.total_sisa','>',0)
                ->orderBy('i.id','ASC')
                ->get();
        // dd($dataSewa);
        // dd($dataSewa);
        return view('pages.invoice.pemutihan_invoice.index',[
            'judul'=>"PEMUTIHAN INVOICE",
            'dataInvoice' => $dataInvoice,
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
    public function edit(Invoice $pemutihan_invoice)
    {
        //
        $data_pengaturan = PengaturanKeuangan::where('id', 1)->first();
        $data_customer = Customer::where('is_aktif', 'Y')
        ->where('id', $pemutihan_invoice->billing_to)
        ->first();

        return view('pages.invoice.pemutihan_invoice.form',[
            'judul'=>"PEMUTIHAN INVOICE",
            'pemutihan_invoice' => $pemutihan_invoice,
            'data_pengaturan' => $data_pengaturan,
            'data_customer' => $data_customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $pemutihan_invoice)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $pesanKustom = [
                'tanggal_pemutihan.required' => 'Tanggal pemutihan wajib diisi!',
                'jumlah_pemutihan.required' => 'Jumlah pemutihan wajib diisi!',
                // 'catatan_pemutihan.required' => 'Catatan pemutihan wajib diisi',
            ];
            $request->validate([
                'tanggal_pemutihan' => 'required',
                'jumlah_pemutihan' => 'required',
                // 'catatan_pemutihan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
                $pemutihan = new PemutihanInvoice();
                $pemutihan->id_invoice = $data->id;
                $pemutihan->id_customer = $data['id_customer'];
                $pemutihan->created_by = $user;
                $pemutihan->created_at = now();
                $pemutihan->is_aktif = 'Y';
                $pemutihan->save();
            DB::commit();
            return redirect()->route('controller.method')->with(['status' => 'Success', 'msg'  => 'Pemutihan Invoice berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('controller.method')->with(['status' => 'error', 'msg' => 'Pemutihan Invoice gagal!']);
        }
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
