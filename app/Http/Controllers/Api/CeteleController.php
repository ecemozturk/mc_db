<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CeteleRequest;
use App\Http\Resources\CeteleResource;
use App\Models\Cetele;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CeteleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ceteles = Cetele::all();
        return CeteleResource::collection($ceteles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CeteleRequest $request)
    {
        $cetele = new Cetele();
        $cetele->gizli_numara = $request->input('gizli_numara');
        $cetele->arayan_numara = $request->input('arayan_numara');
        $cetele->arama_tarihi = $request->input('arama_tarihi');
        $cetele->anonim_numara = $request->input('anonim_numara');
        $cetele->arayan_adsoyad = $request->input('arayan_adsoyad');
        $cetele->arayan_sehir = $request->input('arayan_sehir');
        $cetele->arayan_ulke = $request->input('arayan_ulke');
        $cetele->arayan_kimin_icin = $request->input('arayan_kimin_icin');
        $cetele->ne_yapildi = $request->input('ne_yapildi');
        $cetele->yonlendirilen_kurumlar = $request->input('yonlendirilen_kurumlar');
        $cetele->mc_nereden_duydu = $request->input('mc_nereden_duydu');
        $cetele->cetele_notlari = $request->input('cetele_notlari');

        $cetele->user_id = Auth::id();
        $cetele->save();

        return new CeteleResource($cetele);

    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cetele = Cetele::findOrFail($id);
        return new CeteleResource($cetele);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CeteleRequest $request, $id)
    {
        $cetele = Cetele::findOrFail($id);
        $cetele->update($request->all());

        return new CeteleResource($cetele);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cetele = Cetele::findOrFail($id);
        $cetele->delete();

        return response()->json(null, 204);
    }
}
