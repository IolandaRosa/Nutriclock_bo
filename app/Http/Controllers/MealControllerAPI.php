<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\Meal as MealResource;
use Carbon\Carbon;
use App\Meal;
use App\User;
use App\NutritionalInfoStatic;
use App\NutritionalInfo;
use Response;

class MealControllerAPI extends Controller
{
    public function index()
    {
        return MealResource::collection(Meal::all());
    }

    public function mealDaysCount(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $date = Meal::where('userId', $id)->min('date');
        $dates = Meal::where('userId', $id)->select('date')->get();
        $count = Meal::where('userId', $id)->count();

        if (!$date) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        $days = Carbon::parse($date)->diffInDays(Carbon::now());

        if (count($dates) == 0) {
            return Response::json(['days' => 0], 200);
        }

        $parsedDates = [];
        $i = 0;

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        return Response::json(['daysFromInitialDate' => $days, 'totalDaysRegistered' => count(array_unique($parsedDates)), 'meals' => $count], 200);
    }

    public function getAuthUserMeals(Request $request) {
        $meals = Meal::where('userId', auth()->id())->get();

        if (!$meals) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        return MealResource::collection($meals);
    }

    public function getMealsByUser(Request $request, $id) {
        $dates = Meal::where('userId', $id)->select('date')->get();

        if (count($dates) == 0) {
            return Response::json(['days' => 0], 200);
        }

        $parsedDates = [];
        $mealsByType = [
            'P' => [],
            'A' => [],
            'J' => [],
            'S' => [],
            'L' => [],
        ];

        $i = 0;

        foreach ($dates as $d) {
            $nutritionalArray = [];
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            if (!in_array($parsed, $parsedDates)) {
                $meals = Meal::whereDate('date','=',Carbon::createFromFormat('d/m/Y',$parsed))->get();

                foreach($meals as $m) {
                    $nutritionalInfo = NutritionalInfo::where('mealId', $m->id)->get();
                    $obj['name'] = $m->name;
                    $obj['quantity'] = $m->quantity * $m->numericUnit;
                    $obj['nutritionalInfo'] = $nutritionalInfo;
                    array_push($nutritionalArray, $obj);
                    array_push($mealsByType[$m->type], $m);
                }
                $mealsByType['info']=$nutritionalArray;
                $parsedDates[$parsed] = $mealsByType;
            }

            $i++;
        }

        return Response::json(['meals' => $parsedDates], 200);
    }

    public function show(Request $request, $id) {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        return new MealResource($meal);
    }

    public static function computeNumericUnit($request) {
        $value = 0;

        switch($request->relativeUnit) {
            case "Gramas":
            case "Mililitros": $value = $request->quantity; break;
            case "Colher de sopa": $value = 15; break;
            case "Colher de sobremesa": $value = 7.5; break;
            case "Colher de chá": $value = 5; break;
            case "Colher de café": $value = 2.5; break;
            case "Colher de servir": $value = 30; break;
            case "Copo": $value = 200; break;
            case "Chavena de chá": $value = 240; break;
            case "Pires": $value = 200; break;
            case "Prato": $value = 40; break;
            case "Caneca": $value = 300; break;
            case "Concha de sopa": $value = 160; break;
            case "Tigela média": $value = 350; break;
            case "Chavena de café": $value = 40; break;
            default: $value = 0;
        }

        return $value;
    }

