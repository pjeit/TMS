<?php

namespace App\Http\Controllers;

use App\Models\BonusSupir;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Helper\SewaDataHelper;
use App\Helper\CoaHelper;
use App\Models\KasBank;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
USE App\Models\KasBankTransaction;
class BonusSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataBonusSupir = BonusSupir::where('is_aktif',"Y")
            ->with('karyawanIndex')
            ->get();
        $dataKas = KasBank::where('is_aktif',"Y")->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.finance.bonus_supir.index',[
            'judul'=>"Bonus Supir",
            'dataBonusSupir' => $dataBonusSupir,
            'dataKas' => $dataKas,
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
         //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'tanggal_pencairan.required' => 'Tanggal pencairan wajib diisi!',
                'select_driver.required' => 'Driver wajib dipilih!',
                'select_bank.required' => 'Kas/Bank  wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_pencairan' => 'required',
                'select_driver' => 'required',
                'select_bank' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
            $data= $request->collect();
            // dd($data);   
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_pencairan']);
                $bonus_supir = new BonusSupir();
                $bonus_supir->id_karyawan = $data['select_driver'];
                $bonus_supir->tanggal_pencairan = date_format($tanggal, 'Y-m-d h:i:s');
                $bonus_supir->total_pencairan = floatval(str_replace(',', '', $data['total']));
                $bonus_supir->id_kas_bank = $data['select_bank'];
                $bonus_supir->catatan = $data['catatan'];
                $bonus_supir->created_by = $user;
                $bonus_supir->created_at = now();
                $bonus_supir->is_aktif = 'Y';
                // $bonus_supir->save();
                if ($bonus_supir->save()) {
                    
                    $kas_bank = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank'])
                                    ->first();
                    $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank->updated_at = now();
                    $kas_bank->updated_by = $user;
                    if($kas_bank->save())
                    {
                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                                array(
                                    $data['select_bank'],// id kas_bank dr form
                                    date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                                    0,// debit 
                                    (float)str_replace(',', '', $data['total']), //kredit
                                    CoaHelper::DataCoa(5021), //kode coa bonus, pakai coa gaji
                                    'bonus_supir',
                                    'BONUS SUPIR :'.$data['nama_driver_hidden'].'-'.'CATATAN:'."(".$data['catatan'].")", //keterangan_transaksi
                                    $bonus_supir->id,//keterangan_kode_transaksi
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
                return redirect()->route('bonus_supir.index')->with(['status' => 'Success', 'msg'  => 'Bonus supir berhasil dicairkan!']);
        } catch (ValidationException $e) {
            DB::rollBack();

            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->route('bonus_supir.index')->with(['status' => 'error', 'msg'  => $e->errors()]);


        }   
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            // return redirect()->back()->withErrors($ex->getMessage())->withInput();
            return redirect()->route('bonus_supir.index')->with(['status' => 'error', 'msg'  => $ex->getMessage()]);

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BonusSupir  $bonusSupir
     * @return \Illuminate\Http\Response
     */
    public function show(BonusSupir $bonusSupir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BonusSupir  $bonusSupir
     * @return \Illuminate\Http\Response
     */
    public function edit(BonusSupir $bonusSupir)
    {
        //
        $dataBonusSupir = BonusSupir::where('is_aktif',"Y")
            ->with('karyawanIndex')
            ->where('id',$bonusSupir->id)
            ->first();
        $dataKas = KasBank::where('is_aktif',"Y")->get();
        return view('pages.finance.bonus_supir.edit',[
            'judul'=>"Bonus Supir",
            'dataBonusSupir' => $dataBonusSupir,
            'dataKas' => $dataKas,
            'dataDriver' => SewaDataHelper::DataDriver(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BonusSupir  $bonusSupir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BonusSupir $bonusSupir)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
        // dd(/*date_format(*/$data['tanggal_transaksi']/*,'Y-m-d')*/);
            $pesanKustom = [
                'tanggal_pencairan.required' => 'Tanggal pencairan wajib diisi!',
                'select_driver.required' => 'Driver wajib dipilih!',
                'select_bank.required' => 'Kas/Bank  wajib dipilih!',
                'total.required' => 'Total Nominal wajib diisi!',
                // 'catatan.required' => 'Catatan harus diisi!',
            ];
            $request->validate([
                'tanggal_pencairan' => 'required',
                'select_driver' => 'required',
                'select_bank' => 'required',
                'total' => 'required',
                // 'catatan' => 'required',
            ], $pesanKustom);
                $data= $request->collect();
                // dd(floatval(str_replace(',', '', $bonusSupir->total_pencairan))!=floatval(str_replace(',', '', $data['total'])));   
                $tanggal=date_create_from_format('d-M-Y', $data['tanggal_pencairan']);

                //handle kas yang lama ditambah dulu, ga peduli dia mau ganti nominal atau ndak
                $kas_bank_old = KasBank::where('is_aktif','Y')->where('id',$bonusSupir->id_kas_bank)->first();
                $kas_bank_old->saldo_sekarang +=  floatval(str_replace(',', '', $bonusSupir->total_pencairan));
                $kas_bank_old->updated_at = now();
                $kas_bank_old->updated_by = $user;
                $kas_bank_old->save();

                $bonus_supir = BonusSupir::where('is_aktif', 'Y')->findOrFail($bonusSupir->id);
                $bonus_supir->id_karyawan = $data['select_driver'];
                $bonus_supir->tanggal_pencairan = date_format($tanggal, 'Y-m-d h:i:s');
                $bonus_supir->total_pencairan = floatval(str_replace(',', '', $data['total']));
                $bonus_supir->id_kas_bank = $data['select_bank'];
                $bonus_supir->catatan = $data['catatan'];
                $bonus_supir->updated_by = $user;
                $bonus_supir->updated_at = now();
                // $bonus_supir->save();
                if ($bonus_supir->save()) {
                    $kas_bank = KasBank::where('is_aktif', 'Y')
                                    ->where('id', $data['select_bank'])
                                    ->first();
                    $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '', $data['total']));
                    $kas_bank->updated_at = now();
                    $kas_bank->updated_by = $user;
                    if($kas_bank->save())
                    {
                        $kas_bank_transaksi = KasBankTransaction::where(['is_aktif' => 'Y',
                                                                        'jenis' => 'bonus_supir',
                                                                        'id_kas_bank' =>$bonus_supir->id_kas_bank,    
                                                                        'keterangan_kode_transaksi' =>$bonus_supir->id,    
                                                                        // 'tanggal' => $ujr->tanggal    
                                                                        ])->first();
                        $kas_bank_transaksi->kredit = floatval(str_replace(',', '', $data['total']));
                        $kas_bank_transaksi->keterangan_kode_transaksi=  'BONUS SUPIR :'.$data['nama_driver_hidden'].'-'.'CATATAN:'."(".$data['catatan'].")";
                        $kas_bank_transaksi->updated_by= $user;
                        $kas_bank_transaksi->updated_at = now();
                        $kas_bank_transaksi->save();
                        // DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        //         array(
                        //             $data['select_bank'],// id kas_bank dr form
                        //             date_format($tanggal, 'Y-m-d h:i:s'),//tanggal
                        //             0,// debit 
                        //             (float)str_replace(',', '', $data['total']), //kredit
                        //             CoaHelper::DataCoa(5021), //kode coa bonus, pakai coa gaji
                        //             'bonus_supir',
                        //             'BONUS SUPIR :'.$data['nama_driver_hidden'].'-'.'CATATAN:'."(".$data['catatan'].")", //keterangan_transaksi
                        //             $bonus_supir->id,//keterangan_kode_transaksi
                        //             $user,//created_by
                        //             now(),//created_at
                        //             $user,//updated_by
                        //             now(),//updated_at
                        //             'Y'
                        //         ) 
                        //     );
                    }
                }
                DB::commit();
                return redirect()->route('bonus_supir.index')->with(['status' => 'Success', 'msg'  => 'Berhasil mengubah data pencairan supir!']);
         } catch (ValidationException $e) {
            DB::rollBack();

            // return redirect()->route('transfer_dana.index')->with(['status' => 'error', 'msg' => $e->errors()]);
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->route('bonus_supir.index')->with(['status' => 'error', 'msg'  => $e->errors()]);


        }   
        catch (Exception $ex) {
            // cancel input db
            DB::rollBack();
            // return redirect()->back()->withErrors($ex->getMessage())->withInput();
            return redirect()->route('bonus_supir.index')->with(['status' => 'error', 'msg'  => $ex->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BonusSupir  $bonusSupir
     * @return \Illuminate\Http\Response
     */
    public function destroy(BonusSupir $bonusSupir)
    {
        //
    }
}
