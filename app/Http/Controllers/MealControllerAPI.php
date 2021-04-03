<?php

namespace App\Http\Controllers;

use App\Http\Resources\Meal as MealResource;
use App\Meal;
use App\NutritionalInfo;
use App\NutritionalInfoStatic;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;
use Response;

class MealControllerAPI extends Controller
{
    public function index()
    {
        return MealResource::collection(Meal::all());
    }

    public function getAuthUserMeals(Request $request) {
        $meals = Meal::where('userId', auth()->id())->get();
        $date = Meal::where('userId', auth()->id())->min('date');
        $days = Carbon::parse($date)->diffInDays(Carbon::now());

        $parsedDates = [];
        $data = [];
        $i = 0;

        if (!$meals) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        foreach ($meals as $meal) {
            $parsed = Carbon::parse($meal->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        $parsedDates = array_unique($parsedDates);

        foreach ($parsedDates as $d) {
            $mealsByType = [
                'P' => [],
                'A' => [],
                'J' => [],
                'S' => [],
                'L' => [],
                'O' => [],
                'M' => [],
            ];

            foreach($meals as $m) {
                if (Carbon::parse($m->date)->format('d/m/Y') == $d) {
                    array_push($mealsByType[$m->type], $m);
                }
            }

            $mealsByType['date'] = $d;

            array_push($data, $mealsByType);
        }

        return Response::json([
            'daysFromInitialDate' => $days,
            'meals' => $data,
        ], 200);
    }

    public function getMealsByUser(Request $request, $id) {
        $dates = Meal::where('userId', $id)->select('date')->get();
        $meals = Meal::where('userId',$id)->get();

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
                'M' => [],
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

    public function getNutritionalInfoByUser(Request $request, $id) {
        $parsedDates = [];
        $i = 0;
        $data = [];

        $dates = Meal::where('userId', $id)->select('date')->get();

        if (!$dates || count($dates) == 0) {
            return Response::json(['meals' => $data], 200);
        }

        $meals = Meal::where('userId',$id)->get();

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        $parsedDates = array_unique($parsedDates);

        foreach ($parsedDates as $d) {
            $mealsByType = [
                'P' => [],
                'A' => [],
                'J' => [],
                'S' => [],
                'L' => [],
                'O' => [],
                'M' => [],
            ];

            foreach($meals as $m) {
                $nutritionalInfo = NutritionalInfo::where('mealId', $m->id)->get();
                $obj['meal'] = $m;
                $obj['nutritionalInfo'] = $nutritionalInfo;

                if (Carbon::parse($m->date)->format('d/m/Y') == $d) {
                    array_push($mealsByType[$m->type], $obj);
                }
            }

            $data[$d] = $mealsByType;
        }

        return Response::json(['data' => $data], 200);
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

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|min:3',
            'quantity' => 'required|numeric|between:0,9999.99',
            'foodPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'nutritionalInfoPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'relativeUnit' => 'required',
            'type' => Rule::in(['P','A','J','S','O','L','M']),
            'date' => 'required',
            'time' => 'required'
        ]);

        $user = User::find(auth()->id());

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

            // $path = basename($image->store('food', 'public'));
            $path = basename($image->store('food', 's3'));
            Storage::disk('s3')->put('food/thumb_'.$path, $thumbnail->stream());
            // Storage::put('public/food/thumb_'.$path, $thumbnail->stream());
            $meal->foodPhotoUrl = basename($path);
        }

        if($request->nutritionalInfoPhoto != null) {
            $image = $request->file('nutritionalInfoPhoto');
            $thumbnail= Image::make($image);
            $thumbnail->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            // $path = basename($image->store('nutritionalInfo', 'public'));
            $path = basename($image->store('nutritionalInfo', 's3'));
            Storage::disk('s3')->put('nutritionalInfo/thumb_'.$path, $thumbnail->stream());
            // Storage::put('public/nutritionalInfo/thumb_'.$path, $thumbnail->stream());
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
        $meal->userId = auth()->id();
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
            $infoToInsert[0]['value'] = ($nutritionalInfo->energy_kcal*$grams)/100;
            $infoToInsert[1]['value'] = ($nutritionalInfo->energy_kJ*$grams)/100;
            $infoToInsert[2]['value'] = ($nutritionalInfo->water_g*$grams)/100;
            $infoToInsert[3]['value'] = ($nutritionalInfo->protein_g*$grams)/100;
            $infoToInsert[4]['value'] = ($nutritionalInfo->fats_g*$grams)/100;
            $infoToInsert[5]['value'] = ($nutritionalInfo->carbo_hidrats_g*$grams)/100;
            $infoToInsert[6]['value'] = ($nutritionalInfo->fiber_g*$grams)/100;
            $infoToInsert[7]['value'] = ($nutritionalInfo->colesterol_mg*$grams)/100;
            $infoToInsert[8]['value'] = ($nutritionalInfo->vitA_mg*$grams)/100;
            $infoToInsert[9]['value'] = ($nutritionalInfo->vitD_pg*$grams)/100;
            $infoToInsert[10]['value'] = ($nutritionalInfo->tiamina_mg*$grams)/100;
            $infoToInsert[11]['value'] = ($nutritionalInfo->riboflavina_mg*$grams)/100;
            $infoToInsert[12]['value'] = ($nutritionalInfo->niacina_mg*$grams)/100;
            $infoToInsert[13]['value'] = ($nutritionalInfo->vitB6_mg*$grams)/100;
            $infoToInsert[14]['value'] = ($nutritionalInfo->vit_B12_pg*$grams)/100;
            $infoToInsert[15]['value'] = ($nutritionalInfo->vitC_mg*$grams)/100;
            $infoToInsert[16]['value'] = ($nutritionalInfo->na_mg*$grams)/100;
            $infoToInsert[17]['value'] = ($nutritionalInfo->k_mg*$grams)/100;
            $infoToInsert[18]['value'] = ($nutritionalInfo->ca_mg*$grams)/100;
            $infoToInsert[19]['value'] = ($nutritionalInfo->p_mg*$grams)/100;
            $infoToInsert[20]['value'] = ($nutritionalInfo->mg_mg*$grams)/100;
            $infoToInsert[21]['value'] = ($nutritionalInfo->fe_mg*$grams)/100;
            $infoToInsert[22]['value'] = ($nutritionalInfo->zn_mg*$grams)/100;
        }

        NutritionalInfo::insert($infoToInsert);

        return new MealResource($meal);
    }

