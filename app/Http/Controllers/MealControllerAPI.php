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
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

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
        $meals = Meal::where('userId',$id)->get();

        if (count($dates) == 0) {
            return Response::json(['days' => 0], 200);
        }

        $parsedDates = [];
        $i = 0;
        $k = 0;

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        $parsedDates = array_unique($parsedDates);
        $data = [];

        foreach ($parsedDates as $d) {
            $nutritionalArray = [];
            $mealsByType = [
                'P' => [],
                'A' => [],
                'J' => [],
                'S' => [],
                'L' => [],
                'O' => [],
            ];

            foreach($meals as $m) {
                $nutritionalInfo = NutritionalInfo::where('mealId', $m->id)->get();
                $obj['name'] = $m->name;
                $obj['quantity'] = $m->numericUnit;
                $obj['nutritionalInfo'] = $nutritionalInfo;
                if (Carbon::parse($m->date)->format('d/m/Y') == $d) {
                    array_push($nutritionalArray, $obj);
                    array_push($mealsByType[$m->type], $m);
                }
            }

            $mealsByType['info']=$nutritionalArray;
            $data[$d] = $mealsByType;
        }

        return Response::json(['meals' => $data], 200);
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
            case "Colher de sopa": $value = 15 * $request->quantity; break;
            case "Colher de sobremesa": $value = 7.5 * $request->quantity; break;
            case "Colher de chá": $value = 5 * $request->quantity; break;
            case "Colher de café": $value = 2.5 * $request->quantity; break;
            case "Colher de servir": $value = 30 * $request->quantity; break;
            case "Copo": $value = 200 * $request->quantity; break;
            case "Chavena de chá": $value = 240 * $request->quantity; break;
            case "Pires": $value = 200 * $request->quantity; break;
            case "Prato": $value = 400 * $request->quantity; break;
            case "Caneca": $value = 300 * $request->quantity; break;
            case "Concha de sopa": $value = 160 * $request->quantity; break;
            case "Tigela média": $value = 350 * $request->quantity; break;
            case "Chavena de café": $value = 40 * $request->quantity; break;
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
            $thumbnail= Image::make($image);
            $thumbnail->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path = basename($image->store('food', 'public'));
            Storage::put('public\food\thumb_'.$path, $thumbnail->stream());
            $meal->foodPhotoUrl = basename($path);
        }

        if($request->nutritionalInfoPhoto != null) {
            $image = $request->file('nutritionalInfoPhoto');
            $thumbnail= Image::make($image);
            $thumbnail->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = basename($image->store('nutritionalInfo', 'public'));
            Storage::put('public\nutritionalInfo\thumb_'.$path, $thumbnail->stream());
            $meal->nutritionalInfoPhotoUrl = basename($path);
        }

        $grams = MealControllerAPI::computeNumericUnit($request);

        $meal->name = $request->name;
        $meal->quantity = $request->quantity;
        $meal->relativeUnit = $request->relativeUnit;
        $meal->type = $request->type;
        $meal->date = $request->date;
        $meal->time = $request->time;
        $meal->observations = $request->observations;
        $meal->userId = $id;
        $meal->numericUnit = $grams;

        $meal->save();

        $nutritionalInfo = NutritionalInfoStatic::where('name', $request->name)->first();
        $infoToInsert = array(
            array('label'=> 'energy_kcal', 'unit'=> 'kcal'),
            array('label'=> 'energy_kJ', 'unit'=> 'kJ'),
            array('label'=> 'water_g', 'unit'=> 'g'),
            array('label'=> 'protein_g', 'unit'=> 'g'),
            array('label'=> 'fats_g', 'unit'=> 'g'),
            array('label'=> 'carbo_hidrats_g', 'unit'=> 'g'),
            array('label'=> 'fiber_g', 'unit'=> 'g'),
            array('label'=> 'colesterol_mg', 'unit'=> 'mg'),
            array('label'=> 'vitA_mg', 'unit'=> 'mg'),
            array('label'=> 'vitD_pg', 'unit'=> 'μg'),
            array('label'=> 'tiamina_mg', 'unit'=> 'mg'),
            array('label'=> 'riboflavina_mg', 'unit'=> 'mg'),
            array('label'=> 'niacina_mg', 'unit'=> 'mg'),
            array('label'=> 'vitB6_mg', 'unit'=> 'mg'),
            array('label'=> 'vit_B12_pg', 'unit'=> 'μg'),
            array('label'=> 'vitC_mg', 'unit'=> 'mg'),
            array('label'=> 'na_mg', 'unit'=> 'mg'),
            array('label'=> 'k_mg', 'unit'=> 'mg'),
            array('label'=> 'ca_mg', 'unit'=> 'mg'),
            array('label'=> 'p_mg', 'unit'=> 'mg'),
            array('label'=> 'mg_mg', 'unit'=> 'mg'),
            array('label'=> 'fe_mg', 'unit'=> 'mg'),
            array('label'=> 'zn_mg', 'unit'=> 'mg'),
        );

        for($i = 0, $size = count($infoToInsert); $i < $size; ++$i) {
            $infoToInsert[$i]['value'] = 0;
            $infoToInsert[$i]['mealId'] = $meal->id;
        }

        if ($nutritionalInfo) {
            $infoToInsert[0]['value'] = (($nutritionalInfo->energy_kcal*$grams)/100)*$request->quantity;
            $infoToInsert[1]['value'] = (($nutritionalInfo->energy_kJ*$grams)/100)*$request->quantity;
            $infoToInsert[2]['value'] = (($nutritionalInfo->water_g*$grams)/100)*$request->quantity;
            $infoToInsert[3]['value'] = (($nutritionalInfo->protein_g*$grams)/100)*$request->quantity;
            $infoToInsert[4]['value'] = (($nutritionalInfo->fats_g*$grams)/100)*$request->quantity;
            $infoToInsert[5]['value'] = (($nutritionalInfo->carbo_hidrats_g*$grams)/100)*$request->quantity;
            $infoToInsert[6]['value'] = (($nutritionalInfo->fiber_g*$grams)/100)*$request->quantity;
            $infoToInsert[7]['value'] = (($nutritionalInfo->colesterol_mg*$grams)/100)*$request->quantity;
            $infoToInsert[8]['value'] = (($nutritionalInfo->vitA_mg*$grams)/100)*$request->quantity;
            $infoToInsert[9]['value'] = (($nutritionalInfo->vitD_pg*$grams)/100)*$request->quantity;
            $infoToInsert[10]['value'] = (($nutritionalInfo->tiamina_mg*$grams)/100)*$request->quantity;
            $infoToInsert[11]['value'] = (($nutritionalInfo->riboflavina_mg*$grams)/100)*$request->quantity;
            $infoToInsert[12]['value'] = (($nutritionalInfo->niacina_mg*$grams)/100)*$request->quantity;
            $infoToInsert[13]['value'] = (($nutritionalInfo->vitB6_mg*$grams)/100)*$request->quantity;
            $infoToInsert[14]['value'] = (($nutritionalInfo->vit_B12_pg*$grams)/100)*$request->quantity;
            $infoToInsert[15]['value'] = (($nutritionalInfo->vitC_mg*$grams)/100)*$request->quantity;
            $infoToInsert[16]['value'] = (($nutritionalInfo->na_mg*$grams)/100)*$request->quantity;
            $infoToInsert[17]['value'] = (($nutritionalInfo->k_mg*$grams)/100)*$request->quantity;
            $infoToInsert[18]['value'] = (($nutritionalInfo->ca_mg*$grams)/100)*$request->quantity;
            $infoToInsert[19]['value'] = (($nutritionalInfo->p_mg*$grams)/100)*$request->quantity;
            $infoToInsert[20]['value'] = (($nutritionalInfo->mg_mg*$grams)/100)*$request->quantity;
            $infoToInsert[21]['value'] = (($nutritionalInfo->fe_mg*$grams)/100)*$request->quantity;
            $infoToInsert[22]['value'] = (($nutritionalInfo->zn_mg*$grams)/100)*$request->quantity;
        }

        NutritionalInfo::insert($infoToInsert);

        return new MealResource($meal);
    }
}
