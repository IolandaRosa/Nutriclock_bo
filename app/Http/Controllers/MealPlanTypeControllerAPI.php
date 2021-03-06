<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\MealPlan;
use App\MealPlanType;
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

    public function store(Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'date' => 'required',
            'dayOfWeek' => Rule::in(['MON','TUE','WED','THU','FRI','SAT','SUN']),
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

    public function storeMealType(Request $request, $id){
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

    public static function computeNumericUnit($request) {
        switch($request['unit']) {
            case "Gramas":
            case "Mililitros": $value = $request['quantity']; break;
            case "Colher de sopa": $value = 15 * $request['quantity']; break;
            case "Colher de sobremesa": $value = 7.5 * $request['quantity']; break;
            case "Colher de chá": $value = 5 * $request['quantity']; break;
            case "Colher de café": $value = 2.5 * $request['quantity']; break;
            case "Colher de servir": $value = 30 * $request['quantity']; break;
            case "Copo": $value = 200 * $request['quantity']; break;
            case "Chavena de chá": $value = 240 * $request['quantity']; break;
            case "Pires": $value = 200 * $request['quantity']; break;
            case "Prato": $value = 400 * $request['quantity']; break;
            case "Caneca": $value = 300 * $request['quantity']; break;
            case "Concha de sopa": $value = 160 * $request['quantity']; break;
            case "Tigela média": $value = 350 * $request['quantity']; break;
            case "Chavena de café": $value = 40 * $request['quantity']; break;
            default: $value = 0;
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
                $mealPlanTypes = MealPlanType::where('planMealId', $mealPlan->id)->get();
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
