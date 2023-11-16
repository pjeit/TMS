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
use App\Models\SewaOperasional;
use Symfony\Component\VarDumper\VarDumper;

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

        // $data =  DB::table('invoice AS i')
        //         ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
        //                 ,'g.id as id_grup','ip.no_bukti_potong', 'ip.catatan')
        //         ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
        //         ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
        //         ->leftJoin('invoice_pembayaran AS ip', 'i.id', '=', 'ip.id_invoice')
        //         ->where('i.is_aktif', '=', 'Y')
        //         // ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
        //         ->orderBy('i.id','ASC')
        //         ->get();

        $data = Invoice::where('is_aktif', 'Y')->get();
    
        return view('pages.invoice.pembayaran_invoice.index',[
            'judul' => "PEMBAYARAN INVOICE",
            'data' => $data,
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
        $idInvoice      = session()->get('idInvoice'); 
        $idGrup         = session()->get('idGrup'); 
        $idCust         = session()->get('idCust'); 
        $data           = Invoice::whereIn('id', $idInvoice)->where('is_aktif', 'Y')->get();
        $dataInvoices   = Invoice::where('id_grup', $idGrup)->where('is_aktif', 'Y')->get();
        
        $dataCustomers  = Customer::where('grup_id', $idGrup)
                                ->where('is_aktif', 'Y')->get();
        
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

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
    public function storeV2NotUsed(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id; 
        DB::beginTransaction(); 
        $isErr = false;
        dd($data);
        // simpan data per invoice

        try {
            if($data['detail'] != null){
                $keterangan_transaksi = 'PEMBAYARAN INVOICE | '. $data['cara_pembayaran'] . ' | ' . $data['catatan'] . ' |';
                $id_invoices = '';
                $i = 0;
                foreach ($data['detail'] as $key => $value) {
                    if($value['diterima'] != null || $value['diterima'] != 0){
                        $new = new InvoicePembayaran();
                        $new->id_invoice = $key;
                        $new->no_invoice = $value['no_invoice'];
                        $new->billing_to = $data['billingTo'];
                        $new->tgl_pembayaran = date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
                        
                        $diterima = $value['diterima'];
                        if($i == 0 && substr($value['no_invoice'], -2) != '/I'){
                            // kalau index pertama, dikurangi biaya admin
                            $diterima = $value['diterima']-(float)str_replace(',', '', $data['biaya_admin']);
                            $new->biaya_admin = floatval(str_replace(',', '', $data['biaya_admin']));
                            $new->no_cek = isset($data['no_cek'])? $data['no_cek']:null;
                        }
                        $new->total_diterima = $diterima;
                        $new->total_pph23 = $value['pph23'];
                        $new->cara_pembayaran = $data['cara_pembayaran'];
                        $new->id_kas = $data['kas'];
                        $new->no_bukti_potong = $data['no_bukti_potong'];
                        $new->catatan = $value['catatan'];
                        $new->created_by = $user;
                        $new->created_at = now();
                        if($new->save()){
                            $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($key);
                            $keterangan_transaksi .= ' #'.$invoice->no_invoice;
                            $id_invoices .= $invoice->id . ',';
                            if($invoice){
                                $invoice->total_dibayar += $value['dibayar'];
                                $invoice->total_sisa -=  $invoice->total_dibayar;
                                if($invoice->total_sisa < 0){
                                    $isErr = true;
                                }
                                $currentStatus = '';
                                if($invoice->total_sisa == 0){
                                    $currentStatus = 'SELESAI PEMBAYARAN INVOICE';
                                    $invoice->status = $currentStatus;
                                }
                                $invoice->updated_by = $user;
                                $invoice->updated_at = now();
                                if($invoice->save()){
                                    if($currentStatus == 'SELESAI PEMBAYARAN INVOICE'){
                                        $invoiceDetail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice', $invoice->id)->get();
                                        if($invoiceDetail){
                                            foreach ($invoiceDetail as $i => $item) {
                                                $check = InvoiceDetail::leftJoin('invoice', 'invoice.id', '=', 'invoice_detail.id_invoice')
                                                                        ->where('invoice_detail.is_aktif', 'Y')
                                                                        ->where('invoice.status', 'MENUNGGU PEMBAYARAN INVOICE')
                                                                        ->where('id_sewa', $item->id_sewa)->get();
                                                if ($check->isEmpty()) {
                                                    $updateSewa = Sewa::where('is_aktif', 'Y')->find($item->id_sewa);
                                                    $updateSewa->status = 'SELESAI PEMBAYARAN';
                                                    $updateSewa->updated_by = $user;
                                                    $updateSewa->updated_at = now();
                                                    $updateSewa->save();

                                                    // trigger update status jo detail jika semua sewa sudah selesai
                                                    // trigger update status jo jika semua jo detail sudah selesai
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $isErr = true;
                                }
                            }
                        }else{
                            $isErr = true;
                        }
                    }
                    $i++;
                }

                // dump data ke dump transaction
                $total_bayar = (float)str_replace(',', '', $data['total_diterima']);
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['kas'],// id kas_bank dr form
                        now(),//tanggal
                        $total_bayar, //uang masuk (debit)
                        0,// kredit 0 soalnya kan ini uang masuk
                        1018, //kode coa
                        'BAYAR_INVOICE',
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

                if($isErr === true){
                    db::rollBack();
                    return redirect()->route('pembayaran_invoice.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan!']);
                }else{
                    DB::commit();
                    return redirect()->route('pembayaran_invoice.index')->with(["status" => "Success", "msg" => "Berhasil Membayar invoice!"]);
                }

            }
     
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            db::rollBack();
            return redirect()->route('pembayaran_invoice.index')->with(["status" => "error", "msg" => 'Terjadi Kesalahan ketika pembayaran!']);
        }
       
    }

    public function store(Request $request)
    {
        $data = $request->post();
        $user = Auth::user()->id; 
        DB::beginTransaction(); 
        $isErr = false;
        // dd($data);`

        try {
            if($data['detail'] != null){
                $keterangan_transaksi = 'PEMBAYARAN INVOICE | '. $data['cara_pembayaran'] . ' | ' . $data['catatan'] . ' |';
                $id_invoices = '';
                $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
                $total_pph = isset($data['total_pph23'])? floatval(str_replace(',', '', $data['total_pph23'])):0;
                $i = 0;

                $pembayaran = new InvoicePembayaran();
                $pembayaran->id_kas = $data['kas'];
                $pembayaran->billing_to = $data['billingTo'];
                $pembayaran->tgl_pembayaran = date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
                $pembayaran->total_diterima = floatval(str_replace(',', '', $data['total_diterima']));
                $pembayaran->total_pph = $total_pph;
                $pembayaran->biaya_admin = $biaya_admin;
                $pembayaran->cara_pembayaran = $data['cara_pembayaran'];
                $pembayaran->no_cek = isset($data['no_cek'])? $data['no_cek']:null;
                $pembayaran->no_bukti_potong = $data['no_bukti_potong'];
                $pembayaran->catatan = $data['catatan'];
                $pembayaran->created_by = $user;
                $pembayaran->created_at = now();
                if($pembayaran->save()){
                    foreach ($data['detail'] as $key => $value) {
                        $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($key);

                        $keterangan_transaksi .= ' #'.$invoice->no_invoice;
                        $id_invoices .= $invoice->id . ','; 

                        if($invoice){
                            $invoice->id_pembayaran = $pembayaran->id;
                            $invoice->pph = $value['pph23'];
                            if($i == 0){
                                $invoice->total_dibayar += $value['diterima'] - $biaya_admin;
                                $invoice->biaya_admin = $biaya_admin;
                            }else{
                                $invoice->total_dibayar += $value['diterima'];
                            }
                            $invoice->total_sisa -= $value['dibayar'];
                            if($invoice->total_sisa < 0){
                                $isErr = true;
                            }
                            $currentStatus = '';
                            if($invoice->total_sisa == 0){
                                $currentStatus = 'SELESAI PEMBAYARAN INVOICE';
                                $invoice->status = $currentStatus;
                            }
                            $invoice->updated_by = $user;
                            $invoice->updated_at = now();
                            if($invoice->save()){
                                if($currentStatus == 'SELESAI PEMBAYARAN INVOICE'){
                                    $invoiceDetail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice', $invoice->id)->get();
                                    if($invoiceDetail){
                                        foreach ($invoiceDetail as $i => $item) {
                                            $check = InvoiceDetail::leftJoin('invoice', 'invoice.id', '=', 'invoice_detail.id_invoice')
                                                                    ->where('invoice_detail.is_aktif', 'Y')
                                                                    ->where('invoice.status', 'MENUNGGU PEMBAYARAN INVOICE')
                                                                    ->where('id_sewa', $item->id_sewa)->get();
                                            if ($check->isEmpty()) {
                                                $updateSewa = Sewa::where('is_aktif', 'Y')->find($item->id_sewa);
                                                $updateSewa->status = 'SELESAI PEMBAYARAN';
                                                $updateSewa->updated_by = $user;
                                                $updateSewa->updated_at = now();
                                                $updateSewa->save();

                                                // trigger update status jo detail jika semua sewa sudah selesai 
                                                // trigger update status jo jika semua jo detail sudah selesai 
                                            }
                                        }
                                    }
                                }
                            }else{
                                $isErr = true;
                            }
                        }
                        $i++;
                    }
                }

                // dump data ke dump transaction
                $total_bayar = (float)str_replace(',', '', $data['total_diterima']);
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['kas'],// id kas_bank dr form
                        now(),//tanggal
                        $total_bayar, //uang masuk (debit)
                        0,// kredit 0 soalnya kan ini uang masuk
                        1018, //kode coa
                        'BAYAR INVOICE',
                        $keterangan_transaksi, //keterangan_transaksi
                        $pembayaran->id, // keterangan_kode_transaksi - id pembayaran
                        $user,//created_by
                        now(),//created_at
                        $user,//updated_by
                        now(),//updated_at
                        'Y'
                    ) 
                );

                $cust = Customer::where('is_aktif', 'Y')->findOrFail($data['billingTo']);
                if($cust){
                    $kredit_sekarang = $cust->kredit_sekarang - $total_bayar;
                    if($kredit_sekarang < 0){
                        $isErr = true;
                        // $kredit_sekarang = 0;
                    }
                    $cust->kredit_sekarang = $kredit_sekarang;
                    $cust->updated_by = $user;
                    $cust->updated_at = now();
                    $cust->save();
                }

                if($isErr === true){
                    db::rollBack();
                    return redirect()->route('pembayaran_invoice.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan!']);
                }else{
                    DB::commit();
                    return redirect()->route('pembayaran_invoice.index')->with(["status" => "Success", "msg" => "Berhasil Membayar invoice!"]);
                }

            }
     
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            db::rollBack();
            return redirect()->route('pembayaran_invoice.index')->with(["status" => "error", "msg" => 'Terjadi Kesalahan ketika pembayaran!']);
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
        // dd($id);
        $invoice = Invoice::where('is_aktif', 'Y')->find($id);
        $cek = substr($invoice->no_invoice, -2);
        if($cek != '/I'){
            $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
        }else{
            $invoice = Invoice::where('is_aktif', 'Y')->where('no_invoice', substr($invoice->no_invoice, 0, -2))->first();
            $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
        }

        if($invoice->total_sisa == 0){
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Data invoice sudah terbayar, harap lakukan revisi Invoice untuk melakukan perubahan!']);
        }
        if(isset($reimburse)){
            if($reimburse->total_sisa == 0){
                return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Data Reimburse sudah terbayar, harap lakukan revisi Invoice untuk melakukan perubahan!']);
            }
        }


        $id_invoices = [];
        $checkLTL = false;

        foreach ($invoice->invoiceDetails as $key => $value) {
            if (!in_array($value['id_sewa'], $id_invoices)) {
                $id_invoices[] = $value['id_sewa'];
            }
        }
        if($reimburse){
            foreach ($reimburse->invoiceDetails as $key => $value) {
                if (!in_array($value['id_sewa'], $id_invoices)) {
                    $id_invoices[] = $value['id_sewa'];
                }
            }
        }

        if($invoice){
            // $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
            //         ->leftJoin('customer as c', 'c.id', 'id_customer')
            //         ->where('c.grup_id', $invoice->id_grup)
            //         ->where('sewa.is_aktif', '=', 'Y')
            //         ->where('sewa.status', 'MENUNGGU INVOICE')
            //         ->select('sewa.*')->with('sewaOperasional')
            //         ->get();
            $dataSewa = Sewa::
                    with('sewaOperasional', 'getCustomer', 'getTujuan')
                    ->where('sewa.is_aktif', 'Y')
                    // ->leftJoin('grup_tujuan', function($query) use($invoice){
                    //     $query->on('sewa.id_grup_tujuan', '=', 'grup_tujuan.id')
                    //                 ->where('grup_tujuan.grup_id', $invoice->id_grup);
                    // })
                    ->whereHas('getTujuan', function($query) use ($invoice) {
                        return $query->where('grup_id', $invoice->id_grup);
                    })
                    ->where('sewa.status', 'MENUNGGU INVOICE') 
                    ->orWhere(function ($query) use($id_invoices) {
                        $query->whereIn('sewa.id_sewa', $id_invoices);
                    })
                    ->get();

            if($dataSewa[0]->jenis_tujuan == 'LTL'){
                $checkLTL = true; 
            }
            // dd($dataSewa);


            $dataCust = Customer::where('grup_id', $invoice->id_grup)
                    ->where('is_aktif', 'Y')
                    ->get();

            return view('pages.invoice.pembayaran_invoice.edit',[
                'judul' => "Revisi Invoice",
                'data' => $invoice,
                'reimburse' => isset($reimburse)? $reimburse:NULL,
                'dataSewa' => $dataSewa,
                'checkLTL' => $checkLTL,
                'dataCust' => $dataCust,
                // 'customer' => $cust[0],
            ]);

        }else{
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Data tidak ditemukan!']);
        }
    }

    public function update(Request $request, $id){
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);

        try {
            $invoice = Invoice::where('is_aktif', 'Y')->find($id);
            $cek = substr($invoice->no_invoice, -2);
            if($cek != '/I'){
                $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
            }else{
                $invoice = Invoice::where('is_aktif', 'Y')->where('no_invoice', substr($invoice->no_invoice, 0, -2))->first();
                $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
            }

            // nonaktifin anak2an invoice (invoice details)
            if($invoice->invoiceDetails != null){
                $invoice->catatan = $data['catatan_invoice'];
                $invoice->total_tagihan = floatval(str_replace(',', '', $data['total_tagihan']));
                $invoice->total_sisa = floatval(str_replace(',', '', $data['total_tagihan']));
                $invoice->total_jumlah_muatan = $data['total_jumlah_muatan'];
                $invoice->jatuh_tempo = date("Y-m-d", strtotime($data['jatuh_tempo']));
                $invoice->billing_to = $data['billingTo'];
                $invoice->save();

                foreach ($invoice->invoiceDetails as $key => $value) {
                    $detail = InvoiceDetail::where('is_aktif', 'Y')->find($value->id);
                    $detail->updated_by = $user;
                    $detail->updated_at = now();
                    $detail->is_aktif = 'N';
                    if($detail->save()){
                        // balikin status sewa ke menunggu invoice
                        $sewa = Sewa::where('is_aktif', 'Y')->find($detail->id_sewa);
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->status = 'MENUNGGU INVOICE';
                        $sewa->save();
                    
                        if($detail->invoiceDetailsAddCost != null){
                            foreach ($detail->invoiceDetailsAddCost as $key_cost => $item) {
                                $addcost = InvoiceDetailAddcost::where('is_aktif', 'Y')->find($item->id);
                                $addcost->updated_by = $user;
                                $addcost->updated_at = now();
                                $addcost->is_aktif = 'N';
                                $addcost->save();
                            }
                        }
                    }
                }
            }

            // nonaktifin anak2an reimburse (invoice details)
            if(isset($reimburse) && $reimburse != null){
                if($data['total_pisah'] == 0){
                    $reimburse->updated_by = $user;
                    $reimburse->updated_at = now();
                    $reimburse->is_aktif = 'N';
                    $reimburse->save();
                }

                if($reimburse->invoiceDetails != null){
                    foreach ($reimburse->invoiceDetails as $key => $value) {
                        $detail = InvoiceDetail::where('is_aktif', 'Y')->find($value->id);
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->is_aktif = 'N';
                        if($detail->save()){
                            $sewa = Sewa::where('is_aktif', 'Y')->find($detail->id_sewa);
                            $sewa->updated_by = $user;
                            $sewa->updated_at = now();
                            $sewa->status = 'MENUNGGU INVOICE';
                            $sewa->save();

                            if($detail->invoiceDetailsAddCost != null){
                                foreach ($detail->invoiceDetailsAddCost as $key_cost => $item) {
                                    $addcost = InvoiceDetailAddcost::where('is_aktif', 'Y')->find($item->id);
                                    $addcost->updated_by = $user;
                                    $addcost->updated_at = now();
                                    $addcost->is_aktif = 'N';
                                    $addcost->save();
                                }
                            }
                        }
                    }
                }
            }

            if(isset($data['detail'])){
                foreach ($data['detail'] as $key => $value) {
                    DB::table('sewa')
                        ->where('id_sewa',  $key)
                        ->update(array(
                            'status' => 'MENUNGGU PEMBAYARAN INVOICE',
                            'catatan'=> $value['catatan'],
                            'updated_at'=> now(),
                            'updated_by'=> $user,
                    ));
    
                    if(isset($value['id_jo_hidden']) && isset($value['id_jo_detail_hidden'])){
                        DB::table('job_order_detail')
                            ->where('id', $value['id_jo_detail_hidden'])
                            ->update(array(
                                'status' => 'MENUNGGU PEMBAYARAN INVOICE',
                                'updated_at'=> now(),
                                'updated_by'=>  $user,
                        )); 
                    }

                    $invoice->total_tagihan = floatval(str_replace(',', '', $data['total_tagihan']));
                    $invoice->total_sisa = floatval(str_replace(',', '', $data['total_tagihan']));
                    $invoice->total_dibayar = 0;
                    $invoice->catatan = $data['catatan_invoice'];
                    $invoice->total_jumlah_muatan = $data['total_jumlah_muatan'];
                    $invoice->jatuh_tempo = date("Y-m-d", strtotime($data['jatuh_tempo_pisah']));
                    $invoice->billing_to = $data['billingTo'];
                    $invoice->save();

                    // dd($data);
                    $invoice_d = new InvoiceDetail();
                    $invoice_d->id_invoice = $invoice->id;
                    $invoice_d->id_customer = $value['id_customer'];
                    $invoice_d->id_sewa = $key;
                    $invoice_d->tarif = $value['tarif'];
                    $invoice_d->jumlah_muatan = $value['muatan_satuan'];
                    $invoice_d->add_cost = $value['addcost'];
                    $invoice_d->diskon = $value['diskon'];
                    $invoice_d->add_cost_pisah = $value['addcost_pisah'];
                    $invoice_d->sub_total = $value['subtotal'] - $invoice_d->diskon;
                    $invoice_d->catatan = $value['catatan'];
                    $invoice_d->created_by = $user;
                    $invoice_d->created_at = now();
                    $invoice_d->is_aktif = 'Y';
                    if($invoice_d->save()){
                        $dataAddcost = json_decode($value['addcost_details']);
                        foreach ($dataAddcost as $i => $addcost) {
                            $sewa_oprs = SewaOperasional::where('is_aktif', 'Y')
                                                        ->where('id_sewa', $addcost->id_sewa)
                                                        ->find($addcost->id);
    
                            if($sewa_oprs){
                                $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                $sewa_oprs->updated_by = $user;
                                $sewa_oprs->updated_at = now();
                                $sewa_oprs->save();
                            }
                            if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'N'){
                                $invoice_da = new InvoiceDetailAddcost();
                                $invoice_da->id_invoice = $invoice->id;
                                $invoice_da->id_invoice_detail = $invoice_d->id;
                                $invoice_da->id_sewa_operasional = $addcost->id;
                                $invoice_da->catatan = $addcost->catatan;
                                $invoice_da->created_by = $user;
                                $invoice_da->created_at = now();
                                $invoice_da->is_aktif = 'Y';
                                $invoice_da->save();
                            }
                        }
    
                        $addcost_baru = json_decode($value['addcost_baru']);
                        if($addcost_baru != null){
                            foreach ($addcost_baru as $i => $addcost) {
                                if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'N'){
                                    $sewa_oprs = new SewaOperasional();
                                    $sewa_oprs->id_sewa = $addcost->id_sewa;
                                    $sewa_oprs->deskripsi = $addcost->deskripsi;
                                    $sewa_oprs->total_operasional = $addcost->total_operasional;
                                    // $sewa_oprs->total_dicairkan = $addcost->total_operasional;
                                    $sewa_oprs->tgl_dicairkan = now();
                                    $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                    $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                    $sewa_oprs->created_by = $user;
                                    $sewa_oprs->created_at = now();
                                    $sewa_oprs->save();
                                    
                                    // DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    //     array(
                                    //         $data['pembayaran'], // id kas_bank dr form
                                    //         now(), //tanggal
                                    //         0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                    //         $sewa_oprs->total_dicairkan, //uang keluar (kredit)
                                    //         1015, //kode coa
                                    //         'pencairan_operasional',
                                    //         'REVISI BELUM INVOICE - ' . $addcost->deskripsi . ' : '. $addcost->nama_tujuan .'/'. $addcost->driver, //keterangan_transaksi
                                    //         $sewa_oprs->id, //keterangan_kode_transaksi // id_sewa_operasional
                                    //         $user, //created_by
                                    //         now(), //created_at
                                    //         $user, //updated_by
                                    //         now(), //updated_at
                                    //         'Y'
                                    //     ) 
                                    // );
                                    
                                    $invoice_da = new InvoiceDetailAddcost();
                                    $invoice_da->id_invoice = $invoice->id;
                                    $invoice_da->id_invoice_detail = $invoice_d->id;
                                    $invoice_da->id_sewa_operasional = $sewa_oprs->id;
                                    $invoice_da->catatan = $addcost->catatan;
                                    $invoice_da->created_by = $user;
                                    $invoice_da->created_at = now();
                                    $invoice_da->is_aktif = 'Y';
                                    $invoice_da->save();
                                }
                            }
                        }
                    }
                }
               
                if($data['is_pisah_invoice'] == 'TRUE'){
                    if($reimburse == null){
                        $reimburse = new Invoice();
                        $reimburse->id_grup = $data['grup_id'];
                        $reimburse->no_invoice = $invoice->no_invoice . '/I';
                        $reimburse->tgl_invoice = date_create_from_format('d-M-Y', $data['tanggal_invoice']);
                        $reimburse->total_tagihan = $data['total_pisah'];
                        $reimburse->total_sisa = $data['total_pisah'];
                        $reimburse->total_jumlah_muatan = $data['total_jumlah_muatan'];
                        $reimburse->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo_pisah']);
                        $reimburse->catatan = $data['catatan_invoice'];
                        $reimburse->status = 'MENUNGGU PEMBAYARAN INVOICE';
                        $reimburse->billing_to = $data['billingTo'];
                        $reimburse->created_by = $user;
                        $reimburse->created_at = now();
                        $reimburse->is_aktif = 'Y';
                        $reimburse->save();
                    }else{
                        $reimburse->catatan = $data['catatan_invoice'];
                        $reimburse->total_tagihan = $data['total_pisah'];
                        $reimburse->total_sisa = $data['total_pisah'];
                        $reimburse->total_dibayar = 0;
                        $reimburse->jatuh_tempo = date("Y-m-d", strtotime($data['jatuh_tempo_pisah']));
                        $reimburse->billing_to = $data['billingTo'];
                        $reimburse->save();
                    }
    
                    foreach ($data['detail'] as $key => $value) {
                        $invoice_d_pisah = new InvoiceDetail();
                        $invoice_d_pisah->id_invoice = $reimburse->id;
                        $invoice_d_pisah->id_customer = $value['id_customer'];
                        $invoice_d_pisah->id_sewa = $key;
                        $invoice_d_pisah->add_cost_pisah = $value['addcost_pisah'];
                        $invoice_d_pisah->sub_total = $value['addcost_pisah'];
                        $invoice_d_pisah->catatan = $value['catatan'];
                        $invoice_d_pisah->created_by = $user;
                        $invoice_d_pisah->created_at = now();
                        $invoice_d_pisah->is_aktif = 'Y';
                        if($invoice_d_pisah->save()){
                            $dataAddcost = json_decode($value['addcost_details']);
                            foreach ($dataAddcost as $i => $addcost) {
                                $sewa_oprs = SewaOperasional::where('is_aktif', 'Y')
                                                            ->where('id_sewa', $addcost->id_sewa)
                                                            ->find($addcost->id);
                                if($sewa_oprs){
                                    $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                    $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                    $sewa_oprs->updated_by = $user;
                                    $sewa_oprs->updated_at = now();
                                    $sewa_oprs->save();
                                }
                                if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'Y'){
        
                                    $invoice_da = new InvoiceDetailAddcost();
                                    $invoice_da->id_invoice = $reimburse->id;
                                    $invoice_da->id_invoice_detail = $invoice_d_pisah->id;
                                    $invoice_da->id_sewa_operasional = $addcost->id;
                                    $invoice_da->catatan = $addcost->catatan;
                                    $invoice_da->created_by = $user;
                                    $invoice_da->created_at = now();
                                    $invoice_da->is_aktif = 'Y';
                                    $invoice_da->save();
                                }
                            }
        
                            $addcost_baru = json_decode($value['addcost_baru']);
                            if($addcost_baru != null){
                                foreach ($addcost_baru as $i => $addcost) {
                                    if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'Y'){
                                        $sewa_oprs = new SewaOperasional();
                                        $sewa_oprs->id_sewa = $addcost->id_sewa;
                                        $sewa_oprs->deskripsi = $addcost->deskripsi;
                                        $sewa_oprs->total_operasional = $addcost->total_operasional;
                                        // $sewa_oprs->total_dicairkan = $addcost->total_operasional;
                                        $sewa_oprs->tgl_dicairkan = now();
                                        $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                        $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                        $sewa_oprs->created_by = $user;
                                        $sewa_oprs->created_at = now();
                                        $sewa_oprs->save();
                                        
                                        // DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                        //     array(
                                        //         $data['pembayaran'], // id kas_bank dr form
                                        //         now(), //tanggal
                                        //         0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        //         $sewa_oprs->total_dicairkan, //uang keluar (kredit)
                                        //         1015, //kode coa
                                        //         'pencairan_operasional',
                                        //         'REVISI BELUM INVOICE - ' . $addcost->deskripsi . ' : '. $addcost->nama_tujuan .'/'. $addcost->driver, //keterangan_transaksi
                                        //         $sewa_oprs->id, //keterangan_kode_transaksi // id_sewa_operasional
                                        //         $user, //created_by
                                        //         now(), //created_at
                                        //         $user, //updated_by
                                        //         now(), //updated_at
                                        //         'Y'
                                        //     ) 
                                        // );
                                        
                                        $invoice_da = new InvoiceDetailAddcost();
                                        $invoice_da->id_invoice = $reimburse->id;
                                        $invoice_da->id_invoice_detail = $invoice_d_pisah->id;
                                        $invoice_da->id_sewa_operasional = $sewa_oprs->id;
                                        $invoice_da->catatan = $addcost->catatan;
                                        $invoice_da->created_by = $user;
                                        $invoice_da->created_at = now();
                                        $invoice_da->is_aktif = 'Y';
                                        $invoice_da->save();
                                    }
                                }
                            }
                        }
                    }
                }
    
            }

            DB::commit();
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'Success', 'msg'  => 'Edit berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Edit gagal!']);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


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
                ->select('ip.total_diterima','i.no_invoice', 'i.id as id', 'i.total_sisa','i.jatuh_tempo', 'i.tgl_invoice','c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                        ,'g.id as id_grup','ip.no_bukti_potong', 'ip.catatan', 'ip.id as id_ip')
                ->leftJoin('invoice AS i', 'i.id', '=', 'ip.id_invoice')
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

    public function updateBuktiPotong(Request $request, $id)
    {
        $data = $request->post();
        $user = Auth::user()->id; 
        
        $invoice = InvoicePembayaran::where('is_aktif', 'Y')->findOrFail($id);
        if($invoice){
            $invoice->no_bukti_potong = $data['no_bukti_potong'];
            $invoice->catatan = $data['catatan'];
            $invoice->updated_by = $user;
            $invoice->updated_at = now();
            if($invoice->save()){
                return response()->json(['status' => 'success', 'message' => 'Data tersimpan']);
            }else{
                return response()->json(['status' => 'error', 'message' => 'Data tersimpan']);
            }
        }else{
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
    }

    public function updateResi(Request $request)
    {
        $data = $request->collect();
        $user = Auth::user()->id; 
        DB::beginTransaction(); 

        try {
            $invoice = Invoice::where('is_aktif', 'Y')->find($data['id_invoice']);
            if($invoice){
                $invoice->resi = $data['resi'];
                $invoice->catatan = $data['catatan'];
                $invoice->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
                $invoice->updated_by = $user;
                $invoice->updated_at = now();
                $invoice->save();
                DB::commit();
                return redirect()->route('pembayaran_invoice.index')->with(['status' => 'Success', 'msg' => 'Update Resi berhasil!']);
            }

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Update Resi Gagal!']);
        }
    }
}
