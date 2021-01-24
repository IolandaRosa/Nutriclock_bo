<?php

namespace App\Http\Controllers;
use App\Http\Resources\NutritionalInfoStatic as NutritionalInfoStaticResource;
use App\NutritionalInfoStatic;
use Illuminate\Http\Request;

class NutritionalInfoStaticControllerAPI extends Controller
{
/*
public function getMedicationByUser(Request $request, $id) {
        $medication = Medication::where('user_id', $id)->get();

        return MedicationResource::collection($medication);
    }
    */

    public function getNames(Request $request) {
        return  NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all('name'));
    }
}
