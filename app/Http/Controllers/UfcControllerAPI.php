<?php

namespace App\Http\Controllers;

use App\Http\Resources\Ufc as UfcResource;
use App\Ufc;
use App\UsersUfc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class UfcControllerAPI extends Controller
{
    public function index()
    {
        return UfcResource::collection(Ufc::all());
    }

    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'name'=>'required|unique:ufcs,name',
        ]);

        $ufc = new Ufc();

        $ufc->name = $request->input('name');
        $ufc->save();

        return new UfcResource($ufc);
    }

    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $validateData=$request->validate([
            'name'=>'required|unique:ufcs,name',
        ]);

        $ufc=Ufc::findOrFail($id);

        $ufc->fill($validateData)->save();

        return new UfcResource($ufc);
    }

    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $totalUsfs = UsersUfc::where('ufc_id', $id)->count();

        if ($totalUsfs > 0) {
            return Response::json(['error' => 'Não é possível eliminar pois existem utilizadores associados!'], 400);
        }

        $ufc=Ufc::findOrFail($id);
        $ufc->forceDelete();
        return new UfcResource($ufc);
    }

    public function getUserUfcs(Request $request, $id) {
        $usersUfcs=[];
        if(Auth::guard('api')->user()->role == 'PATIENT'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        if(Auth::guard('api')->user()->role != 'PROFESSIONAL'){
            return UfcResource::collection(Ufc::all());
        } else {
            $usersUfcs = UsersUfc::where('user_id', $id)->get();
        }

        if (!$usersUfcs) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        $ufcs = [];

        foreach ($usersUfcs as $u) {
            $ufc = Ufc::where('id', $u->ufc_id)->first();
            array_push($ufcs, $ufc);
        }

        return UfcResource::collection($ufcs);
    }

}
