<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
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
            ->select('karyawan.id','karyawan.nama_panggilan','karyawan.tempat_lahir','karyawan.alamat_domisili','karyawan.telp1','role.nama as posisi')
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
        $dataAgama = DB::table('agama')
        ->get();
        $dataJenis = DB::table('m_jenis_identitas')
        ->where('m_jenis_identitas.is_aktif', '=', "Y")
        ->get();
        return view('pages.master.karyawan.create',[
            'judul'=>"Karyawan",
             'dataRole' => $dataRole,
             'dataKota' => $dataKota,
             'dataPtkp' => $dataPtkp,
             'dataAgama'=>$dataAgama,
             'dataJenis'=>$dataJenis,

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
        $user = 1; // masih hardcode nanti diganti cookies atau auth masih gatau
        
        try {

            $pesanKustom = [
             
                // 'nama.required' => 'Nama kas Harus diisi!',
                // 'no_akun.required' => 'Nomor kas akun Harus diisi!',
                // 'tipe.required' =>'Tipe kas harap dipilih salah satu!',
                // 'saldo_awal.required' => 'Saldo awal Harus diisi!',
                // 'tgl_saldo.required' => 'Tanngal saldo awal Harus diisi!',

      
            ];
             
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button

                // 'tanggal_gabung' => 'required',
                // 'posisi' => 'required',
                // 'telp1' =>'required',
                // 'nama_lengkap' => 'required',
                // 'nama_panggilan' => 'required',
                // 'foto' => 'required|image|mimes:jpg,png,jpeg|max:2048',

            ], $pesanKustom);

            $data = $request->post();
            // dd($data);
            // var_dump($data['identitas']); die;
            $path = "";
            
            if ($request->hasFile('foto') && $data['nama_panggilan']!=null) {
                $fotoKaryawan = $request->file('foto');
                $ekstensiGambar = $fotoKaryawan->getClientOriginalExtension();
                $nama_gambar='karyawan_'.$data['nama_panggilan'].'_'.time() . '.' . $ekstensiGambar;
                $fotoKaryawan->move(public_path('/img/fotoKaryawan'), $nama_gambar );
                $path = '/img/fotoKaryawan' . $nama_gambar;
            }
   
            //====== logic otomatis nik ======
            $year = Carbon::now()->format('y');
            $maxNik = DB::table('karyawan')
                ->select(DB::raw('max(substr(nik,-4)) as max_nik'))
                ->where(DB::raw('substr(nik,1,2)'), $year)
                ->value('max_nik');

            $newNik = $year . str_pad((intval($maxNik) + 1), 4, '0', STR_PAD_LEFT);

            if (empty($newNik)) {
                $newNik = $year . '0001';
            }
            //====== end logic otomatis nik ======
            $tanggal_lahir = date_create_from_format('d-M-Y', $data['tanggal_lahir']);
            $tanggal_kontrak = date_create_from_format('d-M-Y', $data['tanggal_kontrak']);
            $tanggal_gabung = date_create_from_format('d-M-Y', $data['tanggal_gabung']);
            $tanggal_selesai_kontrak = date_create_from_format('d-M-Y', $data['tanggal_selesai_kontrak']);
            $tanggal_keluar = date_create_from_format('d-M-Y', $data['tanggal_keluar']);
         
            $idKaryawan=DB::table('karyawan')
                ->insertGetId(array(
                    // data pribadi
                    'foto' => $path ,
                    'nik' => $newNik,
                    'nama_panggilan' => $data['nama_panggilan'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'status_menikah'=>$data['status_menikah'],
                    'jumlah_anak'=>$data['jumlah_anak'],
                    'tempat_lahir'=>$data['tempat_lahir'],
                    'tanggal_lahir'=>date_format($tanggal_lahir, 'Y-m-d'),
                    'agama'=>$data['agama'],
                    // end data pribadi

                    // data Alamat & Kontak
                    'alamat_domisili'=>$data['alamat_sekarang'],
                    'kota_domisili'=>$data['kota_sekarang'],
                    'alamat_ktp'=>$data['alamat_ktp'],
                    'kota_ktp'=>$data['kota_ktp'],
                    'telp1'=>$data['telp1'],
                    'telp2'=>$data['telp2'],
                    'email'=>$data['email'],
                    'ptkp_id'=>$data['ptkp'],
                    'norek'=>$data['no_rekening'],
                    'rek_nama'=>$data['atas_nama'],
                    'bank'=>$data['nama_bank'],
                    'cabang_bank'=>$data['cabang_bank'],
                    // end data Alamat & Kontak

                    // data Kontak Darurat
                    'nama_kontak_darurat'=>$data['nama_kontak_darurat'],
                    'hubungan_kontak_darurat'=>$data['hubungan_kontak_darurat'],
                    'nomor_kontak_darurat'=>$data['nomor_kontak_darurat'],
                    'alamat_kontak_darurat'=>$data['alamat_kontak_darurat'],
                    // end data Kontak Darurat

                     // data status Karyawan
                    'status_pegawai'=>$data['status_pegawai'],
                    'tgl_gabung'=>date_format($tanggal_gabung, 'Y-m-d'),
                    'tgl_mulai_kontrak'=>($data['status_pegawai'] == 'Kontrak')?date_format($tanggal_kontrak, 'Y-m-d'):null,
                    'tgl_selesai_kontrak'=>($data['status_pegawai'] == 'Kontrak')?date_format($tanggal_selesai_kontrak, 'Y-m-d'):null,
                    'posisi_id'=>$data['posisi'], // ini itu idrole
                    'm_kota_id'=>$data['cabang_kantor'],
                    'saldo_cuti'=>12,

                    'gaji'=>$data['gaji'],
                    'is_keluar'=>($data['is_keluar'] == 'Y')?'Y':'N',
                    'tgl_keluar'=>($data['is_keluar'] == 'Y')?date_format($tanggal_keluar, 'Y-m-d'):null,
                    // end data status Karyawan
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            ); 
            
            if($data['identitas'] != null){
                $arrayDokumen = json_decode($data['identitas'], true);

                foreach ($arrayDokumen as $key => $item) {
                   DB::table('karayawan_identitas')
                        ->insert(array(
                        'karyawan_id'=>$idKaryawan,
                        'm_jenis_identitas_id' => $item['m_jenis_identitas_id'] ,
                        'nomor' => $item['nomor'],
                        'catatan' => $item['catatan'],
                        'created_at'=>VariableHelper::TanggalFormat(), 
                        'created_by'=> $user,
                        'updated_at'=> VariableHelper::TanggalFormat(),
                        'updated_by'=> $user,
                        'is_aktif' => "Y",
                        )
                    ); 
            
                }
            }

            if($data['identitas'] != null){
                $arrayDokumen = json_decode($data['identitas'], true);
                foreach ($arrayDokumen as $key => $item) {
                   DB::table('karayawan_identitas')
                        ->insert(array(
                        'karyawan_id'=>$idKaryawan,
                        'nama' => $item['nama'] ,
                        'nominal' => $item['nominal'],
                        'catatan' => $item['catatan'],
                        'created_at'=>VariableHelper::TanggalFormat(), 
                        'created_by'=> $user,
                        'updated_at'=> VariableHelper::TanggalFormat(),
                        'updated_by'=> $user,
                        'is_aktif' => "Y",
                        )
                    ); 
            
                }
            }

            return response()->json(['message' => 'Berhasil menambahkan karyawan', 'id' => $idKaryawan]);

            // return redirect()->route('karyawan.index')->with('status','Success!!');
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
