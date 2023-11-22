<?php

namespace App\Http\Controllers;

use App\Models\StatusKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;

class StatusKendaraanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_STATUS_KENDARAAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_STATUS_KENDARAAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_STATUS_KENDARAAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_STATUS_KENDARAAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $dataStatusKendaraan = DB::table('status_kendaraan as sk')
            ->select('sk.*','sk.id as idStatusKendaraan','k.no_polisi')
            ->leftJoin('kendaraan as k', function($join) {
                $join->on('sk.kendaraan_id', '=', 'k.id')->where('k.is_aktif', '=', "Y");
            })
            ->whereNull('sk.tanggal_selesai')
            ->where('sk.is_selesai',  "N")
            ->where('sk.is_aktif',  "Y")
            ->get();
        $dataKendaraan=DB::table('kendaraan as k')
            ->select('k.*','kkm.nama as kategoriKendaraan')
            ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
            ->where('k.is_aktif', '=', "Y")
            ->get();
        return view('pages.asset.status_kendaraan.index',[
            'judul'=>"Status Kendaraan",
            'dataStatusKendaraan' => $dataStatusKendaraan,
            'dataKendaraan' => $dataKendaraan,
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
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        // dd($data); 
        try {
            $pesanKustom = [
                'select_kendaraan.required' => 'Kendaraan wajib dipilih!',
            ];
            $request->validate([
                'select_kendaraan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();

            $tanggal_mulai=isset( $data['tanggal_mulai'])?date_create_from_format('d-M-Y', $data['tanggal_mulai']):null;
            $tanggal_selesai=isset( $data['tanggal_selesai'])? date_create_from_format('d-M-Y', $data['tanggal_selesai']):null;

            $status_kendaraan = new StatusKendaraan();
            $status_kendaraan->kendaraan_id = $data['select_kendaraan'];
            $status_kendaraan->tanggal_mulai = isset( $data['tanggal_mulai'])? date_format($tanggal_mulai, 'Y-m-d h:i:s'):null;
            $status_kendaraan->is_selesai = $data['is_selesai'];
            $status_kendaraan->tanggal_selesai = isset( $data['tanggal_selesai'])? date_format($tanggal_selesai, 'Y-m-d h:i:s'):null;
            $status_kendaraan->detail_perawatan = $data['detail_perawatan'];
            $status_kendaraan->created_by = $user;
            $status_kendaraan->created_at = now();
            $status_kendaraan->save();

            DB::commit();
            return redirect()->route('status_kendaraan.index')->with(['status' => 'Success', 'msg' => 'Status kendaraan berhasil dibuat!']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();

            // return redirect()->route('status_kendaraan.index')->with(['status' => 'error', 'msg' => 'Status kendaraan gagal dibuat!']);
        }
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function show(StatusKendaraan $statusKendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusKendaraan $status_kendaraan)
    {
        //
        $dataStatusKendaraan = DB::table('status_kendaraan as sk')
            ->select('sk.*')
            ->where('sk.is_aktif', '=', "Y")
            ->where('sk.id',$status_kendaraan->id)
            ->first();
        $dataKendaraan=DB::table('kendaraan as k')
            ->select('k.*','kkm.nama as kategoriKendaraan')
            ->leftJoin('kendaraan_kategori AS kkm', 'k.id_kategori', '=', 'kkm.id')
            ->where('k.is_aktif', '=', "Y")
            ->get();
        return view('pages.asset.status_kendaraan.edit',[
            'judul'=>"Status Kendaraan",
            'dataStatusKendaraan' => $dataStatusKendaraan,
            'dataKendaraan' => $dataKendaraan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StatusKendaraan $status_kendaraan)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        // dd($data); 
        try {
            $pesanKustom = [
                'select_kendaraan.required' => 'Kendaraan wajib dipilih!',
            ];
            $request->validate([
                'select_kendaraan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();

            $tanggal_mulai=isset( $data['tanggal_mulai'])?date_create_from_format('d-M-Y', $data['tanggal_mulai']):null;
            $tanggal_selesai=isset( $data['tanggal_selesai'])? date_create_from_format('d-M-Y', $data['tanggal_selesai']):null;

            $status_kendaraan = StatusKendaraan::where('is_aktif', 'Y')->findOrFail($status_kendaraan->id);
            $status_kendaraan->kendaraan_id = $data['select_kendaraan'];
            $status_kendaraan->tanggal_mulai = isset( $data['tanggal_mulai'])? date_format($tanggal_mulai, 'Y-m-d h:i:s'):null;
            $status_kendaraan->is_selesai = $data['is_selesai'];
            $status_kendaraan->tanggal_selesai = isset( $data['tanggal_selesai'])? date_format($tanggal_selesai, 'Y-m-d h:i:s'):null;
            $status_kendaraan->detail_perawatan = $data['detail_perawatan'];
            $status_kendaraan->updated_by = $user;
            $status_kendaraan->updated_at = now();
            $status_kendaraan->save();
            
            DB::commit();
            return redirect()->route('status_kendaraan.index')->with(['status' => 'Success', 'msg' => 'Status kendaraan berhasil diubah!']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();

            // return redirect()->route('status_kendaraan.index')->with(['status' => 'error', 'msg' => 'Status kendaraan gagal dibuat!']);
        }
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusKendaraan $status_kendaraan)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data); 
        try {
            $status_kendaraan = StatusKendaraan::where('is_aktif', 'Y')->findOrFail($status_kendaraan->id);
            $status_kendaraan->updated_by = $user;
            $status_kendaraan->updated_at = now();
            $status_kendaraan->is_aktif = 'N';
            $status_kendaraan->save();
            DB::commit();
            return redirect()->route('status_kendaraan.index')->with(['status' => 'Success', 'msg' => 'Status kendaraan berhasil dihapus!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
            // return redirect()->route('status_kendaraan.index')->with(['status' => 'error', 'msg' => 'Status kendaraan gagal dibuat!']);
        }
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }
}
