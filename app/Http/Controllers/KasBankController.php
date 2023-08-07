<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KasBank;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;

class KasBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_hapus', '=', "N")
            ->get();

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
        $tanggal = explode('-', $data['tgl_saldo']);
            // dd($tanggal);

        $tahun =$tanggal[0];
        $bulan =$tanggal[1];
        $tanggal =$tanggal[2];
        $gabungan = $tahun.'/'. $bulan.'/'. $tanggal ;
            DB::table('kas_bank')
                ->insert(array(
                    'nama' => $data['nama'],
                    'no_akun' => $data['no_akun']==null ? null : $data['no_akun'],
                    'tipe' => $data['tipe']==1?'Kas':'Bank',
                    'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
                    'tgl_saldo' => $data['tgl_saldo']==null ? null : $gabungan,
                    'no_rek' => $data['no_rek']==null ? null : $data['no_rek'],
                    'rek_nama' => $data['rek_nama']==null ? null : $data['rek_nama'],
                    'bank' => $data['bank']==null ? null : $data['bank'],
                    'cabang' => $data['cabang']==null ? null : $data['cabang'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> 1,// masih hardcode nanti diganti cookies
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> 1,// masih hardcode nanti diganti cookies
                    'is_hapus' => "N",

                )
            ); 
            return redirect()->route('kas_bank.index')->with('status','Success!!');
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
        $tanggal = explode('-', $data['tgl_saldo']);
            // dd($tanggal);

        $tahun =$tanggal[0];
        $bulan =$tanggal[1];
        $tanggal =$tanggal[2];
        $gabungan = $tahun.'/'. $bulan.'/'. $tanggal ;
            // dd($data);
            DB::table('kas_bank')
            ->where('id', $KasBank['id'])
            ->update(array(
                   'nama' => $data['nama'],
                    'no_akun' => $data['no_akun']==null ? null : $data['no_akun'],
                    'tipe' => $data['tipe']==1?'Kas':'Bank',
                    'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
                    'tgl_saldo' => $data['tgl_saldo']==null ? null : $gabungan,
                    'no_rek' => $data['no_rek']==null ? null : $data['no_rek'],
                    'rek_nama' => $data['rek_nama']==null ? null : $data['rek_nama'],
                    'bank' => $data['bank']==null ? null : $data['bank'],
                    'cabang' => $data['cabang']==null ? null : $data['cabang'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> 1,// masih hardcode nanti diganti cookies
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> 1,// masih hardcode nanti diganti cookies
                    'is_hapus' => "N",
                )
            );
        
            return redirect()->route('kas_bank.index')->with('status','Sukses mengupdate data kas!');
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
        try{
            DB::table('kas_bank')
            ->where('id', $KasBank['id'])
            ->update(array(
                'is_hapus' => "Y",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> 1, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('kas_bank.index')->with('status','Sukses Menghapus Data Kas!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
       
    }
    //git cobakgitgit EDWIN
}
