<?php

namespace App\Http\Controllers;

use App\Models\KlaimSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\SewaDataHelper;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;
use Buglinjo\LaravelWebp\Webp;
use App\Models\KasBank;
use App\Models\KlaimSupirRiawayat;
use App\Models\KasBankTransaction;
class KlaimSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $dataKlaimSupir = DB::table('klaim_supir as ks')
            ->select('ks.*','ks.id as id_klaim','k.nama_panggilan as nama_supir','k.telp1 as telp')
            ->leftJoin('karyawan as k', function($join) {
                    $join->on('ks.karyawan_id', '=', 'k.id')->where('k.is_aktif', '=', "Y");
                })
            ->where('ks.is_aktif', '=', "Y")
            // ->where('ks.status_klaim','like',"%PENDING%")
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        return view('pages.finance.klaim_supir.index',[
                'judul'=>"Klaim Supir",
            'dataKlaimSupir' => $dataKlaimSupir,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),


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
        
        try {

            $pesanKustom = [
                'tanggal_klaim.required' => 'Tanggal Klaim harap diisi!',
                'select_kendaraan.required' => 'Kendaraan harap dipilih!',
                'select_driver.required' =>'Driver harap dipilih!',
                'select_klaim.required' => 'Jenis klaim harap dipilih!',
                'total_klaim.required' => 'Total klaim harap diisi!',
                'keterangan_klaim.required' => 'Keterangan klaim harap diisi!',
                'foto_nota.required' => 'Foto nota harap diupload!',
                'foto_barang.required' => 'Foto barang harap diupload!',
            ];
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_klaim' => 'required',
                'select_kendaraan' => 'required',
                'select_driver' =>'required',
                'select_klaim' => 'required',
                'total_klaim' => 'required',
                'keterangan_klaim' => 'required',
                'foto_nota' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'foto_barang' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ],$pesanKustom);
            $data = $request->collect();

            $pathFotoNota = "";

            if ($request->hasFile('foto_nota')) {
                $fotoNota = $request->file('foto_nota');
                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                $nama_gambar = time().'_foto_nota'.'.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoNota);
                $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),20);
                $pathFotoNota = '/img/klaim_supir/' . $nama_gambar;
            }

            $pathFotoBarang = "";

            if ($request->hasFile('foto_barang')) {
                $fotoBarang= $request->file('foto_barang');
                $ekstensiGambar = $fotoBarang->getClientOriginalExtension();
                $nama_gambar = time().'_foto_barang'.'.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoBarang);
                $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),5);
                $pathFotoBarang = '/img/klaim_supir/' . $nama_gambar;
            }
            $data = $request->collect();
            $tanggal_klaim= date_create_from_format('d-M-Y', $data['tanggal_klaim']);

            $klaim_supir = new KlaimSupir();
            $klaim_supir->karyawan_id = $data['select_driver'];
            $klaim_supir->kendaraan_id = $data['select_kendaraan'];

            $klaim_supir->tanggal_klaim = date_format($tanggal_klaim, 'Y-m-d');
            $klaim_supir->jenis_klaim = $data['select_klaim'];
            $klaim_supir->total_klaim =floatval(str_replace(',', '', $data['total_klaim']));
            $klaim_supir->status_klaim = 'PENDING';
            $klaim_supir->keterangan_klaim = $data['keterangan_klaim'];
            $klaim_supir->foto_nota = $pathFotoNota;
            $klaim_supir->foto_barang = $pathFotoBarang;
            $klaim_supir->created_by = $user;
            $klaim_supir->created_at = now();
            $klaim_supir->is_aktif = 'Y';
            $klaim_supir->save();

            DB::commit();
            return redirect()->route('klaim_supir.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambahkan Klaim Supir!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($pathFotoNota)) {
                    if (file_exists(public_path($pathFotoNota))) {
                        unlink(public_path($pathFotoNota));
                    }
            }

            if (!empty($pathFotoBarang)) {
                    if (file_exists(public_path($pathFotoBarang))) {
                        unlink(public_path($pathFotoBarang));
                    }
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoNota)) {
                    if (file_exists(public_path($pathFotoNota))) {
                        unlink(public_path($pathFotoNota));
                    }
            }
              if (!empty($pathFotoBarang)) {
                    if (file_exists(public_path($pathFotoBarang))) {
                        unlink(public_path($pathFotoBarang));
                    }
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();

            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function show(KlaimSupir $klaimSupir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function edit(KlaimSupir $klaimSupir)
    {
        //
        return view('pages.finance.klaim_supir.edit',[
            'judul'=>"Klaim Supir",
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'klaimSupir'=>$klaimSupir
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KlaimSupir $klaimSupir)
    {
        //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $fotoNotaDB = $klaimSupir->foto_nota;
        $fotoBarangDB = $klaimSupir->foto_barang;
        try {

            $pesanKustom = [
                'tanggal_klaim.required' => 'Tanggal Klaim harap diisi!',
                'select_kendaraan.required' => 'Kendaraan harap dipilih!',
                'select_driver.required' =>'Driver harap dipilih!',
                'select_klaim.required' => 'Jenis klaim harap dipilih!',
                'total_klaim.required' => 'Total klaim harap diisi!',
                'keterangan_klaim.required' => 'Keterangan klaim harap diisi!',
                // 'foto_nota.required' => 'Foto nota harap diupload!',
                // 'foto_barang.required' => 'Foto barang harap diupload!',
            ];
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_klaim' => 'required',
                'select_kendaraan' => 'required',
                'select_driver' =>'required',
                'select_klaim' => 'required',
                'total_klaim' => 'required',
                'keterangan_klaim' => 'required',
                // 'foto_nota' => 'image|mimes:jpg,png,jpeg|max:2048',
                // 'foto_barang' => 'image|mimes:jpg,png,jpeg|max:2048',
            ],$pesanKustom);
            $data = $request->collect();

            $pathFotoNota = "";

            if ($request->hasFile('foto_nota')) {
                if (!empty($fotoNotaDB)) {
                    if (file_exists(public_path($fotoNotaDB))) {
                        unlink(public_path($fotoNotaDB));
                    }
                }
                $fotoNota = $request->file('foto_nota');
                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                $nama_gambar = time().'_foto_nota'.'.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoNota);
                $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),20);
                $pathFotoNota = '/img/klaim_supir/' . $nama_gambar;
            }

            $pathFotoBarang = "";

            if ($request->hasFile('foto_barang')) {
                if (!empty($fotoBarangDB)) {
                    if (file_exists(public_path($fotoBarangDB))) {
                        unlink(public_path($fotoBarangDB));
                    }
                }
                $fotoBarang= $request->file('foto_barang');
                $ekstensiGambar = $fotoBarang->getClientOriginalExtension();
                $nama_gambar = time().'_foto_barang'.'.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoBarang);
                $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),5);
                $pathFotoBarang = '/img/klaim_supir/' . $nama_gambar;
            }
            $data = $request->collect();
            $tanggal_klaim= date_create_from_format('d-M-Y', $data['tanggal_klaim']);
            // DD(date_format($tanggal_klaim, 'Y-m-d'));

            $klaim_supir = KlaimSupir::where('is_aktif', 'Y')->findOrFail($klaimSupir->id);
            $klaim_supir->karyawan_id = $data['select_driver'];
            $klaim_supir->kendaraan_id = $data['select_kendaraan'];
            $klaim_supir->tanggal_klaim = date_format($tanggal_klaim, 'Y-m-d');
            $klaim_supir->jenis_klaim = $data['select_klaim'];
            $klaim_supir->total_klaim =floatval(str_replace(',', '', $data['total_klaim']));
            $klaim_supir->keterangan_klaim = $data['keterangan_klaim'];
            if ($request->hasFile('foto_barang')) {
                $klaim_supir->foto_barang = $pathFotoBarang;
            }
            if ($request->hasFile('foto_nota')) {
                $klaim_supir->foto_nota = $pathFotoNota;
            }
            $klaim_supir->updated_by = $user;
            $klaim_supir->updated_at = now();
            $klaim_supir->save();

            DB::commit();
            return redirect()->route('klaim_supir.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mengubah Klaim Supir!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($pathFotoNota)) {
                    if (file_exists(public_path($pathFotoNota))) {
                        unlink(public_path($pathFotoNota));
                    }
            }

            if (!empty($pathFotoBarang)) {
                    if (file_exists(public_path($pathFotoBarang))) {
                        unlink(public_path($pathFotoBarang));
                    }
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoNota)) {
                    if (file_exists(public_path($pathFotoNota))) {
                        unlink(public_path($pathFotoNota));
                    }
            }
              if (!empty($pathFotoBarang)) {
                    if (file_exists(public_path($pathFotoBarang))) {
                        unlink(public_path($pathFotoBarang));
                    }
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();

            
        }
    }

    public function pencairan($id)
    {
        //
        $dataKlaimSupir = DB::table('klaim_supir as ks')
            ->select('ks.*')
            ->where('ks.is_aktif', '=', "Y")
            ->where('ks.id', '=', $id)
            ->first();
        $klaim_supir_riwayat = KlaimSupirRiawayat::where('is_aktif', 'Y')
                                    ->where('id_klaim', $id)
                                        ->first();
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.finance.klaim_supir.pencairan',[
            'judul'=>"Klaim Supir",
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'klaimSupir'=>$dataKlaimSupir,
            'klaim_supir_riwayat'=>$klaim_supir_riwayat,
            'dataKas'=>$dataKas

        ]);
    }

    public function pencairan_save(Request $request, $id)
    {
        //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        // try {
            $data = $request->collect();

            if($data['status_klaim']=='PENDING')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                ],$pesanKustom);
            }
            else if($data['status_klaim']=='REJECTED')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    'alasan_tolak.required' => 'Alasan tolak harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                    'alasan_tolak' => 'required',
                ],$pesanKustom);
            }
            else if($data['status_klaim']=='ACCEPTED')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    'catatan_pencairan.required' => 'Catatan Pencairan harap diisi!',
                    'tanggal_pencatatan.required' => 'Tanggal pencatatan harap diisi!',
                    'total_pencairan.required' => 'Total Pencairan harap diisi!',
                    'kas.required' => 'Kas bank harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                    'catatan_pencairan' => 'required',
                    'tanggal_pencatatan' => 'required',
                    'total_pencairan' => 'required',
                    'kas' => 'required',

                ],$pesanKustom);
            }
            $klaim_supir = KlaimSupir::where('is_aktif', 'Y')
            ->findOrFail($id);
            $klaim_supir->status_klaim = $data['status_klaim'];
            $klaim_supir->updated_by = $user;
            $klaim_supir->updated_at = now();
            // $klaim_supir->save();
            if($klaim_supir->save())
            {
                // dd($klaim_supir->status_klaim);
                if($klaim_supir->status_klaim=="PENDING")
                {
                    return redirect()->back()->withErrors('HARAP UBAH STATUS MENJADI TOLAK/TERIMA!!')->withInput();
                }
                else
                {
                    $klaim_supir_riwayat = KlaimSupirRiawayat::where('is_aktif', 'Y')
                                    ->where('id_klaim', $id)
                                        ->first();
                    // dd( $klaim_supir_riwayat);
                    // dd( $klaim_supir_riwayat);

                    if($klaim_supir_riwayat)
                    {
                        $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_supir_riwayat->id)
                                        ->where('jenis', 'uang_klaim_supir')
                                        ->first();

                    }
                        //   dd( $kas_bank_transaksi);

                    if ($data['status_klaim']=='PENDING') {
                        //kalo ada klaim supir riwayat yang lama

                        if($klaim_supir_riwayat)
                        {
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                $saldo_baru = $saldo->saldo_sekarang + (float)$klaim_supir_riwayat->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $kas_bank_transaksi->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                //setelah saldonya udah ditambah, data dumpnya matiin kan transasksinya batal
                                $kas_bank_transaksi->updated_at = now();
                                $kas_bank_transaksi->updated_by = $user;
                                $kas_bank_transaksi->is_aktif = 'N';
                                $kas_bank_transaksi->save();
                            }
                            //terus matiin riwayatnya yang lama
                            $klaim_supir_riwayat->updated_at = now();
                            $klaim_supir_riwayat->updated_by = $user;
                            $klaim_supir_riwayat->is_aktif = 'N';
                            $klaim_supir_riwayat->save();
                        }
                    }
                    elseif ($data['status_klaim']=='REJECTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_supir_riwayat)
                        {
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                $saldo_baru = $saldo->saldo_sekarang + (float)$klaim_supir_riwayat->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $kas_bank_transaksi->id_kas_bank)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                //setelah saldonya udah ditambah, data dumpnya matiin kan transasksinya batal
                                $kas_bank_transaksi->updated_at = now();
                                $kas_bank_transaksi->updated_by = $user;
                                $kas_bank_transaksi->is_aktif = 'N';
                                $kas_bank_transaksi->save();
                            }
                            //tanggal pencairan sama pencatatan null soalnya kan kalau tolak ga ada
                            $klaim_supir_riwayat->tanggal_pencairan = null;
                            $klaim_supir_riwayat->tanggal_pencatatan = null;
                            $klaim_supir_riwayat->total_pencairan =0;
                            $klaim_supir_riwayat->alasan_tolak = $data['alasan_tolak'];
                            $klaim_supir_riwayat->updated_at = now();
                            $klaim_supir_riwayat->updated_by = $user;
                            $klaim_supir_riwayat->save();   

                        }
                        else
                        {
                            $klaim_supir_riwayat_baru = new KlaimSupirRiawayat();
                            $klaim_supir_riwayat_baru->id_klaim = $klaim_supir->id;
                            $klaim_supir_riwayat_baru->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            $klaim_supir_riwayat_baru->tanggal_pencatatan = null;
                            $klaim_supir_riwayat_baru->total_klaim = $klaim_supir->total_klaim;
                            $klaim_supir_riwayat_baru->total_pencairan =0;
                            $klaim_supir_riwayat_baru->alasan_tolak = $data['alasan_tolak'];
                            $klaim_supir_riwayat_baru->created_at = now();
                            $klaim_supir_riwayat_baru->created_by = $user;
                            $klaim_supir_riwayat_baru->is_aktif = 'Y';
                            //  $klaim_supir_riwayat_baru->save();  
                            
                        }
                        

                    }
                    elseif ($data['status_klaim']=='ACCEPTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        $tanggal_pencatatan= date_create_from_format('d-M-Y', $data['tanggal_pencatatan']);
                    
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_supir_riwayat)
                        {
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $kas_bank_transaksi->id_kas_bank)
                                    ->first();
                                $saldo_baru = $saldo->saldo_sekarang + (float)$klaim_supir_riwayat->total_pencairan;
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
                                $kas_bank_transaksi->updated_at = now();
                                $kas_bank_transaksi->updated_by = $user;
                                $kas_bank_transaksi->is_aktif = 'N';
                                $kas_bank_transaksi->save();
                            }
                            $klaim_supir_riwayat->id_klaim = $klaim_supir->id;
                            $klaim_supir_riwayat->kas_bank_id = $data['kas'];
                            $klaim_supir_riwayat->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            $klaim_supir_riwayat->tanggal_pencatatan = date_format($tanggal_pencatatan, 'Y-m-d');
                            $klaim_supir_riwayat->total_klaim = $klaim_supir->total_klaim;
                            $klaim_supir_riwayat->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $klaim_supir_riwayat->catatan_pencairan =$data['catatan_pencairan'];
                            $klaim_supir_riwayat->updated_at = now();
                            $klaim_supir_riwayat->updated_by = $user;
                            $klaim_supir_riwayat->save();   

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
                                    $tanggal_pencatatan,//tanggal
                                    0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                    floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                    1016, //kode coa
                                    'uang_klaim_supir',
                                    'Pencairan Klaim Supir '.$klaim_supir_riwayat->id.' #'.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi, //keterangan_transaksi
                                    $klaim_supir_riwayat->id,//keterangan_kode_transaksi
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
                            $klaim_supir_riwayat_baru = new KlaimSupirRiawayat();
                            $klaim_supir_riwayat_baru->id_klaim = $klaim_supir->id;
                            $klaim_supir_riwayat_baru->kas_bank_id = $data['kas'];
                            $klaim_supir_riwayat_baru->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
                            $klaim_supir_riwayat_baru->tanggal_pencatatan = date_format($tanggal_pencatatan, 'Y-m-d');
                            $klaim_supir_riwayat_baru->total_klaim = $klaim_supir->total_klaim;
                            $klaim_supir_riwayat_baru->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $klaim_supir_riwayat_baru->catatan_pencairan =$data['catatan_pencairan'];
                            $klaim_supir_riwayat_baru->created_at = now();
                            $klaim_supir_riwayat_baru->created_by = $user;
                            $klaim_supir_riwayat_baru->is_aktif = 'Y';
                            // $klaim_supir_riwayat_baru->save(); 
                            //terus update kasbanknya keluar uang
                            if($klaim_supir_riwayat_baru->save())
                            {
                                $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $klaim_supir_riwayat_baru->kas_bank_id )
                                    ->first();
                                    //kurangin saldo, ini kan keluar uang
                                $saldo_baru = $saldo->saldo_sekarang - (float)$klaim_supir_riwayat_baru->total_pencairan;
                                DB::table('kas_bank')
                                    ->where('id', $klaim_supir_riwayat_baru->kas_bank_id)
                                    ->update(array(
                                        'saldo_sekarang' => $saldo_baru,
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                                    DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $data['kas'],// id kas_bank dr form
                                        $tanggal_pencatatan,//tanggal
                                        0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                        1016, //kode coa
                                        'uang_klaim_supir',
                                        'Pencairan Klaim Supir '.$klaim_supir_riwayat_baru->id.' #'.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi
                                        $klaim_supir_riwayat_baru->id,//keterangan_kode_transaksi
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

                }
               
            }

            DB::commit();
            return redirect()->route('klaim_supir.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mencairkan Klaim Supir!']);

        // } catch (ValidationException $e) {
        //     DB::rollBack();
        //     // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
        //     // return redirect()->back()->withErrors($e->getMessages())->withInput();
        //     return redirect()->back()->withErrors($e->errors())->withInput();

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     DB::rollBack();
        //     // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
        //     return redirect()->back()->withErrors($th->getMessage())->withInput();

        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KlaimSupir  $klaimSupir
     * @return \Illuminate\Http\Response
     */
    public function destroy(KlaimSupir $klaimSupir)
    {
        //
    }
}
