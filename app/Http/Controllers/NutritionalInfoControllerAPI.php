<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NutritionalInfo;
use App\Http\Resources\NutritionalInfo as NutritionalInfoResource;
use Illuminate\Support\Facades\Auth;
use Response;

class NutritionalInfoControllerAPI extends Controller
{
    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN' && Auth::guard('api')->user()->role != 'PROFESSIONAL'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $professionalCategory=NutritionalInfo::findOrFail($id);

        $validateData=$request->validate([
            'value'=>'required|unique:professional_categories,name',
        ]);

        $nutritionalInfo=NutritionalInfo::findOrFail($id);

        $nutritionalInfo->fill($validateData)->save();

        return new NutritionalInfoResource($nutritionalInfo);
    }
}
