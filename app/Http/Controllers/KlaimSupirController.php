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
            ->where('ks.status_klaim','like',"%PENDING%")
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
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.finance.klaim_supir.pencairan',[
            'judul'=>"Klaim Supir",
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'klaimSupir'=>$dataKlaimSupir,
            'dataKas'=>$dataKas

        ]);
    }

    public function pencairan_save(Request $request, $id)
    {
        //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try {

            $pesanKustom = [
                'tanggal_klaim.required' => 'Tanggal Klaim harap diisi!',
            ];
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_klaim' => 'required',
            ],$pesanKustom);

            $data = $request->collect();

            $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
            $tanggal_pencatatan= date_create_from_format('d-M-Y', $data['tanggal_pencatatan']);


            $klaim_supir = KlaimSupir::where('is_aktif', 'Y')
            ->where('status_klaim','like',"%PENDING%")
            ->findOrFail($id);
            $klaim_supir->tanggal_pencairan = date_format($tanggal_pencairan, 'Y-m-d');
            $klaim_supir->tanggal_pencatatan = date_format($tanggal_pencatatan, 'Y-m-d');
            $klaim_supir->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
            $klaim_supir->status_klaim = $data['status_klaim'];
            $klaim_supir->catatan_pencairan = $data['catatan_pencairan'];
            $klaim_supir->updated_by = $user;
            $klaim_supir->updated_at = now();
            $klaim_supir->save();

            DB::commit();
            return redirect()->route('klaim_supir.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mengubah Klaim Supir!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();

        }
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
