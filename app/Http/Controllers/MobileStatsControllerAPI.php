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

        $date = Meal::where('userId', $id)->min('date');
        $dates = Meal::where('userId', $id)->select('date')->get();
        $totalMeals= Meal::where('userId', $id)->count();
        $sleeps = Sleep::where('userId', Auth::guard('api')->user()->id)->get();
        $totalSleeps = count($sleeps);
        $averageSleepHours = 0.0;
        $sum = 0;

        foreach($sleeps as $sleep) {
            $w = SleepControllerAPI::computeTimeInHours($sleep->wakeUpTime);
            $s = SleepControllerAPI::computeTimeInHours($sleep->sleepTime);
            $sum = $sum + abs($s - $w);
        }

        if ($sum > 0) {
            $averageSleepHours = round($sum / $totalSleeps, 2);
        }


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

        return Response::json([
            'daysFromInitialDate' => $days,
            'totalDaysRegistered' => count(array_unique($parsedDates)),
            'meals' => $totalMeals,
            'totalSleepDays' => $totalSleeps,
            'averageSleepHours' => number_format($averageSleepHours, 2),
        ], 200);
    }
}