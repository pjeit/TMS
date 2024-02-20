<?php

namespace App\Http\Controllers;

use App\Models\Karantina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\KarantinaDetail;
use App\Helper\CoaHelper;
use App\Models\KasBank;
use Exception;

class PembayaranKarantinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();
        return view('pages.finance.pembayaran_karantina.index',[
            'judul' => "Biaya Operasional",
            'dataKas' => $dataKas,
            // 'dataCustomerSewa' => $dataCustomerSewa,
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

        try {
            $user = Auth::user()->id;
            $data = $request->post();
            $item = $data['item_hidden'];
            foreach ($data['data'] as $key => $value) {
                if($value['cek_cair'] == 'Y'){
                    if($value['dicairkan'] != 0 || $value['dicairkan'] != null){
                        $karantina = Karantina::where('is_aktif', 'Y')->find($key);
                        $karantina->total_dicairkan = floatval(str_replace(',', '', $value['dicairkan']));
                        $karantina->catatan = $value['catatan'];
                        $karantina->is_ditagihkan = 'Y';
                        $karantina->updated_by = $user;
                        $karantina->updated_at = now();
                        $karantina->save();
                        

                        $no_kontainer = ' - No. Kontainer:';
                        $detail = KarantinaDetail::where('is_aktif', 'Y')->where('id_karantina', $key)->get();
                        foreach ($detail as $key => $item) {
                            $no_kontainer .= ' #'.$item->getJOD->no_kontainer;
                        }

                        DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            array(
                                $data['pembayaran'], // id kas_bank dr form
                                now(), //tanggal
                                0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                                floatval(str_replace(',', '', $value['dicairkan'])), //uang keluar (kredit)
                                CoaHelper::DataCoa(5003), //kode coa karantina
                                'karantina',
                                'No. BL: '.$karantina->getJO->no_bl . ' #Kapal: '.$karantina->getJO->kapal .' #Voyage: '. $karantina->getJO->voyage . $no_kontainer, //keterangan_transaksi
                                $karantina->id, //keterangan_kode_transaksi
                                $user, //created_by
                                now(), //created_at
                                $user, //updated_by
                                now(), //updated_at
                                'Y'
                            ) 
                        );
                        $saldo = DB::table('kas_bank')
                                    ->select('*')
                                    ->where('is_aktif', '=', "Y")
                                    ->where('kas_bank.id', '=', $data['pembayaran'])
                                    ->first();
                        $saldo_baru = $saldo->saldo_sekarang -  floatval(str_replace(',', '', $value['dicairkan']));
                        DB::table('kas_bank')
                            ->where('id', $data['pembayaran'])
                            ->update(array(
                                'saldo_sekarang' => $saldo_baru,
                                'updated_at'=> now(),
                                'updated_by'=> $user,
                            )
                        );
                    }
                }

            }
            DB::commit();
            return redirect()->route('pembayaran_karantina.index')->with(['status' => 'Success', 'msg' => 'Data Karantina berhasil dicairkan!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->back()->with(['status' => 'Error', 'msg' => 'Terjadi Kesalahan!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('pembayaran_karantina.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Karantina  $karantina
     * @return \Illuminate\Http\Response
     */
    public function show(Karantina $karantina)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Karantina  $karantina
     * @return \Illuminate\Http\Response
     */
    public function edit(Karantina $karantina)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Karantina  $karantina
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Karantina $karantina)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Karantina  $karantina
     * @return \Illuminate\Http\Response
     */
    public function destroy(Karantina $karantina)
    {
        //
    }
}
