<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $dataKaryawan = DB::table('karyawan')
            ->select('karyawan.nama_panggilan','karyawan.tempat_lahir','karyawan.alamat_domisili','karyawan.telp1','role.nama as posisi')
            ->leftJoin('role', 'karyawan.posisi_id', '=', 'role.id')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            ->get();

            return view('pages.master.karyawan.index',[
            'judul'=>"Karyawan",
            'dataKaryawan' => $dataKaryawan,
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
        $dataRole = DB::table('role')
            ->where('role.is_aktif', '=', "Y")
            ->get();
        $dataKota = DB::table('m_kota')
            ->get();
        $dataPtkp = DB::table('ptkp')
            ->where('ptkp.is_aktif', '=', "Y")
            ->get();
        return view('pages.master.karyawan.create',[
            'judul'=>"Karyawan",
             'dataRole' => $dataRole,
             'dataKota' => $dataKota,
             'dataPtkp' => $dataPtkp,

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
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('karyawan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function show(Karyawan $karyawan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function edit(Karyawan $karyawan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Karyawan  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Karyawan $karyawan)
    {
        //
    }
}
