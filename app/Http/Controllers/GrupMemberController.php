<?php

namespace App\Http\Controllers;

use App\Models\GrupMember;
use Illuminate\Http\Request;

class GrupMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = GrupMember::where('is_aktif', 'Y')->get();

            return view('pages.master.customer.index',[
            'judul' => "Grup Member",
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function show(GrupMember $grupMember)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function edit(GrupMember $grupMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GrupMember $grupMember)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(GrupMember $grupMember)
    {
        //
    }
}
