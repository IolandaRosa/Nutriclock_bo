<?php

namespace App\Http\Controllers;

use App\MealPlan;
use App\MealPlanType;
use App\Plan;
use Illuminate\Http\Request;
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
            'dayOfWeek' => Rule::in([0, 1, 2, 3, 4, 5, 6]),
            'type' => Rule::in(['P','A','J','S','O','L','M']),
            'portion' => 'required',
            'hour' => 'required',
            'ingredients' => 'required|array'
        ]);

        $plan = new Plan();
        $plan->userId = $request->userId;
        $plan->save();

        $mealPlan = new MealPlan();
        $mealPlan->planId = $plan->id;
        $mealPlan->date = $request->date;
        $mealPlan->dayOfWeek = $request->dayOfWeek;
        $mealPlan->save();

        $mealPlanType = new MealPlanType();
        $mealPlanType->planMealId = $mealPlan->id;
        $mealPlanType->type = $request->type;
        $mealPlanType->portion = $request->portion;
        $mealPlanType->hour = $request->hour;
        $mealPlanType->save();

        // userId -> plan

        // 'planId', 'date', 'dayOfWeek' -> mealPlan

        // 'type', 'planMealId', 'portion', 'hour' -> mealPlanType

        // 'code', 'name', 'quantity', 'unit', 'grams', 'planMealId'
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