    public function updateMealImage(Request $request, $id) {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        $image = $meal->foodPhotoUrl;
        $filePath = 'food';

        if ($request->type == 'NUTRI_INFO_PHOTO') {
            $image = $meal->nutritionalInfoPhotoUrl;
            $filePath = 'nutritionalInfo';
        }

        if ($image) {
            Storage::disk('s3')->delete($filePath.'/'.$image);
            Storage::disk('s3')->delete($filePath.'/thumb_'.$image);
        }

        $image = $request->file('file');
        $thumbnail= Image::make($image);
        $thumbnail->resize(null, 200, function ($constraint) {
            $constraint->aspectRatio();
        });

        $path = basename($image->store($filePath, 's3'));
        Storage::disk('s3')->put($filePath.'/thumb_'.$path, $thumbnail->stream());

        if ($request->type == 'FOOD_PHOTO') {
            $meal->foodPhotoUrl = basename($path);
        } else {
            $meal->nutritionalInfoPhotoUrl = basename($path);
        }

        $meal ->save();
        return Response::json(['data' => basename($path)], 200);
    }

    public function updateQuantity(Request $request, $id) {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        $meal->numericUnit = $request->quantity;
        $meal->update();
        return new MealResource($meal);
    }

    public function update(Request $request, $id) {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        $grams = MealControllerAPI::computeNumericUnit($request);

        $meal->name = $request->name;
        $meal->quantity = $request->quantity;
        $meal->relativeUnit = $request->relativeUnit;
        $meal->type = $request->type;
        $meal->date = $request->date;
        $meal->time = $request->time;
        $meal->observations = $request->observations;
        $meal->numericUnit = $grams;

        $meal->update();

        return new MealResource($meal);
    }

    public function destroy ($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        $nutritionalInfo = NutritionalInfo::where('mealId', $meal->id)->get();

        if($meal->foodPhotoUrl) {
            Storage::disk('s3')->delete('food/'.$meal->foodPhotoUrl);
            Storage::disk('s3')->delete('food/thumb_'.$meal->foodPhotoUrl);
            // Storage::disk('public')->delete('food/'.$meal->foodPhotoUrl);
            // Storage::disk('public')->delete('food/thumb_'.$meal->foodPhotoUrl);
        }

        if ($meal->nutritionalInfoPhotoUrl) {
            Storage::disk('s3')->delete('nutritionalInfo/'.$meal->nutritionalInfoPhotoUrl);
            Storage::disk('s3')->delete('nutritionalInfo/thumb_'.$meal->nutritionalInfoPhotoUrl);
            // Storage::disk('public')->delete('nutritionalInfo/'.$meal->nutritionalInfoPhotoUrl);
            // Storage::disk('public')->delete('nutritionalInfo/thumb_'.$meal->nutritionalInfoPhotoUrl);
        }

        foreach ($nutritionalInfo as $nutri){
            $nutri->forceDelete();
        }

        $meal->forceDelete();

        return new MealResource($meal);
    }
}
