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
        $cetele = new Cetele($request->all());
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
