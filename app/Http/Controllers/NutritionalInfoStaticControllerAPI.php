<?php

namespace App\Http\Controllers;
use App\Http\Resources\NutritionalInfoStatic as NutritionalInfoStaticResource;
use App\NutritionalInfoStatic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NutritionalInfoStaticControllerAPI extends Controller
{
    public function getNames() {
        return  NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all('name'));
    }

    public function getByQuery($query) {
        $info = NutritionalInfoStatic::whereRaw('LOWER(name) LIKE ? ',[trim(strtolower($query)).'%'])->get();

        return  NutritionalInfoStaticResource::collection($info);
    }

    public function index()
    {
        return NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all());
    }

    public function store (Request $request) {
        $codeExists = NutritionalInfoStatic::where('code', $request->code)->first();
        if ($codeExists) {
            return Response::json(['error' => 'O código já existe.'], 400);
        }

        $name = NutritionalInfoStatic::where('name', $request->name)->first();
        if ($name) {
            return Response::json(['error' => 'O nome já existe.'], 400);
        }

        $info = new NutritionalInfoStatic();
        $info->fill($request->all());
        $info->save();

        return new NutritionalInfoStaticResource($info);
    }

    public function destroy($code) {
        NutritionalInfoStatic::where('code', $code)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }

    public function update (Request $request, $code) {
        $info = NutritionalInfoStatic::where('code', $code)->first();
        if (!$info) {
            return Response::json(['error' => 'nao existe o alimento.'], 400);
        }

        $info->fill($request->all());
        $info->update();

        return new NutritionalInfoStaticResource($info);
    }
}
