<?php

namespace App\Http\Controllers;

use App\Models\LemburMekanik;
use Illuminate\Http\Request;
use App\Helper\SewaDataHelper;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Buglinjo\LaravelWebp\Webp;
use Illuminate\Validation\ValidationException;
use App\Models\KasBank;
use App\Models\LemburMekanikRiwayat;
use App\Helper\CoaHelper;
use App\Models\KasBankTransaction;
use App\Models\LemburMekanikKendaraan;
use Exception;

class LemburMekanikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataLemburMekanik = LemburMekanik::where('is_aktif',"Y")
            ->with('karyawan')
            ->with('lemburRiwayat')
            ->where('status','like',"%PENDING%")
            ->get();
        $dataMekanik = Karyawan::where('is_aktif',"Y")
            // ->where('grup_tujuan.is_aktif', '=', "Y")
            // ->orderBy('grup_tujuan.nama_tujuan')
            ->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.lembur_mekanik.index',[
            'judul'=>"Lembur Mekanik",
            'dataLemburMekanik' => $dataLemburMekanik,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataMekanik' => $dataMekanik
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
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $src="/home/pjexpres/tms.pjexpress.co.id/img/lembur_mekanik/";
        $srcUpdateDelete="/home/pjexpres/tms.pjexpress.co.id";
        $data = $request->all();

        try {
            $pesanKustom = [
                'tanggal_lembur.required' => 'Tanggal Lembur harap diisi!',
                'jam_mulai.required' => 'Jam Mulai harap dipilih!',
                'jam_selesai.required' => 'Jam Selsesai harap dipilih!',
                'select_mekanik.required' =>'Mekanik harap dipilih!',
                // 'select_kendaraan.required' => 'Kendaraan harap dipilih!',
                'total_nominal.required' => 'Nominal lembur harap diisi!',
                // 'keterangan.required' => 'Keterangan harap diisi!',
                // 'foto_lembur.required' => 'Foto lembur mekanik harap diupload!',
            ];
            $request->validate([
                
                'tanggal_lembur' => 'required',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'select_mekanik' =>'required',
                // 'select_kendaraan' => 'required',
                'total_nominal' => 'required',
                // 'keterangan' => 'Keterangan harap diisi!',
                // 'foto_lembur' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ],$pesanKustom);
            
            // dd($data['kendaraan'][0]['foto_lembur']);
            
            $tanggal_lembur= date_create_from_format('d-M-Y', $data['tanggal_lembur']);
            // cek apakah ada data atau tidak di db dengan hari yang sama, karena lembur tidak bisa 2x
            $dataLemburMekanik = LemburMekanik::where('is_aktif',"Y")
            ->with('karyawan')
            ->where('id_karyawan',$data['select_mekanik'])
            ->where('tanggal_lembur',date_format($tanggal_lembur, 'Y-m-d'))
            ->first();
            // dd($dataLemburMekanik);
            if ($dataLemburMekanik) {
                return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => 'Data lembur '. $dataLemburMekanik->karyawan->nama_panggilan.' sudah ada!']);
            }
            else{
                $lembur_mekanik = new LemburMekanik();
                $lembur_mekanik->id_karyawan = $data['select_mekanik'];
                $lembur_mekanik->tanggal_lembur = date_format($tanggal_lembur, 'Y-m-d');
                $lembur_mekanik->jam_mulai_lembur = $data['jam_mulai'];
                $lembur_mekanik->jam_akhir_lembur = $data['jam_selesai'];
                $lembur_mekanik->nominal_lembur =floatval(str_replace(',', '', $data['total_nominal']));
                $lembur_mekanik->status = 'PENDING';
                $lembur_mekanik->created_by = $user;
                $lembur_mekanik->created_at = now();
                $lembur_mekanik->is_aktif = 'Y';
                // $lembur_mekanik->save();
    
                if ($lembur_mekanik->save()) {
                    if(isset($data['kendaraan']))
                    {
                        foreach ($data['kendaraan'] as $key => $value) {
                            if (!isset($value['foto_lembur'])) {
                                return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => 'Foto lembur minimal diisi 1']);
                            }
                            $pathFotoLembur = "";
                            if (isset($value['foto_lembur'])) {
                                $fotoNota = $value['foto_lembur'];
                                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                                $nama_gambar = time().'_'.$value['no_polisi'].'_foto_lembur'.'.' . $ekstensiGambar;
                                // Convert and save the image to WebP format
                                $webp = Webp::make($fotoNota);
                                // $webp->save(public_path('/img/lembur_mekanik/' . $nama_gambar ),20);
                                $webp->save($src.$nama_gambar ,20);
                                $pathFotoLembur = '/img/lembur_mekanik/' . $nama_gambar;
                            }
                            $lembur_mekanik_kendaraan = new LemburMekanikKendaraan();
                            $lembur_mekanik_kendaraan->id_lembur_mekanik = $lembur_mekanik->id;
                            $lembur_mekanik_kendaraan->id_kendaraan = $value['select_kendaraan'];
                            $lembur_mekanik_kendaraan->no_pol = $value['no_polisi'];
                            $lembur_mekanik_kendaraan->keterangan = $value['keterangan'];
                            $lembur_mekanik_kendaraan->foto_lembur = $pathFotoLembur;
                            $lembur_mekanik_kendaraan->created_by = $user;
                            $lembur_mekanik_kendaraan->created_at = now();
                            $lembur_mekanik_kendaraan->is_aktif = 'Y';
                            $lembur_mekanik_kendaraan->save();
                        }
                    }
                }
                DB::commit();
                return redirect()->route('lembur_mekanik.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambahkan data lembur mekanik!']);
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            // cuman sekali aja, karenakan kalau gagal pasti ke index pertama dulu
            if (!empty($pathFotoLembur)) {
                    // if (file_exists(public_path($pathFotoLembur))) {
                    //     unlink(public_path($pathFotoLembur));
                    // }
                    if (file_exists($srcUpdateDelete.$pathFotoLembur)) {
                        unlink($srcUpdateDelete.$pathFotoLembur);
                    }
                    
            }
            // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoLembur)) {
                    // if (file_exists(public_path($pathFotoLembur))) {
                    //     unlink(public_path($pathFotoLembur));
                    // }
                    if (file_exists($srcUpdateDelete.$pathFotoLembur)) {
                        unlink($srcUpdateDelete.$pathFotoLembur);
                    }
            }
            
            // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LemburMekanik  $lemburMekanik
     * @return \Illuminate\Http\Response
     */
    public function show(LemburMekanik $lemburMekanik)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LemburMekanik  $lemburMekanik
     * @return \Illuminate\Http\Response
     */
    public function edit(LemburMekanik $lemburMekanik)
    {
        //
        $dataLemburMekanik = LemburMekanik::where('is_aktif',"Y")
            ->with('karyawan')
            ->with('lemburRiwayat')
            ->where('status','like',"%PENDING%")
            ->where('id',$lemburMekanik->id)
            ->first();
        $dataMekanik = Karyawan::where('is_aktif',"Y")
            // ->where('grup_tujuan.is_aktif', '=', "Y")
            // ->orderBy('grup_tujuan.nama_tujuan')
            ->get();
        $dataMekanik = Karyawan::where('is_aktif',"Y")
            // ->where('grup_tujuan.is_aktif', '=', "Y")
            // ->orderBy('grup_tujuan.nama_tujuan')
            ->get();
        $dataLemburMekanikKendaraan = LemburMekanikKendaraan::where('is_aktif',"Y")
            ->with('kendaraan')
            ->where('id_lembur_mekanik',$lemburMekanik->id)
            ->get();
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.lembur_mekanik.edit',[
            'judul'=>"Lembur Mekanik",
            'dataLemburMekanik' => $dataLemburMekanik,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            // 'dataDriver' => SewaDataHelper::DataDriver(),
            'dataMekanik' => $dataMekanik,
            'dataLemburMekanikKendaraan' => $dataLemburMekanikKendaraan
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LemburMekanik  $lemburMekanik
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LemburMekanik $lemburMekanik)
    {
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $src="/home/pjexpres/tms.pjexpress.co.id/img/lembur_mekanik/";
        $srcUpdateDelete="/home/pjexpres/tms.pjexpress.co.id";
        // $fotoLemburDB = $lemburMekanik->foto_lembur;

        try {
            // $pesanKustom = [
            //     'tanggal_lembur.required' => 'Tanggal Lembur harap diisi!',
            //     'jam_mulai.required' => 'Jam Mulai harap dipilih!',
            //     'jam_selesai.required' => 'Jam Selsesai harap dipilih!',
            //     'select_mekanik.required' =>'Mekanik harap dipilih!',
            //     'select_kendaraan.required' => 'Kendaraan harap dipilih!',
            //     'total_nominal.required' => 'Nominal lembur harap diisi!',
            //     // 'keterangan.required' => 'Keterangan harap diisi!',
            //     // 'foto_lembur.required' => 'Foto lembur mekanik harap diupload!',
            // ];
            // $request->validate([
                
            //     'tanggal_lembur' => 'required',
            //     'jam_mulai' => 'required',
            //     'jam_selesai' => 'required',
            //     'select_mekanik' =>'required',
            //     'select_kendaraan' => 'required',
            //     'total_nominal' => 'required',
            //     // 'keterangan' => 'Keterangan harap diisi!',
            //     // 'foto_lembur' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            // ],$pesanKustom);

            $data = $request->all();
            $tanggal_lembur= date_create_from_format('d-M-Y', $data['tanggal_lembur']);
            // dd($data);   
            $lembur_mekanik = LemburMekanik::where('is_aktif', 'Y')->findOrFail($lemburMekanik->id);
            $lembur_mekanik->id_karyawan = $data['select_mekanik'];
            $lembur_mekanik->tanggal_lembur = date_format($tanggal_lembur, 'Y-m-d');
            $lembur_mekanik->jam_mulai_lembur = $data['jam_mulai'];
            $lembur_mekanik->jam_akhir_lembur = $data['jam_selesai'];
            $lembur_mekanik->nominal_lembur =floatval(str_replace(',', '', $data['total_nominal']));
            $lembur_mekanik->updated_by = $user;
            $lembur_mekanik->updated_at = now();
            $lembur_mekanik->is_aktif = 'Y';
            // $lembur_mekanik->save();
            if ($lembur_mekanik->save()) {
                if(isset($data['kendaraan']))
                {
                    foreach ($data['kendaraan'] as $key => $value) {
                            $pathFotoLembur = "";

                        if($value['id_database']=='data_baru')
                        {
                            if (isset($value['foto_lembur'])) {
                                $fotoNota = $value['foto_lembur'];
                                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                                $nama_gambar = time().'_'.$value['no_polisi'].'_foto_lembur'.'.' . $ekstensiGambar;
                                // Convert and save the image to WebP format
                                $webp = Webp::make($fotoNota);
                                // $webp->save(public_path('/img/lembur_mekanik/' . $nama_gambar ),20);
                                $webp->save($src.$nama_gambar ,20);
                                $pathFotoLembur = '/img/lembur_mekanik/' . $nama_gambar;
                            }
                            $lembur_mekanik_kendaraan = new LemburMekanikKendaraan();
                            $lembur_mekanik_kendaraan->id_lembur_mekanik = $lembur_mekanik->id;
                            $lembur_mekanik_kendaraan->id_kendaraan = $value['select_kendaraan'];
                            $lembur_mekanik_kendaraan->no_pol = $value['no_polisi'];
                            $lembur_mekanik_kendaraan->keterangan = $value['keterangan'];
                            $lembur_mekanik_kendaraan->foto_lembur = $pathFotoLembur;
                            $lembur_mekanik_kendaraan->created_by = $user;
                            $lembur_mekanik_kendaraan->created_at = now();
                            $lembur_mekanik_kendaraan->is_aktif = $value['is_aktif'];
                            $lembur_mekanik_kendaraan->save();
                        }
                        else if ($value['id_database']!='data_baru' && $value['is_aktif']=='N')
                        {
                            $lembur_mekanik_kendaraan = LemburMekanikKendaraan::where('is_aktif', 'Y')->findOrFail($value['id_database']);
                            $lembur_mekanik_kendaraan->updated_by = $user;
                            $lembur_mekanik_kendaraan->updated_at = now();
                            $lembur_mekanik_kendaraan->is_aktif = $value['is_aktif'];
                            $lembur_mekanik_kendaraan->save();
                            if (!empty($lembur_mekanik_kendaraan->foto_lembur)) {
                                // if (file_exists(public_path($lembur_mekanik_kendaraan->foto_lembur))) {
                                //     unlink(public_path($lembur_mekanik_kendaraan->foto_lembur));
                                // }
                                if (file_exists($srcUpdateDelete.$pathFotoLembur)) {
                                    unlink($srcUpdateDelete.$pathFotoLembur);
                                }
                            }
                        }
                        else if ($value['id_database']!='data_baru' && $value['is_aktif']=='Y')
                        {
                            $pathFotoLembur = "";
                            $lembur_mekanik_kendaraan = LemburMekanikKendaraan::where('is_aktif', 'Y')->findOrFail($value['id_database']);
                            $lembur_mekanik_kendaraan->id_kendaraan = $value['select_kendaraan'];
                            $lembur_mekanik_kendaraan->no_pol = $value['no_polisi'];
                            $lembur_mekanik_kendaraan->keterangan = $value['keterangan'];
                            if (isset($value['foto_lembur'])) {
                                if (!empty($lembur_mekanik_kendaraan->foto_lembur)) {
                                    // if (file_exists(public_path($lembur_mekanik_kendaraan->foto_lembur))) {
                                    //     unlink(public_path($lembur_mekanik_kendaraan->foto_lembur));
                                    // }
                                    if (file_exists($srcUpdateDelete.$lembur_mekanik_kendaraan->foto_lembur)) {
                                        unlink($srcUpdateDelete.$lembur_mekanik_kendaraan->foto_lembur);
                                    }
                                }
                                $fotoNota = $value['foto_lembur'];
                                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                                $nama_gambar = time().'_'.$value['no_polisi'].'_foto_lembur'.'.' . $ekstensiGambar;
                                // Convert and save the image to WebP format
                                $webp = Webp::make($fotoNota);
                                // $webp->save(public_path('/img/lembur_mekanik/' . $nama_gambar ),20);
                                $webp->save($src.$nama_gambar ,20);
                                $pathFotoLembur = '/img/lembur_mekanik/' . $nama_gambar;
                                $lembur_mekanik_kendaraan->foto_lembur = $pathFotoLembur;
                            }
                            $lembur_mekanik_kendaraan->updated_by = $user;
                            $lembur_mekanik_kendaraan->updated_at = now();
                            $lembur_mekanik_kendaraan->is_aktif = $value['is_aktif'];
                            $lembur_mekanik_kendaraan->save();
                        }
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('lembur_mekanik.index')->with(['status' => 'Success', 'msg' => 'Berhasil mengubah data lembur mekanik!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($pathFotoLembur)) {
                // if (file_exists(public_path($pathFotoLembur))) {
                //     unlink(public_path($pathFotoLembur));
                // }
                if (file_exists($srcUpdateDelete.$pathFotoLembur)) {
                    unlink($srcUpdateDelete.$pathFotoLembur);
                }
            }
            // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoLembur)) {
                    // if (file_exists(public_path($pathFotoLembur))) {
                    //     unlink(public_path($pathFotoLembur));
                    // }
                    if (file_exists($srcUpdateDelete.$pathFotoLembur)) {
                        unlink($srcUpdateDelete.$pathFotoLembur);
                    }
            }
            
            // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();
        }
    }

    private function methodEdit($id) {
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();
        $dataLemburMekanik = LemburMekanik::where('is_aktif',"Y")
            ->with('karyawan')
            ->with('lemburRiwayat')
            ->with('kendaraan')
            // ->where('status','like',"%PENDING%")
            ->where('id',$id)
            ->first();
        $dataLemburMekanikRiwayat = LemburMekanikRiwayat::where('is_aktif', 'Y')
            ->where('id_lembur_mekanik', $id)
            ->first();
        $dataMekanik = Karyawan::where('is_aktif',"Y")
            // ->where('grup_tujuan.is_aktif', '=', "Y")
            // ->orderBy('grup_tujuan.nama_tujuan')
            ->get();
        $dataLemburMekanikKendaraan = LemburMekanikKendaraan::where('is_aktif',"Y")
            ->with('kendaraan')
            ->where('id_lembur_mekanik',$id)
            ->get();
        // dd($dataLemburMekanikKendaraan);
       

        // dd($dataDumpMekanik->nama_mekanik);
        return [
            'dataLemburMekanik' => $dataLemburMekanik,
            'dataLemburMekanikRiwayat' => $dataLemburMekanikRiwayat,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataMekanik' => $dataMekanik,
            'dataKas' => $dataKas,
            'dataLemburMekanikKendaraan' => $dataLemburMekanikKendaraan,

        ];
    }
    


    public function pencairan($id)
    {
        //
        $data = $this->methodEdit($id);
        return view('pages.finance.lembur_mekanik.pencairan',[
            'judul'=>"Klaim Supir",
            'dataLemburMekanik' => $data['dataLemburMekanik'],
            'dataLemburMekanikRiwayat' => $data['dataLemburMekanikRiwayat'],
            'dataKendaraan' =>  $data['dataKendaraan'],
            'dataMekanik' => $data['dataMekanik'],
            'dataKas' => $data['dataKas'],
            'dataLemburMekanikKendaraan' => $data['dataLemburMekanikKendaraan']
        ]);
    }
    public function pencairan_save(Request $request, $id)
    {
        //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try {
            $data = $request->collect();
            $dataDumpMekanik = DB::table('lembur_mekanik')
            ->join('lembur_mekanik_kendaraan', 'lembur_mekanik.id', '=', 'lembur_mekanik_kendaraan.id_lembur_mekanik')
            ->join('karyawan', 'lembur_mekanik.id_karyawan', '=', 'karyawan.id')
            ->select(
                'karyawan.nama_lengkap as nama_mekanik',
                'lembur_mekanik.jam_mulai_lembur as jam_mulai_lembur',
                'lembur_mekanik.jam_akhir_lembur as jam_akhir',
                DB::raw("GROUP_CONCAT(lembur_mekanik_kendaraan.no_pol) as no_pol")
            )
            ->where('lembur_mekanik.is_aktif', 'Y')
            ->where('lembur_mekanik.id', $id)
            ->groupBy('lembur_mekanik.id')
            ->first();
            // dd($data);
            /* if($data['status']=='PENDING')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                ],$pesanKustom);
            }
            else*/ if($data['status']=='REJECTED')
            {
                $pesanKustom = [
                    // 'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    'alasan_tolak.required' => 'Alasan tolak harap diisi!',
                ];
                
                $request->validate([
                    // 'tanggal_pencairan' => 'required',
                    'alasan_tolak' => 'required',
                ],$pesanKustom);
            }
            else if($data['status']=='ACCEPTED')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    // 'catatan_pencairan.required' => 'Catatan Pencairan harap diisi!',
                    // 'tanggal_pencatatan.required' => 'Tanggal pencatatan harap diisi!',
                    'total_pencairan.required' => 'Total Pencairan harap diisi!',
                    'kas.required' => 'Kas bank harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                    // 'catatan_pencairan' => 'required',
                    // 'tanggal_pencatatan' => 'required',
                    'total_pencairan' => 'required',
                    'kas' => 'required',

                ],$pesanKustom);
            }
            $lembur_mekanik = LemburMekanik::where('is_aktif', 'Y')
            ->findOrFail($id);
            if($lembur_mekanik->status=="PENDING" &&$data['status']=="PENDING")
            {
                return redirect()->back()->withErrors('HARAP UBAH STATUS MENJADI TOLAK/TERIMA!!')->withInput();
            }
            $lembur_mekanik->status = $data['status'];
            $lembur_mekanik->updated_by = $user;
            $lembur_mekanik->updated_at = now();
            // $lembur_mekanik->save();
            if($lembur_mekanik->save())
            {
                // dd($lembur_mekanik->status);
                // else
                // {
                    $lembur_mekanik_riwayat = LemburMekanikRiwayat::where('is_aktif', 'Y')
                                    ->where('id_lembur_mekanik', $id)
                                        ->first();
                    // dd( $lembur_mekanik_riwayat);
                    // dd( $lembur_mekanik_riwayat);

                    // if($lembur_mekanik_riwayat)
                    // {
                    //     $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                    //                     ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                    //                     ->where('jenis', 'lembur_mekanik')
                    //                     ->first();

                    // }
                        //   dd( $kas_bank_transaksi);

                    if ($data['status']=='PENDING') {
                        //kalo ada klaim supir riwayat yang lama

                        if($lembur_mekanik_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                        ->where('jenis', 'lembur_mekanik')
                                        ->first();
                            //  dd($kas_bank_transaksi);

                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                //  dd($saldo);
                                
                                $saldo_baru = $saldo->saldo_sekarang + (float)$lembur_mekanik_riwayat->total_pencairan;
                                //  dd($saldo_baru);
                                
                                DB::table('kas_bank')
                                    ->where('id', $kas_bank_transaksi->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                //setelah saldonya udah ditambah, data dumpnya matiin kan transasksinya batal

                                DB::table('kas_bank_transaction')
                                    ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                    ->where('jenis', 'lembur_mekanik')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                        'is_aktif'=> 'N',

                                    )
                                );
                                // $kas_bank_transaksi->updated_at = now();
                                // $kas_bank_transaksi->updated_by = $user;
                                // $kas_bank_transaksi->is_aktif = 'N';
                                // $kas_bank_transaksi->save();

                                //  dd($kas_bank_transaksi);

                            }
                            //terus matiin riwayatnya yang lama
                            $lembur_mekanik_riwayat->updated_at = now();
                            $lembur_mekanik_riwayat->updated_by = $user;
                            $lembur_mekanik_riwayat->is_aktif = 'N';
                            $lembur_mekanik_riwayat->save();
                        }
                    }
                    elseif ($data['status']=='REJECTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($lembur_mekanik_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                        ->where('jenis', 'lembur_mekanik')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                    // dd($saldo->saldo_sekarang + (float)$lembur_mekanik_riwayat->total_pencairan);
                                $saldo_baru = $saldo->saldo_sekarang + (float)$lembur_mekanik_riwayat->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $kas_bank_transaksi->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                //setelah saldonya udah ditambah, data dumpnya matiin kan transasksinya batal
                                // $kas_bank_transaksi->updated_at = now();
                                // $kas_bank_transaksi->updated_by = $user;
                                // $kas_bank_transaksi->is_aktif = 'N';
                                // $kas_bank_transaksi->save();
                                DB::table('kas_bank_transaction')
                                    ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                    ->where('jenis', 'lembur_mekanik')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                        'is_aktif'=> 'N',

                                    )
                                );
                            }
                            //tanggal pencairan sama pencatatan null soalnya kan kalau tolak ga ada
                            $lembur_mekanik_riwayat->tanggal_pencairan = null;
                            // $lembur_mekanik_riwayat->tanggal_pencatatan = null;
                            $lembur_mekanik_riwayat->total_pencairan =0;
                            $lembur_mekanik_riwayat->alasan_tolak = $data['alasan_tolak'];
                            $lembur_mekanik_riwayat->catatan_pencairan =null;
                            $lembur_mekanik_riwayat->updated_at = now();
                            $lembur_mekanik_riwayat->updated_by = $user;
                            $lembur_mekanik_riwayat->save();   

                        }
                        else
                        {
                            $lembur_mekanik_riwayat_baru = new LemburMekanikRiwayat();
                            $lembur_mekanik_riwayat_baru->id_klaim = $lembur_mekanik->id;
                            $lembur_mekanik_riwayat_baru->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            // $lembur_mekanik_riwayat_baru->tanggal_pencatatan = null;
                            $lembur_mekanik_riwayat_baru->total_klaim = $lembur_mekanik->nominal_lembur;
                            $lembur_mekanik_riwayat_baru->total_pencairan =0;
                            $lembur_mekanik_riwayat_baru->alasan_tolak = $data['alasan_tolak'];
                            $lembur_mekanik_riwayat_baru->created_at = now();
                            $lembur_mekanik_riwayat_baru->created_by = $user;
                            $lembur_mekanik_riwayat_baru->is_aktif = 'Y';
                            //  $lembur_mekanik_riwayat_baru->save();  
                            
                        }
                        

                    }
                    elseif ($data['status']=='ACCEPTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        // $tanggal_pencatatan= date_create_from_format('d-M-Y', $data['tanggal_pencatatan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($lembur_mekanik_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                        ->where('jenis', 'lembur_mekanik')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                $saldo_baru = $saldo->saldo_sekarang + (float)$lembur_mekanik_riwayat->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $kas_bank_transaksi->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                // dd('masuk sini');
                                //setelah saldonya udah ditambah, data dumpnya matiin kan transasksinya batal
                                // $kas_bank_transaksi->updated_at = now();
                                // $kas_bank_transaksi->updated_by = $user;
                                // $kas_bank_transaksi->is_aktif = 'N';
                                // $kas_bank_transaksi->save();
                                DB::table('kas_bank_transaction')
                                    ->where('keterangan_kode_transaksi', $lembur_mekanik_riwayat->id)
                                    ->where('jenis', 'lembur_mekanik')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                        'is_aktif'=> 'N',

                                    )
                                );
                            }
                            $lembur_mekanik_riwayat->id_lembur_mekanik = $lembur_mekanik->id;
                            $lembur_mekanik_riwayat->id_kas_bank = $data['kas'];
                            $lembur_mekanik_riwayat->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            // $lembur_mekanik_riwayat->tanggal_pencatatan = date_format($tanggal_pencatatan, 'Y-m-d');
                            $lembur_mekanik_riwayat->total_lembur = $lembur_mekanik->nominal_lembur;
                            $lembur_mekanik_riwayat->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $lembur_mekanik_riwayat->catatan_pencairan =$data['catatan_pencairan'];
                            $lembur_mekanik_riwayat->alasan_tolak = null;

                            $lembur_mekanik_riwayat->updated_at = now();
                            $lembur_mekanik_riwayat->updated_by = $user;
                            $lembur_mekanik_riwayat->save();   

                            //setelah itu update lagi kasbanknya, kan ini keluar uang kalo diterima
                            $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=',  $data['kas'])
                                    ->first();
                                $saldo_baru = $saldo->saldo_sekarang - floatval(str_replace(',', '', $data['total_pencairan']));
                                DB::table('kas_bank')
                                    ->where('id',  $data['kas'])
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                            //terus masukin dump lagi
                            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['kas'],// id kas_bank dr form
                                    $tanggal_pencairan,//tanggal
                                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                    floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                    CoaHelper::DataCoa(5021), //kode coa gaji (beban gaji pegawai)
                                    'lembur_mekanik',
                                    'LEMBUR MEKANIK:'.$dataDumpMekanik->nama_mekanik.' # ('.$dataDumpMekanik->no_pol.')'.'['.$dataDumpMekanik->jam_mulai_lembur .'-'.$dataDumpMekanik->jam_akhir.']', //keterangan_transaksi, //keterangan_transaksi
                                    $lembur_mekanik_riwayat->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                        }
                        else
                        {
                            //kalo nggak ada riwayatnya buat baru
                            $lembur_mekanik_riwayat_baru = new LemburMekanikRiwayat();
                            $lembur_mekanik_riwayat_baru->id_lembur_mekanik = $lembur_mekanik->id;
                            $lembur_mekanik_riwayat_baru->id_kas_bank = $data['kas'];
                            $lembur_mekanik_riwayat_baru->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            // $lembur_mekanik_riwayat_baru->tanggal_pencatatan = date_format($tanggal_pencatatan, 'Y-m-d');
                            $lembur_mekanik_riwayat_baru->total_lembur = $lembur_mekanik->nominal_lembur;
                            $lembur_mekanik_riwayat_baru->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $lembur_mekanik_riwayat_baru->catatan_pencairan =$data['catatan_pencairan'];
                            $lembur_mekanik_riwayat_baru->created_at = now();
                            $lembur_mekanik_riwayat_baru->created_by = $user;
                            $lembur_mekanik_riwayat_baru->is_aktif = 'Y';
                            // $lembur_mekanik_riwayat_baru->save(); 
                            //terus update kasbanknya keluar uang
                            if($lembur_mekanik_riwayat_baru->save())
                            {
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $lembur_mekanik_riwayat_baru->id_kas_bank )
                                    ->first();
                                    //kurangin saldo, ini kan keluar uang
                                $saldo_baru = $saldo->saldo_sekarang - (float)$lembur_mekanik_riwayat_baru->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $lembur_mekanik_riwayat_baru->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                
                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['kas'],// id kas_bank dr form
                                        $tanggal_pencairan,//tanggal
                                        0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                        CoaHelper::DataCoa(5021), //kode coa gaji (beban gaji pegawai)
                                        'lembur_mekanik',
                                        'LEMBUR MEKANIK:'.$dataDumpMekanik->nama_mekanik.' # ('.$dataDumpMekanik->no_pol.')'.'['.$dataDumpMekanik->jam_mulai_lembur .'-'.$dataDumpMekanik->jam_akhir.']', //keterangan_transaksi, //keterangan_transaksi
                                        $lembur_mekanik_riwayat_baru->id,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
                            }
                            
                        }
                    }
                // }
            }

            DB::commit();
            return redirect()->route('lembur_mekanik.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mencairkan Lembur Mekanik!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();

        } 
        // catch (\Throwable $th) {
        //     //throw $th;
        //     DB::rollBack();
        //     // return redirect()->route('lembur_mekanik.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
        //     return redirect()->back()->withErrors($th->getMessage())->withInput();

        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LemburMekanik  $lemburMekanik
     * @return \Illuminate\Http\Response
     */
    public function destroy(LemburMekanik $lemburMekanik)
    {
        //
    }
}
