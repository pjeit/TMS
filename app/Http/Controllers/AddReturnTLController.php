<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\Sewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Exception;
use App\Helper\SewaDataHelper;
use App\Models\SewaBiaya;

class AddReturnTLController extends Controller
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

        $data = DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('pages.finance.add_return_TL.index',[
            'judul' => "ADD RETURN TL",
            'data' => $data,
        ]);
    }

    public function cair(Request $request, $id)
    {
        dd($id);
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
        $data_sewa = Sewa::where('is_aktif', 'Y')->findOrFail($id);
        $checkTL = SewaBiaya::where('is_aktif', 'Y')
                            ->where('deskripsi', 'TL')
                            ->where('id_sewa', $id)
                            ->first();
        $dataBooking = DB::table('booking as b')
                ->select('*','b.id as idBooking')
                ->Join('customer AS c', 'b.id_customer', '=', 'c.id')
                ->Join('grup_tujuan AS gt', 'b.id_grup_tujuan', '=', 'gt.id')
                ->where('b.is_aktif', "Y")
                ->where('b.id', $data_sewa['id_booking'])
                ->orderBy('tgl_booking')
                ->whereNull('b.id_jo_detail')
                ->get();

        return view('pages.finance.add_return_TL.edit',[
            'judul' => "Edit Trucking Order",
            'data' => $data_sewa,
            'checkTL' => $checkTL,
            'datajO' => SewaDataHelper::DataJO(),
            'dataCustomer' => SewaDataHelper::DataCustomer(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataBooking' => $dataBooking,
            'dataChassis' => SewaDataHelper::DataChassis()
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
        $data = $request->post();
        dd($data);
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
