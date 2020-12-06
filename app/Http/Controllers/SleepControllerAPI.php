<?php

namespace App\Http\Controllers;

use App\Exports\SleepsExport;
use Illuminate\Http\Request;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Sleep;
use App\Http\Resources\Sleep as SleepResource;

class SleepControllerAPI extends Controller
{
    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'date' => 'required',
            'userId' => 'required',
            'wakeUpTime' => 'required',
            'sleepTime' => 'required',
            'hasWakeUp' => 'required',
        ]);

        $user=User::find($request->userId);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $sleep = new Sleep();

        $sleep->date = $request->date;
        $sleep->userId = $request->userId;
        $sleep->wakeUpTime = $request->wakeUpTime;
        $sleep->sleepTime = $request->sleepTime;
        $sleep->hasWakeUp = $request->hasWakeUp;
        $sleep->activities = null;
        $sleep->motives = null;

        if ($request->activities && count($request->activities) > 0) {
            $activities = $request->activities[0];
            for($i = 1; $i < count($request->activities); $i++) {
                $activities=$activities.", ".$request->activities[$i];
            }

            $sleep->activities = $activities;
        }

        if ($request->motives && count($request->motives) > 0) {
            $motives = $request->motives[0];

            for($i = 1; $i < count($request->motives); $i++) {
                $motives=$motives.", ".$request->motives[$i];
            }

            $sleep->motives = $motives;
        }

        $sleep->save();

        return new SleepResource($sleep);
    }

    public function getSleepStatsByUser($id)
    {
        $user=User::find($id);

        if (!$user) {
             return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $sleeps = Sleep::where('userId', $id)->get();

        if (!$sleeps || count($sleeps) == 0) {
            return Response::json(['error' => 'Não existem registos de sono.'], 404);
        }

        $sleepsSum = 0;
        $sleepsCount = count($sleeps);
        $valuesToFilter = [];

        foreach($sleeps as $sleep) {
            $parsedDate = explode('/', $sleep->date);
            $valuesToFilter[$parsedDate[2]][$parsedDate[1]] = [];
        }

        foreach($sleeps as $sleep) {
            $w = SleepControllerAPI::computeTimeInHours($sleep->wakeUpTime);
            $s = SleepControllerAPI::computeTimeInHours($sleep->sleepTime);
            $sleepsSum += abs($s - $w);
            $parsedDate = explode('/', $sleep->date);
            array_push($valuesToFilter[$parsedDate[2]][$parsedDate[1]], [
                'label' => $parsedDate[0],
                'value' => round(abs($s - $w), 2),
            ]);
        }

        $average = $sleepsSum/$sleepsCount;

        return Response::json(['stats' => [
            'totalRegisters'=>$sleepsCount,
            'averageTime'=>round($average, 2),
            'chartStats' => $valuesToFilter
        ]]);
    }

    public static function computeTimeInHours($value) {
       $time = explode(':', $value);

       $minutes = $time[1]/60;

       return $time[0]+$minutes;
    }

    public static function getSleepStatsForAuthUser(Request $request) {
        if($request->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $sleeps = Sleep::where('userId', Auth::guard('api')->user()->id)->get();

        if (!$sleeps || count($sleeps) == 0) {
            return Response::json(['error' => 'Não existem registos de sono.'], 404);
        }

        $valuesToFilter = [];

        foreach($sleeps as $sleep) {
            $parsedDate = explode('/', $sleep->date);
            $valuesToFilter[$parsedDate[2]][$parsedDate[1]] = [];
        }

        foreach($sleeps as $sleep) {
            $w = SleepControllerAPI::computeTimeInHours($sleep->wakeUpTime);
            $s = SleepControllerAPI::computeTimeInHours($sleep->sleepTime);
            $parsedDate = explode('/', $sleep->date);
            array_push($valuesToFilter[$parsedDate[2]][$parsedDate[1]], [
                'label' => $parsedDate[0],
                'value' => abs($s - $w),
            ]);
        }

        return Response::json(['data' => $valuesToFilter]);
    }

    public function show($id)
    {
        $sleeps = Sleep::where('userId', $id)->get();

        if (!$sleeps) {
            return Response::json(['error' => 'O registo nao existe para o utilizador.'], 404);
        }

        foreach($sleeps as $sleep) {
            $w = SleepControllerAPI::computeTimeInHours($sleep->wakeUpTime);
            $s = SleepControllerAPI::computeTimeInHours($sleep->sleepTime);
            $sleep->totalHours = round(abs($s - $w),2);
        }

        return SleepResource::collection($sleeps);
    }

    public function getSleepDates(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $user=User::find($request->userId);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $sleeps = Sleep::where('userId', $user->id)->get(['date']);

        return SleepResource::collection($sleeps);
    }

    public function export()
    {
        return Excel::download(new SleepsExport, 'sleeps.xlsx');
    }
}
