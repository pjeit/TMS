<?php

namespace App\Http\Controllers;

use App\Models\InvoicePembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RevisiInvoiceTruckingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    public function load_data(Request $request)
    {
        if ($request->ajax()) {
            $data =  InvoicePembayaran::latest()->with('getInvoice.getGroup', 'getBillingTo')->where('is_aktif', 'Y')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('billing_to', function($item){ // edit supplier
                    return $item->getBillingTo->nama;
                }) 
                ->addColumn('grup', function($row){ // tambah kolom baru
                    return $row->getInvoice->getGroup->nama_grup;
                })
                ->addColumn('customer', function($row){ // tambah kolom baru
                    $customer = '';
                    foreach ($row->getInvoice->invoiceDetails as $key => $value) {
                        $customer .=  ' <small class="font-weight-bold">#' .$value->sewa->getCustomer->nama .'</small>' . '<br>';
                    } 
                    return substr($customer, 1);
                })
                ->editColumn('tgl_pembayaran', function($item){ // edit supplier
                    return date("d-M-Y", strtotime($item->tgl_pembayaran));
                })
                ->editColumn('total_diterima', function($item){ // edit format uang
                    return number_format($item->total_diterima);
                }) 
                ->addColumn('action', function($row){
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <a href="/revisi_invoice_trucking/'.$row->id.'/edit" class="dropdown-item edit">
                                            <span class="fas fa-pen-alt mr-3"></span> Edit 
                                        </a>
                                        <button type="button" class="dropdown-item delete" value="'.$row->id.'">
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </button>
                                    </div>
                                </div>';
                                    // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                                    // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'tgl_pembayaran', 'customer']) // ini buat render raw html, kalo ga pake nanti jadi text biasa
                ->make(true);
        }
    }
}
