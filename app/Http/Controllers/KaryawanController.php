<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Mockery\Undefined;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Buglinjo\LaravelWebp\Webp;
use Illuminate\Support\Facades\Storage;
use Exception;
class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $dataKaryawan = DB::table('karyawan')
            ->select('karyawan.id','karyawan.nama_panggilan','karyawan.tempat_lahir','karyawan.alamat_domisili','karyawan.telp1','role.nama as posisi')
            ->leftJoin('role', 'karyawan.posisi_id', '=', 'role.id')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            // ->paginate(10);
            ->get();

            $title = 'Data akan dihapus!';
            $text = "Apakah Anda yakin?";
            $confirmButtonText = 'Ya';
            $cancelButtonText = "Batal";
            confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
            
            return view('pages.master.karyawan.indexx',[
            'judul'=>"Karyawan",
            'dataKaryawan' => $dataKaryawan,
        ]);
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = DB::table('karyawan')
    //                 ->select('karyawan.id','karyawan.nama_panggilan as nama_panggilan',
    //                     'karyawan.tempat_lahir as tempat_lahir','karyawan.alamat_domisili as alamat_domisili',
    //                     'karyawan.telp1 as telp1','role.nama as posisi')
    //                 ->leftJoin('role', 'karyawan.posisi_id', '=', 'role.id')
    //                 ->where('karyawan.is_aktif', '=', "Y")
    //                 ->where('karyawan.is_keluar', '=', "N")
    //                 ->get();

    //         return Datatables::of($data)->addIndexColumn() //bukan error ga bisa
    //             ->addColumn('action', function($row){
    //                 $btn = '
    //                     <a class="btn btn-default bg-info radiusSendiri" href="karyawan/'.$row->id.'/edit">
    //                         <i class="fas fa-edit"></i> Edit
    //                     </a>   
    //                     <button type="button" class="btn btn-danger delete-button radiusSendiri" data-toggle="modal" data-target="#modalHapus">
    //                             <i class="fas fa-trash"></i> Hapus
    //                     </button>   
    //                     ';
    //                 return $btn; 
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }
    //     $dataKaryawan = DB::table('karyawan')
    //             ->select('karyawan.id','karyawan.nama_panggilan','karyawan.tempat_lahir','karyawan.alamat_domisili','karyawan.telp1','role.nama as posisi')
    //             ->leftJoin('role', 'karyawan.posisi_id', '=', 'role.id')
    //             ->where('karyawan.is_aktif', '=', "Y")
    //             ->where('karyawan.is_keluar', '=', "N")
    //             ->get();
    //     return view('pages.master.karyawan.index',[
    //         'judul'=>"Karyawan",
    //         'dataKaryawan'=> $dataKaryawan,
    //     ]);
    // }

    public function getData()
    {
        $data = DB::table('karyawan')
            ->select('karyawan.id','karyawan.nama_panggilan','karyawan.tempat_lahir','karyawan.alamat_domisili','karyawan.telp1','role.nama as posisi')
            ->leftJoin('role', 'karyawan.posisi_id', '=', 'role.id')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            ->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        
        try {

            $pesanKustom = [
             
                'tanggal_gabung.required' => 'Tanggal gabung Karyawan harap diisi!',
                'posisi.required' => 'Posisi karyawan harap diisi!',
                'telp1.required' =>'Nomor telpon 1 harap diisi ',
                'nama_lengkap.required' => 'Nama lengkap karyawan harap diisi!',
                'nama_panggilan.required' => 'Nama panggilan karyawan harap diisi!',
                // 'foto.required' => 'Foto karyawan harap diisi!',
            ];
             
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_gabung' => 'required',
                'posisi' => 'required',
                'telp1' =>'required',
                'nama_lengkap' => 'required',
                'nama_panggilan' => 'required',
                'foto' => 'image|mimes:jpg,png,jpeg|max:2048',

            ],$pesanKustom);
           

            $data = $request->collect();
            //  dd($rules);

              // Validate the incoming request data
            // $validator = Validator::make($data, $rules, $pesanKustom);

            // Check if validation fails
            // if ($validator->fails()) {
            //     // Return validation errors in JSON format
            //     return response()->json(['errors' => $validator->errors()], 422);
            // }
            // dd($data);
            // var_dump($data['identitas']); die;
            // $path = "";
            
            // if ($request->hasFile('foto') && $data['nama_panggilan']!=null ) {
            //     $fotoKaryawan = Webp::make($request->file('foto'));
            //     $ekstensiGambar = $fotoKaryawan->getClientOriginalExtension();
            //     $nama_gambar='karyawan_'.$data['nama_panggilan'].'_'.time() . '.' . $ekstensiGambar;
            //     $fotoKaryawan->move(public_path('/img/fotoKaryawan'), $nama_gambar );
            //     $path = '/img/fotoKaryawan/' . $nama_gambar;
            // }

            $path = "";

            if ($request->hasFile('foto') && $data['nama_panggilan'] != null) {
                $fotoKaryawan = $request->file('foto');
                $ekstensiGambar = $fotoKaryawan->getClientOriginalExtension();
                $nama_gambar = 'karyawan_' . $data['nama_panggilan'] . '_' . time() . '.' . $ekstensiGambar. '.webp';

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoKaryawan);
                $webp->save(public_path('/img/fotoKaryawan/' . $nama_gambar ),20);

                // Save the original image
                // $fotoKaryawan->move(public_path('/img/fotoKaryawan'), $nama_gambar);
                $path = '/img/fotoKaryawan/' . $nama_gambar;
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
            $telp1= $data['telp1'];
             // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp1, 0, 1) == "0" && $telp1!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp1 = (string) "+62" . substr($telp1, 1);
            } else if (substr($telp1, 0, 2) == "62"&& $telp1!=null) {
                $telp1 = (string) "+" . $telp1;
            }
            $telp2 = $data['telp2'];

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp2, 0, 1) == "0"&& $telp2!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp2 = (string) "+62" . substr($telp2, 1);
            } else if (substr($telp2, 0, 2) == "62"&& $telp2!=null) {
                $telp2 = (string) "+" . $telp2;
            }
            $telpDarurat = $data['nomor_kontak_darurat'];

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telpDarurat, 0, 1) == "0"&& $telpDarurat!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telpDarurat = (string) "+62" . substr($telpDarurat, 1);
            } else if (substr($telpDarurat, 0, 2) == "62"&& $telpDarurat!=null) {
                $telpDarurat = (string) "+" . $telpDarurat;
            }
            // var_dump($data['status_pegawai']);die;
            $idKaryawan=DB::table('karyawan')
                ->insertGetId(array(
                    // data pribadi
                    'foto' => ($request->hasFile('foto'))?$path:null,
                    'nik' => $newNik,
                    'nama_panggilan' => strtoupper($data['nama_panggilan']),
                    'nama_lengkap' => strtoupper($data['nama_lengkap']),
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'status_menikah'=>$data['status_menikah'],
                    'jumlah_anak'=>$data['jumlah_anak']?$data['jumlah_anak']:0,
                    'tempat_lahir'=>strtoupper($data['tempat_lahir']),
                    'tanggal_lahir'=>date_format($tanggal_lahir, 'Y-m-d'),
                    'agama'=>$data['agama'],
                    // end data pribadi

                    // data Alamat & Kontak
                    'alamat_domisili'=>strtoupper($data['alamat_sekarang']),
                    'kota_domisili'=>strtoupper($data['kota_sekarang']),
                    'alamat_ktp'=>strtoupper($data['alamat_ktp']),
                    'kota_ktp'=>strtoupper($data['kota_ktp']),
                    'telp1'=>$telp1,
                    'telp2'=>$telp2?$telp2:null,
                    'email'=>$data['email'],
                    'ptkp_id'=>$data['ptkp'],
                    'norek'=>$data['no_rekening'],
                    'rek_nama'=>strtoupper($data['atas_nama']),
                    'bank'=>strtoupper($data['nama_bank']),
                    'cabang_bank'=>strtoupper($data['cabang_bank']),
                    // end data Alamat & Kontak

                    // data Kontak Darurat
                    'nama_kontak_darurat'=>strtoupper($data['nama_kontak_darurat']),
                    'hubungan_kontak_darurat'=>strtoupper($data['hubungan_kontak_darurat']),
                    'nomor_kontak_darurat'=>$telpDarurat,
                    'alamat_kontak_darurat'=>strtoupper($data['alamat_kontak_darurat']),
                    // end data Kontak Darurat

                     // data status Karyawan
                    'status_pegawai'=>$data['status_pegawai'],
                    'tgl_gabung'=>date_format($tanggal_gabung, 'Y-m-d'),
                    'tgl_mulai_kontrak'=>($data['status_pegawai'] == 'Kontrak'||$data['status_pegawai'] == 'Magang')?date_format($tanggal_kontrak, 'Y-m-d'):null,
                    'tgl_selesai_kontrak'=>($data['status_pegawai'] == 'Kontrak'||$data['status_pegawai'] == 'Magang')?date_format($tanggal_selesai_kontrak, 'Y-m-d'):null,
                    'posisi_id'=>$data['posisi'], // ini itu idrole
                    'm_kota_id'=>$data['cabang_kantor'],
                    'saldo_cuti'=>$data['sisa_cuti'],

                    'gaji'=>($data['gaji'])?str_replace(',', '',$data['gaji']):null,
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
            if($data['komponen'] != null){
                $arrayDokumen = json_decode($data['komponen'], true);
                foreach ($arrayDokumen as $key => $item) {
                   DB::table('karyawan_komponen')
                        ->insert(array(
                        'karyawan_id'=>$idKaryawan,
                        'nama' => strtoupper($item['nama']) ,
                        'nominal' => str_replace(',', '',$item['nominal']),
                        'created_at'=>VariableHelper::TanggalFormat(), 
                        'created_by'=> $user,
                        'updated_at'=> VariableHelper::TanggalFormat(),
                        'updated_by'=> $user,
                        'is_aktif' => "Y",
                        )
                    ); 
            
                }
            }
            // var_dump($data['gaji']);
            // var_dump( response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idKaryawan]));

            return response()->json(['message' => 'Berhasil menambahkan data karyawan', 'id' => $idKaryawan]);

            // return redirect()->route('karyawan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
             // If there's an error, unlink the image if it was uploaded
            DB::rollBack();
            if (!empty($path)) {
                    if (file_exists(public_path($path))) {
                        unlink(public_path($path));
                    }
            }
                return response()->json(['errorsCatch' => $e->errors()], 422);
        }
       catch (Exception $ex) {
            // cancel input db
            DB::rollBack();

            if (!empty($path)) {
                if (file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }
            return response()->json(['errorServer' => $ex->getMessage()],500);
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

         $dataKaryawanIdentitas = DB::table('karayawan_identitas')
            ->select('karayawan_identitas.*')
            ->where('karayawan_identitas.is_aktif', '=', "Y")
            ->where('karayawan_identitas.karyawan_id', '=', $karyawan['id'])
            ->get();
        
         $dataKaryawanKomponen = DB::table('karyawan_komponen')
            ->select('karyawan_komponen.*')
            ->where('karyawan_komponen.is_aktif', '=', "Y")
            ->where('karyawan_komponen.karyawan_id', '=', $karyawan['id'])
            ->get();
        //  $dataKaryawanIdentitasHapus = DB::table('karayawan_identitas')
        //             ->select('karayawan_identitas.*')
        //             ->where('karayawan_identitas.karyawan_id', '=', $karyawan['id'])
        //             ->where('karayawan_identitas.id', '!=', 47)
        //             ->first();
        // dd($dataKaryawanIdentitasHapus);
                     

        return view('pages.master.karyawan.edit',[
            'judul'=>"Karyawan",
            'karyawan'=>$karyawan,
             'dataRole' => $dataRole,
             'dataKota' => $dataKota,
             'dataPtkp' => $dataPtkp,
             'dataAgama'=>$dataAgama,
             'dataJenis'=>$dataJenis,
             'dataKaryawanKomponen'=>$dataKaryawanKomponen,
             'dataKaryawanIdentitas'=>$dataKaryawanIdentitas,

        ]);
        
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
          //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        
        try {

            $pesanKustom = [
             
                'tanggal_gabung.required' => 'Tanggal gabung Karyawan harap diisi!',
                'posisi.required' => 'Posisi karyawan harap diisi!',
                'telp1.required' =>'Nomor telpon 1 harap diisi ',
                'nama_lengkap.required' => 'Nama lengkap karyawan harap diisi!',
                'nama_panggilan.required' => 'Nama panggilan karyawan harap diisi!',
                // 'foto.required' => 'Foto karyawan harap diisi!',
            ];
             
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_gabung' => 'required',
                'posisi' => 'required',
                'telp1' =>'required',
                'nama_lengkap' => 'required',
                'nama_panggilan' => 'required',
                'foto' => 'image|mimes:jpg,png,jpeg',

            ],$pesanKustom);
           

            $data = $request->collect();
            $fotoPathDariDB = $karyawan->foto;

            $path = "";

            if ($request->hasFile('foto') && $data['nama_panggilan'] != null) {
                // Unlink (delete) the existing image file
                if (!empty($fotoPathDariDB)) {
                    if (file_exists(public_path($fotoPathDariDB))) {
                        unlink(public_path($fotoPathDariDB));
                    }
                }

                 $fotoKaryawan = $request->file('foto');
                $ekstensiGambar = $fotoKaryawan->getClientOriginalExtension();
                $nama_gambar = 'karyawan_' . $data['nama_panggilan'] . '_' . time() . '.' . $ekstensiGambar. '.webp';

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoKaryawan);
                $webp->save(public_path('/img/fotoKaryawan/' . $nama_gambar ),20);

                // Save the original image
                // $fotoKaryawan->move(public_path('/img/fotoKaryawan'), $nama_gambar);
                $path = '/img/fotoKaryawan/' . $nama_gambar;
            }

              
  
            //====== logic otomatis nik ======
            // $year = Carbon::now()->format('y');
            // $maxNik = DB::table('karyawan')
            //     ->select(DB::raw('max(substr(nik,-4)) as max_nik'))
            //     ->where(DB::raw('substr(nik,1,2)'), $year)
            //     ->value('max_nik');

            // $newNik = $year . str_pad((intval($maxNik) + 1), 4, '0', STR_PAD_LEFT);

            // if (empty($newNik)) {
            //     $newNik = $year . '0001';
            // }
            //====== end logic otomatis nik ======
            $tanggal_lahir = date_create_from_format('d-M-Y', $data['tanggal_lahir']);
            $tanggal_kontrak = date_create_from_format('d-M-Y', $data['tanggal_kontrak']);
            $tanggal_gabung = date_create_from_format('d-M-Y', $data['tanggal_gabung']);
            $tanggal_selesai_kontrak = date_create_from_format('d-M-Y', $data['tanggal_selesai_kontrak']);
            $tanggal_keluar = date_create_from_format('d-M-Y', $data['tanggal_keluar']);

            $telp1= $data['telp1'];
            $telp2 = $data['telp2'];
             // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp1, 0, 1) == "0") {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp1 = (string) "+62" . substr($telp1, 1);
            } else if (substr($telp1, 0, 2) == "62") {
                $telp1 = (string) "+" . $telp1;
            }

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp2, 0, 1) == "0") {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp2 = (string) "+62" . substr($telp2, 1);
            } else if (substr($telp2, 0, 2) == "62") {
                $telp2 = (string) "+" . $telp2;
            }

            $telpDarurat = $data['nomor_kontak_darurat'];

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telpDarurat, 0, 1) == "0"&& $telpDarurat!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telpDarurat = (string) "+62" . substr($telpDarurat, 1);
            } else if (substr($telpDarurat, 0, 2) == "62"&& $telpDarurat!=null) {
                $telpDarurat = (string) "+" . $telpDarurat;
            }
            // else
            // {
            //     $telpDarurat = (string) "+62" . $telpDarurat;

            // }
            DB::table('karyawan')
            ->where('id', $karyawan['id'])
                ->update(array(
                    // data pribadi
                    'foto' => ($request->hasFile('foto'))?$path:$fotoPathDariDB ,
                    'nik' => $data['nik'],
                    'nama_panggilan' => strtoupper($data['nama_panggilan']) ,
                    'nama_lengkap' => strtoupper($data['nama_lengkap']),
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'status_menikah'=>$data['status_menikah'],
                    'jumlah_anak'=>$data['jumlah_anak']?$data['jumlah_anak']:0,
                    'tempat_lahir'=>strtoupper($data['tempat_lahir']),
                    'tanggal_lahir'=>date_format($tanggal_lahir, 'Y-m-d'),
                    'agama'=>$data['agama'],
                    // end data pribadi

                    // data Alamat & Kontak
                    'alamat_domisili'=>strtoupper($data['alamat_sekarang']),
                    'kota_domisili'=>strtoupper($data['kota_sekarang']),
                    'alamat_ktp'=>strtoupper($data['alamat_ktp']),
                    'kota_ktp'=>strtoupper($data['kota_ktp']),
                    'telp1'=>$telp1,
                    'telp2'=>$telp2,
                    'email'=>$data['email'],
                    'ptkp_id'=>$data['ptkp'],
                    'norek'=>$data['no_rekening'],
                    'rek_nama'=>strtoupper($data['atas_nama']),
                    'bank'=>strtoupper($data['nama_bank']),
                    'cabang_bank'=>strtoupper($data['cabang_bank']),
                    // end data Alamat & Kontak

                    // data Kontak Darurat
                    'nama_kontak_darurat'=>strtoupper($data['nama_kontak_darurat']),
                    'hubungan_kontak_darurat'=>strtoupper($data['hubungan_kontak_darurat']),
                    'nomor_kontak_darurat'=>$telpDarurat,
                    'alamat_kontak_darurat'=>strtoupper($data['alamat_kontak_darurat']),
                    // end data Kontak Darurat

                     // data status Karyawan
                    'status_pegawai'=>$data['status_pegawai'],
                    'tgl_gabung'=>date_format($tanggal_gabung, 'Y-m-d'),
                    'tgl_mulai_kontrak'=>($data['status_pegawai'] == 'Kontrak'||$data['status_pegawai'] == 'Magang')?date_format($tanggal_kontrak, 'Y-m-d'):null,
                    'tgl_selesai_kontrak'=>($data['status_pegawai'] == 'Kontrak'||$data['status_pegawai'] == 'Magang')?date_format($tanggal_selesai_kontrak, 'Y-m-d'):null,
                    'posisi_id'=>$data['posisi'], // ini itu idrole
                    'm_kota_id'=>$data['cabang_kantor'],
                    'saldo_cuti'=>$data['sisa_cuti'],

                    'gaji'=>($data['gaji'])?str_replace(',', '',$data['gaji']):null,
                    'is_keluar'=>($data['is_keluar'] == 'Y')?'Y':'N',
                    'tgl_keluar'=>($data['is_keluar'] == 'Y')?date_format($tanggal_keluar, 'Y-m-d'):null,
                    // end data status Karyawan
                    // 'created_at'=>VariableHelper::TanggalFormat(), 
                    // 'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            ); 
            
            // var_dump($data['nomor_kontak_darurat']); die;
          
            // buat ? data diri
            if (isset($data['identitas'])) {
                $arrayIdentitasForm = json_decode($data['identitas'], true);
                    // Set is_aktif = "N" untuk data identitas yang tidak ada di form
                $identitasIDsFromForm = array_column($arrayIdentitasForm, 'identitas_id');
                DB::table('karayawan_identitas')
                    ->where('karyawan_id', $karyawan['id'])
                    ->whereNotIn('id', $identitasIDsFromForm)
                    ->update([
                        'updated_at' => VariableHelper::TanggalFormat(),
                        'updated_by' => $user,
                        'is_aktif' => "N",
                    ]);

                foreach ($arrayIdentitasForm as $itemFormIdentitas) {
                    // $matchingRecord = null;

                    // foreach ($dataKaryawanIdentitas as $record) {
                    //     if ($record->id === $item['identitas_id'] && $karyawan['id']==$record->karyawan_id) {
                    //         $matchingRecord = $record;
                    //         break;
                    //     }
                    // }
                   
                    // cek id dr form sama atau nggak
                    $dataKaryawanIdentitas = DB::table('karayawan_identitas')
                    ->select('karayawan_identitas.*')
                    ->where('karayawan_identitas.is_aktif', '=', "Y")
                    ->where('karayawan_identitas.karyawan_id', '=', $karyawan['id'])
                    ->where('karayawan_identitas.id', '=', $itemFormIdentitas['identitas_id'])
                    ->first();
                    
                    if ($dataKaryawanIdentitas) {
                        DB::table('karayawan_identitas')
                            ->where('karyawan_id', $karyawan['id'])
                            ->where('id', $itemFormIdentitas['identitas_id'])
                            ->update([
                                'm_jenis_identitas_id' => $itemFormIdentitas['m_jenis_identitas_id'],
                                'nomor' => $itemFormIdentitas['nomor'],
                                'catatan' => $itemFormIdentitas['catatan'],
                                'updated_at' => VariableHelper::TanggalFormat(),
                                'updated_by' => $user,
                                'is_aktif' => "Y",
                            ]);
                    }
                    else {
                        DB::table('karayawan_identitas')
                            ->insert([
                                'karyawan_id' => $karyawan['id'],
                                'm_jenis_identitas_id' => $itemFormIdentitas['m_jenis_identitas_id'],
                                'nomor' => $itemFormIdentitas['nomor'],
                                'catatan' => $itemFormIdentitas['catatan'],
                                'created_at' => VariableHelper::TanggalFormat(),
                                'created_by' => $user,
                                'updated_at' => VariableHelper::TanggalFormat(),
                                'updated_by' => $user,
                                'is_aktif' => "Y",
                            ]);
                    }
                    
                 

                }
            }


            if ( isset($data['komponen'])) {
                $arrayKomponenForm = json_decode($data['komponen'], true);
                // $identitasIDsFromForm = array_column($arrayKomponenForm, 'komponen_id');
                // var_dump($arrayKomponenForm ); die;
                foreach ($arrayKomponenForm as $key => $value) {
                    // edit dan delete, tergantung is_aktif yg diterima
                    if($value['komponen_id'] != null){
                           DB::table('karyawan_komponen')
                                ->where('karyawan_id', $karyawan['id'])
                                ->where('id', $value['komponen_id'])
                                    ->update(array(
                                    'nama' => strtoupper($value['nama']) ,
                                    'nominal' => str_replace(',', '',$value['nominal']),
                                    'updated_at'=> VariableHelper::TanggalFormat(),
                                    'is_aktif' => $value['is_aktif'],
                                    'updated_at'=> now(),
                                    'updated_by'=> $user,
                                    )
                                );  
                    }else{
                        DB::table('karyawan_komponen')
                                ->insert(array(
                                'karyawan_id'=>$karyawan['id'],
                                'nama' =>strtoupper($value['nama'])  ,
                                'nominal' => str_replace(',', '',$value['nominal']),
                                'created_by'=> $user,
                                'created_at'=> now(), 
                                'is_aktif' => "Y",
                                )
                            );  
                    }
                }
         
            }
        
            return response()->json(['message' => 'Berhasil update data karyawan', 'id' => $karyawan['id']]);

            // return redirect()->route('karyawan.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            // cancel input db
            DB::rollBack();
            
            if ( public_path($path)!== $fotoPathDariDB) {
                // Delete the image using the Storage facade
                if (file_exists(public_path($path))) {
                        unlink(public_path($path));
                    }
            }
                return response()->json(['errorsCatch' => $e->errors()], 422);
        } catch (Exception $ex) {
            // cancel input db
            DB::rollBack();

            if (!empty($path)) {
                // Jika gambar sudah di-upload sebelumnya, hapus gambar yang baru saja dibuat
                if(public_path($path)!== $fotoPathDariDB)
                {
                     if (file_exists(public_path($path))) {
                            unlink(public_path($path));
                        }
                }
               
            }
            return response()->json(['errorServer' => $ex->getMessage()],500);
        }
        
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
         //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try{
            DB::table('karyawan')
            ->where('id', $karyawan['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );

             DB::table('karyawan_komponen')
            ->where('id', $karyawan['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );

             DB::table('karayawan_identitas')
            ->where('id', $karyawan['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('karyawan.index')->with('status','Sukses Menghapus Data Karyawan!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
