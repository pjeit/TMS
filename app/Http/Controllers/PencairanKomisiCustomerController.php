<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\KasBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PencairanKomisiCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $kasBank = DB::table('kas_bank')
        ->where('is_aktif', 'Y')
        ->orderBy('nama', 'asc')
        ->get();

        $dataCustomer = DB::table('customer')
        ->where('is_aktif', 'Y')
        ->orderBy('nama', 'asc')
        ->get();

        return view('pages.finance.pencairan_komisi_customer.index',[
            'judul' => "PENCAIRAN KOMISI CUSTOMER",
            'kasBank'=>$kasBank,
            'dataCustomer'=>$dataCustomer
        ]);
    }

    public function load_data(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $customer  = $request->input('customer');

        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);

        try {
            $data = DB::table('sewa as s')
            ->select('s.*')
            ->whereNull('id_supplier') 
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status_pencairan_customer', 'BELUM DICAIRKAN')
            ->where('s.status', 'SELESAI')
            ->where('s.id_customer', $customer)
            ->where('s.total_komisi', '!=', 0)
            ->whereBetween('s.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
            // ->whereRaw("CAST(s.tanggal_berangkat AS DATE) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")
            // ->orderBy('s.tanggal_berangkat', 'DESC')
            ->get();
            return response()->json(['status'=>'success','data'=>$data]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status'=>'error','error'=>$th->getMessage()]);

        }

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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {

            DB::beginTransaction();
            $data = $request->collect();
            // dd($data);  
            // $tanggal_pencairan = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
            $arr_tampungan=[];

            foreach ($data['data'] as $value) {
                  $objTampungan = [
                        'Tujuan'=>$value['nama_tujuan'],
                        'Komisi' =>'Rp.'.number_format($value['komisi_customer']) ,
                    ];
                    array_push($arr_tampungan, $objTampungan);
                DB::table('sewa')
                ->where('id_sewa', $value['id_sewa'])
                ->where('id_customer', $data['customer'])
                ->update(array(
                        'status_pencairan_customer' =>'SUDAH DICAIRKAN',
                        // 'tanggal_pencairan_driver' =>date_format($tanggal_pencairan, 'Y-m-d'),
                        'tanggal_pencairan_customer' =>now(),
                        'updated_at'=> now(),
                        'updated_by'=> $user,
                    )
                );
            }
            $ubahKeString = [];
            foreach ($arr_tampungan as $item) {
                $ubahKeString[] = '(' . implode(', ', array_map(
                    function ($key, $value) {
                        return "\"$key\" = \"$value\"";
                    },
                    array_keys($item),
                    $item
                )) . ')';
            }

            $hasil_tampungan_string = implode('|', $ubahKeString);
            // dd($hasil_tampungan_string);

            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'],// id kas_bank dr form
                    // date_format($tanggal_pencairan, 'Y-m-d'),//tanggal
                    now(),
                    0,// uang masuk (debit)
                    floatval(str_replace(',', '', $data['total_komisi_customer'])), //uang keluar (kredit)
                    1016, //kode coa masik belum di komunikasiin
                    'komisi_customer',
                    'KOMISI CUSTOMER '.$data['valueCustomer'].
                    '# RINCIAN :'.$hasil_tampungan_string.
                    '# TOTAL KOMISI :Rp. '.$data['total_komisi_customer'],
                    $data['customer'],// nyimpen id customer buat di sewa kalo butuh di konekin ke dump
                    $user,//created_by
                    now(),//created_at
                    $user,//updated_by
                    now(),//updated_at
                    'Y'
                ) 
            );
            $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
            $kasbank->saldo_sekarang -= floatval(str_replace(',', '', $data['total_komisi_customer']));
            $kasbank->updated_by = $user;
            $kasbank->updated_at = now();
            $kasbank->save();
            DB::commit();
            return redirect()->route('pencairan_komisi_driver.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mencairkan Komisi Supir']);
        } 
        catch (\Throwable $th) {
            return redirect()->back()->withErrors($th->getMessage())->withInput();
            DB::rollBack();
            // return redirect()->route('pencairan_komisi_driver.index')->with(['status' => 'Gagal', 'msg' =>$th->getMessage()],500)->withErrors($th->getMessage())->withInput();

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $sewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
