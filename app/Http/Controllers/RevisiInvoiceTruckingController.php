<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoicePembayaran;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\CoaHelper;
use Exception;

class RevisiInvoiceTruckingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_REVISI_INVOICE_TRUCKING', ['only' => ['index']]);
		$this->middleware('permission:CREATE_REVISI_INVOICE_TRUCKING', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_REVISI_INVOICE_TRUCKING', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_REVISI_INVOICE_TRUCKING', ['only' => ['destroy']]);  
    }

    // sementara tak matiin, kalau memang dipakai, idupin lagi aja
    // revisi invoice ini ngefeknya kalo udah selesai dibayar soalnya, bukan yg masih belum di bayar
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $data = InvoicePembayaran::where('is_aktif', 'Y')->get();

        return view('pages.revisi.revisi_invoice_trucking.index',[
            'judul' => "Revisi Invoice Trucking",
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
        $invoice = Invoice::where('is_aktif', 'Y')->find($id);
        // dd($id);
        $cek = substr($invoice->no_invoice, -2);
        if($cek != '/I'){
            $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
        }else{
            $invoice = Invoice::where('is_aktif', 'Y')->where('no_invoice', substr($invoice->no_invoice, 0, -2))->first();
            $reimburse = Invoice::where('is_aktif', 'Y')->where('no_invoice', $invoice->no_invoice.'/I')->first();
        }
        // dd($reimburse);
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

            return view('pages.revisi.revisi_invoice_trucking.edit',[
                'judul' => "Revisi Invoice Trucking",
                'data' => $invoice,
                'reimburse' => isset($reimburse)? $reimburse:NULL,
                'dataSewa' => $dataSewa,
                'checkLTL' => $checkLTL,
                'dataCust' => $dataCust,
            ]);

        }else{
            return redirect()->route('pembayaran_invoice.index')->with(['status' => 'error', 'msg' => 'Data tidak ditemukan!']);
        }
    }

    public function editPembayaran($id)
    {
        $invoice = InvoicePembayaran::where('is_aktif', 'Y')->with('get_pembayaran_detail')->find($id);
        $customers = Customer::where('is_aktif', 'Y')->where('grup_id', $invoice->get_pembayaran_detail->get_invoice_value[0]->id_grup)->get();
        $kasbank = KasBank::where('is_aktif', 'Y')->get();
        // dd($invoice);
        $dataInvoices   = Invoice::where('billing_to', $invoice->billing_to)
        // ->whereNull('id_pembayaran')
        ->whereDoesntHave('get_invoice_pembayaran_detail')
        ->where('is_aktif', 'Y')->get();
        $dataInvoicesAll   = Invoice::where('billing_to', $invoice->billing_to)
        ->where('is_aktif', 'Y')->get();
        return view('pages.revisi.revisi_invoice_trucking.edit_pembayaran',[
            'judul' => "Revisi Pembayaran Invoice Trucking",
            'data' => $invoice,
            'customers' => $customers,
            'kasbank' => $kasbank,
            'dataInvoices' => $dataInvoices,
            'dataInvoicesAll' => $dataInvoicesAll,
        ]);
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
        $user = Auth::user()->id;
        $data = $request->collect();
        $isErr = FALSE;
        DB::beginTransaction(); 
        // dd($data);

        try {
            // nonaktifkan invoice pembayaran
            $oldPembayaran = InvoicePembayaran::where('is_aktif', 'Y')->find($id);
            // kembalikan uang ke kas bank
            $kasbank = KasBank::where('is_aktif', 'Y')->find($oldPembayaran->id_kas);
            $kasbank->saldo_sekarang -= $oldPembayaran->total_diterima;
            $kasbank->updated_by = $user; 
            $kasbank->updated_at = now();
            // $kasbank->save();
            if($kasbank->save())
            {
                if($data['detail'] != null){
                    $keterangan_transaksi = 'REVISI PEMBAYARAN INVOICE | '. $data['cara_pembayaran'] . ' | ' . $data['catatan'] . ' |';
                    $id_invoices = '';
                    $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
                    $total_pph = isset($data['total_pph'])? floatval(str_replace(',', '', $data['total_pph'])):0;
                    $i = 0;
    
                    $pembayaran = InvoicePembayaran::where('is_aktif', 'Y')->find($id);
                    $pembayaran->id_kas = $data['kas'];
                    $pembayaran->billing_to = $data['billingTo'];
                    $pembayaran->tgl_pembayaran = date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
                    $pembayaran->total_diterima = floatval(str_replace(',', '', $data['total_dibayar']));
                    $pembayaran->total_pph = $total_pph;
                    $pembayaran->biaya_admin = $biaya_admin;
                    $pembayaran->cara_pembayaran = $data['cara_pembayaran'];
                    $pembayaran->no_cek = isset($data['no_cek'])? $data['no_cek']:null;
                    $pembayaran->no_bukti_potong = $data['no_bukti_potong'];
                    $pembayaran->catatan = $data['catatan'];
                    $pembayaran->updated_by = $user;
                    $pembayaran->updated_at = now();
                    if($pembayaran->save()){
                        foreach ($data['detail'] as $key => $value) {
                            $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($key);
                            if($invoice){
                                if ($value['id_pembayaran']!="pembayaran_baru" && $value['hapus_detail_bayar']=="Y")
                                {
                                    $invoice->id_pembayaran =null;
                                    $invoice->pph = 0;
                                    $invoice->total_sisa = $invoice->total_tagihan; // dikembaliin jadi full dulu baru dikurangin lagi
                                    $invoice->total_dibayar = 0;
                                    $invoice->biaya_admin = 0;
                                    $invoice->status = 'MENUNGGU PEMBAYARAN INVOICE';
                                    $invoice->catatan = $value['catatan'];
                                    $invoice->updated_by = $user;
                                    $invoice->updated_at = now();
                                    // $invoice->save();
                                    if($invoice->save())
                                    {
                                        $invoice_detail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice',$invoice->id)->get();
                                        foreach ($invoice_detail as  $details) {
                                            $sewa = Sewa::where('is_aktif','Y')->where('id_sewa',$details->id_sewa)->first();
                                            $sewa->status = 'MENUNGGU PEMBAYARAN INVOICE';
                                            $sewa->updated_by = $user;
                                            $sewa->updated_at = now();
                                            $sewa->save();
                                            //buat yang JO ADA DI TRIGGER update_jo_selesai_dooring
                                            $cust = Customer::where('is_aktif', 'Y')->findOrFail($sewa->id_customer);
                                            if($cust){
                                                $cust->kredit_sekarang += $sewa->total_tarif;
                                                $cust->updated_by = $user;
                                                $cust->updated_at = now();
                                                $cust->save();
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $keterangan_transaksi .= ' >> '.$invoice->no_invoice;
                                    $id_invoices .= $invoice->id . ','; 
                                    $invoice->id_pembayaran = $pembayaran->id;
                                    $invoice->pph = $value['pph23'];
                                    $invoice->total_sisa = $invoice->total_tagihan; // dikembaliin jadi full dulu baru dikurangin lagi
                                    if($i == 0){
                                        $invoice->total_dibayar = $value['total_dibayar'] ;
                                        $invoice->biaya_admin = $biaya_admin;
                                        $invoice->total_sisa -= $value['total_dibayar'] + $value['pph23']+$biaya_admin;
                                        // dd($invoice->total_sisa);
                                    }else{
                                        $invoice->total_dibayar = $value['total_dibayar'];
                                        $invoice->total_sisa -= $value['total_dibayar'] + $value['pph23'];
                                    }
        
                                    if($invoice->total_sisa < 0){
                                        // dd( $value['total_dibayar']);
                                        $isErr = true; // ini error karna minus
                                    }
                                    $currentStatus = '';
                                    if($invoice->total_sisa == 0){
                                        $currentStatus = 'SELESAI PEMBAYARAN INVOICE';
                                        $invoice->status = $currentStatus;
                                    }
                                    $invoice->catatan = $value['catatan'];
                                    $invoice->updated_by = $user;
                                    $invoice->updated_at = now();
                                    $invoice->save();
                                    $i++;
                                }
                            }
                        }
                    }
                    if($isErr === true){
                        db::rollBack();
                        return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan!']);
                    }else{
                        $oldTransaction = KasBankTransaction::where([
                            'is_aktif' => 'Y',
                                'jenis' => 'invoice_customer',
                                'keterangan_kode_transaksi' => $id
                                ])->first();
                        $oldTransaction->keterangan_transaksi = 'REVISI - '. $keterangan_transaksi;
                        $oldTransaction->debit = floatval(str_replace(',', '', $data['total_dibayar'])); 
                        $oldTransaction->updated_by = $user; 
                        $oldTransaction->updated_at = now();
                        // $oldTransaction->save();
                        if($oldTransaction->save())
                        {
                            $kas_bank = KasBank::where('is_aktif','Y')->find($data['kas']);
                            $kas_bank->saldo_sekarang += floatval(str_replace(',', '', $data['total_dibayar']));
                            $kas_bank->updated_by = $user;
                            $kas_bank->updated_at = now();
                            $kas_bank->save();
                        }
                        DB::commit();
                        return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "Success", "msg" => "Berhasil Revisi Pembayaran invoice!"]);
                    }
    
                }
               
            }
            DB::commit();
            return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'Success', 'msg'  => 'Berhasil Revisi Pembayaran invoice!']);
        } 
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
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
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
            $inv_bayar = InvoicePembayaran::where('is_aktif', 'Y')->find($id);
            $inv_bayar->updated_by = $user;
            $inv_bayar->updated_at = now();
            $inv_bayar->is_aktif = 'N';
            if($inv_bayar->save()){
                $invoice = Invoice::where('is_aktif', 'Y')
                ->with('get_invoice_pembayaran_detail')
                ->whereHas('get_invoice_pembayaran_detail', function ($query) use ($id) {
                    $query->where('id_invoice_pembayaran', $id);
                })
                ->get();
                // dd($invoice);
                foreach ($invoice as $invoices) {
                    // $invoices->id_pembayaran = null;
                    $invoices->total_sisa += $invoices->get_invoice_pembayaran_detail->dibayar;
                    // $invoices->total_sisa = $invoices->total_tagihan;
                    $invoices->pph = 0;
                    $invoices->biaya_admin = 0;
                    $invoices->total_dibayar = 0;
                    $invoices->status = 'MENUNGGU PEMBAYARAN INVOICE';
                    $invoices->updated_by = $user;
                    $invoices->updated_at = now();
                    // $detail->save();
                    if($invoices->save())
                    {
                        // matiin pembayaran detail
                        $invoices->get_invoice_pembayaran_detail->is_aktif='N';
                        $invoices->get_invoice_pembayaran_detail->save();
                        // end matiin pembayaran detail

                        $invoice_detail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice',$invoices->id)->get();
                        foreach ($invoice_detail as  $details) {
                            $sewa = Sewa::where('is_aktif','Y')->where('id_sewa',$details->id_sewa)->first();
                            $sewa->status = 'MENUNGGU PEMBAYARAN INVOICE';
                            $sewa->updated_by = $user;
                            $sewa->updated_at = now();
                            $sewa->save();
                            //buat yang JO ADA DI TRIGGER update_jo_selesai_dooring

                            $cust = Customer::where('is_aktif', 'Y')->findOrFail($sewa->id_customer);
                            if($cust){
                                $cust->kredit_sekarang += $sewa->total_tarif;
                                $cust->updated_by = $user;
                                $cust->updated_at = now();
                                $cust->save();
                            }
                        }

                    }
                }
                $history = KasBankTransaction::where('is_aktif','Y')
                ->where('keterangan_kode_transaksi', $id)
                ->where('jenis', 'invoice_customer')
                ->first();
                $history->keterangan_transaksi = 'HAPUS - ' . isset($history->keterangan_transaksi)? $history->keterangan_transaksi:'';
                $history->is_aktif = 'N';
                $history->updated_by = $user;
                $history->updated_at = now();
                if($history->save()){
                    // kembalikan kasbank sekarang
                    $returnKas = KasBank::where('is_aktif','Y')->find($inv_bayar->id_kas);
                    $returnKas->saldo_sekarang += floatval(str_replace(',', '', $history['kredit']));
                    $returnKas->updated_by = $user;
                    $returnKas->updated_at = now();
                    $returnKas->save();
                }
            }
            DB::commit();
            return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'Success', 'msg' => 'Pembayaran Invoice berhasil dihapus!']);

        } catch (\Throwable $th) {
            //throw $th;
            db::rollBack();
            return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
            
        }
    }

    public function load_data(Request $request)
    {
        if ($request->ajax()) {
            $data =  InvoicePembayaran::latest()->with('getInvoices.getGroup', 'getBillingTo')->where('is_aktif', 'Y')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('billing_to', function($item){ // edit supplier
                    return $item->getBillingTo->nama;
                }) 
                ->addColumn('customer', function($row){ 
                    $customer_array = [];
                    $customer = '';
                    
                    foreach ($row->getInvoices as $key => $value) {
                        foreach ($value->invoiceDetails as $key => $value) {
                            $newValue = $value->sewa->getCustomer->nama;
                            if (array_search($newValue, $customer_array) === false) {
                                array_push($customer_array, $newValue);
                            }
                        } 
                    }
                    foreach ($customer_array as $key => $value) {
                        $customer .=  ' <small class="font-weight-bold">#' .$value .'</small>' . '<br>';
                    }
                    return substr($customer, 1);
                })
                ->editColumn('tgl_pembayaran', function($item){ // edit supplier
                    return date("d-M-Y", strtotime($item->tgl_pembayaran));
                })
                ->addColumn('no_invoice', function($row){ // edit supplier
                    $customer = '';
                    foreach ($row->getInvoices as $key => $value) {
                        $customer .=  ' <small class="font-weight-bold">#' .$value->no_invoice .'</small>' . '<br>';
                    } 
                    return substr($customer, 1);
                })
                ->editColumn('total_diterima', function($item){ // edit format uang
                    return number_format($item->total_diterima);
                }) 
                ->addColumn('action', function($row){
                     // $actionBtn = '
                    //             <div class="btn-group dropleft">
                    //                 <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    //                     <i class="fa fa-list"></i>
                    //                 </button>
                    //                 <div class="dropdown-menu" >
                    //                     <a href="/revisi_invoice_trucking/edit-pembayaran/'.$row->id.'" class="dropdown-item edit">
                    //                         <span class="fas fa-pencil-alt mr-3"></span> Edit 
                    //                     </a>
                    //                 </div>
                    //             </div>';
                    //                 // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                    //                 // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                    // return $actionBtn;
                    $edit=auth()->user()->can('EDIT_REVISI_INVOICE_TRUCKING')?'<a href="/revisi_invoice_trucking/edit-pembayaran/'.$row->id.'" class="dropdown-item edit">
                                            <span class="fas fa-pencil-alt mr-3"></span> Edit 
                                        </a>':'';
                    $delete=auth()->user()->can('DELETE_PEMBAYARAN_INVOICE')?'<a href="/revisi_invoice_trucking/'.$row->id.'" class="dropdown-item edit" data-confirm-delete="true">
                    <span class="fas fa-trash mr-3"></span> Delete
                    </a>':'';
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        '.$edit. $delete.'
                                    </div>
                                </div>';
                                    // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                                    // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'tgl_pembayaran', 'customer', 'no_invoice']) // ini buat render raw html, kalo ga pake nanti jadi text biasa
                ->make(true);
        }
    }

    // public function updateOld(Request $request, $id)
    // {
    //     $user = Auth::user()->id;
    //     $data = $request->collect();
    //     $isErr = FALSE;
    //     DB::beginTransaction(); 
    //     // dd($data);

    //     try {
    //         // nonaktifkan invoice pembayaran
    //         $oldPembayaran = InvoicePembayaran::where('is_aktif', 'Y')->find($id);
    //         // dd($oldPembayaran->total_diterima );
    //         $oldPembayaran->updated_by = $user; 
    //         $oldPembayaran->updated_at = now();
    //         $oldPembayaran->catatan = 'REVISI - '. $data['catatan'];
    //         // $oldPembayaran->is_aktif = 'N';
    //         $oldPembayaran->save();
    //         if($oldPembayaran->save())
    //         {
                
    //         }

    //         // nonaktifkan kas bank transaction
    //         $oldTransaction = KasBankTransaction::where([
    //                                                     'is_aktif' => 'Y',
    //                                                     'jenis' => 'invoice_customer',
    //                                                     'keterangan_kode_transaksi' => $id
    //                                                     ])->first();
    //         $oldTransaction->keterangan_transaksi = 'REVISI - '. $oldTransaction->keterangan_transaksi;
    //         $oldTransaction->updated_by = $user; 
    //         $oldTransaction->updated_at = now();
    //         $oldTransaction->is_aktif = 'N';
    //         $oldTransaction->save();
            
    //         // kembalikan uang ke kas bank
    //         $kasbank = KasBank::where('is_aktif', 'Y')->find($oldTransaction->id_kas_bank);
    //         $kasbank->saldo_sekarang -= $oldTransaction->debit;
    //         $kasbank->updated_by = $user; 
    //         $kasbank->updated_at = now();
    //         $kasbank->save();

    //         // // kembalikan kredit customer
    //         // $cust = Customer::where('is_aktif', 'Y')->findOrFail($data['billingTo']);
    //         // if($cust){
    //         //     $cust->kredit_sekarang += $oldTransaction->debit;
    //         //     $cust->updated_by = $user;
    //         //     $cust->updated_at = now();
    //         //     $cust->save();
    //         // }

    //         // dd($kasbank);

    //         if($data['detail'] != null){
    //             $keterangan_transaksi = 'REVISI PEMBAYARAN INVOICE | '. $data['cara_pembayaran'] . ' | ' . $data['catatan'] . ' |';
    //             $id_invoices = '';
    //             $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
    //             $total_pph = isset($data['total_pph'])? floatval(str_replace(',', '', $data['total_pph'])):0;
    //             $i = 0;

    //             $pembayaran = new InvoicePembayaran();
    //             $pembayaran->id_kas = $data['kas'];
    //             $pembayaran->billing_to = $data['billingTo'];
    //             $pembayaran->tgl_pembayaran = date_create_from_format('d-M-Y', $data['tanggal_pembayaran']);
    //             $pembayaran->total_diterima = floatval(str_replace(',', '', $data['total_dibayar']));
    //             $pembayaran->total_pph = $total_pph;
    //             $pembayaran->biaya_admin = $biaya_admin;
    //             $pembayaran->cara_pembayaran = $data['cara_pembayaran'];
    //             $pembayaran->no_cek = isset($data['no_cek'])? $data['no_cek']:null;
    //             $pembayaran->no_bukti_potong = $data['no_bukti_potong'];
    //             $pembayaran->catatan = $data['catatan'];
    //             $pembayaran->created_by = $user;
    //             $pembayaran->created_at = now();
    //             if($pembayaran->save()){
    //                 foreach ($data['detail'] as $key => $value) {
    //                     $invoice = Invoice::where('is_aktif', 'Y')->findOrFail($key);

    //                     $keterangan_transaksi .= ' #'.$invoice->no_invoice;
    //                     $id_invoices .= $invoice->id . ','; 

    //                     if($invoice){
    //                         $invoice->id_pembayaran = $pembayaran->id;
    //                         $invoice->pph = $value['pph23'];
    //                         $invoice->total_sisa = $invoice->total_tagihan; // dikembaliin jadi full dulu baru dikurangin lagi
    //                         if($i == 0){
    //                             $invoice->total_dibayar = $value['total_dibayar'] - $biaya_admin;
    //                             $invoice->biaya_admin = $biaya_admin;
    //                             $invoice->total_sisa -= $value['total_dibayar'] + $value['pph23']+$biaya_admin;
    //                         }else{
    //                             $invoice->total_dibayar = $value['total_dibayar'];
    //                             $invoice->total_sisa -= $value['total_dibayar'] + $value['pph23'];
    //                         }
    //                         // dd($value['total_dibayar']);
    //                         // dd($value['pph23']);
    //                         // dd($biaya_admin);

    //                         // dd($invoice->total_sisa);
    //                         // dd($invoice->total_dibayar);


    //                         if($invoice->total_sisa < 0){
    //                             $isErr = true; // ini error karna minus
    //                         }
    //                         $currentStatus = '';
    //                         if($invoice->total_sisa == 0){
    //                             $currentStatus = 'SELESAI PEMBAYARAN INVOICE';
    //                             $invoice->status = $currentStatus;
    //                         }
    //                         $invoice->catatan = $value['catatan'];
    //                         $invoice->updated_by = $user;
    //                         $invoice->updated_at = now();
    //                         if($invoice->save()){
    //                             // if($currentStatus == 'SELESAI PEMBAYARAN INVOICE'){
    //                             //     $invoiceDetail = InvoiceDetail::where('is_aktif', 'Y')->where('id_invoice', $invoice->id)->get();
    //                             //     if($invoiceDetail){
    //                             //         foreach ($invoiceDetail as $i => $item) {
    //                             //             $check = InvoiceDetail::leftJoin('invoice', 'invoice.id', '=', 'invoice_detail.id_invoice')
    //                             //                                     ->where('invoice_detail.is_aktif', 'Y')
    //                             //                                     ->where('invoice.status', 'MENUNGGU PEMBAYARAN INVOICE')
    //                             //                                     ->where('id_sewa', $item->id_sewa)->get();
    //                             //             // ini ngecek
    //                             //             // apakah masih ada invoice yg statusnya masih menunggu pembayaran?
    //                             //             // kalau tidak ada, berarti invoice sudah dibayar lunas semua
    //                             //             // kalau dibayar lunas semua, kita lanjut update status sewa sama update kredit customer
    //                             //             if($check->isEmpty()) {
    //                             //                 $updateSewa = Sewa::where('is_aktif', 'Y')->find($item->id_sewa);
    //                             //                 $updateSewa->status = 'SELESAI PEMBAYARAN';
    //                             //                 $updateSewa->updated_by = $user;
    //                             //                 $updateSewa->updated_at = now();
    //                             //                 $updateSewa->save();

    //                             //                 // trigger update status jo detail jika semua sewa sudah selesai 
    //                             //                 // trigger update status jo jika semua jo detail sudah selesai 

    //                             //                 // rubah kredit customer
    //                             //                 // cari data kredit customer berdasarkan sewa yg ada, lalu dikurangi biaya tarif sewanya
    //                             //                 // dengan cara ini kredit customer bakal match, nambah berapa dan berkurang berapa
    //                             //                 // ini kredit customer berdasarkan sewa, jadi meski di invoice billing to dirubah2, 
    //                             //                 // tetep sewa itu bakal yg dikurangi, bukan kredit customer yg di billing to
    //                             //                 $cust = Customer::where('is_aktif', 'Y')->findOrFail($updateSewa['id_customer']);
    //                             //                 if($cust){
    //                             //                     $kredit_sekarang = $cust->kredit_sekarang - $updateSewa->total_tarif;
    //                             //                     $cust->kredit_sekarang = $kredit_sekarang;
    //                             //                     $cust->updated_by = $user;
    //                             //                     $cust->updated_at = now();
    //                             //                     $cust->save();
    //                             //                 }
    //                             //             }
    //                             //         }
    //                             //     }
    //                             // }
    //                         }else{
    //                             $isErr = true;
    //                         }
    //                     }
    //                     $i++;
    //                 }
    //             }

    //             // dump data ke dump transaction
    //             $total_bayar = (float)str_replace(',', '', $data['total_dibayar']);
    //             DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
    //                 array(
    //                     $data['kas'],// id kas_bank dr form
    //                     now(),//tanggal
    //                     $total_bayar, //uang masuk (debit)
    //                     0,// kredit 0 soalnya kan ini uang masuk
    //                     CoaHelper::DataCoa(1100), //kode coa invoice
    //                     'pembayaran_invoice',
    //                     $keterangan_transaksi, //keterangan_transaksi
    //                     $pembayaran->id, // keterangan_kode_transaksi - id pembayaran
    //                     $user,//created_by
    //                     now(),//created_at
    //                     $user,//updated_by
    //                     now(),//updated_at
    //                     'Y'
    //                 ) 
    //             );
    //             $kas_bank = KasBank::where('is_aktif','Y')->find($data['kas']);
    //             $kas_bank->saldo_sekarang += floatval(str_replace(',', '', $data['total_dibayar']));
    //             $kas_bank->updated_by = $user;
    //             $kas_bank->updated_at = now();
    //             $kas_bank->save();

    //             // $cust = Customer::where('is_aktif', 'Y')->findOrFail($data['billingTo']);
    //             // if($cust){
    //             //     $kredit_sekarang = $cust->kredit_sekarang - $total_bayar;
    //             //     if($kredit_sekarang < 0){
    //             //         $isErr = true;
    //             //         // $kredit_sekarang = 0;
    //             //     }
    //             //     $cust->kredit_sekarang = $kredit_sekarang;
    //             //     $cust->updated_by = $user;
    //             //     $cust->updated_at = now();
    //             //     $cust->save();
    //             // }

    //             if($isErr === true){
    //                 db::rollBack();
    //                 return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan!']);
    //             }else{
    //                 DB::commit();
    //                 return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "Success", "msg" => "Berhasil Membayar invoice!"]);
    //             }

    //         }

    //         DB::commit();
    //         return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
    //     } catch (ValidationException $e) {
    //         db::rollBack();
    //         return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
    //     }
    //     catch (\Throwable $th) {
    //         db::rollBack();
    //         return redirect()->route('revisi_invoice_trucking.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
    //     }
    // }
}
