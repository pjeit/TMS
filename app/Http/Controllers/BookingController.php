<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\GrupTujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
 use Carbon\Carbon;
use App\Helper\VariableHelper;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Booking::where('is_aktif', 'Y')->paginate(5);

        return view('pages.order.booking.index',[
            'judul' => "Booking",
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::where('is_aktif', 'Y')->get();

        return view('pages.order.booking.create',[
            'judul' => "Booking",
            'customers' => $customers,
        ]);
    }

    public function getTujuan($id)
    {
        $cust = Customer::where('id', $id)->first();
        $Tujuan = GrupTujuan::where('grup_id', $cust->grup_id)->where('is_aktif', 'Y')->get();
        return response()->json($Tujuan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user()->id;
        try {
            // var_dump($request->post()); die; 
            $currentYear = Carbon::now()->format('y');
            $currentMonth = Carbon::now()->format('m');

            //substr itu ambil nilai dr belakang misal 3DW2308001 yang diambil 001, substr mulai dr 1 bukan 0
            //bisa juga substr(no_booking, 8,10)
            //length 10
            $maxBooking = DB::table('booking')
                ->selectRaw("ifnull(max(substr(no_booking, -3)), 0) + 1 as max_booking")
                ->whereRaw("substr(no_booking, 1, length(no_booking) - 3) = concat(?, ?, ?)", [$request->kode_cust,$currentYear, $currentMonth])
                ->value('max_booking');
            
            // str pad itu nambain angka 0 ke sebelah kiri (str_pad_left, defaultnya ke kanan) misal maxbookint 4 jadinya 004
            $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . str_pad($maxBooking, 3, '0', STR_PAD_LEFT);

            if (is_null($maxBooking)) {
                $newBookingNumber = $request->kode_cust . $currentYear . $currentMonth . '001';
            }
            
            $booking = new Booking();
            $booking->no_booking =$newBookingNumber;
            $booking->tgl_booking = date_create_from_format('d-M-Y', $request->tgl_booking);
            // $booking->tgl_berangkat =date_format($tgl_berangkat, 'Y-m-d');
            $booking->id_customer =$request->id_customer;
            $booking->id_grup_tujuan =$request->id_tujuan;
            // $booking->no_kontainer =$request->no_kontainer;
            $booking->catatan =$request->catatan;
            $booking->created_at = date("Y-m-d h:i:s");
            $booking->created_by = $user; // manual
            $booking->updated_at = date("Y-m-d h:i:s");
            $booking->updated_by = $user; // manual
            $booking->is_aktif = "Y";
            $booking->save();

            return redirect()->route('booking.index')->with('status','Sukses Menambahkan Booking Baru!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
         $customers = Customer::where('is_aktif', 'Y')->get();

        return view('pages.order.booking.edit',[
            'judul' => "Booking",
            'booking'=>$booking,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
        $user = Auth::user()->id;
            // $booking->no_booking =$newBookingNumber;
            // $booking->tgl_booking =date("Y-m-d h:i:s");
            // $booking->tgl_berangkat =date_format($tgl_berangkat, 'Y-m-d');
            // $booking->id_customer =$request->id_customer;
            // $booking->id_grub_tujuan =$request->id_tujuan;
            // $booking->no_kontainer =$request->no_kontainer;
            // $booking->catatan =$request->catatan;
         try {
            $data = $request->collect();
            // $tgl_berangkat = date_create_from_format('d-M-Y', $request->tgl_berangkat);
             DB::table('booking')
                ->where('id', $booking['id'])
                ->update(array(
                        // 'tgl_berangkat' => date_format($tgl_berangkat, 'Y-m-d'),
                        'tgl_booking' => date_create_from_format('d-M-Y', $request->tgl_booking),
                        'id_customer' => $data['id_customer'],
                        'id_grup_tujuan' => $data['id_tujuan'],
                        // 'no_kontainer' => $data['no_kontainer'],
                        'catatan' => $data['catatan'],
                        'updated_at'=> now(),
                        'updated_by'=> $user,
                    )
                );
            return redirect()->route('booking.index')->with('status','Sukses Mengubah Data Booking!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
