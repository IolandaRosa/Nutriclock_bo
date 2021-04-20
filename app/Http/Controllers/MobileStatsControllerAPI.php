<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\MealPlanType;
use App\Ingredient;
use App\Meal;
use App\MealPlan;
use App\Plan;
use App\Sleep;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MobileStatsControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/stats",
     *      operationId="stats",
     *      tags={"Mobile Stats"},
     *      summary="Return mobile stats",
     *      description="Return mobile stats",
     *      @OA\Response(
     *          response=200,
     *          description="return mobile stats"
     *       )
     *     )
     */
    public function getStats(Request $request) {
        if($request->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $id = Auth::guard('api')->user()->id;

        $userPlan = Plan::where('userId', $id)->first();
        $mealPlanType = null;

        if ($userPlan) {
            $mealPlan = MealPlan::where('planId', $userPlan->id)->where('date', date('d-m-Y'))->first();

            if ($mealPlan) {
                $mealPlanType = MealPlanType::where('planMealId', $mealPlan->id)->orderBy('hour')->where('hour', '>=', date('H:i'))->first();

                if ($mealPlanType) {
                    $ingredients = Ingredient::where('mealPlanTypeId', $mealPlanType->id)->get();
                    if ($ingredients) $mealPlanType->ingredients = $ingredients;
                    else $mealPlanType->ingredients = [];
                }
            }
        }

        $dates = Meal::where('userId', $id)->select('date')->get();
        $totalMeals= Meal::where('userId', $id)->count();
        $sleeps = Sleep::where('userId', Auth::guard('api')->id())->get();
        $exercises = Exercise::where('userId', Auth::guard('api')->id())->get();
        $parsedDates = [];
        $i = 0;


        $totalSleeps = count($sleeps);
        $averageSleepHours = 0.0;
        $sum = 0;
        $totalBurnedCals = 0;
        $averageBurnedCals = 0;
        $totalDuration = 0;
        $averageDuration = 0;

        foreach($sleeps as $sleep) {
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $sum = $sum + $diff;
        }

        foreach($exercises as $e) {
            $totalBurnedCals += $e->burnedCalories;
            $totalDuration += $e->duration;
            $parsed = Carbon::parse($e->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        $totalDuration = round($totalDuration / 60, 2);
        $totalSportDays = count(array_unique($parsedDates));

        if ($totalDuration > 0) $averageDuration = round($totalDuration / count($exercises), 2);
        if ($totalBurnedCals > 0) $averageBurnedCals = round($totalBurnedCals / count($exercises), 2);

        if ($sum > 0) {
            $averageSleepHours = round($sum / $totalSleeps, 2);
        }


        if (count($dates) == 0) {
            return Response::json([
                'totalDaysRegistered' => 0,
                'meals' => 0,
                'totalSleepDays' => 0,
                'averageSleepHours' => '0.0',
                'totalSportDays' => 0,
                'totalDuration' => 0,
                'averageDuration' => '0.0',
                'totalBurnedCals' => 0,
                'averageBurnedCals' => 0,
                'mealPlanType' => $mealPlanType
            ]);
        }

        $parsedDates = [];
        $i = 0;

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        return Response::json([
            'totalDaysRegistered' => count(array_unique($parsedDates)),
            'meals' => $totalMeals,
            'totalSleepDays' => $totalSleeps,
            'averageSleepHours' => number_format($averageSleepHours, 2),
            'totalSportDays' => intval($totalSportDays),
            'totalDuration' => intval($totalDuration),
            'averageDuration' => number_format($averageDuration, 2),
            'totalBurnedCals' => intval($totalBurnedCals),
            'averageBurnedCals' => intval($averageBurnedCals),
            'mealPlanType' => $mealPlanType
        ]);
    }
}
