<?php

namespace App\Http\Controllers;

use App\Http\Resources\SleepTip as SleepTipResource;
use App\SleepTip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SleepTipControllerAPI extends Controller
{
    public function index()
    {
        return SleepTipResource::collection(SleepTip::all());
    }

    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'description'=>'required',
        ]);

        $tip = new SleepTip();
        $tip->description = $request->input('description');
        return new SleepTipResource($tip);
    }

    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $validateData=$request->validate([
            'description'=>'required',
        ]);

        $tip=SleepTip::findOrFail($id);

        $tip->fill($validateData)->save();

        return new SleepTipResource($tip);
    }

    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $tip=SleepTip::findOrFail($id);

        $tip->forceDelete();

        return new SleepTipResource($tip);
    }
}
