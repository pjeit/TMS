<?php

namespace App\Http\Controllers;

use App\Models\KlaimOperasional;
use App\Models\KlaimOperasionalRiwayat;
use Illuminate\Http\Request;
use App\Helper\SewaDataHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Buglinjo\LaravelWebp\Webp;

class KlaimOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataKlaimOps = KlaimOperasional::select('klaim_operasional.*','klaim_operasional.id as id_klaim','k.nama_panggilan as nama_supir','k.telp1 as telp','kor.total_pencairan')
            ->leftJoin('karyawan as k', function($join) {
                    $join->on('klaim_operasional.id_karyawan', '=', 'k.id')->where('k.is_aktif', '=', "Y");
                })
             ->leftJoin('klaim_operasional_riwayat as kor', function($join) {
                    $join->on('klaim_operasional.id', '=', 'kor.id_klaim_operasional')->where('kor.is_aktif', '=', "Y");
                })
            ->where('klaim_operasional.is_aktif', '=', "Y")
            ->where('klaim_operasional.status_klaim','like',"%PENDING%")
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
         confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.klaim_operasional.index',[
             'judul'=>"Klaim Operasional",
            'dataKlaimOps' => $dataKlaimOps,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            // 'dataDriver' => SewaDataHelper::get_supir_by_klaim_ops(),
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
        $src="/home/pjexpres/tms.pjexpress.co.id/img/klaim_operasional/";
        $srcUpdateDelete="/home/pjexpres/tms.pjexpress.co.id";
        try {

            $pesanKustom = [
                'tanggal_klaim.required' => 'Tanggal Klaim harap diisi!',
                'select_kendaraan.required' => 'Kendaraan harap dipilih!',
                'select_driver.required' =>'Driver harap dipilih!',
                'select_klaim.required' => 'Jenis klaim harap dipilih!',
                'total_klaim.required' => 'Total klaim harap diisi!',
                'keterangan_klaim.required' => 'Keterangan klaim harap diisi!',
                'foto_klaim.required' => 'Foto nota harap diupload!',
            ];
            
            $request->validate([
                // 'telp1' =>'required|in:1,2',  // buat radio button
                'tanggal_klaim' => 'required',
                'select_kendaraan' => 'required',
                'select_driver' =>'required',
                'select_klaim' => 'required',
                'total_klaim' => 'required',
                'keterangan_klaim' => 'required',
                'foto_klaim' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ],$pesanKustom);

            $pathFotoKlaim = "";
            $data = $request->collect();

            if ($request->hasFile('foto_klaim')) {
                $fotoNota = $request->file('foto_klaim');
                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                $nama_gambar = time().'_foto_klaim_ops_'.$data['select_klaim'].'_'.$data['driver_nama'].'.' . $ekstensiGambar;

                // Convert and save the image to WebP format
                $webp = Webp::make($fotoNota);
                $webp->save(public_path('/img/klaim_operasional/' . $nama_gambar ),20);
                // $webp->save($src.$nama_gambar ,20);
               
                
                $pathFotoKlaim = '/img/klaim_operasional/' . $nama_gambar;
            }

           
            $tanggal_klaim= date_create_from_format('d-M-Y', $data['tanggal_klaim']);

            $klaim_operasional = new KlaimOperasional();
            $klaim_operasional->id_sewa = $data['id_sewa_hidden'];
            $klaim_operasional->id_karyawan = $data['select_driver'];
            $klaim_operasional->id_kendaraan = $data['select_kendaraan'];
            $klaim_operasional->tanggal_klaim = date_format($tanggal_klaim, 'Y-m-d');
            $klaim_operasional->jenis_klaim = $data['select_klaim'];
            $klaim_operasional->total_klaim =floatval(str_replace(',', '', $data['total_klaim']));
            $klaim_operasional->status_klaim = 'PENDING';
            $klaim_operasional->keterangan_klaim = $data['keterangan_klaim'];
            $klaim_operasional->foto_klaim = $pathFotoKlaim;
            $klaim_operasional->created_by = $user;
            $klaim_operasional->created_at = now();
            $klaim_operasional->is_aktif = 'Y';
            $klaim_operasional->save();

            DB::commit();
            return redirect()->route('klaim_operasional.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambahkan Klaim Operasional!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($pathFotoKlaim)) {
                    if (file_exists(public_path($pathFotoKlaim))) {
                        unlink(public_path($pathFotoKlaim));
                    }
                    //  if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
                    //     unlink($srcUpdateDelete.$pathFotoKlaim);
                    // }
                    
                    
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoKlaim)) {
                    if (file_exists(public_path($pathFotoKlaim))) {
                        unlink(public_path($pathFotoKlaim));
                    }
                    //  if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
                    //     unlink($srcUpdateDelete.$pathFotoKlaim);
                    // }
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KlaimOperasional  $klaimOperasional
     * @return \Illuminate\Http\Response
     */
    public function show(KlaimOperasional $klaimOperasional)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KlaimOperasional  $klaimOperasional
     * @return \Illuminate\Http\Response
     */
    public function edit(KlaimOperasional $klaimOperasional)
    {
        //
        return view('pages.finance.klaim_operasional.edit',[
            'judul'=>"Klaim Operasional",
            'klaimOperasional' => $klaimOperasional,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KlaimOperasional  $klaimOperasional
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KlaimOperasional $klaimOperasional)
    {
        //
         //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $foto_klaim_DB = $klaimOperasional->foto_klaim;
        $src="/home/pjexpres/tms.pjexpress.co.id/img/klaim_operasional/";
        $srcUpdateDelete="/home/pjexpres/tms.pjexpress.co.id";
        try {

            $pesanKustom = [
                'tanggal_klaim.required' => 'Tanggal Klaim harap diisi!',
                // 'select_kendaraan.required' => 'Kendaraan harap dipilih!',
                // 'select_driver.required' =>'Driver harap dipilih!',
                // 'select_klaim.required' => 'Jenis klaim harap dipilih!',
                'total_klaim.required' => 'Total klaim harap diisi!',
                'keterangan_klaim.required' => 'Keterangan klaim harap diisi!',
                // 'foto_klaim.required' => 'Foto nota harap diupload!',
            ];
            
            $request->validate([
                'tanggal_klaim' => 'required',
                // 'select_kendaraan' => 'required',
                // 'select_driver' =>'required',
                // 'select_klaim' => 'required',
                'total_klaim' => 'required',
                'keterangan_klaim' => 'required',
                // 'foto_klaim' => 'image|mimes:jpg,png,jpeg|max:2048',
            ],$pesanKustom);
            $data = $request->collect();

            $pathFotoKlaim = "";
            if ($request->hasFile('foto_klaim')) {
                // if (!empty($fotoNotaDB)) {
                //     if (file_exists(public_path($fotoNotaDB))) {
                //         unlink(public_path($fotoNotaDB));
                //     }
                // }
                if (!empty($fotoNotaDB)) {
                    if (file_exists($srcUpdateDelete.$fotoNotaDB)) {
                        unlink($srcUpdateDelete.$fotoNotaDB);
                    }
                }
                $fotoNota = $request->file('foto_klaim');
                $ekstensiGambar = $fotoNota->getClientOriginalExtension();
                // $nama_gambar = time().'_foto_klaim_ops_'.$data['select_klaim'].'_'.$data['driver_nama'].'.' . $ekstensiGambar;
                $nama_gambar = time().'_foto_klaim_ops_'.$klaimOperasional->jenis_klaim.'_'.$data['driver_nama'].'.' . $ekstensiGambar;


                // Convert and save the image to WebP format
                $webp = Webp::make($fotoNota);
                $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),20);
                // $webp->save($src.$nama_gambar ,20);
                $pathFotoKlaim = '/img/klaim_supir/' . $nama_gambar;
            }

            $data = $request->collect();
            $tanggal_klaim= date_create_from_format('d-M-Y', $data['tanggal_klaim']);
            $klaim_operasional = KlaimOperasional::where('is_aktif', 'Y')->findOrFail($klaimOperasional->id);
            // $klaim_operasional->id_sewa = $data['id_sewa_hidden'];
            // $klaim_operasional->id_karyawan = $data['select_driver'];
            // $klaim_operasional->id_kendaraan = $data['select_kendaraan'];
            $klaim_operasional->tanggal_klaim = date_format($tanggal_klaim, 'Y-m-d');
            // $klaim_operasional->jenis_klaim = $data['select_klaim'];
            $klaim_operasional->total_klaim =floatval(str_replace(',', '', $data['total_klaim']));
            // $klaim_operasional->status_klaim = 'PENDING';
            $klaim_operasional->keterangan_klaim = $data['keterangan_klaim'];
            if ($request->hasFile('foto_klaim')) {
                $klaim_operasional->foto_klaim = $pathFotoKlaim;
            }
            $klaim_operasional->foto_klaim = $pathFotoKlaim;
            $klaim_operasional->updated_at = now();
            $klaim_operasional->updated_by = $user;
            $klaim_operasional->is_aktif = 'Y';
            $klaim_operasional->save();
            DB::commit();
            return redirect()->route('klaim_operasional.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mengubah Klaim Operasional!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            if (!empty($pathFotoKlaim)) {
                    if (file_exists(public_path($pathFotoKlaim))) {
                        unlink(public_path($pathFotoKlaim));
                    }
            }

            // if (!empty($pathFotoKlaim)) {
            //     if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
            //         unlink($srcUpdateDelete.$pathFotoKlaim);
            //     }
            // }

            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoKlaim)) {
                    if (file_exists(public_path($pathFotoKlaim))) {
                        unlink(public_path($pathFotoKlaim));
                    }
            }
            // if (!empty($pathFotoKlaim)) {
            //     if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
            //         unlink($srcUpdateDelete.$pathFotoKlaim);
            //     }
            // }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $th->getMessage()]);
            return redirect()->back()->withErrors($th->getMessage())->withInput();

            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KlaimOperasional  $klaimOperasional
     * @return \Illuminate\Http\Response
     */
    public function destroy(KlaimOperasional $klaimOperasional)
    {
        //
        $user = Auth::user()->id; 
        // dd($klaimSupir);   
        $src="/home/pjexpres/tms.pjexpress.co.id";
        try{
            // $klaim_supir_riwayat = KlaimSupirRiawayat::where('is_aktif', 'Y')
            //                        ->where('id_klaim', $klaimSupir->id)
            //                        ->first();
            // dd($klaim_supir_riwayat);   

            // $klaim_supir_riwayat->updated_at = now();
            // $klaim_supir_riwayat->updated_by = $user;
            // $klaim_supir_riwayat->is_aktif = 'N';
            // $klaim_supir_riwayat->save();  
            $fotoKlaimDb = $klaimOperasional->foto_nota;
           
            if (!empty($fotoKlaimDb)) {
                if (file_exists(public_path($fotoKlaimDb))) {
                    unlink(public_path($fotoKlaimDb));
                }
                
            }
           
            // if (!empty($fotoKlaimDb)) {
            //     if (file_exists($src.$fotoKlaimDb)) {
            //         unlink($src.$fotoKlaimDb);
            //     }
                
            // }
           
            $klaim_operasional = KlaimOperasional::where('is_aktif', 'Y')
            ->findOrFail($klaimOperasional->id);
            $klaim_operasional->updated_by = $user;
            $klaim_operasional->updated_at = now();
            $klaim_operasional->is_aktif = 'N';
            $klaim_operasional->save();
            return redirect()->route('klaim_operasional.index')->with(['status' => 'Success', 'msg' => 'Berhasil Menghapus Data Klaim Supir!']);
        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
