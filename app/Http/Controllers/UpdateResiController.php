<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateResiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_UPDATE_RESI', ['only' => ['index']]);
		$this->middleware('permission:EDIT_UPDATE_RESI', ['only' => ['edit','update']]);
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.invoice.update_resi.index',[
            'judul' => "Update Resi INVOICE",
        ]);
    }

    public function store(Request $request) {
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
                return redirect()->route('update_resi.index')->with(['status' => 'Success', 'msg' => 'Update Resi berhasil!']);
            }

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('update_resi.index')->with(['status' => 'error', 'msg' => 'Update Resi Gagal!']);
        }
    }

    public function load_data() {
        $data = null;
        $data = DB::table('invoice AS i')
        ->select('i.*', 'c.id AS id_cust','c.nama AS nama_cust','g.nama_grup'
                ,'g.id as id_grup', 'i.catatan', 'c.ketentuan_bayar')
        ->leftJoin('customer AS c', 'c.id', '=', 'i.billing_to')
        ->leftJoin('grup AS g', 'g.id', '=', 'i.id_grup')
        ->where('i.is_aktif', '=', 'Y')
        ->where('i.status', 'MENUNGGU PEMBAYARAN INVOICE')
        ->whereRaw("RIGHT(i.no_invoice, 2) != '/I'") 
        ->orderBy('i.id','ASC')
        ->get();
        return $data;
    }

}
