<?php

namespace App\Http\Controllers;

use App\Helper\CoaHelper;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Sewa;
use App\Models\SewaOperasional;
use App\Models\SewaOperasionalPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf; // use PDF;
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BelumInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_BELUM_INVOICE', ['only' => ['index']]);
		$this->middleware('permission:CREATE_BELUM_INVOICE', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_BELUM_INVOICE', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_BELUM_INVOICE', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        // Session::flush();
        // Session::forget(['sewa', 'cust', 'grup']);
        if (session()->has('sewa') || session()->has('cust') || session()->has('grup')) {
            session()->forget(['sewa', 'cust', 'grup']);
        }
        $dataSewa =  DB::table('sewa AS s')
                ->select('s.*','s.id_sewa as idSewanya','c.id AS id_cust','c.nama AS nama_cust','g.nama_grup','g.id as id_grup','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir','sp.nama as namaSupplier')
                ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                ->leftJoin('grup AS g', 'c.grup_id', '=', 'g.id')
                ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('supplier AS sp', 's.id_supplier', '=', 'sp.id')

                ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                ->where('s.is_aktif', '=', 'Y')
                // ->where('s.jenis_tujuan', 'like', '%FTL%')
                ->where('s.status', 'MENUNGGU INVOICE')
            //      ->where(function ($query) {
            //     $query->where('s.status', 'MENUNGGU INVOICE')
            //         ->orWhere('s.status', 'BATAL MUAT');
            // })
                // ->whereNull('s.id_supplier')
                // ->whereNull('s.tanggal_kembali')
                ->orderBy('c.id','ASC')
                ->get();
        // dd($dataSewa);
    
        return view('pages.invoice.belum_invoice.index',[
            'judul'=>"BELUM INVOICE",
            'dataSewa' => $dataSewa,
        ]);
    }
    public function setSewaID(Request $request)
    {
        try {
            //code...
            $sewa = session()->get('sewa'); //buat ambil session
            $cust = session()->get('cust'); //buat ambil session
            $grup = session()->get('grup'); //buat ambil session
            // Session::forget(['sewa', 'cust', 'grup']);

            $data= $request->collect();
            session()->put('sewa', $data['idSewa']);
            session()->put('cust', $data['idCust']);
            session()->put('grup', $data['idGrup']);
            return response()->json(['status'=>'ok','dataSewa'=>$sewa,'dataCustomer'=>$cust,'dataGrup'=>$grup],200);
        } catch (Exception $ex) {
            //throw $th;
                return response()->json(['status'=>'error','message' => $ex->getMessage()], 500);

        }
        

        
    }
    public function invoiceKembali(Request $request)
    {
        //
        $user = Auth::user()->id;
        $data= $request->collect();
        // dd(isset($data['idJo']));

        try {
            DB::table('sewa')
            ->where('id_sewa',  $data['idSewa'])
            ->update(array(
                'status' => 'PROSES DOORING',
                'is_kembali' => 'N',
                'tanggal_kembali' => null,
                'updated_at'=> now(),
                'updated_by'=>  $user,
            ));
            if(isset($data['idJo'])&&isset($data['idJo_detail']))
            {
                DB::table('job_order_detail')
                ->where('id',  $data['idJo_detail'])
                ->update(array(
                    'status' => 'PROSES DOORING',
                    'updated_at'=> now(),
                    'updated_by'=>  $user,
                )); 
            }
            return redirect()->route('invoice.index')->with('status','Sukses Mengubah mengembalikan data sewa!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sewa = session()->get('sewa'); //buat ambil session
        $cust = session()->get('cust'); //buat ambil session
        $grup = session()->get('grup'); //buat ambil session
        try {
            $data = Sewa::whereIn('sewa.id_sewa', $sewa)
                ->leftJoin('supplier AS sp', 'sewa.id_supplier', '=', 'sp.id')
                ->where('sewa.status', 'MENUNGGU INVOICE')
                ->where('sewa.is_aktif', '=', 'Y')
                ->select('sewa.*','sp.nama as namaSupplier')
                ->get();
            $checkBedaJenisTujuan = false;
            $checkLTL = false;
            
            for ($i=0; $i <count($data) ; $i++) { 
                if ($data[$i]->jenis_tujuan !== $data[0]->jenis_tujuan) {
                    $checkBedaJenisTujuan = true; 
                    break;
                }
            }
            
            if ($data[0]->jenis_tujuan == 'LTL') {
                $checkLTL = true; 
            }
            
            if($checkBedaJenisTujuan)
            {
                return redirect()->route('belum_invoice.index')
                    ->with(['status' => 'Gagal', 'msg' => 'Sewa yang dibuat Berbeda!']);
            }
            else
            {
                //ini buat yang di dalem modal
                $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
                        ->leftJoin('customer as c', 'c.id', 'id_customer')
                        ->where('c.grup_id', $grup[0])
                        ->where('sewa.is_aktif', '=', 'Y')
                        ->where('sewa.status', 'MENUNGGU INVOICE')
                        ->select('sewa.*')
                        ->get();
    
                $dataCust = Customer::where('grup_id', $grup[0])
                        ->where('is_aktif', 'Y')
                        ->get();

                $bank = KasBank::where('is_aktif', 'Y')->get();

                return view('pages.invoice.belum_invoice.form',[
                    'judul'=>"BELUM INVOICE",
                    'data' => $data,
                    'bank' => $bank,
                    'dataSewa' => $dataSewa,
                    'dataCust' => $dataCust,
                    'grup' => $grup[0],
                    'customer' => $cust[0],
                    'checkLTL' => $checkLTL,
                ]);
            }
            
        } catch (\Throwable $th) {
            return redirect()->route('belum_invoice.index')
                    ->with(['status' => 'Gagal', 'msg' => 'Tidak ada sewa yang terpilih!']);
        }
        
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
        DB::beginTransaction(); 
        // dd($data);

        try {
            // logic nomer 
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
            $invoice->total_jumlah_muatan = ($data['total_jumlah_muatan'] != '')? floatval( $data['total_jumlah_muatan']):0;
            $invoice->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
            $invoice->catatan = $data['catatan_invoice'];
            $invoice->status = 'MENUNGGU PEMBAYARAN INVOICE';
            $invoice->billing_to = $data['billingTo'];
            $invoice->created_by = $user;
            $invoice->created_at = now();
            $invoice->is_aktif = 'Y';
            if($invoice->save()){
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

                    $is_invoice_detail = false;
                    $dataAddcost = json_decode($value['addcost_details']);
                    $result = array_filter($dataAddcost, function ($addcost) {
                        return $addcost->is_ditagihkan === "Y" && $addcost->is_dipisahkan === "Y";
                    });

                    if (!empty($result)) {
                        $is_invoice_detail = true;
                    }

                    $addcost_baru = json_decode($value['addcost_baru']);
                    if($addcost_baru != null){
                        $result_baru = array_filter($addcost_baru, function ($addcost_baru) {
                            return $addcost_baru->is_ditagihkan === "Y" && $addcost_baru->is_dipisahkan === "Y";
                        });

                        if (!empty($result_baru)) {
                            $is_invoice_detail = true;
                        }
                    }

                    if(($is_invoice_detail == true && $value['addcost'] != null) || $value['tarif'] != 0){
                        $invoice_d = new InvoiceDetail();
                        $invoice_d->id_invoice = $invoice->id;
                        $invoice_d->id_customer = $value['id_customer'];
                        $invoice_d->id_sewa = $key;
                        $invoice_d->tarif = $value['tarif']!=NULL? $value['tarif']:0;
                        $invoice_d->jumlah_muatan = $value['muatan_satuan']!=NULL? $value['muatan_satuan']:0;
                        $invoice_d->add_cost = $value['addcost']!=NULL? $value['addcost']:0;
                        $invoice_d->diskon = $value['diskon']!=NULL? floatval(str_replace(',', '', $value['diskon'])):0;
                        $invoice_d->add_cost_pisah = $value['addcost_pisah']!=NULL? $value['addcost_pisah']:0;
                        $invoice_d->sub_total = $value['subtotal']!=NULL? $value['subtotal']:0 - $invoice_d->diskon;
                        $invoice_d->catatan = $value['catatan'];
                        $invoice_d->created_by = $user;
                        $invoice_d->created_at = now();
                        $invoice_d->is_aktif = 'Y';
                        if($invoice_d->save()){
                            // data addcost lama
                            foreach ($dataAddcost as $i => $addcost) {
                                $sewa_oprs = SewaOperasional::where('is_aktif', 'Y')
                                                            ->where('id_sewa', $addcost->id_sewa)
                                                            ->find($addcost->id);
    
                                if($sewa_oprs){
                                    if($sewa_oprs->total_dicairkan != $addcost->total_dicairkan){
                                        $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($sewa_oprs->id_pembayaran);
                                        $pembayaran->total_dicairkan = $addcost->total_dicairkan;
                                        $pembayaran->updated_by = $user;
                                        $pembayaran->updated_at = now();
                                        if($pembayaran->save()){
                                            // rollback transaksi lama
                                            $transaction = KasBankTransaction::where('is_aktif', 'Y')
                                                                ->where('jenis', 'pencairan_operasional')
                                                                ->where('keterangan_kode_transaksi', $pembayaran->id)
                                                                ->first();
                                            if($transaction){
                                                $transaction->is_aktif = 'N';
                                                $transaction->updated_by = $user;
                                                $transaction->updated_at = now();
                                                if($transaction->save()){
                                                    $saldo = KasBank::where('is_aktif', 'Y')->find($transaction->id_kas_bank);
                                                    if($saldo){
                                                        $saldo->saldo_sekarang += $transaction->kredit;
                                                        $saldo->updated_by = $user;
                                                        $saldo->updated_at = now();
                                                        $saldo->save();
                                                    }
                                                }
                                            }
            
                                            // buat data transaction baru
                                            $keterangan_transaksi = substr($transaction->keterangan_transaksi, 0, 9) == "REVISI - "? $transaction->keterangan_transaksi: 'REVISI - '.$transaction->keterangan_transaksi;
                                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                array(
                                                    $data['bank'], //id kas_bank dr form
                                                    now(), //tanggal
                                                    0, //debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                    $addcost->total_dicairkan, //uang keluar (kredit)
                                                    CoaHelper::DataCoa(5007), //kode coa
                                                    'pencairan_operasional',
                                                    $keterangan_transaksi, //keterangan_transaksi
                                                    $pembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional_pembayaran
                                                    $user, //created_by
                                                    now(), //created_at
                                                    $user, //updated_by
                                                    now(), //updated_at
                                                    'Y'
                                                ) 
                                            );
                        
                                            $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                                            $saldo->saldo_sekarang -= $addcost->total_dicairkan;
                                            $saldo->updated_by = $user;
                                            $saldo->updated_at = now();
                                            $saldo->save();
                                        }
                                    }
                                    $sewa_oprs->total_dicairkan = $addcost->total_dicairkan;
                                    $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                    $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                    $sewa_oprs->catatan = $addcost->catatan;
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

                            // data addcost baru
                            if($addcost_baru != null){
                                if($data['bank'] == null){
                                    db::rollBack();
                                    return redirect()->back()->with(['status' => 'Error', 'msg' => 'Harap isi Kas untuk pencairan!']);
                                }
                                foreach ($addcost_baru as $i => $addcost) {
                                    $pembayaran = new SewaOperasionalPembayaran();
                                    $pembayaran->deskripsi = $addcost->deskripsi;
                                    $pembayaran->total_operasional = $addcost->total_dicairkan;
                                    $pembayaran->total_dicairkan = $addcost->total_dicairkan;
                                    // $pembayaran->catatan = '';
                                    $pembayaran->created_by = $user;
                                    $pembayaran->created_at = now();
                                    $pembayaran->save();
                                        
                                    $sewa_oprs = new SewaOperasional();
                                    $sewa_oprs->id_sewa = $addcost->id_sewa;
                                    $sewa_oprs->id_pembayaran = $pembayaran->id;
                                    $sewa_oprs->deskripsi = $addcost->deskripsi;
                                    $sewa_oprs->total_operasional = $addcost->total_dicairkan;
                                    $sewa_oprs->total_dicairkan = $addcost->total_dicairkan;
                                    $sewa_oprs->tgl_dicairkan = now();
                                    $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                    $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                    $sewa_oprs->status = 'SUDAH DICAIRKAN';
                                    $sewa_oprs->created_by = $user;
                                    $sewa_oprs->created_at = now();
                                    $sewa_oprs->save();
                                    if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'N'){
                                        $keterangan = $addcost->deskripsi . ' : ' . $value['nama_tujuan'] . ' #' . $value['driver'];
                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                            array(
                                                $data['bank'], // id kas_bank dr form
                                                now(), //tanggal
                                                0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                $sewa_oprs->total_dicairkan, //uang keluar (kredit)
                                                1015, //kode coa
                                                'pencairan_operasional',
                                                $keterangan, //keterangan_transaksi
                                                $pembayaran->id, //keterangan_kode_transaksi // id_pembayaran
                                                $user, //created_by
                                                now(), //created_at
                                                $user, //updated_by
                                                now(), //updated_at
                                                'Y'
                                            ) 
                                        );

                                        $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                                        $saldo->saldo_sekarang -= $addcost->total_dicairkan;
                                        $saldo->created_by = $user;
                                        $saldo->created_at = now();
                                        $saldo->save();
                                        
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
                }
            }

            if($data['is_pisah_invoice'] == 'TRUE'){
                $invoicePisah = new Invoice();
                $invoicePisah->id_grup = $data['grup_id'];
                $invoicePisah->no_invoice = $newInvoiceNumber .'/'.'I';
                $invoicePisah->tgl_invoice = date_create_from_format('d-M-Y', $data['tanggal_invoice']);
                $invoicePisah->total_tagihan = ($data['total_pisah'] != '' || $data['total_pisah'] != 0)? $data['total_pisah']:0;
                $invoicePisah->total_sisa = ($data['total_pisah'] != ''|| $data['total_pisah'] != 0)? $data['total_pisah']:0;
                // $invoicePisah->total_jumlah_muatan = ($data['total_jumlah_muatan'] != '')? floatval( $data['total_jumlah_muatan']):0;
                $invoicePisah->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo_pisah']);
                $invoicePisah->catatan = $data['catatan_invoice'];
                $invoicePisah->status = 'MENUNGGU PEMBAYARAN INVOICE';
                $invoicePisah->billing_to = $data['billingTo'];
                $invoicePisah->created_by = $user;
                $invoicePisah->created_at = now();
                $invoicePisah->is_aktif = 'Y';
                if($invoicePisah->save()){
                    $total_tagih = 0;

                    foreach ($data['detail'] as $key => $value) {
                        $is_invoice_detail_pisah = false;
                        $dataAddcost = json_decode($value['addcost_details']);
                        $result = array_filter($dataAddcost, function ($addcost) {
                            return $addcost->is_ditagihkan === "Y" && $addcost->is_dipisahkan === "Y";
                        });
                        if (!empty($result)) {
                            $is_invoice_detail_pisah = true;
                        }

                        $addcost_baru = json_decode($value['addcost_baru']);
                        if($addcost_baru != null){
                            $result_baru = array_filter($addcost_baru, function ($addcost_baru) {
                                return $addcost_baru->is_ditagihkan === "Y" && $addcost_baru->is_dipisahkan === "Y";
                            });

                            if (!empty($result_baru)) {
                                $is_invoice_detail_pisah = true;
                            }
                        }

                        if($is_invoice_detail_pisah == true){
                            $invoice_d_pisah = new InvoiceDetail();
                            $invoice_d_pisah->id_invoice = $invoicePisah->id;
                            $invoice_d_pisah->id_customer = $value['id_customer'];
                            $invoice_d_pisah->id_sewa = $key;
                            $invoice_d_pisah->add_cost_pisah = $value['addcost_pisah'] != NULL? $value['addcost_pisah']:0;
                            $invoice_d_pisah->sub_total = $value['addcost_pisah'] != NULL? $value['addcost_pisah']:0;
                            $invoice_d_pisah->catatan = $value['catatan'];
                            $invoice_d_pisah->created_by = $user;
                            $invoice_d_pisah->created_at = now();
                            $invoice_d_pisah->is_aktif = 'Y';
                            if($invoice_d_pisah->save()){
                                // data addcost lama
                                foreach ($dataAddcost as $i => $addcost) {
                                    if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'Y'){
                                        $sewa_oprs = SewaOperasional::where('is_aktif', 'Y')
                                                                    ->where('id_sewa', $addcost->id_sewa)
                                                                    ->find($addcost->id);
                                        if($sewa_oprs){
                                            if($sewa_oprs->total_dicairkan != $addcost->total_dicairkan){
                                                $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($sewa_oprs->id_pembayaran);
                                                $pembayaran->total_dicairkan = $addcost->total_dicairkan;
                                                $pembayaran->updated_by = $user;
                                                $pembayaran->updated_at = now();
                                                if($pembayaran->save()){
                                                    // rollback transaksi lama
                                                    $transaction = KasBankTransaction::where('is_aktif', 'Y')
                                                                        ->where('jenis', 'pencairan_operasional')
                                                                        ->where('keterangan_kode_transaksi', $pembayaran->id)
                                                                        ->first();
                                                    if($transaction){
                                                        $transaction->is_aktif = 'N';
                                                        $transaction->updated_by = $user;
                                                        $transaction->updated_at = now();
                                                        if($transaction->save()){
                                                            $saldo = KasBank::where('is_aktif', 'Y')->find($transaction->id_kas_bank);
                                                            if($saldo){
                                                                $saldo->saldo_sekarang += $transaction->kredit;
                                                                $saldo->updated_by = $user;
                                                                $saldo->updated_at = now();
                                                                $saldo->save();
                                                            }
                                                        }
                                                    }
                    
                                                    // buat data transaction baru
                                                    $keterangan_transaksi = substr($transaction->keterangan_transaksi, 0, 9) == "REVISI - "? $transaction->keterangan_transaksi: 'REVISI - '.$transaction->keterangan_transaksi;
                                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                        array(
                                                            $data['bank'], //id kas_bank dr form
                                                            now(), //tanggal
                                                            0, //debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                            $addcost->total_dicairkan, //uang keluar (kredit)
                                                            CoaHelper::DataCoa(5007), //kode coa
                                                            'pencairan_operasional',
                                                            $keterangan_transaksi, //keterangan_transaksi
                                                            $pembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional_pembayaran
                                                            $user, //created_by
                                                            now(), //created_at
                                                            $user, //updated_by
                                                            now(), //updated_at
                                                            'Y'
                                                        ) 
                                                    );
                                
                                                    $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                                                    $saldo->saldo_sekarang -= $addcost->total_dicairkan;
                                                    $saldo->updated_by = $user;
                                                    $saldo->updated_at = now();
                                                    $saldo->save();
                                                }
                                            }
                                            $sewa_oprs->total_dicairkan = $addcost->total_dicairkan;
                                            $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                            $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                            $sewa_oprs->catatan = $addcost->catatan;
                                            $sewa_oprs->updated_by = $user;
                                            $sewa_oprs->updated_at = now();
                                            $sewa_oprs->save();

                                            $total_tagih += $addcost->total_dicairkan;
                                        }
            
                                        $invoice_da = new InvoiceDetailAddcost();
                                        $invoice_da->id_invoice = $invoicePisah->id;
                                        $invoice_da->id_invoice_detail = $invoice_d_pisah->id;
                                        $invoice_da->id_sewa_operasional = $addcost->id;
                                        $invoice_da->catatan = $addcost->catatan;
                                        $invoice_da->created_by = $user;
                                        $invoice_da->created_at = now();
                                        $invoice_da->is_aktif = 'Y';
                                        $invoice_da->save();
                                    }
                                }
                                
                                // data addcost baru
                                if($addcost_baru != null){
                                    if($data['bank'] == null){
                                        db::rollBack();
                                        return redirect()->back()->with(['status' => 'Error', 'msg' => 'Harap isi Kas untuk pencairan!']);
                                    }

                                    foreach ($addcost_baru as $i => $addcost) {
                                        if($addcost->is_ditagihkan == 'Y' && $addcost->is_dipisahkan == 'Y'){
                                            $pembayaran = new SewaOperasionalPembayaran();
                                            $pembayaran->deskripsi = $addcost->deskripsi;
                                            $pembayaran->total_operasional = $addcost->total_dicairkan;
                                            $pembayaran->total_dicairkan = $addcost->total_dicairkan;
                                            // $pembayaran->catatan = '';
                                            $pembayaran->created_by = $user;
                                            $pembayaran->created_at = now();
                                            $pembayaran->save();

                                            $sewa_oprs = new SewaOperasional();
                                            $sewa_oprs->id_sewa = $addcost->id_sewa;
                                            $sewa_oprs->deskripsi = $addcost->deskripsi;
                                            $sewa_oprs->total_operasional = $addcost->total_dicairkan;
                                            $sewa_oprs->total_dicairkan = $addcost->total_dicairkan;
                                            $sewa_oprs->tgl_dicairkan = now();
                                            $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                                            $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                                            $sewa_oprs->catatan = $addcost->catatan;
                                            $sewa_oprs->status = 'SUDAH DICAIRKAN';
                                            $sewa_oprs->created_by = $user;
                                            $sewa_oprs->created_at = now();
                                            $sewa_oprs->save();
                                            $total_tagih += $addcost->total_dicairkan;
                                            
                                            $keterangan = $addcost->deskripsi . ' : ' . $value['nama_tujuan'] . ' #' . $value['driver'];
                                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                array(
                                                    $data['bank'], // id kas_bank dr form
                                                    now(), //tanggal
                                                    0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                    $sewa_oprs->total_dicairkan, //uang keluar (kredit)
                                                    1015, //kode coa
                                                    'pencairan_operasional',
                                                    $keterangan, //keterangan_transaksi
                                                    $pembayaran->id, //keterangan_kode_transaksi // id_pembayaran
                                                    $user, //created_by
                                                    now(), //created_at
                                                    $user, //updated_by
                                                    now(), //updated_at
                                                    'Y'
                                                ) 
                                            );
    
                                            $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                                            $saldo->saldo_sekarang -= $addcost->total_dicairkan;
                                            $saldo->created_by = $user;
                                            $saldo->created_at = now();
                                            $saldo->save();
                                            
                                            $invoice_da = new InvoiceDetailAddcost();
                                            $invoice_da->id_invoice = $invoicePisah->id;
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

                    $editInvoicePisah = Invoice::where('is_aktif', 'Y')->find($invoicePisah->id);
                    $editInvoicePisah->total_tagihan = $total_tagih;
                    $editInvoicePisah->total_sisa = $total_tagih;
                    $editInvoicePisah->save();

                }
            }

            DB::commit();
            return redirect()->route('belum_invoice.index')
                    ->with('id_print_invoice', $invoice->id)
                    ->with('id_print_invoice_pisah', isset($invoicePisah->id)? $invoicePisah->id:null)
                    ->with(['status' => 'Success', 'msg' => 'Pembuatan Invoice Berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
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
    public function edit($id)
    {
        try {
            $data = Sewa::where('sewa.id_sewa', $id)
                ->leftJoin('supplier AS sp', 'sewa.id_supplier', '=', 'sp.id')
                ->where('sewa.status', 'MENUNGGU INVOICE')
                ->where('sewa.is_aktif', '=', 'Y')
                ->select('sewa.*','sp.nama as namaSupplier')
                ->first();

            $checkLTL = false;
            
            if ($data->jenis_tujuan == 'LTL') {
                $checkLTL = true; 
            }

            $bank = KasBank::where('is_aktif', 'Y')->get();
        
            //ini buat yang di dalem modal
            $dataSewa = Sewa::leftJoin('grup as g', 'g.id', 'id_grup_tujuan')
                    ->leftJoin('customer as c', 'c.id', 'id_customer')
                    ->where('c.grup_id', $data->getTujuan->grup_id)
                    ->where('sewa.is_aktif', '=', 'Y')
                    ->where('sewa.status', 'MENUNGGU INVOICE')
                    ->select('sewa.*')
                    ->get();

            $dataCust = Customer::where('grup_id', $data->getTujuan->grup_id)
                    ->where('is_aktif', 'Y')
                    ->get();

            return view('pages.invoice.belum_invoice.edit',[
                'judul'=>"EDIT BELUM INVOICE",
                'data' => $data,
                'bank' => $bank,
                'dataSewa' => $dataSewa,
                'dataCust' => $dataCust,
                'grup' => $data->getTujuan->grup_id,
                'customer' => $data->id_customer,
                'checkLTL'=> $checkLTL
            ]);
            
        } catch (\Throwable $th) {
            return redirect()->route('belum_invoice.index')
                    ->with(['status' => 'Gagal', 'msg' => 'Tidak ada sewa yang terpilih!']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);

        try {
            foreach ($data['detail'] as $key => $value) {
                // data addcost lama
                $dataAddcost = json_decode($value['addcost_details']);
                foreach ($dataAddcost as $i => $addcost) {
                    $sewa_oprs = SewaOperasional::where('is_aktif', 'Y')
                                                ->where('id_sewa', $addcost->id_sewa)
                                                ->find($addcost->id);

                    if($sewa_oprs){
                        if($sewa_oprs->total_dicairkan != $addcost->total_dicairkan){
                            $pembayaran = SewaOperasionalPembayaran::where('is_aktif', 'Y')->find($sewa_oprs->id_pembayaran);
                            $pembayaran->total_dicairkan = $addcost->total_dicairkan;
                            $pembayaran->updated_by = $user;
                            $pembayaran->updated_at = now();
                            if($pembayaran->save()){
                                // rollback transaksi lama
                                $transaction = KasBankTransaction::where('is_aktif', 'Y')
                                                    ->where('jenis', 'pencairan_operasional')
                                                    ->where('keterangan_kode_transaksi', $pembayaran->id)
                                                    ->first();
                                if($transaction){
                                    $transaction->is_aktif = 'N';
                                    $transaction->updated_by = $user;
                                    $transaction->updated_at = now();
                                    if($transaction->save()){
                                        $saldo = KasBank::where('is_aktif', 'Y')->find($transaction->id_kas_bank);
                                        if($saldo){
                                            $saldo->saldo_sekarang += $transaction->kredit;
                                            $saldo->updated_by = $user;
                                            $saldo->updated_at = now();
                                            $saldo->save();
                                        }
                                    }
                                }

                                // buat data transaction baru
                                $keterangan_transaksi = substr($transaction->keterangan_transaksi, 0, 9) == "REVISI - "? $transaction->keterangan_transaksi: 'REVISI - '.$transaction->keterangan_transaksi;
                                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['bank'], //id kas_bank dr form
                                        now(), //tanggal
                                        0, //debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        $addcost->total_dicairkan, //uang keluar (kredit)
                                        CoaHelper::DataCoa(5007), //kode coa
                                        'pencairan_operasional',
                                        $keterangan_transaksi, //keterangan_transaksi
                                        $pembayaran->id, //keterangan_kode_transaksi // id_sewa_operasional_pembayaran
                                        $user, //created_by
                                        now(), //created_at
                                        $user, //updated_by
                                        now(), //updated_at
                                        'Y'
                                    ) 
                                );
            
                                $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                                $saldo->saldo_sekarang -= $addcost->total_dicairkan;
                                $saldo->updated_by = $user;
                                $saldo->updated_at = now();
                                $saldo->save();
                            }
                        }
                        $sewa_oprs->total_dicairkan = $addcost->total_dicairkan;
                        $sewa_oprs->is_ditagihkan = $addcost->is_ditagihkan;
                        $sewa_oprs->is_dipisahkan = $addcost->is_dipisahkan;
                        $sewa_oprs->catatan = $addcost->catatan;
                        $sewa_oprs->updated_by = $user;
                        $sewa_oprs->updated_at = now();
                        $sewa_oprs->save();
                    }
                }

                // data addcost baru
                $new_addcosts = json_decode($value['addcost_baru']);
                if($new_addcosts != null){
                    if($data['bank'] == null){
                        db::rollBack();
                        return redirect()->route('belum_invoice.index')->with(['status' => 'error', 'msg' => 'Harap isi bank dahulu untuk pencairan!']);
                    }

                    foreach ($new_addcosts as $new_addcost) {
                        $pembayaran = new SewaOperasionalPembayaran();
                        $pembayaran->deskripsi = $new_addcost->deskripsi;
                        $pembayaran->total_operasional = $new_addcost->total_dicairkan;
                        $pembayaran->total_dicairkan = $new_addcost->total_dicairkan;
                        $pembayaran->created_by = $user;
                        $pembayaran->created_at = now();
                        $pembayaran->save();

                        $sewa_oprs = new SewaOperasional();
                        $sewa_oprs->id_sewa = $new_addcost->id_sewa;
                        $sewa_oprs->id_pembayaran = $pembayaran->id;
                        $sewa_oprs->deskripsi = $new_addcost->deskripsi;
                        $sewa_oprs->total_operasional = $new_addcost->total_dicairkan;
                        $sewa_oprs->total_dicairkan = $new_addcost->total_dicairkan;
                        $sewa_oprs->tgl_dicairkan = now();
                        $sewa_oprs->is_ditagihkan = $new_addcost->is_ditagihkan;
                        $sewa_oprs->is_dipisahkan = $new_addcost->is_dipisahkan;
                        $sewa_oprs->catatan = $new_addcost->catatan;
                        $sewa_oprs->status = 'SUDAH DICAIRKAN';
                        $sewa_oprs->created_by = $user;
                        $sewa_oprs->created_at = now();
                        $sewa_oprs->save();

                        $deskripsi = $new_addcost->deskripsi . ' : ' . $value['nama_tujuan'] . ' #' . $value['driver'];
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['bank'], // id kas_bank dr form
                                now(), //tanggal
                                0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                $new_addcost->total_dicairkan, //uang keluar (kredit)
                                1015, //kode coa
                                'pencairan_operasional',
                                $deskripsi, // keterangan_transaksi
                                $pembayaran->id, //keterangan_kode_transaksi // id_pembayaran
                                $user, //created_by
                                now(), //created_at
                                $user, //updated_by
                                now(), //updated_at
                                'Y'
                            ) 
                        );

                        $saldo = KasBank::where('is_aktif', 'Y')->find($data['bank']);
                        $saldo->saldo_sekarang -= $new_addcost->total_dicairkan;
                        $saldo->updated_by = $user;
                        $saldo->updated_at = now();
                        $saldo->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('belum_invoice.index')->with(['status' => 'Success', 'msg'  => 'Edit data berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('belum_invoice.index')->with(['status' => 'error', 'msg' => 'Edit data gagal!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $belum_invoice)
    {
        //
    }
    public function print($id)
    {
        $data = Invoice::where('is_aktif', '=', "Y")->with('invoiceDetails', 'invoiceDetails.invoiceDetailsAddCost.sewaOperasional')->find($id);
        if($data == null){
            return redirect()->route('cetak_invoice.index')->with(['status' => 'Error', 'msg'  => 'Data tidak ditemukan!']);
        }
        // dd(substr($data->no_invoice, -2));
        // dd(strlen($data->no_invoice));

        $qrcode = QrCode::size(150)
        // ->backgroundColor(255, 0, 0, 25)
        ->generate(
            'No. Invoice: ' . $data->no_invoice . "\n" .
            'Total tagihan: ' .'Rp.' .number_format($data->total_tagihan,2) 
        );
        // dd($qrcode);
        $pdf = Pdf::loadView('pages.invoice.belum_invoice.print',[
            'judul' => "Invoice",
            'data' => $data,
            'qrcode'=>$qrcode,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 250, // Set a high DPI for better resolution
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);

        return $pdf->stream($data->no_invoice.'.pdf'); 
    }

    public function printGabung($no_invoice)
    {
        $no_invoice_split =explode("/",$no_invoice);
        $data = Invoice::where('is_aktif', '=', "Y")
            ->where('no_invoice', $no_invoice_split[2])
            ->get();
        dd($data);
        // $dataInvoiceAddCost = InvoiceDetailAddcost::where('is_aktif', '=', "Y")
        //     ->where('id_invoice', $id)
        //     ->get();

        $arrIdOperasional=[];
        foreach ($data->invoiceDetailsCost as $key => $value) {
            # code...
            array_push( $arrIdOperasional, $value->id_sewa_operasional);
        }
        $dataOperasional = SewaOperasional::where('is_aktif', '=', "Y")
        ->whereIn('id', $arrIdOperasional)
        // ->groupBy('deskripsi') // Group by 'deskripsi'
        // ->selectRaw('*, SUM(total_operasional) as total')
        ->selectRaw('*, total_operasional as total')

        ->get();
        // dd($arrIdOperasional);
        // dd($data->invoiceDetailsCost);
        // dd($dataOperasional);

        // dd($dataInvoiceAddCost->sewaOperasionalDetail);

        $TotalBiayaRev = 0;
        // dd($data);
        $qrcode = QrCode::size(150)
        // ->backgroundColor(255, 0, 0, 25)
        ->generate(
             'No. Invoice: ' . $data->no_invoice . "\n" .
             'Total tagihan: ' .'Rp.' .number_format($data->total_tagihan,2) 
        );
        // dd($qrcode);
        // dd($dataJoDetail);   
        $pdf = Pdf::loadView('pages.invoice.belum_invoice.print',[
            'judul' => "Invoice",
            'data' => $data,
            'qrcode'=>$qrcode,
            'dataOperasional'=>$dataOperasional

        ]);
        
        $pdf->setPaper('A4', 'portrait');
 
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, // Enable HTML5 parser
            'isPhpEnabled' => true, // Enable inline PHP execution
            'defaultFont' => 'sans-serif',
             'dpi' => 250, // Set a high DPI for better resolution
             'chroot' => public_path('/img') // harus tambah ini buat gambar kalo nggk dia unknown
        ]);

        return $pdf->stream('xxxxx'.'.pdf'); 
        // return view('pages.invoice.belum_invoice.print',[
        //     'judul'=>"Invoice",
        //     'data' => $data,
        //     'qrcode'=>$qrcode,
        //     'dataOperasional'=>$dataOperasional

        // ]);

    }
}
