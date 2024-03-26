<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KasBank;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\KasBankTransaction;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Exception;

class KasBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_KASBANK', ['only' => ['index']]);
		$this->middleware('permission:CREATE_KASBANK', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_KASBANK', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_KASBANK', ['only' => ['destroy']]);  
    }

    public function index()
    {
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
            return view('pages.master.kas_bank.index',[
            'judul'=>"Kas Bank",
            'dataKas' => $dataKas,
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
        return view('pages.master.kas_bank.create',[
            'judul'=>"Kas Bank",
            // 'dataCOAHead' => $dataCOAHead,
            // 'dataCOADetail' => $dataCOADetail,
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
            // $tanggal = explode('-', $data['tgl_saldo']);
            //     // dd($tanggal);
            $cek_kas = false;
            $data_kas = KasBank::where('is_aktif','Y')->get();
            foreach ($data_kas as $key => $value) {
                if(strtoupper(trim($value->nama))== strtoupper(trim($data['nama'])))
                {
                    $cek_kas = true;
                    break;
                }
            }
            if($cek_kas)
            {
                return redirect()->back()->with(['status' => 'error', 'msg' => 'Data'.strtoupper($data['nama']).'sudah ada!, harap pilih nama lain']);
            }
            // $tahun =$tanggal[0];
            // $bulan =$tanggal[1];
            // $tanggal =$tanggal[2];
            // $gabungan = $tahun.'-'. $bulan.'-'. $tanggal ;
            $tgl_saldo = date_create_from_format('d-M-Y', $data['tgl_saldo']);
            // dd(date_format($tgl_saldo, 'm') == "11");
            // DB::table('kas_bank')
            //     ->insert(array(
            //         'nama' => strtoupper($data['nama']),
            //         'no_akun' => $data['no_akun']==null ? null :strtoupper($data['no_akun']) ,
            //         'tipe' => $data['tipe']==1?'KAS':'BANK',
            //         'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
            //         'tgl_saldo' => $data['tgl_saldo']==null ? null : date_format($tgl_saldo, 'Y-m-d'),
            //         'no_rek' => $data['no_rek']==null ? null : strtoupper($data['no_rek']),
            //         'rek_nama' => $data['rek_nama']==null ? null :strtoupper($data['rek_nama']) ,
            //         'bank' => $data['bank']==null ? null : strtoupper($data['bank']),
            //         'cabang' => $data['cabang']==null ? null :strtoupper($data['cabang']) ,
            //         'created_at'=>VariableHelper::TanggalFormat(), 
            //         'created_by'=> $user,
            //         'updated_at'=> VariableHelper::TanggalFormat(),
            //         'updated_by'=> $user,
            //         'is_aktif' => "Y",

            //     )
            // ); 

            $kas_bank  = new KasBank();
            $kas_bank->nama =  strtoupper($data['nama']);
            $kas_bank->no_akun = $data['no_akun']==null ? null :strtoupper($data['no_akun']) ;
            $kas_bank->tipe = $data['tipe']==1?'KAS':'BANK';
            $kas_bank->saldo_awal = $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']);
            $kas_bank->tgl_saldo = $data['tgl_saldo']==null ? null :$tgl_saldo;
            $kas_bank->no_rek = $data['no_rek']==null ? null : strtoupper($data['no_rek']);
            $kas_bank->rek_nama = $data['rek_nama']==null ? null :strtoupper($data['rek_nama']);
            $kas_bank->bank = $data['bank']==null ? null : strtoupper($data['bank']);
            $kas_bank->cabang = $data['cabang']==null ? null :strtoupper($data['cabang']) ;
            $kas_bank->created_at = now();
            $kas_bank->created_by = $user;
            $kas_bank->updated_at = now();
            $kas_bank->updated_by = $user;
            $kas_bank->is_aktif = 'Y';
            // $kas_bank->save();

            if( $kas_bank->save())
            {
                DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                    array(
                                        $kas_bank->id,// id kas_bank dr form
                                        $kas_bank->tgl_saldo ,//tanggal
                                        0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                        $kas_bank->saldo_awal   , //uang keluar (kredit), udah ke handle di front end kalau ada teluklamong
                                        1100, //kode coa uang jalan
                                        'saldo_awal',
                                        'Saldo Awal', //keterangan_transaksi
                                        null,//keterangan_kode_transaksi
                                        $user,//created_by
                                        now(),//created_at
                                        $user,//updated_by
                                        now(),//updated_at
                                        'Y'
                                    ) 
                                );
            }




            // return redirect()->route('kas_bank.index')->with('status','Sukses menambahkan Kas Bank Baru!!');
            return redirect()->route('kas_bank.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambahkan data kas!']);
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(KasBank $KasBank)
    {
        //
        // Lock the record for update
        // $updatedRecord = DB::table('kas_bank')
        //     ->where('id', $KasBank->id)
        //     ->lockForUpdate()
        //     ->first();
        //     // dd($updatedRecord);

        // if (!$updatedRecord) {
        //     return redirect()->route('kas_bank.index')->with('status', 'Data sedang diupdate oleh user lain');
        // }
        // dd($KasBank);
        return view('pages.master.kas_bank.edit',[
            'KasBank'=>$KasBank,
            'judul'=>"Kas Bank",

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KasBank $KasBank)
    {
        //
           //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

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
        // $tanggal = explode('-', $data['tgl_saldo']);
        //     // dd($tanggal);

        // $tahun =$tanggal[0];
        // $bulan =$tanggal[1];
        // $tanggal =$tanggal[2];
        // $gabungan = $tahun.'-'. $bulan.'-'. $tanggal ;
            $tgl_saldo = date_create_from_format('d-M-Y', $data['tgl_saldo']);
        
            // // dd($data);
            // DB::table('kas_bank')
            // ->where('id', $KasBank['id'])
            // ->update(array(
            //         'nama' => strtoupper($data['nama']),
            //         'no_akun' => $data['no_akun']==null ? null :strtoupper($data['no_akun']) ,
            //         'tipe' => $data['tipe']==1?'KAS':'BANK',
            //         'saldo_awal' => $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']),
            //         'tgl_saldo' => $data['tgl_saldo']==null ? null : date_format($tgl_saldo, 'Y-m-d'),
            //         'no_rek' => $data['no_rek']==null ? null : strtoupper($data['no_rek']),
            //         'rek_nama' => $data['rek_nama']==null ? null :strtoupper($data['rek_nama']) ,
            //         'bank' => $data['bank']==null ? null : strtoupper($data['bank']),
            //         'cabang' => $data['cabang']==null ? null :strtoupper($data['cabang']) ,
            //         'updated_at'=> VariableHelper::TanggalFormat(),
            //         'updated_by'=> $user,
            //         'is_aktif' => "Y",
            //     )
            // );

            $kas_bank =KasBank::where('is_aktif','Y')->find($KasBank['id']);
            if($kas_bank)
            {
                $kas_bank->nama =  strtoupper($data['nama']);
                $kas_bank->no_akun = $data['no_akun']==null ? null :strtoupper($data['no_akun']) ;
                $kas_bank->tipe = $data['tipe']==1?'KAS':'BANK';
                $kas_bank->saldo_awal = $data['saldo_awal']==null ? null : str_replace(',', '', $data['saldo_awal']);
                $kas_bank->tgl_saldo = $data['tgl_saldo']==null ? null :$tgl_saldo;
                $kas_bank->no_rek = $data['no_rek']==null ? null : strtoupper($data['no_rek']);
                $kas_bank->rek_nama = $data['rek_nama']==null ? null :strtoupper($data['rek_nama']);
                $kas_bank->bank = $data['bank']==null ? null : strtoupper($data['bank']);
                $kas_bank->cabang = $data['cabang']==null ? null :strtoupper($data['cabang']) ;
                $kas_bank->updated_at = now();
                $kas_bank->updated_by = $user;
                $kas_bank->is_aktif = 'Y';
                $kas_bank->save();
    
                // if( $kas_bank->save())
                // {
                //     $saldo_awal_transaksi = KasBankTransaction::where('is_aktif','Y')->where('jenis','saldo_awal')->find( $kas_bank->id);
                //     if($saldo_awal_transaksi)
                //     {
                //         $saldo_awal_transaksi->cabang = $data['cabang']==null ? null :strtoupper($data['cabang']) ;
                //         $saldo_awal_transaksi->updated_at = now();
                //         $saldo_awal_transaksi->updated_by = $user;
                //         $saldo_awal_transaksi->is_aktif = 'Y';
                //         $saldo_awal_transaksi->save();
                //     }
                // }
                return redirect()->route('kas_bank.index')->with(['status' => 'Success', 'msg' => 'Berhasil mengubah data kas!']);
            }
            else
            {
                return redirect()->route('kas_bank.index')->with(['status' => 'error', 'msg' => 'Data kas tidak ditemukan']);
            }
        
            // return redirect()->route('kas_bank.index')->with('status','Sukses Mengubah Data Kas Bank!!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(KasBank $KasBank)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try{
            DB::table('kas_bank')
            ->where('id', $KasBank['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );
            //  return redirect()->route('kas_bank.index')->with('status','Sukses Menghapus Data Kas Bank!!');
            return redirect()->route('kas_bank.index')->with(['status' => 'Success', 'msg' => 'Berhasil menghapus data kas!']);


        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
       
    }
    //git cobakgitgit EDWIN
    // git Timot
}
