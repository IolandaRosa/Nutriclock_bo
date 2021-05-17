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
use DateTime;
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

    /**
     * @OA\Get(
     *      path="/api/reports",
     *      operationId="reports",
     *      tags={"Mobile reports"},
     *      summary="Return mobile reports",
     *      description="Return mobile reports",
     *      @OA\Response(
     *          response=200,
     *          description="return mobile reports"
     *       )
     *     )
     */
    public function getReport()
    {
        $totalSleep = '-';
        $totalExercise = '-';
        $averageSleepTime = 0;
        $averageExerciseTime = 0;
        $averageExerciseCalories = 0;
        $maximumSleepHour = 0;
        $maximumSleepDate = '-';
        $minimumSleepHour = 99999;
        $minimumSleepDate = '-';
        $maximumExercise = 0;
        $maximumCalories = 0;
        $minimumExercise = 99999;
        $minimumCalories = 99999;
        $averageSleepArray = [0, 0, 0, 0, 0, 0, 0];
        $percentageSleepArray = [];
        $averageCaloriesArray = [0, 0, 0, 0, 0, 0, 0];
        $percentageCaloriesArray = [];
        $averageExerciseArray = [0, 0, 0, 0, 0, 0, 0];
        $percentageExerciseArray = [];

        if (!Auth::guard('api')->user()) {
            return Response::json(['error' => 'Utilizador nao encontrado'], 404);
        }

        $sleeps = Sleep::where('userId', Auth::guard('api')->id())->orderBy('date')->get();
        $exercises = Exercise::where('userId', Auth::guard('api')->id())->orderBy('date')->get();

        if ($sleeps && count($sleeps) > 0) {
            $totalSleep = count($sleeps) . '/' . $this->getDayBetweenDateSlash($sleeps[0]->date, true);
            $duration = 0;

            $monD = 0;
            $monT = 0;
            $tueD = 0;
            $tueT = 0;
            $wedD = 0;
            $wedT = 0;
            $thuD = 0;
            $thuT = 0;
            $friD = 0;
            $friT = 0;
            $satD = 0;
            $satT = 0;
            $sunD = 0;
            $sunT = 0;
            $maxValue = 0;

            foreach ($sleeps as $sleep) {
                $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
                $duration += $diff;

                $dayOfWeek = date('l', strtotime($this->parseDate($sleep->date)));

                switch ($dayOfWeek) {
                    case 'Monday':
                    {
                        $monD += $diff;
                        $monT++;
                        break;
                    }
                    case 'Tuesday':
                    {
                        $tueD += $diff;
                        $tueT++;
                        break;
                    }
                    case 'Wednesday':
                    {
                        $wedD += $diff;
                        $wedT++;
                        break;
                    }
                    case 'Thursday':
                    {
                        $thuD += $diff;
                        $thuT++;
                        break;
                    }
                    case 'Friday':
                    {
                        $friD += $diff;
                        $friT++;
                        break;
                    }
                    case 'Saturday':
                    {
                        $satD += $diff;
                        $satT++;
                        break;
                    }
                    case 'Sunday':
                    {
                        $sunD += $diff;
                        $sunT++;
                        break;
                    }
                }

                if ($diff > $maximumSleepHour) {
                    $maximumSleepHour = $diff;
                    $maximumSleepDate = $sleep->date;
                }

                if ($diff < $minimumSleepHour) {
                    $minimumSleepHour = $diff;
                    $minimumSleepDate = $sleep->date;
                }
            }

            if ($monT > 0) {
                $value = round($monD / $monT, 1);
                $averageSleepArray[0] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[0] = '0';
            }

            if ($tueT > 0) {
                $value = round($tueD / $tueT, 1);
                $averageSleepArray[1] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[1] = '0';
            }

            if ($wedT > 0) {
                $value = round($wedD / $wedT, 1);
                $averageSleepArray[2] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[2] = '0';
            }

            if ($thuT > 0) {
                $value = round($thuD / $thuT, 1);
                $averageSleepArray[3] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[3] = '0';
            }

            if ($friT > 0) {
                $value = round($friD / $friT, 1);
                $averageSleepArray[4] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[4] = '0';
            }

            if ($satT > 0) {
                $value = round($satD / $satT, 1);
                $averageSleepArray[5] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[5] = '0';
            }

            if ($sunT > 0) {
                $value = round($sunD / $sunT, 1);
                $averageSleepArray[6] = ''.$value;

                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            } else {
                $averageSleepArray[6] = '0';
            }

            if ($maxValue > 0)
                foreach ($averageSleepArray as $av) {
                    array_push($percentageSleepArray, ''.(round(($av / $maxValue) * 100)));
                }

            $averageSleepTime = $duration / count($sleeps);
        }

        if ($exercises && count($exercises) > 0) {
            $totalExercise = count($exercises) . '/' . $this->getDayBetweenDateSlash($exercises[0]->date, false);
            $duration = 0;
            $calories = 0;

            $monExerciseD = 0;
            $monT = 0;
            $tueExerciseD = 0;
            $tueT = 0;
            $wedExerciseD = 0;
            $wedT = 0;
            $thuExerciseD = 0;
            $thuT = 0;
            $friExerciseD = 0;
            $friT = 0;
            $satExerciseD = 0;
            $satT = 0;
            $sunExerciseD = 0;
            $sunT = 0;
            $maxExerciseValue = 0;

            $monCaloriesD = 0;
            $tueCaloriesD = 0;
            $wedCaloriesD = 0;
            $thuCaloriesD = 0;
            $friCaloriesD = 0;
            $satCaloriesD = 0;
            $sunCaloriesD = 0;
            $maxCaloriesValue = 0;

            foreach ($exercises as $exercise) {
                $duration += $exercise->duration;
                $calories += $exercise->burnedCalories;

                $dayOfWeek = date('l', strtotime($exercise->date));

                switch ($dayOfWeek) {
                    case 'Monday':
                    {
                        $monExerciseD += $exercise->duration;
                        $monCaloriesD += $exercise->burnedCalories;
                        $monT++;
                        break;
                    }
                    case 'Tuesday':
                    {
                        $tueExerciseD += $exercise->duration;
                        $tueCaloriesD += $exercise->burnedCalories;
                        $tueT++;
                        break;
                    }
                    case 'Wednesday':
                    {
                        $wedExerciseD += $exercise->duration;
                        $wedCaloriesD += $exercise->burnedCalories;
                        $wedT++;
                        break;
                    }
                    case 'Thursday':
                    {
                        $thuExerciseD += $exercise->duration;
                        $thuCaloriesD += $exercise->burnedCalories;
                        $thuT++;
                        break;
                    }
                    case 'Friday':
                    {
                        $friExerciseD += $exercise->duration;
                        $friCaloriesD += $exercise->burnedCalories;
                        $friT++;
                        break;
                    }
                    case 'Saturday':
                    {
                        $satExerciseD += $exercise->duration;
                        $satCaloriesD += $exercise->burnedCalories;
                        $satT++;
                        break;
                    }
                    case 'Sunday':
                    {
                        $sunExerciseD += $exercise->duration;
                        $sunCaloriesD += $exercise->burnedCalories;
                        $sunT++;
                        break;
                    }
                }

                if ($exercise->duration > $maximumExercise) {
                    $maximumExercise = $exercise->duration;
                }

                if ($exercise->duration < $minimumExercise) {
                    $minimumExercise = $exercise->duration;
                }

                if ($exercise->burnedCalories > $maximumCalories) {
                    $maximumCalories = $exercise->burnedCalories;
                }

                if ($exercise->burnedCalories < $minimumCalories) {
                    $minimumCalories = $exercise->burnedCalories;
                }
            }

            if ($monT > 0) {
                $value = round($monExerciseD / $monT, 1);
                $valueCalories = round($monCaloriesD / $monT, 1);
                $averageExerciseArray[0] = ''.$value;
                $averageCaloriesArray[0] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[0] = '0';
                $averageCaloriesArray[0] = '0';
            }

            if ($tueT > 0) {
                $value = round($tueExerciseD / $tueT, 1);
                $valueCalories = round($tueCaloriesD / $tueT, 1);
                $averageExerciseArray[1] = ''.$value;
                $averageCaloriesArray[1] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[1] = '0';
                $averageCaloriesArray[1] = '0';
            }

            if ($wedT > 0) {
                $value = round($wedExerciseD / $wedT, 1);
                $valueCalories = round($wedCaloriesD / $wedT, 1);
                $averageExerciseArray[2] = ''.$value;
                $averageCaloriesArray[2] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[2] = '0';
                $averageCaloriesArray[2] = '0';
            }

            if ($thuT > 0) {
                $value = round($thuExerciseD / $thuT, 1);
                $valueCalories = round($thuCaloriesD / $thuT, 1);
                $averageExerciseArray[3] = ''.$value;
                $averageCaloriesArray[3] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[3] = '0';
                $averageCaloriesArray[3] = '0';
            }

            if ($friT > 0) {
                $value = round($friExerciseD / $friT, 1);
                $valueCalories = round($friCaloriesD / $friT, 1);
                $averageExerciseArray[4] = ''.$value;
                $averageCaloriesArray[4] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[4] = '0';
                $averageCaloriesArray[4] = '0';
            }

            if ($satT > 0) {
                $value = round($satExerciseD / $satT, 1);
                $valueCalories = round($satCaloriesD / $satT, 1);
                $averageExerciseArray[5] = ''.$value;
                $averageCaloriesArray[5] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[5] = '0';
                $averageCaloriesArray[5] = '0';
            }

            if ($sunT > 0) {
                $value = round($sunExerciseD / $sunT, 1);
                $valueCalories = round($sunCaloriesD / $sunT, 1);
                $averageExerciseArray[6] = ''.$value;
                $averageCaloriesArray[6] = ''.$valueCalories;

                if ($value > $maxExerciseValue) {
                    $maxExerciseValue = $value;
                }

                if ($valueCalories > $maxCaloriesValue) {
                    $maxCaloriesValue = $valueCalories;
                }
            } else {
                $averageExerciseArray[6] = '0';
                $averageCaloriesArray[6] = '0';
            }

            if ($maxExerciseValue > 0)
                foreach ($averageExerciseArray as $av) {
                    array_push($percentageExerciseArray, ''.(round(($av / $maxExerciseValue) * 100)));
                }

            if ($maxCaloriesValue > 0)
                foreach ($averageCaloriesArray as $av) {
                    array_push($percentageCaloriesArray, ''.(round(($av/$maxCaloriesValue) * 100)));
                }

            if ($duration > 0) $averageExerciseTime = $duration / count($exercises);
            if ($calories > 0) $averageExerciseCalories = $calories / count($exercises);
        }

        return Response::json([
            'totalSleeps' => $totalSleep,
            'averageSleepTime' => ''.round($averageSleepTime, 1),
            'maximumSleepHour' => ''.$maximumSleepHour,
            'maximumSleepDate' => $maximumSleepDate,
            'minimumSleepHour' => ''.$minimumSleepHour,
            'minimumSleepDate' => $minimumSleepDate,
            'averageSleepArray' => $averageSleepArray,
            'percentageSleepArray' => $percentageSleepArray,
            'totalExercises' => $totalExercise,
            'averageExerciseTime' => ''.round($averageExerciseTime, 1),
            'averageExerciseCalories' => ''.round($averageExerciseCalories, 1),
            'maximumExercise' => ''.$maximumExercise,
            'minimumExercise' => ''.$minimumExercise,
            'maximumCalories' => ''.$maximumCalories,
            'minimumCalories' => ''.$minimumCalories,
            'averageExerciseArray' => $averageExerciseArray,
            'percentageExerciseArray' => $percentageExerciseArray,
            'averageCaloriesArray' => $averageCaloriesArray,
            'percentageCaloriesArray' => $percentageCaloriesArray
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/data-status/{id}",
     *      operationId="dataStatus",
     *      tags={"Return the status to sinalize on backoffice"},
     *      summary="Return patient register status",
     *      description="Return patient register status",
     *      @OA\Parameter(
     *         description="ID of patient",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return patient register status"
     *       )
     *     )
     */
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
                    else $mealColor = $this->setStatusTreeColor($totalDaysRegistered / 3, 1 / 3);
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

                if ($mealPlanTypes) {
                    foreach ($mealPlanTypes as $mealPlanType) {
                        $total++;
                        if ($mealPlanType->confirmed) $totalConfirmed++;
                    }
                }
            }

            if ($totalConfirmed > 0) {
                $planColor = $this->setStatusColor($totalConfirmed / $total, 1 / 4);
            }
        }

        return Response::json([
            'sleepColor' => $sleepColor,
            'mealColor' => $mealColor,
            'planColor' => $planColor
        ]);
    }

    private function parseDate($date)
    {
        $dateParts = explode('/', $date);

        return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    }

    private function getDayBetweenDateSlash($date, $parseSlash)
    {
        if ($parseSlash) {
            $datetime1 = new DateTime($this->parseDate($date));
        } else {
            $datetime1 = new DateTime($date);
        }

        $datetime2 = new DateTime();

        $difference = $datetime1->diff($datetime2);

        return $difference->d;
    }

    private function getSleepColor($sleepDates, $isQuarter)
    {
        $total = count($sleepDates);
        $dateDiff = $this->getDayBetweenDateSlash($sleepDates[0], true);

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
