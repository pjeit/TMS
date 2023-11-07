<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceKarantina;
use App\Models\InvoiceKarantinaPembayaran;
use App\Models\KasBank;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class PembayaranInvoiceKarantinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = InvoiceKarantina::where('is_aktif', 'Y')->where('sisa_tagihan', '>', 0)->with('details.kontainers.getJOD')->get();
        // dd($data);

        return view('pages.invoice.pembayaran_invoice_karantina.index',[
            'judul' => "PEMBAYARAN INVOICE",
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bayar(Request $request)
    {
        $collect = $request->collect();
        $data = InvoiceKarantina::whereIn('id', $collect['idInvoice'])->where('is_aktif', 'Y')->get();
        
        $dataCustomers  = Customer::where('grup_id', $data[0]->getCustomer->getGrup->id)
                                ->where('is_aktif', 'Y')->get();
        
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.invoice.pembayaran_invoice_karantina.bayar',[
            'judul' => "Bayar Invoice Karantina",
            'data' => $data,
            'dataCustomers' => $dataCustomers,
            'dataKas' => $dataKas,
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
        $data= $request->collect();
        DB::beginTransaction(); 
        // dd($data);

        try {
            $no_invoices = '';
            $id_invoices = '';
            foreach ($data['detail'] as $key => $value) {
                $invoice = InvoiceKarantina::where('is_aktif', 'Y')->find($key);
                if($invoice){
                    $invoice->tagihan_dibayar += $value['diterima'];
                    $invoice->sisa_tagihan -= $value['diterima'];
                    $invoice->updated_by = $user;
                    $invoice->updated_at = now();
                    if($invoice->save()){
                        $no_invoices .= '#'. $value['no_invoice'] .' ';
                        $id_invoices .= $key.', ';

                        $pembayaran = new InvoiceKarantinaPembayaran();
                        $pembayaran->id_invoice_k = $key;
                        $pembayaran->billing_to = $data['billingTo'];
                        $pembayaran->tgl_pembayaran = date("Y-m-d", strtotime($data['tanggal_pembayaran']));
                        $pembayaran->total_diterima = $value['diterima'];
                        $pembayaran->cara_pembayaran = $data['cara_pembayaran'];
                        $pembayaran->id_kas = $data['kas'];
                        $pembayaran->biaya_admin = $data['biaya_admin'];
                        $pembayaran->no_cek = $data['no_cek'];
                        $pembayaran->no_bukti_potong = $data['no_bukti_potong'];
                        $pembayaran->catatan = $data['catatan'];
                        $pembayaran->created_by = $user;
                        $pembayaran->created_at = now();
                        $pembayaran->save();
                    }
                }

            }
            
            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['kas'], // id kas_bank dr form
                        now(),//tanggal
                        floatval(str_replace(',', '', $data['total_diterima'])), //uang masuk (debit)
                        0,// kredit 0 soalnya kan ini uang masuk
                        1018, //kode coa
                        'BAYAR_INVOICE_KARANTINA',
                        'Pembayaran invoice karantina '. $no_invoices, //keterangan_transaksi
                        substr($id_invoices, 0, -2), // keterangan_kode_transaksi // id invoices
                        $user,//created_by
                        now(),//created_at
                        $user,//updated_by
                        now(),//updated_at
                        'Y'
                    ) 
                );


            DB::commit();
            return redirect()->route('pembayaran_invoice_karantina.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('pembayaran_invoice_karantina.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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
}
