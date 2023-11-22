<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KasBank;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Illuminate\Support\Facades\Auth;
use DataTables;

class KasBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_KASBANK', ['only' => ['index']]);
		$this->middleware('permission:CREATE_KASBANK', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_KASBANK', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_KASBANK', ['only' => ['destroy']]);  
    }

    public function index()
    {
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
            return view('pages.master.kas_bank.index',[
            'judul'=>"Kas Bank",
            'dataKas' => $dataKas,
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
        return view('pages.master.kas_bank.create',[
            'judul'=>"Kas Bank",
            // 'dataCOAHead' => $dataCOAHead,
            // 'dataCOADetail' => $dataCOADetail,
        ]);
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

            $pesanKustom = [
             
                'nama.required' => 'Nama kas Harus diisi!',
                // 'no_akun.required' => 'Nomor kas akun Harus diisi!',
                'tipe.required' =>'Tipe kas harap dipilih salah satu!',
                'saldo_awal.required' => 'Saldo awal Harus diisi!',
                'tgl_saldo.required' => 'Tanngal saldo awal Harus diisi!',
      
            ];
            
            $request->validate([
                'nama' => 'required',
                // 'no_akun' => 'required',
                'tipe' =>'required|in:1,2',
                'saldo_awal' => 'required',
                'tgl_saldo' => 'required'
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data = $request->collect();
            // $tanggal = explode('-', $data['tgl_saldo']);
            //     // dd($tanggal);

            // $tahun =$tanggal[0];
            // $bulan =$tanggal[1];
            // $tanggal =$tanggal[2];
            // $gabungan = $tahun.'-'. $bulan.'-'. $tanggal ;
            $tgl_saldo = date_create_from_format('d-M-Y', $data['tgl_saldo']);
            // dd(date_format($tgl_saldo, 'm') == "11");
            DB::table('kas_bank')
                ->insert(array(
                    'nama' => strtoupper($data['nama']),
                    'no_akun' => $data['no_akun']==null ? null :strtoupper($data['no_akun']) ,
                    'tipe' => $data['tipe']==1?'KAS':'BANK',
                    'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
                    'tgl_saldo' => $data['tgl_saldo']==null ? null : date_format($tgl_saldo, 'Y-m-d'),
                    'no_rek' => $data['no_rek']==null ? null : strtoupper($data['no_rek']),
                    'rek_nama' => $data['rek_nama']==null ? null :strtoupper($data['rek_nama']) ,
                    'bank' => $data['bank']==null ? null : strtoupper($data['bank']),
                    'cabang' => $data['cabang']==null ? null :strtoupper($data['cabang']) ,
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('kas_bank.index')->with('status','Sukses menambahkan Kas Bank Baru!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
    public function edit(KasBank $KasBank)
    {
        //
        // Lock the record for update
        // $updatedRecord = DB::table('kas_bank')
        //     ->where('id', $KasBank->id)
        //     ->lockForUpdate()
        //     ->first();
        //     // dd($updatedRecord);

        // if (!$updatedRecord) {
        //     return redirect()->route('kas_bank.index')->with('status', 'Data sedang diupdate oleh user lain');
        // }
        // dd($KasBank);
        return view('pages.master.kas_bank.edit',[
            'KasBank'=>$KasBank,
            'judul'=>"Kas Bank",

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KasBank $KasBank)
    {
        //
           //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

           try {
            $pesanKustom = [
             
                'nama.required' => 'Nama kas Harus diisi!',
                // 'no_akun.required' => 'Nomor kas akun Harus diisi!',
                'tipe.required' =>'Tipe kas harap dipilih salah satu!',
                'saldo_awal.required' => 'Saldo awal Harus diisi!',
                'tgl_saldo.required' => 'Tanngal saldo awal Harus diisi!',
      
            ];
            
            $request->validate([
                'nama' => 'required',
                // 'no_akun' => 'required',
                'tipe' =>'required|in:1,2',
                'saldo_awal' => 'required',
                'tgl_saldo' => 'required'
                // 'catatan' => 'required',
            ], $pesanKustom);
    
               $data = $request->collect();
        // $tanggal = explode('-', $data['tgl_saldo']);
        //     // dd($tanggal);

        // $tahun =$tanggal[0];
        // $bulan =$tanggal[1];
        // $tanggal =$tanggal[2];
        // $gabungan = $tahun.'-'. $bulan.'-'. $tanggal ;
            $tgl_saldo = date_create_from_format('d-M-Y', $data['tgl_saldo']);
        
            // dd($data);
            DB::table('kas_bank')
            ->where('id', $KasBank['id'])
            ->update(array(
                   'nama' => strtoupper($data['nama']),
                    'no_akun' => $data['no_akun']==null ? null :strtoupper($data['no_akun']) ,
                    'tipe' => $data['tipe']==1?'KAS':'BANK',
                    'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
                    'tgl_saldo' => $data['tgl_saldo']==null ? null : date_format($tgl_saldo, 'Y-m-d'),
                    'no_rek' => $data['no_rek']==null ? null : strtoupper($data['no_rek']),
                    'rek_nama' => $data['rek_nama']==null ? null :strtoupper($data['rek_nama']) ,
                    'bank' => $data['bank']==null ? null : strtoupper($data['bank']),
                    'cabang' => $data['cabang']==null ? null :strtoupper($data['cabang']) ,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            );
        
            return redirect()->route('kas_bank.index')->with('status','Sukses Mengubah Data Kas Bank!!');
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
    public function destroy(KasBank $KasBank)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try{
            DB::table('kas_bank')
            ->where('id', $KasBank['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('kas_bank.index')->with('status','Sukses Menghapus Data Kas Bank!!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
       
    }
    //git cobakgitgit EDWIN
    // git Timot
}
