<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NutritionalInfo;
use App\Http\Resources\NutritionalInfo as NutritionalInfoResource;
use Illuminate\Support\Facades\Auth;
use Response;
use App\NutritionalInfoStatic;

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

    public function updateByMeal(Request $request, $id)
    {
        $nutritionalInfo = NutritionalInfoStatic::where('name', $request->name)->first();
        $energy_kcal = NutritionalInfo::where('mealId', $request->id)->where('label', 'energy_kcal')->first();
        $energy_kJ = NutritionalInfo::where('mealId', $request->id)->where('label', 'energy_kJ')->first();
        $water_g = NutritionalInfo::where('mealId', $request->id)->where('label', 'water_g')->first();
        $protein_g = NutritionalInfo::where('mealId', $request->id)->where('label', 'protein_g')->first();
        $fats_g = NutritionalInfo::where('mealId', $request->id)->where('label', 'fats_g')->first();
        $carbo_hidrats_g = NutritionalInfo::where('mealId', $request->id)->where('label', 'carbo_hidrats_g')->first();
        $fiber_g = NutritionalInfo::where('mealId', $request->id)->where('label', 'fiber_g')->first();
        $colesterol_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'colesterol_mg')->first();
        $vitA_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'vitA_mg')->first();
        $vitD_pg = NutritionalInfo::where('mealId', $request->id)->where('label', 'vitD_pg')->first();
        $tiamina_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'tiamina_mg')->first();
        $riboflavina_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'riboflavina_mg')->first();
        $niacina_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'niacina_mg')->first();
        $vitB6_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'vitB6_mg')->first();
        $vit_B12_pg = NutritionalInfo::where('mealId', $request->id)->where('label', 'vit_B12_pg')->first();
        $vitC_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'vitC_mg')->first();
        $na_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'na_mg')->first();
        $k_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'k_mg')->first();
        $ca_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'ca_mg')->first();
        $p_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'p_mg')->first();
        $mg_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'mg_mg')->first();
        $fe_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'fe_mg')->first();
        $zn_mg = NutritionalInfo::where('mealId', $request->id)->where('label', 'zn_mg')->first();

        $nutri = $request->nutritionalInfo;

        if (!$nutritionalInfo) {
            return Response::json(['error' => 'Não encontrado.'], 400);
        }

        $energy_kcal->value = ($nutritionalInfo->energy_kcal * $request->quantity)/100;
        $energy_kJ->value = ($nutritionalInfo->energy_kJ * $request->quantity)/100;
        $water_g->value = ($nutritionalInfo->water_g * $request->quantity)/100;
        $protein_g->value = ($nutritionalInfo->protein_g * $request->quantity)/100;
        $fats_g->value = ($nutritionalInfo->fats_g * $request->quantity)/100;
        $carbo_hidrats_g->value = ($nutritionalInfo->carbo_hidrats_g * $request->quantity)/100;
        $fiber_g->value = ($nutritionalInfo->fiber_g * $request->quantity)/100;
        $colesterol_mg->value = ($nutritionalInfo->colesterol_mg * $request->quantity)/100;
        $vitA_mg->value = ($nutritionalInfo->vitA_mg * $request->quantity)/100;
        $vitD_pg->value = ($nutritionalInfo->vitD_pg * $request->quantity)/100;
        $tiamina_mg->value = ($nutritionalInfo->tiamina_mg * $request->quantity)/100;
        $riboflavina_mg->value = ($nutritionalInfo->riboflavina_mg * $request->quantity)/100;
        $niacina_mg->value = ($nutritionalInfo->niacina_mg * $request->quantity)/100;
        $vitB6_mg->value = ($nutritionalInfo->vitB6_mg * $request->quantity)/100;
        $vit_B12_pg->value = ($nutritionalInfo->vit_B12_pg * $request->quantity)/100;
        $vitC_mg->value = ($nutritionalInfo->vitC_mg * $request->quantity)/100;
        $na_mg->value = ($nutritionalInfo->na_mg * $request->quantity)/100;
        $k_mg->value = ($nutritionalInfo->k_mg * $request->quantity)/100;
        $ca_mg->value = ($nutritionalInfo->ca_mg * $request->quantity)/100;
        $p_mg->value = ($nutritionalInfo->p_mg * $request->quantity)/100;
        $mg_mg->value = ($nutritionalInfo->mg_mg * $request->quantity)/100;
        $fe_mg->value = ($nutritionalInfo->fe_mg * $request->quantity)/100;
        $zn_mg->value = ($nutritionalInfo->zn_mg * $request->quantity)/100;

        $energy_kcal->update();
        $energy_kJ->update();
        $water_g->update();
        $protein_g->update();
        $fats_g->update();
        $carbo_hidrats_g->update();
        $fiber_g->update();
        $colesterol_mg->update();
        $vitA_mg->update();
        $vitD_pg->update();
        $tiamina_mg->update();
        $riboflavina_mg->update();
        $niacina_mg->update();
        $vitB6_mg->update();
        $vit_B12_pg->update();
        $vitC_mg->update();
        $na_mg->update();
        $k_mg->update();
        $ca_mg->update();
        $p_mg->update();
        $mg_mg->update();
        $fe_mg->update();
        $zn_mg->update();

        $nutri[0]['value'] = $energy_kcal->value;
        $nutri[1]['value'] = $energy_kJ->value;
        $nutri[2]['value'] = $water_g->value;
        $nutri[3]['value'] = $protein_g->value;
        $nutri[4]['value'] = $fats_g->value;
        $nutri[5]['value'] = $carbo_hidrats_g->value;
        $nutri[6]['value'] = $fiber_g->value;
        $nutri[7]['value'] = $colesterol_mg->value;
        $nutri[8]['value'] = $vitA_mg->value;
        $nutri[9]['value'] = $vitD_pg->value;
        $nutri[10]['value'] = $tiamina_mg->value;
        $nutri[11]['value'] = $riboflavina_mg->value;
        $nutri[12]['value'] = $niacina_mg->value;
        $nutri[13]['value'] = $vitB6_mg->value;
        $nutri[14]['value'] = $vit_B12_pg->value;
        $nutri[15]['value'] = $vitC_mg->value;
        $nutri[16]['value'] = $na_mg->value;
        $nutri[17]['value'] = $k_mg->value;
        $nutri[18]['value'] = $ca_mg->value;
        $nutri[19]['value'] = $p_mg->value;
        $nutri[20]['value'] = $mg_mg->value;
        $nutri[21]['value'] = $fe_mg->value;
        $nutri[22]['value'] = $zn_mg->value;

        return Response::json(['data' => $nutri], 200);
    }
}
