<?php

namespace App\Http\Controllers;

use App\Models\KlaimSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\SewaDataHelper;
use League\Config\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;
use Buglinjo\LaravelWebp\Webp;

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
            ->select('*','k.nama_panggilan as nama_supir','k.telp1 as telp')
            ->leftJoin('karyawan as k', function($join) {
                    $join->on('ks.karyawan_id', '=', 'k.id')->where('k.is_aktif', '=', "Y");
                })
            ->where('ks.is_aktif', '=', "Y")
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
             
                'tanggal_gabung.required' => 'Tanggal gabung Karyawan harap diisi!',
                'role.required' => 'Posisi karyawan harap diisi!',
                'telp1.required' =>'Nomor telpon 1 harap diisi ',
                'nama_lengkap.required' => 'Nama lengkap karyawan harap diisi!',
                'nama_panggilan.required' => 'Nama panggilan karyawan harap diisi!',
                // 'foto.required' => 'Foto karyawan harap diisi!',
            ];
             
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_gabung' => 'required',
                'role' => 'required',
                'telp1' =>'required',
                'nama_lengkap' => 'required',
                'nama_panggilan' => 'required',
                'foto' => 'image|mimes:jpg,png,jpeg|max:2048',

            ],$pesanKustom);
            $data = $request->collect();

            $path = "";

            if ($request->hasFile('foto')) {
                $fotoKaryawan = $request->file('foto');
                $ekstensiGambar = $fotoKaryawan->getClientOriginalExtension();
                $nama_gambar = 'karyawan_' . $data['nama_panggilan'] . '_' . time() . '.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoKaryawan);
                $webp->save(public_path('/img/fotoKaryawan/' . $nama_gambar ),20);

                // Save the original image
                // $fotoKaryawan->move(public_path('/img/fotoKaryawan'), $nama_gambar);
                $path = '/img/fotoKaryawan/' . $nama_gambar;
            }
           

            $data = $request->collect();
            DB::commit();
            return redirect()->route('klaim_supir.index')->with(['status' => 'Success', 'msg' => 'Pembayaran berhasil!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($path)) {
                    if (file_exists(public_path($path))) {
                        unlink(public_path($path));
                    }
            }
            return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($path)) {
                    if (file_exists(public_path($path))) {
                        unlink(public_path($path));
                    }
            }
            return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
            
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
