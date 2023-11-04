<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceKarantina;
use App\Models\InvoiceKarantinaDetail;
use App\Models\InvoiceKarantinaDetailKontainer;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceKarantinaController extends Controller
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

        $customer = JobOrder::where('is_aktif', 'Y')->with('getCustomer')->groupBy('id_customer')->get();

        // dd($customer[0]->getCustomer);
        return view('pages.invoice.invoice_karantina.index',[
            'judul' => "Invoice Karantina",
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      
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
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);

        try {
            $kode = 'PJE_KRNT/'.$data['kode'].'/'.date("y").date("m");
            $maxInvoice = DB::table('invoice')
                ->selectRaw("ifnull(max(substr(no_invoice, -3)), 0) + 1 as max_invoice")
                ->where('no_invoice', 'like', $kode.'%')
                ->orderBy('no_invoice', 'DESC')
                ->value('max_invoice');
                
            $newInvoiceNumber = 'PJE_KRNT/' . $data['kode'] . '/' . date("y") . date("m") . str_pad($maxInvoice, 3, '0', STR_PAD_LEFT);

            if (is_null($maxInvoice)) {
                $newInvoiceNumber = 'PJE_KRNT/' . $data['kode'] .'/'. date("y") . date("m") . '001';
            }

            $invoice = new InvoiceKarantina();
            $invoice->id_customer = $data['customer'];
            $invoice->no_invoice_k = $newInvoiceNumber;
            $invoice->tgl_invoice = date("Y-m-d", strtotime($data['tanggal_invoice']));
            $invoice->total_tagihan = $data['total_nominal'];
            $invoice->sisa_tagihan = $data['total_nominal'];
            $invoice->status = "MENUNGGU PEMBAYARAN";
            $invoice->created_by = $user;
            $invoice->created_at = now();
            if($invoice->save()){
                foreach ($data['data'] as $key => $value) {
                    $detail = new InvoiceKarantinaDetail();
                    $detail->id_invoice_k       = $invoice->id; 
                    $detail->id_jo              = $key; 
                    $detail->tarif_karantina    = floatval(str_replace(',', '', $value['nominal']));
                    $detail->tarif_karantina    = $key; 
                    $detail->created_by         = $user;
                    $detail->created_at         = now();
                    if($detail->save()){
                        foreach ($value['idJOD'] as $item) {
                            $kontainer = new InvoiceKarantinaDetailKontainer();
                            $kontainer->id_invoice_k = $invoice->id; 
                            $kontainer->id_invoice_k_detail = $detail->id; 
                            $kontainer->id_jo_detail = $item; 
                            $kontainer->created_by = $user;
                            $kontainer->created_at = now();
                            $kontainer->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('invoice_karantina.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('invoice_karantina.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
        }
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
    public function load_data($id)
    {
        $data = JobOrder::where([
                    'is_aktif' => 'Y',
                    'id_customer' => $id,
                ])->with('getDetails.getTujuan')->get();

        return $data;
    }
}
