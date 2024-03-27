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
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Helper\CoaHelper;
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
    public function revisi()
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
            // ->where('klaim_operasional.status_klaim','like',"%PENDING%")
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
         confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.revisi.revisi_klaim_operasional.index',[
             'judul'=>"Klaim Operasional",
            'dataKlaimOps' => $dataKlaimOps,
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            // 'dataDriver' => SewaDataHelper::get_supir_by_klaim_ops(),
        ]);
    }

    public function index_server(Request $request)
    {
        if ($request->ajax()) {
            
            $query = KlaimOperasional::where('is_aktif','Y')
            ->with('karyawan')
            ->with('klaimRiwayat')
            ->where('status_klaim','!=',"PENDING");
            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->whereHas('karyawan', function($query) use ($searchValue) {
                        $query->where('nama_panggilan', 'like', '%' . $searchValue . '%');
                    })
                    // $q->whereHas(['klaimRiwayat' => function ($query) use ($searchValue) {
                    //     $query->where('total_pencairan', 'like', '%' . $searchValue . '%'); 
                    // }])
                    ->orWhere('jenis_klaim', 'like', '%' . $searchValue . '%')
                    // ->orWhere('tanggal_klaim', 'like', '%' . $searchValue . '%')
                    ->orWhere('total_klaim', 'like', '%' . $searchValue . '%')
                    ->orWhere('status_klaim', 'like', '%' . $searchValue . '%')
                    ->orWhere('keterangan_klaim', 'like', '%' . $searchValue . '%');
                });
            }

            $totalData = $query->count();
            $start = $request->input('start');
            $length = $request->input('length');
            $query->skip($start)->take($length);
            $data = $query->get();

            $campur_data = [];
            foreach ($data as $value) {
                    # code...
                    // $edit=/*auth()->user()->can('EDIT_REVISI_KLAIM_SUPIR')?*/'<a href="/revisi_klaim_operasional/pencairan/'.$value->id.'" class="dropdown-item edit">
                    //                         <span class="fas fa-pencil-alt mr-3"></span> Edit Pencairan 
                    //                     </a>'/*:''*/;
                    $edit='<a href="/revisi_klaim_operasional/pencairan/'.$value->id.'" class="dropdown-item edit">
                              <span class="fas fa-pencil-alt mr-3"></span> Edit Pencairan 
                           </a>';
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        '.$edit.'
                                    </div>
                                </div>';

                    $badge_status='';
                    $pencairan='';
                    if ($value->status_klaim == 'PENDING') {
                        
                            $badge_status=  '
                            <span class="badge badge-warning">
                                MENUNGGU PERSETUJUAN
                                <i class="fas fa-solid fa-clock"></i>
                            </span>
                        ';
                        $pencairan= "Tidak ada pencairan";

                    }
                    else if($value->status_klaim == 'ACCEPTED')
                    {
                            $badge_status= '
                            <span class="badge badge-success">
                                DITERIMA
                                <i class="fas fa-regular fa-thumbs-up"></i>
                            </span>
                        ';
                        $pencairan=number_format($value->klaimRiwayat->total_pencairan);

                    }
                    else
                    {
                        $pencairan= "Tidak ada pencairan";
                            $badge_status =  '
                            <span class="badge badge-danger">
                                DITOLAK
                                <i class="fas fa-regular fa-thumbs-down"></i>
                            </span>
                        ';
                    }
                    $obj_button = [
                        'nama_supir_server'=> $value->karyawan->nama_panggilan,//1
                        'jenis_klaim_server'=> $value->jenis_klaim,//2
                        'tanggal_klaim_server'=> date('d-M-Y',strtotime($value->tanggal_klaim)),//3
                        'jumlah_klaim_server'=> number_format($value->total_klaim),//4
                        'jumlah_dicairkan_server'=>  $pencairan,//5
                        'status_klaim_server'=> $badge_status,//6
                        'keterangan_server'=> $value->keterangan_klaim,//7
                        'action_server'=> $actionBtn,//8
                    ];
                    $campur_data[] = array_merge((array)$value, $obj_button);
                }
                return response()->json([
                    'draw' => $request->input('draw'),
                    'recordsTotal' => $totalData,
                    'recordsFiltered' => $totalData,
                    'data' => $campur_data
                ]);
        }
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
                // $webp->save(public_path('/img/klaim_operasional/' . $nama_gambar ),20);
                $webp->save($src.$nama_gambar ,20);
               
                
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
                    // if (file_exists(public_path($pathFotoKlaim))) {
                    //     unlink(public_path($pathFotoKlaim));
                    // }
                     if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
                        unlink($srcUpdateDelete.$pathFotoKlaim);
                    }
                    
                    
            }
            // return redirect()->route('klaim_supir.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (!empty($pathFotoKlaim)) {
                    // if (file_exists(public_path($pathFotoKlaim))) {
                    //     unlink(public_path($pathFotoKlaim));
                    // }
                     if (file_exists($srcUpdateDelete.$pathFotoKlaim)) {
                        unlink($srcUpdateDelete.$pathFotoKlaim);
                    }
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
                // $webp->save(public_path('/img/klaim_supir/' . $nama_gambar ),20);
                $webp->save($src.$nama_gambar ,20);
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

    public function pencairan($id)
    {
        //
        $klaim_ops = KlaimOperasional::where('is_aktif', '=', "Y") 
            ->where('id', $id)
            ->first();
        $klaim_ops_riwayat = KlaimOperasionalRiwayat::where('is_aktif', 'Y')
            ->where('id_klaim_operasional', $id)
            ->first();
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.finance.klaim_operasional.pencairan',[
            'judul'=>"Pencairan klaim operasional",
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'klaim_ops' => $klaim_ops,
            'klaim_ops_riwayat' => $klaim_ops_riwayat,
            'dataKas' => $dataKas
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

             if($data['status_klaim']=='REJECTED')
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
            else if($data['status_klaim']=='ACCEPTED')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    'catatan_pencairan.required' => 'Catatan Pencairan harap diisi!',
                    'total_pencairan.required' => 'Total Pencairan harap diisi!',
                    'kas.required' => 'Kas bank harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                    'catatan_pencairan' => 'required',
                    'total_pencairan' => 'required',
                    'kas' => 'required',

                ],$pesanKustom);
            }
            $klaim_ops = KlaimOperasional::where('is_aktif', 'Y')
            ->findOrFail($id);
            if($klaim_ops->status_klaim=="PENDING" &&$data['status_klaim']=="PENDING")
            {
                return redirect()->back()->withErrors('HARAP UBAH STATUS MENJADI TOLAK/TERIMA!!')->withInput();
            }
            $klaim_ops->status_klaim = $data['status_klaim'];
            $klaim_ops->updated_by = $user;
            $klaim_ops->updated_at = now();
            // $klaim_ops->save();
            if($klaim_ops->save())
            {
                
                    $klaim_ops_riwayat = KlaimOperasionalRiwayat::where('is_aktif', 'Y')
                                    ->where('id_klaim_operasional', $id)
                                        ->first();
                
                    if ($data['status_klaim']=='PENDING') {
                        //kalo ada klaim supir riwayat yang lama

                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();

                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            //terus matiin riwayatnya yang lama
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            $klaim_ops_riwayat->is_aktif = 'N';
                                            $klaim_ops_riwayat->save();
                                        }
                                    }

                                }
                                // DB::table('kas_bank_transaction')
                                //     ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                //     ->where('jenis', 'klaim_supir')
                                //     ->where('is_aktif', 'Y')
                                //     ->update(array(
                                //         'updated_at'=> now(),
                                //         'updated_by'=> $user,
                                //         'is_aktif'=> 'N',

                                //     )
                                // );
                            }
                         
                        }
                    }
                    elseif ($data['status_klaim']=='REJECTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            //terus matiin riwayatnya yang lama
                                            //tanggal pencairan sama pencatatan null soalnya kan kalau tolak ga ada
                                            $klaim_ops_riwayat->tanggal_pencairan = null;
                                            $klaim_ops_riwayat->total_pencairan =0;
                                            $klaim_ops_riwayat->alasan_tolak = $data['alasan_tolak'];
                                            $klaim_ops_riwayat->catatan_pencairan =null;
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            $klaim_ops_riwayat->save();   
                                        }
                                    }

                                }
                            }
                            

                        }
                        else
                        {
                            $klaim_ops_riwayat_baru = new KlaimOperasionalRiwayat();
                            $klaim_ops_riwayat_baru->id_klaim_operasional = $klaim_ops->id;
                            $klaim_ops_riwayat_baru->tanggal_pencairan = $tanggal_pencairan;
                            $klaim_ops_riwayat_baru->total_klaim = $klaim_ops->total_klaim;
                            $klaim_ops_riwayat_baru->total_pencairan =0;
                            $klaim_ops_riwayat_baru->alasan_tolak = $data['alasan_tolak'];
                            $klaim_ops_riwayat_baru->created_at = now();
                            $klaim_ops_riwayat_baru->created_by = $user;
                            $klaim_ops_riwayat_baru->is_aktif = 'Y';
                            $klaim_ops_riwayat_baru->save();  
                        }
                    }
                    elseif ($data['status_klaim']=='ACCEPTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        // $tanggal_pencatatan= date_create_from_format('d-M-Y', $data['tanggal_pencatatan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            $klaim_ops_riwayat->id_klaim_operasional = $klaim_ops->id;
                                            $klaim_ops_riwayat->id_kas_bank = $data['kas'];
                                            $klaim_ops_riwayat->tanggal_pencairan = $tanggal_pencairan;
                                            $klaim_ops_riwayat->total_klaim = $klaim_ops->total_klaim;
                                            $klaim_ops_riwayat->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                                            $klaim_ops_riwayat->catatan_pencairan =$data['catatan_pencairan'];
                                            $klaim_ops_riwayat->alasan_tolak = null;
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            // $klaim_ops_riwayat->save();   
                                            if( $klaim_ops_riwayat->save())
                                            {
                                                //setelah itu update lagi kasbanknya, kan ini keluar uang kalo diacc
                                                 $saldo_baru = KasBank::where('is_aktif','Y')->find($data['kas']);
                                                 if($saldo_baru)
                                                 {
                                                    $saldo_baru->saldo_sekarang -= floatval(str_replace(',', '', $data['total_pencairan']));
                                                    $saldo_baru->updated_at = now();
                                                    $saldo_baru->updated_by = $user;
                                                    // $saldo_lama->save();
                                                    if($saldo_baru->save())
                                                    {
                                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                            array(
                                                                $data['kas'],// id kas_bank dr form
                                                                $tanggal_pencairan,//tanggal
                                                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                                floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                                                CoaHelper::DataCoa(5004), //kode coa
                                                                'klaim_operasional',
                                                                'Pencairan Klaim Operasional '.$klaim_ops_riwayat->id.' >> '.$data['jenis_klaim'].' >> '.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi, //keterangan_transaksi
                                                                $klaim_ops_riwayat->id,//keterangan_kode_transaksi
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
                                }
                            }
                        }
                        else
                        {
                            //kalo nggak ada riwayatnya buat baru
                            $klaim_ops_riwayat_baru = new KlaimOperasionalRiwayat();
                            $klaim_ops_riwayat_baru->id_klaim_operasional = $klaim_ops->id;
                            $klaim_ops_riwayat_baru->id_kas_bank = $data['kas'];
                            $klaim_ops_riwayat_baru->tanggal_pencairan = $tanggal_pencairan;
                            $klaim_ops_riwayat_baru->total_klaim = $klaim_ops->total_klaim;
                            $klaim_ops_riwayat_baru->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $klaim_ops_riwayat_baru->catatan_pencairan =$data['catatan_pencairan'];
                            $klaim_ops_riwayat_baru->created_at = now();
                            $klaim_ops_riwayat_baru->created_by = $user;
                            $klaim_ops_riwayat_baru->is_aktif = 'Y';
                            //terus update kasbanknya keluar uang
                            if($klaim_ops_riwayat_baru->save())
                            {
                                 //setelah itu update lagi kasbanknya, kan ini keluar uang kalo diacc
                                 $saldo_baru = KasBank::where('is_aktif','Y')->find($data['kas']);
                                 if($saldo_baru)
                                 {
                                    $saldo_baru->saldo_sekarang -= floatval(str_replace(',', '', $data['total_pencairan']));
                                    $saldo_baru->updated_at = now();
                                    $saldo_baru->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_baru->save())
                                    {
                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                            array(
                                                $data['kas'],// id kas_bank dr form
                                                $tanggal_pencairan,//tanggal
                                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                                CoaHelper::DataCoa(5004), //kode coa klaim supir (biaya servis)
                                                'klaim_operasional',
                                                'Pencairan Klaim Operasional '.$klaim_ops_riwayat_baru->id.' >> '.$data['jenis_klaim'].' >> '.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi
                                                $klaim_ops_riwayat_baru->id,//keterangan_kode_transaksi
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

                // }
            }
            DB::commit();
            return redirect()->route('klaim_operasional.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mencairkan Klaim Operasional!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            // return redirect()->route('klaim_operasional.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('klaim_operasional.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }
    public function revisi_pencairan($id)
    {
        //
        $klaim_ops = KlaimOperasional::where('is_aktif', '=', "Y") 
            ->where('id', $id)
            ->first();
        $klaim_ops_riwayat = KlaimOperasionalRiwayat::where('is_aktif', 'Y')
            ->where('id_klaim_operasional', $id)
            ->first();
        $dataKas = KasBank::where('is_aktif', 'Y')->orderBy('nama', 'ASC')->get();

        return view('pages.revisi.revisi_klaim_operasional.pencairan',[
            'judul'=>"Pencairan klaim operasional",
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'klaim_ops' => $klaim_ops,
            'klaim_ops_riwayat' => $klaim_ops_riwayat,
            'dataKas' => $dataKas
        ]);
    }
    public function revisi_pencairan_save(Request $request, $id)
    {
        //
        //
        DB::beginTransaction(); 
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try {
            $data = $request->collect();

             if($data['status_klaim']=='REJECTED')
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
            else if($data['status_klaim']=='ACCEPTED')
            {
                $pesanKustom = [
                    'tanggal_pencairan.required' => 'Tanggal Pencairan harap diisi!',
                    'catatan_pencairan.required' => 'Catatan Pencairan harap diisi!',
                    'total_pencairan.required' => 'Total Pencairan harap diisi!',
                    'kas.required' => 'Kas bank harap diisi!',
                ];
                
                $request->validate([
                    'tanggal_pencairan' => 'required',
                    'catatan_pencairan' => 'required',
                    'total_pencairan' => 'required',
                    'kas' => 'required',

                ],$pesanKustom);
            }
            $klaim_ops = KlaimOperasional::where('is_aktif', 'Y')
            ->findOrFail($id);
            if($klaim_ops->status_klaim=="PENDING" &&$data['status_klaim']=="PENDING")
            {
                return redirect()->back()->withErrors('HARAP UBAH STATUS MENJADI TOLAK/TERIMA!!')->withInput();
            }
            $klaim_ops->status_klaim = $data['status_klaim'];
            $klaim_ops->updated_by = $user;
            $klaim_ops->updated_at = now();
            // $klaim_ops->save();
            if($klaim_ops->save())
            {
                
                    $klaim_ops_riwayat = KlaimOperasionalRiwayat::where('is_aktif', 'Y')
                                    ->where('id_klaim_operasional', $id)
                                        ->first();
                
                    if ($data['status_klaim']=='PENDING') {
                        //kalo ada klaim supir riwayat yang lama

                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();

                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            //terus matiin riwayatnya yang lama
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            $klaim_ops_riwayat->is_aktif = 'N';
                                            $klaim_ops_riwayat->save();
                                        }
                                    }

                                }
                                // DB::table('kas_bank_transaction')
                                //     ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                //     ->where('jenis', 'klaim_supir')
                                //     ->where('is_aktif', 'Y')
                                //     ->update(array(
                                //         'updated_at'=> now(),
                                //         'updated_by'=> $user,
                                //         'is_aktif'=> 'N',

                                //     )
                                // );
                            }
                         
                        }
                    }
                    elseif ($data['status_klaim']=='REJECTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            //terus matiin riwayatnya yang lama
                                            //tanggal pencairan sama pencatatan null soalnya kan kalau tolak ga ada
                                            $klaim_ops_riwayat->tanggal_pencairan = null;
                                            $klaim_ops_riwayat->total_pencairan =0;
                                            $klaim_ops_riwayat->alasan_tolak = $data['alasan_tolak'];
                                            $klaim_ops_riwayat->catatan_pencairan =null;
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            $klaim_ops_riwayat->save();   
                                        }
                                    }

                                }
                            }
                            

                        }
                        else
                        {
                            $klaim_ops_riwayat_baru = new KlaimOperasionalRiwayat();
                            $klaim_ops_riwayat_baru->id_klaim_operasional = $klaim_ops->id;
                            $klaim_ops_riwayat_baru->tanggal_pencairan = $tanggal_pencairan;
                            $klaim_ops_riwayat_baru->total_klaim = $klaim_ops->total_klaim;
                            $klaim_ops_riwayat_baru->total_pencairan =0;
                            $klaim_ops_riwayat_baru->alasan_tolak = $data['alasan_tolak'];
                            $klaim_ops_riwayat_baru->created_at = now();
                            $klaim_ops_riwayat_baru->created_by = $user;
                            $klaim_ops_riwayat_baru->is_aktif = 'Y';
                            $klaim_ops_riwayat_baru->save();  
                        }
                    }
                    elseif ($data['status_klaim']=='ACCEPTED') {
                        $tanggal_pencairan= date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                        // $tanggal_pencatatan= date_create_from_format('d-M-Y', $data['tanggal_pencatatan']);
                        //kalo ada klaim supir riwayat yang lama
                        if($klaim_ops_riwayat)
                        {
                            $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $klaim_ops_riwayat->id)
                                        ->where('jenis', 'klaim_operasional')
                                        ->first();
                            //kalo ada kas bank transaksi (dumpnya itu)
                            if($kas_bank_transaksi)
                            {
                                //  dd($kas_bank_transaksi);

                                //select saldo dulu buat nambah dr datayang lama kan mau di matiin dumpnya,makannya banknya saldonya ditambah
                                $saldo_lama = KasBank::where('is_aktif','Y')->find($kas_bank_transaksi->id_kas_bank);
                                if($saldo_lama)
                                {
                                    $saldo_lama->saldo_sekarang += (float)$klaim_ops_riwayat->total_pencairan;
                                    $saldo_lama->updated_at = now();
                                    $saldo_lama->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_lama->save())
                                    {
                                        $kas_bank_transaksi->updated_at = now();
                                        $kas_bank_transaksi->updated_by = $user;
                                        $kas_bank_transaksi->is_aktif = 'N';
                                        // $kas_bank_transaksi->save(); 
                                        if( $kas_bank_transaksi->save())
                                        {
                                            $klaim_ops_riwayat->id_klaim_operasional = $klaim_ops->id;
                                            $klaim_ops_riwayat->id_kas_bank = $data['kas'];
                                            $klaim_ops_riwayat->tanggal_pencairan = $tanggal_pencairan;
                                            $klaim_ops_riwayat->total_klaim = $klaim_ops->total_klaim;
                                            $klaim_ops_riwayat->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                                            $klaim_ops_riwayat->catatan_pencairan =$data['catatan_pencairan'];
                                            $klaim_ops_riwayat->alasan_tolak = null;
                                            $klaim_ops_riwayat->updated_at = now();
                                            $klaim_ops_riwayat->updated_by = $user;
                                            // $klaim_ops_riwayat->save();   
                                            if( $klaim_ops_riwayat->save())
                                            {
                                                //setelah itu update lagi kasbanknya, kan ini keluar uang kalo diacc
                                                 $saldo_baru = KasBank::where('is_aktif','Y')->find($data['kas']);
                                                 if($saldo_baru)
                                                 {
                                                    $saldo_baru->saldo_sekarang -= floatval(str_replace(',', '', $data['total_pencairan']));
                                                    $saldo_baru->updated_at = now();
                                                    $saldo_baru->updated_by = $user;
                                                    // $saldo_lama->save();
                                                    if($saldo_baru->save())
                                                    {
                                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                                            array(
                                                                $data['kas'],// id kas_bank dr form
                                                                $tanggal_pencairan,//tanggal
                                                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                                floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                                                CoaHelper::DataCoa(5004), //kode coa
                                                                'klaim_operasional',
                                                                'Pencairan Klaim Operasional '.$klaim_ops_riwayat->id.' >> '.$data['jenis_klaim'].' >> '.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi, //keterangan_transaksi
                                                                $klaim_ops_riwayat->id,//keterangan_kode_transaksi
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
                                }
                            }
                        }
                        else
                        {
                            //kalo nggak ada riwayatnya buat baru
                            $klaim_ops_riwayat_baru = new KlaimOperasionalRiwayat();
                            $klaim_ops_riwayat_baru->id_klaim_operasional = $klaim_ops->id;
                            $klaim_ops_riwayat_baru->id_kas_bank = $data['kas'];
                            $klaim_ops_riwayat_baru->tanggal_pencairan = $tanggal_pencairan;
                            $klaim_ops_riwayat_baru->total_klaim = $klaim_ops->total_klaim;
                            $klaim_ops_riwayat_baru->total_pencairan =floatval(str_replace(',', '', $data['total_pencairan']));
                            $klaim_ops_riwayat_baru->catatan_pencairan =$data['catatan_pencairan'];
                            $klaim_ops_riwayat_baru->created_at = now();
                            $klaim_ops_riwayat_baru->created_by = $user;
                            $klaim_ops_riwayat_baru->is_aktif = 'Y';
                            //terus update kasbanknya keluar uang
                            if($klaim_ops_riwayat_baru->save())
                            {
                                 //setelah itu update lagi kasbanknya, kan ini keluar uang kalo diacc
                                 $saldo_baru = KasBank::where('is_aktif','Y')->find($data['kas']);
                                 if($saldo_baru)
                                 {
                                    $saldo_baru->saldo_sekarang -= floatval(str_replace(',', '', $data['total_pencairan']));
                                    $saldo_baru->updated_at = now();
                                    $saldo_baru->updated_by = $user;
                                    // $saldo_lama->save();
                                    if($saldo_baru->save())
                                    {
                                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                            array(
                                                $data['kas'],// id kas_bank dr form
                                                $tanggal_pencairan,//tanggal
                                                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                                floatval(str_replace(',', '', $data['total_pencairan'])), //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                                CoaHelper::DataCoa(5004), //kode coa klaim supir (biaya servis)
                                                'klaim_operasional',
                                                'Pencairan Klaim Operasional '.$klaim_ops_riwayat_baru->id.' >> '.$data['jenis_klaim'].' >> '.$data['no_polisi'].'-'.$data['driver_nama'], //keterangan_transaksi
                                                $klaim_ops_riwayat_baru->id,//keterangan_kode_transaksi
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

                // }
            }
            DB::commit();
            return redirect()->route('klaim_operasional_revisi.index')->with(['status' => 'Success', 'msg' => 'Berhasil Mencairkan Klaim Operasional!']);

        } catch (ValidationException $e) {
            DB::rollBack();
            // return redirect()->route('klaim_operasional_revisi.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->getMessages())->withInput();
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('klaim_operasional_revisi.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
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
        $src="/home/pjexpres/tms.pjexpress.co.id";
        try{
            $fotoKlaimDb = $klaimOperasional->foto_nota;
            // if (!empty($fotoKlaimDb)) {
            //     if (file_exists(public_path($fotoKlaimDb))) {
            //         unlink(public_path($fotoKlaimDb));
            //     }
                
            // }
           
            if (!empty($fotoKlaimDb)) {
                if (file_exists($src.$fotoKlaimDb)) {
                    unlink($src.$fotoKlaimDb);
                }
                
            }
           
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
