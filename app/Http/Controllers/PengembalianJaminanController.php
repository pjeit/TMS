<?php

namespace App\Http\Controllers;

use App\Models\Jaminan;
use App\Models\JobOrder;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\CoaHelper;
class PengembalianJaminanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_PENGEMBALIAN_JAMINAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_PENGEMBALIAN_JAMINAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_PENGEMBALIAN_JAMINAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_PENGEMBALIAN_JAMINAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $data = JobOrder::where('is_aktif', 'Y')->with('getDetails.getSewa', 'jaminan')
                        ->whereHas('jaminan',function ($query) {
                            $query->where('is_aktif', 'Y')
                            ->whereIn('status', ['DIBAYARKAN', 'REQUEST']);
                        })
                        ->whereHas('getDetails.getSewa',function ($query) {
                            $query->where('is_aktif', 'Y')
                            ->whereIn('status', ['MENUNGGU INVOICE', 'MENUNGGU PEMBAYARAN INVOICE', 'SELESAI PEMBAYARAN']);
                        })->get();

        $bank = KasBank::where('is_aktif', 'Y')->get();
        return view('pages.finance.pengembalian_jaminan.index',[
            'judul' => 'Pengembalian Jaminan',
            'data' => $data,
            'bank' => $bank,
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
        $data = $request->collect();
        $ket_potongan = isset($data['potongan'])? " (POTONGAN: ". $data['potongan'].")":'';
        $total = floatval(str_replace(',', '', $data['total']));
        $potongan = isset($data['potongan'])? floatval(str_replace(',', '', $data['potongan'])):NULL;
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        DB::beginTransaction(); 

        try {
            $jaminan = Jaminan::where('is_aktif', 'Y')->where('id_job_order', $data['id_jo'])->first();
            if($jaminan){
                $jaminan->tgl_kembali = now();
                $jaminan->potongan_jaminan = $potongan;
                $jaminan->nominal_kembali = $total;
                $jaminan->alasan = $data['catatan'];
                $jaminan->status = "KEMBALI";
                $jaminan->updated_by = $user;
                $jaminan->updated_at = now();
                if($jaminan->save()){
                    $kasBank = new KasBankTransaction();
                    $kasBank->id_kas_bank = $data['id_kas'];
                    $kasBank->tanggal = now();
                    $kasBank->debit = $total;
                    $kasBank->kredit = 0; 
                    $kasBank->kode_coa =  CoaHelper::DataCoa(5003); //coa biaya pelayaran
                    $kasBank->jenis = "PENGEMBALIAN_JAMINAN"; 
                    $kasBank->keterangan_transaksi = "PENGEMBALIAN JAMINAN - " .$data['catatan'] . " - " . $data['customer'] ." - ". $data['supplier'] . $ket_potongan;
                    $kasBank->keterangan_kode_transaksi = $data['id_jo'];
                    $kasBank->created_by = $user;
                    $kasBank->created_at = now();
                    $kasBank->is_aktif = "Y";
                    $kasBank->save();
                    DB::commit();
                    return redirect()->route('pengembalian_jaminan.index')->with(['status' => 'Success', 'msg' => 'Pengembalian berhasil!']);
                }
            }
            DB::rollBack();
            return redirect()->route('pengembalian_jaminan.index')->with(['status' => 'Error', 'msg' => 'Data tidak ditemukan!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('pengembalian_jaminan.index')->with(['status' => 'Error', 'msg' => 'Pengembalian gagal!']);
        }
    }

    public function request(Request $request)
    {
        $user = Auth::user()->id; 
        $data = $request->collect();
        DB::beginTransaction(); 
        try {
            $jaminan = Jaminan::where('is_aktif', 'Y')->where('id_job_order', $data['id_jo'])->first();
            if($jaminan){
                $jaminan->status = 'REQUEST';
                $jaminan->catatan = $data['catatan'];
                $jaminan->tgl_request = date_create_from_format('d-M-Y', $data['tgl_request']);
                $jaminan->updated_by = $user;
                $jaminan->updated_by = now();
                $jaminan->save();

                DB::commit();
                return redirect()->route('pengembalian_jaminan.index')->with(['status' => 'Success', 'msg' => 'Request berhasil diajukan']);
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('pengembalian_jaminan.index')->with(['status' => 'Error', 'msg' => 'Request gagal!']);
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
