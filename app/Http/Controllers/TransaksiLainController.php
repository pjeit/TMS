<?php

namespace App\Http\Controllers;

use App\Models\TransaksiLain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Coa;
use Exception;
class TransaksiLainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataKasLain= DB::table('kas_bank_lain as ksl')
            ->select('ksl.*','c.nama_jenis')
            ->leftJoin('coa as c', function($join) {
                    $join->on('ksl.coa_id', '=', 'c.id')
                    ->where('c.is_kas_bank_lain', '=', "Y")
                    ->where('c.is_aktif', '=', "Y");
                })
            ->where('ksl.is_aktif', '=', "Y")
            ->get();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();

         $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            // ->paginate(10);
            ->where('coa.is_kas_bank_lain', '=', "Y")
            ->get();
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
         confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.transaksi_lain.index',[
             'judul'=>"Transaksi Lain",
            'dataKasLain' => $dataKasLain,
            'dataKas' => $dataKas,
            'dataCOA' => $dataCOA,
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

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi!',
                'select_coa.required' => 'Jenis Transaksi wajib dipilih!',
                'select_bank.required' => 'Kas/Bank  wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_transaksi' => 'required',
                'select_coa' => 'required',
                'select_bank' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_transaksi']);
                // dd(date_format($tanggal, 'Y-m-d h:i:s'));
                $new_transaksi = new TransaksiLain();
                $new_transaksi->tanggal = date_format($tanggal, 'Y-m-d h:i:s');
                $new_transaksi->tanggal_catat = now();
                $new_transaksi->coa_id = $data['select_coa'];
                $new_transaksi->kas_bank_id = $data['select_bank'];
                $new_transaksi->total = floatval(str_replace(',', '', $data['total']));
                $new_transaksi->catatan = $data['catatan'];
                $new_transaksi->created_by = $user;
                $new_transaksi->created_at = now();
                $new_transaksi->is_aktif = 'Y';
                // $new_transaksi->save();
                if ($new_transaksi->save()) {
                    $coa = Coa::where('is_aktif', 'Y')
                                    ->where('id', $data['select_coa'])
                                    ->first();
                    $kas_bank = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank'])
                                    ->first();
                    if ($coa->tipe=='pengeluaran') {
                        $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                    }
                    else
                    {
                        $kas_bank->saldo_sekarang +=  floatval(str_replace(',', '', $data['total']));
                    }
                    $kas_bank->updated_at = now();
                    $kas_bank->updated_by = $user;
                    // $kas_bank->save();
                    if($kas_bank->save())
                    {
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['select_bank'],// id kas_bank dr form
                                    date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                    $coa->tipe=='penerimaan'?(float)str_replace(',', '', $data['total']):0,// debit 
                                    $coa->tipe=='pengeluaran'?(float)str_replace(',', '', $data['total']):0, //kredit
                                    $data['id_coa_hidden'], //kode coa
                                    'lainnya',
                                    $data['nama_coa_hidden'].'-'.$data['catatan'], //keterangan_transaksi
                                    $new_transaksi->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                    }
                    
                }
                DB::commit();

                return redirect()->route('transaksi_lain.index')->with(['status' => 'Success', 'msg'  => 'Transaksi lain berhasil dibuat!']);

        } catch (ValidationException $e) {
            db::rollBack();

            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();

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
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function show(TransaksiLain $transaksiLain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function edit(TransaksiLain $transaksi_lain)
    {
        //
        $dataKasLain= DB::table('kas_bank_lain as ksl')
            ->select('ksl.*')
            ->where('ksl.is_aktif', '=', "Y")
            ->where('ksl.id', '=', $transaksi_lain->id)
            ->first();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
         $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            ->where('c.is_kas_bank_lain', '=', "Y")

            // ->paginate(10);
            ->get();
        
        return view('pages.finance.transaksi_lain.edit',[
             'judul'=>"Transaksi Lain",
            'dataKasLain' => $dataKasLain,
            'dataKas' => $dataKas,
            'dataCOA' => $dataCOA,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransaksiLain $transaksi_lain)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi!',
                'select_coa.required' => 'Jenis Transaksi wajib dipilih!',
                'select_bank.required' => 'Kas/Bank  wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_transaksi' => 'required',
                'select_coa' => 'required',
                'select_bank' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_transaksi']);
                // dd(date_format($tanggal, 'Y-m-d h:i:s'));
                $transaksi = TransaksiLain::where('is_aktif', 'Y')->findOrFail($transaksi_lain->id);
            // dd($transaksi)
                //=============logic buat handle yang lama dulu===================
                $kas_bank_old = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $transaksi->kas_bank_id)
                                    ->first();
                $coa_old = Coa::where('is_aktif', 'Y')
                                    ->where('id', $transaksi->coa_id)
                                    ->first();
                // misal, coanya itu pengeluaran terus nominal 50.000 berarti kan yang dulu keluar uang, gajadi, makanya nambah uang
                if ($coa_old->tipe=='pengeluaran') {
                    $kas_bank_old->saldo_sekarang +=  floatval(str_replace(',', '', $transaksi->total));
                }
                // ini kalau coanya penerimaan kan dapet uang, gajadi dapet uang mkanya dikurangin
                else
                {
                    $kas_bank_old->saldo_sekarang -=  floatval(str_replace(',', '', $transaksi->total));
                }
                $kas_bank_old->updated_at = now();
                $kas_bank_old->updated_by = $user;
                $kas_bank_old->save();
                //=============logic buat handle yang lama dulu===================

                // if($kas_bank_old->save())
                // {
                    $transaksi->tanggal = date_format($tanggal, 'Y-m-d h:i:s');
                    $transaksi->tanggal_catat = now();
                    $transaksi->coa_id = $data['select_coa'];
                    $transaksi->kas_bank_id = $data['select_bank'];
                    $transaksi->total = floatval(str_replace(',', '', $data['total']));
                    $transaksi->catatan = $data['catatan'];
                    $transaksi->updated_by = $user;
                    $transaksi->updated_at = now();
                    // $transaksi->save();
                    if ($transaksi->save()) {
                        $coa = Coa::where('is_aktif', 'Y')
                                        ->where('id', $data['select_coa'])
                                        ->first();
                        $kas_bank = KasBank::where('is_aktif', 'Y')
                                        ->where('id', $data['select_bank'])
                                        ->first();
                        if ($coa->tipe=='pengeluaran') {
                            $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                        }
                        else
                        {
                            $kas_bank->saldo_sekarang +=  floatval(str_replace(',', '', $data['total']));
                        }
                        $kas_bank->updated_at = now();
                        $kas_bank->updated_by = $user;
                        // $kas_bank->save();
                        if($kas_bank->save())
                        {
                            DB::table('kas_bank_transaction')
                                    ->where('keterangan_kode_transaksi', $transaksi->id)
                                    ->where('jenis', 'lainnya')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'id_kas_bank'=>$data['select_bank'],
                                        'debit'=>$coa->tipe=='penerimaan'?(float)str_replace(',', '', $data['total']):0,
                                        'kredit'=> $coa->tipe=='pengeluaran'?(float)str_replace(',', '', $data['total']):0,
                                        'keterangan_transaksi'=>$data['nama_coa_hidden'].'-'.$data['catatan'],
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,

                                    )
                                );
                            //   $coa->tipe=='penerimaan'?(float)str_replace(',', '', $data['total']):0,// debit 
                            //   $coa->tipe=='pengeluaran'?(float)str_replace(',', '', $data['total']):0, //kredit
                        
                        }
                        
                    }

                // }

                DB::commit();

                return redirect()->route('transaksi_lain.index')->with(['status' => 'Success', 'msg'  => 'Transaksi lain berhasil diubah!']);

        } catch (ValidationException $e) {
            db::rollBack();

            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();

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
     * @param  \App\Models\TransaksiLain  $transaksiLain
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransaksiLain $transaksi_lain)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        DB::beginTransaction(); 

        try{
                $transaksi = TransaksiLain::where('is_aktif', 'Y')->findOrFail($transaksi_lain->id);
                // dd($transaksi)
                //=============logic buat handle yang lama dulu===================
                $kas_bank_old = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $transaksi->kas_bank_id)
                                    ->first();
                $coa_old = Coa::where('is_aktif', 'Y')
                                    ->where('id', $transaksi->coa_id)
                                    ->first();
                // misal, coanya itu pengeluaran terus nominal 50.000 berarti kan yang dulu keluar uang, gajadi, makanya nambah uang
                if ($coa_old->tipe=='pengeluaran') {
                    $kas_bank_old->saldo_sekarang +=  floatval(str_replace(',', '', $transaksi->total));
                }
                // ini kalau coanya penerimaan kan dapet uang, gajadi dapet uang mkanya dikurangin
                else
                {
                    $kas_bank_old->saldo_sekarang -=  floatval(str_replace(',', '', $transaksi->total));
                }
                $kas_bank_old->updated_at = now();
                $kas_bank_old->updated_by = $user;
                $kas_bank_old->save();

                $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $transaksi->id)
                                        ->where('jenis', 'lainnya')
                                        ->first();
                if($kas_bank_transaksi)
                {

                    DB::table('kas_bank_transaction')
                        ->where('keterangan_kode_transaksi', $transaksi->id)
                        ->where('jenis', 'lainnya')
                        ->where('is_aktif', 'Y')
                        ->update(array(
                            'updated_at'=> now(),
                            'updated_by'=> $user,
                            'is_aktif'=> 'N',
                        )
                    );
                }
            $transaksi->updated_at = now();
            $transaksi->updated_by = $user;
            $transaksi->is_aktif = "N";
            $transaksi->save();
            DB::commit();
            return redirect()->route('transfer_dana.index')->with(['status' => 'Success', 'msg' => 'Berhasil Menghapus data transfer!']);
        }
        catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors());
        }
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
       
    }
}
