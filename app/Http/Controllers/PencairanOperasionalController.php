<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helper\VariableHelper;
use App\Models\Customer;
use App\Models\Grup;
use App\Models\Jaminan;
use App\Models\JobOrder;
use App\Models\JobOrderDetail;
use App\Models\Sewa;
use App\Models\SewaOperasional;
use App\Http\Controllers\Builder;

class PencairanOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sewa::select('sewa.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 'sewa.id_customer')
                ->leftJoin('grup_tujuan AS gt', 'sewa.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 'sewa.id_karyawan', '=', 'k.id')
                ->where('sewa.is_aktif', '=', 'Y')
                ->where('sewa.jenis_tujuan', 'FTL')
                ->whereNull('sewa.id_supplier')
                ->where('sewa.status', "MENUNGGU OPERASIONAL")
                ->orderBy('c.id','ASC')
                ->get();
                // ->groupBy('c.nama');
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.finance.pencairan_operasional.index',[
            'judul' => "Pencairan Operasional",
            'data' => $data,
        ]);
    }

    public function pencairan($id)
    {
        $grup = Grup::where('is_aktif', 'Y')->findOrFail($id);
        $data = SewaOperasional::select('sewa_operasional.*', 's.id_customer as id_custoemr', 's.id_jo as id_jo')
            ->leftJoin('sewa as s', function($join) use($id){
                $join->on('sewa_operasional.id_sewa', '=', "s.id_sewa")
                    ->where('s.is_aktif', "Y");
            })
            ->leftJoin('customer as c', function($join) use($id){
                $join->on('c.id', '=', "s.id_customer")
                    ->where('c.is_aktif', "Y");
            })
            ->where('c.grup_id', $id)
            ->where('sewa_operasional.is_aktif', "Y")
            ->groupBy('sewa_operasional.deskripsi')
            ->get();
            
        $customers = Customer::where('grup_id', $id)->get();
            
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
        
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.finance.pencairan_operasional.pencairan',[
            'judul'=>"Pencairan Operasional || " . $grup->nama_grup,
            'dataKas' => $dataKas,
            'customers' => $customers,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,

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
        $data = $request->post();
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        // dd($data);
        DB::beginTransaction(); 
        try {
            foreach ($data['detail'] as $key => $value) {
                if($value['total_dicairkan'] != null){
                    $sewaOprs = SewaOperasional::where('is_aktif', 'Y')->find($key);
                    $sewaOprs->total_dicairkan = floatval(str_replace(',', '', $value['total_dicairkan']));
                    $sewaOprs->catatan = $value['catatan'];
                    $sewaOprs->tgl_dicairkan = date_create_from_format('d-M-Y', $data['tgl_dicairkan']);
                    $sewaOprs->status = 'SUDAH DICAIRKAN';
                    $sewaOprs->updated_by = $user;
                    $sewaOprs->updated_at = now();
                    $sewaOprs->save();

                    // execute trigger update status sewa = PROSES DOORING

                    $saldo = DB::table('kas_bank')
                        ->select('*')
                        ->where('is_aktif', '=', "Y")
                        ->where('kas_bank.id', '=', $data['pembayaran'])
                        ->get();

                    $saldo_baru = $saldo[0]->saldo_sekarang - ( floatval(str_replace(',', '', $value['total_dicairkan'])) );

                    DB::table('kas_bank')
                        ->where('id', $data['pembayaran'])
                        ->update(array(
                            'saldo_sekarang' => $saldo_baru,
                            'updated_at'=> now(),
                            'updated_by'=> $user,
                        )
                    );

                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        array(
                            $data['pembayaran'],// id kas_bank dr form
                            now(),//tanggal
                            0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                            floatval(str_replace(',', '', $value['total_dicairkan'])), //uang keluar (kredit)
                            1015, //kode coa
                            'pencairan_operasional',
                            'PENCAIRAN '.$value['jenis'], //keterangan_transaksi
                            $key,//keterangan_kode_transaksi
                            $user,//created_by
                            now(),//created_at
                            $user,//updated_by
                            now(),//updated_at
                            'Y'
                        ) 
                    );
                }
            }
            DB::commit();
            return redirect()->route('pencairan_operasional.index')->with('status', "Success!");
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
}
