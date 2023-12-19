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
        $invoice = InvoicePembayaran::where('is_aktif', 'Y')->with('getInvoices')->find($id);
        $customers = Customer::where('is_aktif', 'Y')->where('grup_id', $invoice->getInvoices[0]->id_grup)->get();
        $kasbank = KasBank::where('is_aktif', 'Y')->get();
        // dd($invoice);
        return view('pages.revisi.revisi_invoice_trucking.edit_pembayaran',[
            'judul' => "Revisi Pembayraan Invoice Trucking",
            'data' => $invoice,
            'customers' => $customers,
            'kasbank' => $kasbank,
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
            $oldPembayaran->updated_by = $user; 
            $oldPembayaran->updated_at = now();
            $oldPembayaran->catatan = 'REVISI - '. $oldPembayaran->catatan;
            $oldPembayaran->is_aktif = 'N';
            $oldPembayaran->save();

            // nonaktifkan kas bank transaction
            $oldTransaction = KasBankTransaction::where([
                                                        'is_aktif' => 'Y',
                                                        'jenis' => 'BAYAR INVOICE',
                                                        'keterangan_kode_transaksi' => $id
                                                        ])->first();
            $oldTransaction->keterangan_transaksi = 'REVISI - '. $oldTransaction->keterangan_transaksi;
            $oldTransaction->updated_by = $user; 
            $oldTransaction->updated_at = now();
            $oldTransaction->is_aktif = 'N';
            $oldTransaction->save();
            
            // kembalikan uang ke kas bank
            $kasbank = KasBank::where('is_aktif', 'Y')->find($oldTransaction->id_kas_bank);
            $kasbank->saldo_sekarang -= $oldTransaction->debit;
            $kasbank->updated_by = $user; 
            $kasbank->updated_at = now();
            $kasbank->save();

            // kembalikan kredit customer
            $cust = Customer::where('is_aktif', 'Y')->findOrFail($data['billingTo']);
            if($cust){
                $cust->kredit_sekarang += $oldTransaction->debit;
                $cust->updated_by = $user;
                $cust->updated_at = now();
                $cust->save();
            }

            // dd($kasbank);

            if($data['detail'] != null){
                $keterangan_transaksi = 'REVISI PEMBAYARAN INVOICE | '. $data['cara_pembayaran'] . ' | ' . $data['catatan'] . ' |';
                $id_invoices = '';
                $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
                $total_pph = isset($data['total_pph23'])? floatval(str_replace(',', '', $data['total_pph23'])):0;
                $i = 0;

                $pembayaran = new InvoicePembayaran();
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
                                $invoice->total_dibayar = $value['total_dibayar'] - $biaya_admin;
                                $invoice->biaya_admin = $biaya_admin;
                            }else{
                                $invoice->total_dibayar = $value['total_dibayar'];
                            }
                            $invoice->total_sisa = $invoice->total_tagihan; // dikembaliin jadi full dulu baru dikurangin lagi
                            $invoice->total_sisa -= $value['total_dibayar'] + $value['pph23'];
                            if($invoice->total_sisa < 0){
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
                            if($invoice->save()){
                            }else{
                                $isErr = true;
                            }
                        }
                        $i++;
                    }
                }

                // dump data ke dump transaction
                $total_bayar = (float)str_replace(',', '', $data['total_dibayar']);
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    array(
                        $data['kas'],// id kas_bank dr form
                        now(),//tanggal
                        $total_bayar, //uang masuk (debit)
                        0,// kredit 0 soalnya kan ini uang masuk
                        CoaHelper::DataCoa(1100), //kode coa invoice
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
                $kas_bank = KasBank::where('is_aktif','Y')->find($data['kas']);
                $kas_bank->saldo_sekarang += floatval(str_replace(',', '', $data['total_dibayar']));
                $kas_bank->updated_by = $user;
                $kas_bank->updated_at = now();
                $kas_bank->save();

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
                    return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan!']);
                }else{
                    DB::commit();
                    return redirect()->route('revisi_invoice_trucking.index')->with(["status" => "Success", "msg" => "Berhasil Membayar invoice!"]);
                }

            }

            DB::commit();
            return redirect()->route('controller.method')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('controller.method')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        '.$edit.'
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
}
