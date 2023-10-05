<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\InvoicePembayaran;
use App\Models\KasBank;
use App\Models\Sewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Carbon\Carbon;
use App\Helper\UserHelper;


class PembayaranInvoiceController extends Controller
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
        $dataSewa =  DB::table('invoice AS i')
                ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup')
                ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
                ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
                ->where('i.is_aktif', '=', 'Y')
                ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
                ->orderBy('i.id','ASC')
                ->get();
    
        return view('pages.invoice.pembayaran_invoice.index',[
            'judul' => "PEMBAYARAN INVOICE",
            'dataSewa' => $dataSewa,
        ]);
    }

    public function setInvoiceId(Request $request)
    {
        $idInvoice  = session()->get('idInvoice'); //buat ambil session
        $idCust     = session()->get('idCust'); //buat ambil session
        $idGrup     = session()->get('idGrup'); //buat ambil session
        
        $data = $request->collect();
        Session::forget(['idInvoice', 'idCust', 'idGrup']);

        session()->put('idInvoice', $data['idInvoice']);
        session()->put('idCust', $data['idCust']);
        session()->put('idGrup', $data['idGrup']);

        return $idInvoice;
    }

    public function bayar(Request $request)
    {
        $idInvoice  = session()->get('idInvoice'); 
        $idGrup     = session()->get('idGrup'); 
        $idCust     = session()->get('idCust'); 
        $data = Invoice::whereIn('id', $idInvoice)->where('is_aktif', 'Y')->get();
        $dataInvoices = Invoice::where('id_grup', $idGrup)->where('is_aktif', 'Y')->get();
        
        $dataCustomers = Customer::where('grup_id', $idGrup)
                                ->where('is_aktif', 'Y')->get();
        
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        // var_dump($idCust); die;

        return view('pages.invoice.pembayaran_invoice.bayar',[
            'judul' => "Bayar INVOICE",
            'data' => $data,
            'dataInvoices' => $dataInvoices,
            'dataCustomers' => $dataCustomers,
            'idCust' => $idCust,
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
        $data = $request->post();
        $user = Auth::user()->id; 
        // dd($data);
        try {
            if($data['detail'] != null){
                $jenisBAdmin = isset($data['jenis_badmin'])? ' - '.$data['jenis_badmin']:'';
                $keterangan_transaksi = $data['catatan'] . ' | '. $data['cara_pembayaran'] . $jenisBAdmin . ' |';
                $id_invoices = '';

                foreach ($data['detail'] as $key => $value) {
                    if($value['diterima'] != null || $value['diterima'] != 0){
                        $new = new InvoicePembayaran();
                        $new->id_invoice = $key;
                        $new->no_invoice = $value['no_invoice'];
                        $new->billing_to = $data['billingTo'];
                        $new->tgl_pembayaran = date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
                        $new->total_diterima = $value['diterima'];
                        $new->total_pph23 = $value['pph23'];
                        $new->total_bayar = $value['dibayar'];
                        $new->cara_pembayaran = $data['cara_pembayaran'];
                        $new->id_kas = $data['kas'];
                        $new->metode_transfer = isset($data['jenis_badmin'])? $data['jenis_badmin']:null;
                        $new->biaya_admin = $jenisBAdmin;
                        $new->no_cek = isset($data['no_cek'])? $data['no_cek']:null;
                        $new->no_bukti_potong = $value['no_bukti_potong'];
                        $new->catatan = $value['catatan'];
                        $new->created_by = $user;
                        $new->created_at = now();
                        if($new->save()){
                            $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($key);
                            $keterangan_transaksi .= ' #'.$invoice->no_invoice;
                            $id_invoices .= $invoice->id . ',';
                            if($invoice){
                                $invoice->total_dibayar += $new->total_bayar;
                                $invoice->total_sisa = $invoice->total_tagihan - $invoice->total_dibayar;
                                if($invoice->total_sisa == 0){
                                    $invoice->status = 'SELESAI PEMBAYARAN INVOICE';
                                }
                                $invoice->updated_by = $user;
                                $invoice->updated_at = now();
                                $invoice->save();
    
                                // jika status == SELESAI PEMBAYARAN INVOICE, otomatis TRIGGER "update_status_invoice_detail" di tabel invoice di DB
                            }
                        }
                    }
                }

                // dump data ke dump transaction
                $total_bayar = (float)str_replace(',', '', $data['total_dibayar'])+ (isset($data['biaya_admin'])? (float)str_replace(',', '', $data['biaya_admin']):0) ;
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['kas'],// id kas_bank dr form
                        now(),//tanggal
                        0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                        $total_bayar, //uang keluar (kredit)
                        1018, //kode coa
                        'bayar_invoice',
                        $keterangan_transaksi, //keterangan_transaksi
                        substr($id_invoices, 0, -1),//keterangan_kode_transaksi
                        $user,//created_by
                        now(),//created_at
                        $user,//updated_by
                        now(),//updated_at
                        'Y'
                    ) 
                );

                $cust = Customer::where('is_aktif', 'Y')->findOrFail($data['billingTo']);
                if($cust){
                    $kredit_sekarang = $cust->kredit_sekarang - $value['dibayar'];
                    if($kredit_sekarang < 0){
                        $kredit_sekarang = 0;
                    }
                    $cust->kredit_sekarang = $kredit_sekarang;
                    $cust->updated_by = $user;
                    $cust->updated_at = now();
                    $cust->save();
                }

            }

            return redirect()->route('pembayaran_invoice.index')->with('status', "Success!");
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