    public function store(Request $request, $id) {
        $request->validate([
            'name' => 'required|min:3',
            'quantity' => 'required|numeric|between:0,9999.99',
            'foodPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'nutritionalInfoPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'relativeUnit' => 'required',
            'type' => Rule::in(['P','A','J','S','O','L']),
            'date' => 'required',
            'time' => 'required'
        ]);

        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $meal = new Meal();

        if($request->foodPhoto != null) {
            $image = $request->file('foodPhoto');
            $path = basename($image->store('food', 'public'));
            $meal->foodPhotoUrl = basename($path);
        }

        if($request->nutritionalInfoPhoto != null) {
            $image = $request->file('nutritionalInfoPhoto');
            $path = basename($image->store('nutritionalInfo', 'public'));
            $meal->nutritionalInfoPhotoUrl = basename($path);
        }

        $grams = MealControllerAPI::computeNumericUnit($request);

        $meal->name = $request->name;
        $meal->quantity = $request->quantity;
        $meal->relativeUnit = $request->relativeUnit;
        $meal->type = $request->type;
        $meal->date = $request->date;
        $meal->time = $request->time;
        $meal->userId = $id;
        $meal->numericUnit = $grams;

        $meal->save();

        $nutritionalInfo = NutritionalInfoStatic::where('name', $request->name)->first();
        $infoToInsert = [];

        if ($nutritionalInfo) {
            $infoToInsert[0] = [
                'label'=> 'energy_kcal',
                'value'=> (($nutritionalInfo->energy_kcal*$grams)/100)*$request->quantity,
                'unit'=> 'kcal',
                'mealId'=> $meal->id
            ];
            $infoToInsert[1] = [
                'label'=> 'energy_kJ',
                'value'=> ($nutritionalInfo->energy_kJ*$grams)/100,
                'unit'=> 'kJ',
                'mealId'=> $meal->id
            ];
            $infoToInsert[2] = [
                'label'=> 'water_g',
                'value'=> (($nutritionalInfo->water_g*$grams)/100)*$request->quantity,
                'unit'=> 'g',
                'mealId'=> $meal->id
            ];
            $infoToInsert[3] = [
                'label'=> 'protein_g',
                'value'=> (($nutritionalInfo->protein_g*$grams)/100)*$request->quantity,
                'unit'=> 'g',
                'mealId'=> $meal->id
            ];
            $infoToInsert[4] = [
                'label'=> 'fats_g',
                'value'=> (($nutritionalInfo->fats_g*$grams)/100)*$request->quantity,
                'unit'=> 'g',
                'mealId'=> $meal->id
            ];
            $infoToInsert[5] = [
                'label'=> 'carbo_hidrats_g',
                'value'=> (($nutritionalInfo->carbo_hidrats_g*$grams)/100)*$request->quantity,
                'unit'=> 'g',
                'mealId'=> $meal->id
            ];
            $infoToInsert[6] = [
                'label'=> 'fiber_g',
                'value'=> (($nutritionalInfo->fiber_g*$grams)/100)*$request->quantity,
                'unit'=> 'g',
                'mealId'=> $meal->id
            ];
            $infoToInsert[7] = [
                'label'=> 'colesterol_mg',
                'value'=> (($nutritionalInfo->colesterol_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[8] = [
                'label'=> 'vitA_mg',
                'value'=> (($nutritionalInfo->vitA_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[9] = [
                'label'=> 'vitD_pg',
                'value'=> (($nutritionalInfo->vitD_pg*$grams)/100)*$request->quantity,
                'unit'=> 'μm',
                'mealId'=> $meal->id
            ];
            $infoToInsert[10] = [
                'label'=> 'tiamina_mg',
                'value'=> (($nutritionalInfo->tiamina_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[11] = [
                'label'=> 'riboflavina_mg',
                'value'=> (($nutritionalInfo->riboflavina_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[12] = [
                'label'=> 'niacina_mg',
                'value'=> (($nutritionalInfo->niacina_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[13] = [
                'label'=> 'vitB6_mg',
                'value'=> (($nutritionalInfo->vitB6_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[14] = [
                'label'=> 'vit_B12_pg',
                'value'=> (($nutritionalInfo->vit_B12_pg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[15] = [
                'label'=> 'vitC_mg',
                'value'=> (($nutritionalInfo->vitC_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[16] = [
                'label'=> 'niacina_mg',
                'value'=> (($nutritionalInfo->na_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[17] = [
                'label'=> 'k_mg',
                'value'=> (($nutritionalInfo->k_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[18] = [
                'label'=> 'ca_mg',
                'value'=> (($nutritionalInfo->ca_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[19] = [
                'label'=> 'p_mg',
                'value'=> (($nutritionalInfo->p_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[20] = [
                'label'=> 'mg_mg',
                'value'=> (($nutritionalInfo->mg_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[21] = [
                'label'=> 'fe_mg',
                'value'=> (($nutritionalInfo->fe_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];
            $infoToInsert[22] = [
                'label'=> 'zn_mg',
                'value'=> (($nutritionalInfo->zn_mg*$grams)/100)*$request->quantity,
                'unit'=> 'mg',
                'mealId'=> $meal->id
            ];

            NutritionalInfo::insert($infoToInsert);
        }

        return new MealResource($meal);
    }
}
