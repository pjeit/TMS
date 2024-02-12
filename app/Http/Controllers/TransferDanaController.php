<?php

namespace App\Http\Controllers;

use App\Models\TransferDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\KasBank;
use App\Models\KasBankTransaction;
use Exception;
class TransferDanaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_TRANSFER_DANA', ['only' => ['index']]);
		$this->middleware('permission:CREATE_TRANSFER_DANA', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_TRANSFER_DANA', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_TRANSFER_DANA', ['only' => ['destroy']]);  
    }

    public function index()
    {
        //
        $dataKasTransfer= DB::table('kas_bank_transfer as kst')
            ->select('kst.*')
            ->where('kst.is_aktif', '=', "Y")
            ->get();
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
        return view('pages.finance.transfer_dana.index',[
             'judul'=>"Transfer dana",
            'dataKasTransfer' => $dataKasTransfer,
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
                'select_bank_dari.required' => 'Kas/Bank dari wajib dipilih!',
                'select_bank_ke.required' => 'Kas/Bank ke wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_transaksi' => 'required',
                'select_bank_dari' => 'required',
                'select_bank_ke' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            if($data['select_bank_dari']==$data['select_bank_ke'])
            {
                return redirect()->back()->withErrors('Kas/bank dari tidak boleh sama dengan tujuan!')->withInput();
            }
            else
            {
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_transaksi']);
                // dd(date_format($tanggal, 'Y-m-d h:i:s'));
                $new_transfer = new TransferDana();
                $new_transfer->tanggal = $tanggal;
                $new_transfer->kas_bank_id_dari = $data['select_bank_dari'];
                $new_transfer->kas_bank_id_ke = $data['select_bank_ke'];
                $new_transfer->total = floatval(str_replace(',', '', $data['total']));
                $new_transfer->catatan = $data['catatan'];
                $new_transfer->created_by = $user;
                $new_transfer->created_at = now();
                $new_transfer->is_aktif = 'Y';
                // $new_transfer->save();

                if ($new_transfer->save()) {
                    $kas_bank_dari = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank_dari'])
                                    ->first();
                    $kas_bank_dari->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank_dari->updated_at = now();
                    $kas_bank_dari->updated_by = $user;
                    // $kas_bank_dari->save();
                    if($kas_bank_dari->save())
                    {
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['select_bank_dari'],// id kas_bank dr form
                                    // date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                    $tanggal,
                                    0,// debit 
                                    (float)str_replace(',', '', $data['total']), //kredit
                                    1001, //kode coa
                                    'transfer_dana',
                                    $data['catatan'], //keterangan_transaksi
                                    $new_transfer->id,//keterangan_kode_transaksi
                                    $user,//created_by
                                    now(),//created_at
                                    $user,//updated_by
                                    now(),//updated_at
                                    'Y'
                                ) 
                            );
                    }
                    $kas_bank_ke= KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank_ke'])
                                    ->first();
                    $kas_bank_ke->saldo_sekarang +=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank_ke->updated_at = now();
                    $kas_bank_ke->updated_by = $user;
                    // $kas_bank_ke->save();
                    if($kas_bank_ke->save())
                    {
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['select_bank_ke'],// id kas_bank dr form
                                    // date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                    $tanggal,
                                    (float)str_replace(',', '', $data['total']),//debit
                                    0,//kredit
                                    1001, //kode coa
                                    'transfer_dana',
                                    $data['catatan'], //keterangan_transaksi
                                    $new_transfer->id,//keterangan_kode_transaksi
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
                return redirect()->route('transfer_dana.index')->with(['status' => 'Success', 'msg'  => 'Transfer dana berhasil!']);
            }

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
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function show(TransferDana $transferDana)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferDana $transfer_dana)
    {
        //
        $dataKasTransfer= DB::table('kas_bank_transfer as kst')
            ->select('kst.*')
            ->where('kst.is_aktif', '=', "Y")
            ->where('kst.id', '=', $transfer_dana->id)

            ->first();
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        //  $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
        //                                 ->where('keterangan_kode_transaksi', $transfer_dana->id)
        //                                 ->where('jenis', 'transfer_dana')
        //                                 ->get();
        // dd( $kas_bank_transaksi[0]->id);
        return view('pages.finance.transfer_dana.edit',[
            'dataKasTransfer'=>$dataKasTransfer,
            'dataKas'=>$dataKas,
            'judul'=>"Trnasfer Dana",

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransferDana $transfer_dana)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi!',
                'select_bank_dari.required' => 'Kas/Bank dari wajib dipilih!',
                'select_bank_ke.required' => 'Kas/Bank ke wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_transaksi' => 'required',
                'select_bank_dari' => 'required',
                'select_bank_ke' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            if($data['select_bank_dari']==$data['select_bank_ke'])
            {
                return redirect()->back()->withErrors('Kas/bank dari tidak boleh sama dengan tujuan!')->withInput();
            }
            else
            {
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_transaksi']);
                // dd(date_format($tanggal, 'Y-m-d h:i:s'));
                $transfer = TransferDana::where('is_aktif', 'Y')->findOrFail($transfer_dana->id);

                //========== handle update logic saldo yang lama============================
                // kalau yang dari, keluar uang
                // misal kas bca keluar 50.000 buat tf ke mayapada, kalau gajadi bank bca mbalek duitnya brarti +50.000
                $kas_bank_dari_old = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $transfer->kas_bank_id_dari)
                                    ->first();
                $kas_bank_dari_old->saldo_sekarang +=  floatval(str_replace(',', '', $transfer->total));
                $kas_bank_dari_old->updated_at = now();
                $kas_bank_dari_old->updated_by = $user;
                $kas_bank_dari_old->save();
                // kalau yang ke, dapet uang
                // misal kas bca keluar 50.000 buat tf ke mayapada, kalau gajadi brarti bank mayapada-50.000
                $kas_bank_ke_old= KasBank::where('is_aktif', 'Y')
                                ->where('id', $transfer->kas_bank_id_ke)
                                ->first();
                $kas_bank_ke_old->saldo_sekarang -=  floatval(str_replace(',', '',  $transfer->total));
                $kas_bank_ke_old->updated_at = now();
                $kas_bank_ke_old->updated_by = $user;
                $kas_bank_ke_old->save();
                //========== handle update logic saldo yang lama=============================

                $transfer->tanggal = date_format($tanggal, 'Y-m-d h:i:s');
                $transfer->kas_bank_id_dari = $data['select_bank_dari'];
                $transfer->kas_bank_id_ke = $data['select_bank_ke'];
                $transfer->total = floatval(str_replace(',', '', $data['total']));
                $transfer->catatan = $data['catatan'];
                $transfer->updated_by = $user;
                $transfer->updated_at = now();
                // $transfer->save();
                if ($transfer->save()) {
                
                    $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $transfer->id)
                                        ->where('jenis', 'transfer_dana')
                                        ->get();
                    $kas_bank_dari = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank_dari'])
                                    ->first();

                    $kas_bank_dari->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank_dari->updated_at = now();
                    $kas_bank_dari->updated_by = $user;
                    $kas_bank_dari->save();

                    DB::table('kas_bank_transaction')
                                    ->where('id',  $kas_bank_transaksi[0]->id)
                                    ->where('keterangan_kode_transaksi', $transfer->id)
                                    ->where('jenis', 'transfer_dana')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'id_kas_bank'=>$data['select_bank_dari'],
                                        'tanggal'=>$tanggal,
                                        'kredit'=>floatval(str_replace(',', '', $data['total'])),
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,

                                    )
                                );
                   
                    $kas_bank_ke= KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank_ke'])
                                    ->first();

                    $kas_bank_ke->saldo_sekarang +=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank_ke->updated_at = now();
                    $kas_bank_ke->updated_by = $user;
                    $kas_bank_ke->save();
                    DB::table('kas_bank_transaction')
                                    ->where('id',  $kas_bank_transaksi[1]->id)
                                    ->where('keterangan_kode_transaksi', $transfer->id)
                                    ->where('jenis', 'transfer_dana')
                                    ->where('is_aktif', 'Y')
                                    ->update(array(
                                        'tanggal'=>$tanggal,
                                        'id_kas_bank'=>$data['select_bank_ke'],
                                        'debit'=>floatval(str_replace(',', '', $data['total'])),
                                        'updated_at'=> now(),
                                        'updated_by'=> $user,
                                    )
                                );
                    
                }
                DB::commit();
                return redirect()->route('transfer_dana.index')->with(['status' => 'Success', 'msg'  => 'Revisi Transfer dana berhasil!']);
            }

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
     * @param  \App\Models\TransferDana  $transferDana
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferDana $transfer_dana)
    {
        //
        // dd($transfer_dana);
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        DB::beginTransaction(); 

        try{
            
            $transfer = TransferDana::where('is_aktif', 'Y')->findOrFail($transfer_dana->id);
          

            //========== handle update logic saldo yang lama============================
            // kalau yang dari, keluar uang
            // misal kas bca keluar 50.000 buat tf ke mayapada, kalau gajadi bank bca mbalek duitnya brarti +50.000
            $kas_bank_dari_old = KasBank::where('is_aktif', 'Y')
                                ->where('id', $transfer_dana->kas_bank_id_dari)
                                ->first();
            $kas_bank_dari_old->saldo_sekarang +=  floatval(str_replace(',', '', $transfer_dana->total));
            $kas_bank_dari_old->updated_at = now();
            $kas_bank_dari_old->updated_by = $user;
            $kas_bank_dari_old->save();
            // kalau yang ke, dapet uang
            // misal kas bca keluar 50.000 buat tf ke mayapada, kalau gajadi brarti bank mayapada-50.000
            $kas_bank_ke_old= KasBank::where('is_aktif', 'Y')
                            ->where('id', $transfer_dana->kas_bank_id_ke)
                            ->first();
            $kas_bank_ke_old->saldo_sekarang -=  floatval(str_replace(',', '',  $transfer_dana->total));
            $kas_bank_ke_old->updated_at = now();
            $kas_bank_ke_old->updated_by = $user;
            $kas_bank_ke_old->save();


             $kas_bank_transaksi = KasBankTransaction::where('is_aktif', 'Y')
                                        ->where('keterangan_kode_transaksi', $transfer_dana->id)
                                        ->where('jenis', 'transfer_dana')
                                        ->get();
            if($kas_bank_transaksi)
            {

                DB::table('kas_bank_transaction')
                    ->where('keterangan_kode_transaksi', $transfer_dana->id)
                    ->where('jenis', 'transfer_dana')
                    ->where('is_aktif', 'Y')
                    ->update(array(
                        'updated_at'=> now(),
                        'updated_by'=> $user,
                        'is_aktif'=> 'N',
    
                    )
                );
            }
              $transfer->updated_at = now();
            $transfer->updated_by = $user;
            $transfer->is_aktif = "N";
            $transfer->save();
            //========== handle update logic saldo yang lama=============================

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
