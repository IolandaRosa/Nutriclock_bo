<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\MealPlan;
use App\MealPlanType;
use App\NutritionalInfoStatic;
use App\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class MealPlanTypeControllerAPI extends Controller
{
    public function index()
    {
        //
    }

    public function statsByPlanDay($id) {
        $totalProtein = 0;
        $totalFat = 0;
        $totalCarbs = 0;
        $totalFiber = 0;
        $totalEnergyKcal = 0;
        $totalWater = 0;
        $mealStats = [];

        $mealTypes = MealPlanType::where('planMealId', $id)->orderBy('hour')->get(['id', 'type', 'hour']);
        if (!$mealTypes) {
            return Response::json(['error' => 'Não existem refeições associadas aos plano'], 400);
        }

        foreach ($mealTypes as $mealType) {
            $ingredients = Ingredient::where('mealPlanTypeId', $mealType['id'])->get(['code', 'grams']);
            $mealTotalProtein = 0;
            $mealTotalFat = 0;
            $mealTotalCarbs = 0;
            $mealTotalFiber = 0;
            $mealTotalEnergyKcal = 0;
            $mealTotalWater = 0;

            if ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $nutriInfo = NutritionalInfoStatic::where('code', $ingredient['code'])->first(['protein_g', 'fats_g', 'carbo_hidrats_g', 'fiber_g', 'energy_kcal', 'water_g']);

                    if ($nutriInfo) {
                        $totalProtein += (($nutriInfo->protein_g * $ingredient['grams']) / 100);
                        $totalFat += (($nutriInfo->fats_g * $ingredient['grams']) / 100);
                        $totalCarbs += (($nutriInfo->carbo_hidrats_g * $ingredient['grams']) / 100);
                        $totalFiber += (($nutriInfo->fiber_g * $ingredient['grams']) / 100);
                        $totalEnergyKcal += (($nutriInfo->energy_kcal * $ingredient['grams']) / 100);
                        $totalWater += (($nutriInfo->water_g * $ingredient['grams']) / 100);

                        $mealTotalProtein += (($nutriInfo->protein_g * $ingredient['grams']) / 100);
                        $mealTotalFat += (($nutriInfo->fats_g * $ingredient['grams']) / 100);
                        $mealTotalCarbs += (($nutriInfo->carbo_hidrats_g * $ingredient['grams']) / 100);
                        $mealTotalFiber += (($nutriInfo->fiber_g * $ingredient['grams']) / 100);
                        $mealTotalEnergyKcal += (($nutriInfo->energy_kcal * $ingredient['grams']) / 100);
                        $mealTotalWater += (($nutriInfo->water_g * $ingredient['grams']) / 100);
                    }
                }

                $totalMeal = $mealTotalProtein + $mealTotalFat + $mealTotalCarbs + $mealTotalFiber;

                if($mealTotalProtein > 0) $mealTotalProtein = ($mealTotalProtein * 100) / $totalMeal;
                if($mealTotalFat > 0) $mealTotalFat = ($mealTotalFat * 100) / $totalMeal;
                if($mealTotalCarbs > 0) $mealTotalCarbs = ($mealTotalCarbs * 100) / $totalMeal;
                if($mealTotalFiber > 0) $mealTotalFiber = ($mealTotalFiber * 100) / $totalMeal;

                array_push($mealStats, [
                    'name' => $mealType['type'],
                    'hour' => $mealType['hour'],
                    'stats' => [
                        round($mealTotalProtein, 2),
                        round($mealTotalFat, 2),
                        round($mealTotalCarbs, 2),
                        round($mealTotalFiber, 2),
                    ],
                    'energy' => round($mealTotalEnergyKcal, 2),
                    'water' => round($mealTotalWater, 2),
                ]);
            }
        }

        $total = $totalProtein + $totalFat + $totalCarbs + $totalFiber;

        if($totalProtein > 0) $totalProtein = ($totalProtein * 100) / $total;
        if($totalFat > 0) $totalFat = ($totalFat * 100) / $total;
        if($totalCarbs > 0) $totalCarbs = ($totalCarbs * 100) / $total;
        if($totalFiber > 0) $totalFiber = ($totalFiber * 100) / $total;

        return Response::json([
            'data' => ['stats' => [round($totalProtein, 2), round($totalFat, 2), round($totalCarbs, 2), round($totalFiber, 2)],
                'water' => round(($totalWater / 1000), 2),
                'kcal' => round($totalEnergyKcal, 2),
                'days' => $mealStats]
        ]);
    }

    public function statsMealType(Request $request)
    {
        $totalProtein = 0;
        $totalFat = 0;
        $totalCarbo = 0;
        $totalFiber = 0;
        $totalEnergyKcal = 0;
        $totalWater = 0;

        if (!$request->ingredients) {
            return Response::json(['error' => 'A refeição não tem ingredientes associados'], 400);
        }

        foreach ($request->ingredients as $ingredient) {
            $nutriInfo = NutritionalInfoStatic::where('code', $ingredient['code'])->first(['protein_g', 'fats_g', 'carbo_hidrats_g', 'fiber_g', 'energy_kcal', 'water_g']);

            $quantity = self::computeNumericUnit($ingredient);
            if ($nutriInfo) {
                $totalProtein += (($nutriInfo->protein_g * $quantity) / 100);
                $totalFat += (($nutriInfo->fats_g * $quantity) / 100);
                $totalCarbo += (($nutriInfo->carbo_hidrats_g * $quantity) / 100);
                $totalFiber += (($nutriInfo->fiber_g * $quantity) / 100);
                $totalEnergyKcal += (($nutriInfo->energy_kcal * $quantity) / 100);
                $totalWater += (($nutriInfo->water_g * $quantity) / 100);
            }
        }

        $total = $totalProtein + $totalFat + $totalCarbo + $totalFiber;

        if($totalProtein > 0) $totalProtein = ($totalProtein * 100) / $total;
        if($totalFat > 0) $totalFat = ($totalFat * 100) / $total;
        if($totalCarbo > 0) $totalCarbo = ($totalCarbo * 100) / $total;
        if($totalFiber > 0) $totalFiber = ($totalFiber * 100) / $total;

        return Response::json([
            'data' => ['stats' => [round($totalProtein, 2), round($totalFat, 2), round($totalCarbo, 2), round($totalFiber, 2)],
            'water' => round(($totalWater / 1000), 2),
            'kcal' => round($totalEnergyKcal, 2)]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'date' => 'required',
            'dayOfWeek' => Rule::in(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN']),
            'meals' => 'required|array'
        ]);

        $plan = Plan::where('userId', $request->userId)->first();

        if (!$plan) {
            $plan = new Plan();
            $plan->userId = $request->userId;
            $plan->save();
        }

        $mealPlan = MealPlan::where('planId', $plan->id)->where('date', $request->date)->first();

        if (!$mealPlan) {
            $mealPlan = new MealPlan();
            $mealPlan->planId = $plan->id;
            $mealPlan->date = $request->date;
            $mealPlan->dayOfWeek = $request->dayOfWeek;
            $mealPlan->save();
        }

        foreach ($request->meals as $meal) {
            $mealPlanType = MealPlanType::where('planMealId', $mealPlan->id)->where('type', $meal['name'])->first();
            if ($mealPlanType) {
                return Response::json(['error' => 'Já existe uma refeição com o mesmo nome registada na mesma data'], 400);
            }

            $mealPlanType = MealPlanType::where('planMealId', $mealPlan->id)->where('hour', $meal['time'])->first();
            if ($mealPlanType) {
                return Response::json(['error' => 'Já existe uma refeição com o mesmo horário registada na mesma data'], 400);
            }

            $mealPlanType = new MealPlanType();
            $mealPlanType->planMealId = $mealPlan->id;
            $mealPlanType->type = $meal['name'];
            $mealPlanType->portion = $meal['portion'];
            $mealPlanType->hour = $meal['time'];
            $mealPlanType->save();

            foreach ($meal['ingredients'] as $i) {
                $ingredient = new Ingredient();
                $ingredient->code = $i['code'];
                $ingredient->name = $i['name'];
                $ingredient->quantity = $i['quantity'];
                $ingredient->unit = $i['unit'];
                $ingredient->description = $i['description'];
                $ingredient->grams = self::computeNumericUnit($ingredient);
                $ingredient->mealPlanTypeId = $mealPlanType->id;
                $ingredient->save();
            }
        }

        return Response::json(['data' => 'A refeição foi criada com sucesso']);
    }

    public function storeMealType(Request $request, $id)
    {
        $mealPlanType = MealPlanType::where('planMealId', $id)->where('type', $request->name)->first();
        if ($mealPlanType) {
            return Response::json(['error' => 'Já existe uma refeição com o mesmo nome registada'], 400);
        }

        $mealPlanType = MealPlanType::where('planMealId', $id)->where('hour', $request->time)->first();
        if ($mealPlanType) {
            return Response::json(['error' => 'Já existe uma refeição com o mesmo horário registada'], 400);
        }

        $mealPlanType = new MealPlanType();
        $mealPlanType->planMealId = $id;
        $mealPlanType->type = $request->name;
        $mealPlanType->portion = $request->portion;
        $mealPlanType->hour = $request->time;
        $mealPlanType->save();

        return new \App\Http\Resources\MealPlanType($mealPlanType);
    }

    public static function computeNumericUnit($request)
    {
        switch ($request['unit']) {
            case "Gramas":
            case "Mililitros":
                $value = $request['quantity'];
                break;
            case "Colher de sopa":
                $value = 15 * $request['quantity'];
                break;
            case "Colher de sobremesa":
                $value = 7.5 * $request['quantity'];
                break;
            case "Colher de chá":
                $value = 5 * $request['quantity'];
                break;
            case "Colher de café":
                $value = 2.5 * $request['quantity'];
                break;
            case "Colher de servir":
                $value = 30 * $request['quantity'];
                break;
            case "Pires":
            case "Copo":
                $value = 200 * $request['quantity'];
                break;
            case "Chavena de chá":
                $value = 240 * $request['quantity'];
                break;
            case "Prato":
                $value = 400 * $request['quantity'];
                break;
            case "Caneca":
                $value = 300 * $request['quantity'];
                break;
            case "Concha de sopa":
                $value = 160 * $request['quantity'];
                break;
            case "Tigela média":
                $value = 350 * $request['quantity'];
                break;
            case "Chavena de café":
                $value = 40 * $request['quantity'];
                break;
            default:
                $value = 0;
        }

        return $value;
    }

    public function show($id)
    {
        $plan = Plan::where('userId', $id)->first();

        if (!$plan) {
            return Response::json(['data' => []]);
        }

        $meal_plans = MealPlan::where('planId', $plan->id)->get();

        if ($meal_plans) {
            foreach ($meal_plans as $mealPlan) {
                $mealPlanTypes = MealPlanType::where('planMealId', $mealPlan->id)->orderBy('hour')->get();
                if ($mealPlanTypes) {
                    foreach ($mealPlanTypes as $mealType) {
                        $ingredients = Ingredient::where('mealPlanTypeId', $mealType->id)->get();

                        if ($ingredients) {
                            $mealType['ingredients'] = $ingredients;
                        }
                    }

                    $mealPlan['mealTypes'] = $mealPlanTypes;
                }
            }
        }

        return Response::json(['data' => $meal_plans]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $mealPlanType = MealPlanType::find($id);

        if (!$mealPlanType) {
            return Response::json(['error' => 'A refeição não existe!'], 400);
        }

        $mealPlanType->forceDelete();

        return Response::json(['data' => 'Refeição eliminada com sucesso!']);
    }
}
