<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\KasBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\PencairanKomisi;
use App\Models\PencairanKomisiDetail;
use App\Helper\CoaHelper;
class PencairanKomisiDriverController extends Controller
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

        $dataDriver = DB::table('karyawan')
        ->select('karyawan.*')
        ->distinct()
        ->Join('sewa as s', function($join) {
                    $join->on('karyawan.id', '=', 's.id_karyawan')
                    ->whereNull('s.id_supplier') 
                    ->where('s.status_pencairan_driver', 'BELUM DICAIRKAN')
                    ->where('s.total_komisi_driver', '!=', 0)
                    ->where('s.is_aktif', '=', "Y")
                    ;
                })
        ->where('karyawan.is_aktif', 'Y')
        ->where('karyawan.role_id', 5)//5 itu driver
        ->orderBy('karyawan.nama_panggilan', 'asc')
        ->get();

        return view('pages.finance.pencairan_komisi_driver.index',[
            'judul' => "PENCAIRAN KOMISI DRIVER",
            'kasBank'=>$kasBank,
            'dataDriver'=>$dataDriver
        ]);
    }

    public function load_data(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $karyawan  = $request->input('karyawan');

        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);

        try {
            $data = DB::table('sewa as s')
            ->select('s.*')
            ->whereNull('id_supplier') 
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status_pencairan_driver', 'BELUM DICAIRKAN')
            ->where('s.id_karyawan', $karyawan)
            ->where('s.total_komisi_driver', '!=', 0)
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
        DB::beginTransaction();

        try {

            $data = $request->collect();
            // dd($data);  
            // $tanggal_pencairan = date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
            $arr_tampungan=[];

            $pencairan_komisi = new PencairanKomisi();
            $pencairan_komisi->id_driver = $data['karyawan'];
            $pencairan_komisi->jenis_pencairan = 'DRIVER';
            $pencairan_komisi->total_komisi = floatval(str_replace(',', '', $data['total_komisi_driver']));
            $pencairan_komisi->total_pencairan = floatval(str_replace(',', '', $data['total_pencairan']));
            $pencairan_komisi->created_at = now();
            $pencairan_komisi->created_by = $user;
            $pencairan_komisi->is_aktif = 'Y';
            // $pencairan_komisi->save();

            if($pencairan_komisi->save())
            {
                foreach ($data['data'] as $value) {
                  $objTampungan = [
                        'Tujuan'=>$value['nama_tujuan'],
                        'Komisi' =>'Rp.'.number_format($value['komisi_driver']) ,
                    ];
                    array_push($arr_tampungan, $objTampungan);

                    $pencairan_komisi_detail = new PencairanKomisiDetail();
                    $pencairan_komisi_detail->id_pencairan_komisi  = $pencairan_komisi->id;
                    $pencairan_komisi_detail->id_sewa  =$value['id_sewa'];
                    $pencairan_komisi_detail->created_at = now();
                    $pencairan_komisi_detail->created_by = $user;
                    $pencairan_komisi_detail->is_aktif = 'Y';   
                    $pencairan_komisi_detail->save();


                    DB::table('sewa')
                    ->where('id_sewa', $value['id_sewa'])
                    ->where('id_karyawan', $data['karyawan'])
                    ->update(array(
                            'status_pencairan_driver' =>'SUDAH DICAIRKAN',
                            // 'tanggal_pencairan_driver' =>date_format($tanggal_pencairan, 'Y-m-d'),
                            'tanggal_pencairan_driver' =>now(),
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
                        floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit)
                        CoaHelper::DataCoaBank($data['pembayaran']), //kode coa dari bank mana (parameter id bank)
                        'komisi_driver',
                        'KOMISI DRIVER '.$data['valueDriver'].
                        '# RINCIAN :'.$hasil_tampungan_string.
                        '# TOTAL KOMISI :Rp. '.$data['total_komisi_driver'].
                        '# TOTAL PENCAIRAN KOMISI :Rp. '.$data['total_pencairan']
                        ,
                        $data['karyawan'],// nyimpen id karyawan buat di sewa
                        $user,//created_by
                        now(),//created_at
                        $user,//updated_by
                        now(),//updated_at
                        'Y'
                    ) 
                );
                $kasbank = KasBank::where('is_aktif', 'Y')->find($data['pembayaran']);
                $kasbank->saldo_sekarang -= floatval(str_replace(',', '', $data['total_pencairan']));
                $kasbank->updated_by = $user;
                $kasbank->updated_at = now();
                $kasbank->save();
            }
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
