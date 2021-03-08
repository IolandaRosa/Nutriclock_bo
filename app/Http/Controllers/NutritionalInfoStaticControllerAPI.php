<?php

namespace App\Http\Controllers;
use App\Http\Resources\NutritionalInfoStatic as NutritionalInfoStaticResource;
use App\NutritionalInfoStatic;
use Illuminate\Http\Request;

class NutritionalInfoStaticControllerAPI extends Controller
{
    public function getNames() {
        return  NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all('name'));
    }

    public function getByQuery($query) {
        $info = NutritionalInfoStatic::whereRaw('LOWER(`name`) LIKE ? ',[trim(strtolower($query)).'%'])->get();

        return  NutritionalInfoStaticResource::collection($info);
    }
}
