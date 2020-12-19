<?php

namespace App\Http\Controllers;

use App\Meal;
use App\Sleep;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileStatsControllerAPI extends Controller
{
    public function getStats(Request $request) {
        if($request->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $id = Auth::guard('api')->user()->id;

        $dates = Meal::where('userId', $id)->select('date')->get();
        $totalMeals= Meal::where('userId', $id)->count();
        $sleeps = Sleep::where('userId', Auth::guard('api')->user()->id)->get();
        $totalSleeps = count($sleeps);
        $averageSleepHours = 0.0;
        $sum = 0;

        foreach($sleeps as $sleep) {
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $sum = $sum + $diff;
        }

        if ($sum > 0) {
            $averageSleepHours = round($sum / $totalSleeps, 2);
        }


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

        return Response::json([
            'totalDaysRegistered' => count(array_unique($parsedDates)),
            'meals' => $totalMeals,
            'totalSleepDays' => $totalSleeps,
            'averageSleepHours' => number_format($averageSleepHours, 2),
        ], 200);
    }
}
