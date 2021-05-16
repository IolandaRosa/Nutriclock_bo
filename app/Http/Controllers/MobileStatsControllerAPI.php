<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\Ingredient;
use App\Meal;
use App\MealPlan;
use App\MealPlanType;
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
    public function getStats(Request $request)
    {
        if ($request->user()->role != 'PATIENT') {
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
        $totalMeals = Meal::where('userId', $id)->count();
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

        foreach ($sleeps as $sleep) {
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $sum = $sum + $diff;
        }

        foreach ($exercises as $e) {
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

    public function getUserFrequency(Request $request, $id)
    {
        $sleepColor = 'GRAY';
        $mealColor = 'GRAY';
        $planColor = 'GRAY';

        $sleepDates = [];
        $mealDates = [];
        $mealPlansStats = [];
        $i = 0;

        $sleeps = Sleep::where('userId', $id)->orderBy('date')->get('date');
        $meals = Meal::where('userId', $id)->orderBy('date')->get('date');
        $plan = Plan::where('userId', $id)->first();

        if ($sleeps && count($sleeps) > 0) {
            foreach ($sleeps as $sleep) {
                $sleepDates[$i] = $sleep->date;
                $sleepDates = array_unique($sleepDates);
                $i++;
            }

            $totalDaysRegistered = count($sleepDates);
            if ($totalDaysRegistered > 0) {
                $sleepColor = $this->getSleepColor($sleepDates, true);
            }
        }

        if ($meals && count($meals) > 0) {
            foreach ($meals as $meal) {
                array_push($mealDates, date('Y-m-d', strtotime($meal->date)));
                $mealDates = array_unique($mealDates);
            }

            $totalDaysRegistered = count($mealDates);
            if ($totalDaysRegistered > 0) {
                $lastDate = $mealDates[$totalDaysRegistered - 1];
                $now = time();
                $dateDiff = round(($now - strtotime($lastDate)) / (60 * 60 * 24)) - 1;

                if ($totalDaysRegistered >= 3) $mealColor = 'GREEN';
                else if ($totalDaysRegistered > 0) {
                    if ($dateDiff == 0) $mealColor = 'GREEN';
                    else if ($dateDiff == 1) $mealColor = 'YELLOW';
                    else $mealColor = $this->setStatusTreeColor($totalDaysRegistered/3, 1 / 3);
                }
            }
        }

        if ($plan) {
            $mealsPlans = MealPlan::where('planId', $plan->id)->get();
            foreach ($mealsPlans as $mealPlan) {
                $nowParts = explode('-', date('d-m-Y'));
                $planDateParts = explode('-', $mealPlan->date);

                if ($nowParts[2] < $planDateParts[2]) {
                    array_push($mealPlansStats, $mealPlan);
                } else if ($nowParts[2] == $planDateParts[2] && $planDateParts[1] < $nowParts[1]) {
                    array_push($mealPlansStats, $mealPlan);
                } else if ($nowParts[2] == $planDateParts[2] && $nowParts[1] == $planDateParts[1] && $planDateParts[0] <= $nowParts[0]) {
                    array_push($mealPlansStats, $mealPlan);
                }
            }

            $totalConfirmed = 0;
            $total = 0;

            foreach ($mealPlansStats as $mealPlansStat) {
                $mealPlanTypes = MealPlanType::where('planMealId', $mealPlansStat->id)->get();

                if($mealPlanTypes) {
                    foreach ($mealPlanTypes as $mealPlanType) {
                        $total++;
                        if ($mealPlanType->confirmed) $totalConfirmed ++;
                    }
                }
            }

            if ($totalConfirmed > 0) {
                $planColor = $this->setStatusColor($totalConfirmed / $total, 1/4);
            }
        }

        return Response::json([
            'sleepColor' => $sleepColor,
            'mealColor' => $mealColor,
            'planColor' => $planColor
        ]);
    }

    private function getSleepColor($sleepDates, $isQuarter)
    {
        $total = count($sleepDates);
        $dateParts = explode('/', $sleepDates[0]);
        $firstDate = strtotime($dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]);
        $now = time();
        $dateDiff = round(($now - $firstDate) / (60 * 60 * 24)) - 1; // excluir data de fim

        if ($isQuarter) return $this->setStatusColor($total, $dateDiff / 4);
        return $this->setStatusTreeColor($total, $dateDiff / 3);
    }

    private function setStatusColor($total, $quarterDay)
    {
        if ($total < $quarterDay) {
            return 'RED';
        }

        if ($total < ($quarterDay * 2)) {
            return 'ORANGE';
        }

        if ($total < ($quarterDay * 3)) {
            return 'YELLOW';
        }

        return 'GREEN';
    }

    private function setStatusTreeColor($total, $thirdDay)
    {
        if ($total <= $thirdDay) {
            return 'RED';
        }

        if ($total <= ($thirdDay * 2)) {
            return 'YELLOW';
        }

        return 'GREEN';
    }
}
