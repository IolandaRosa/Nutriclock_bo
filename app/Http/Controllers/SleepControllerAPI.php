<?php

namespace App\Http\Controllers;

use App\Exports\SleepsExport;
use App\Http\Resources\Sleep as SleepResource;
use App\Sleep;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Response;

class SleepControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/sleeps",
     *      operationId="Creates new sleep",
     *      tags={"Sleep"},
     *      summary="Creates new sleep",
     *      description="Creates new sleep",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="date",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wakeUpTime",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="sleepTime",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="hasWakeUp",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="activities",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="motives",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return usf"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'date' => 'required',
            'wakeUpTime' => 'required',
            'sleepTime' => 'required',
            'hasWakeUp' => 'required',
        ]);

        $user=User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $sleep = new Sleep();

        $sleep->date = $request->date;
        $sleep->userId = $user->id;
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

    /**
     * @OA\Get(
     *      path="/api/sleeps/stats/{id}",
     *      operationId="Get sleep stats by user",
     *      tags={"Sleep"},
     *      summary="Get sleep stats by user",
     *      description="Get sleep stats by user",
     *      @OA\Parameter(
     *         description="ID of user",
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
     *          description="return the list of sleep stats by user"
     *       )
     *     )
     */
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
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $sleepsSum += $diff;
            $parsedDate = explode('/', $sleep->date);
            array_push($valuesToFilter[$parsedDate[2]][$parsedDate[1]], [
                'label' => $parsedDate[0],
                'value' => round($diff, 2),
            ]);
        }

        $average = $sleepsSum/$sleepsCount;

        return Response::json(['stats' => [
            'totalRegisters'=>$sleepsCount,
            'averageTime'=>round($average, 2),
            'chartStats' => $valuesToFilter
        ]]);
    }

    public static function computeTimeInHours($sleepTime, $wakeUpTime) {
        if ($sleepTime == $wakeUpTime) return 24.00;

        $sleepTimeArray = explode(':', $sleepTime); // 22, 23
        $totalHoursSleep = $sleepTimeArray[0]; // 22
        $minutes = round($sleepTimeArray[1]/60, 1); // 0.38
        $beforeMidnight = false;
        $beforeMidDay = false;

        if ($totalHoursSleep > 12 && $totalHoursSleep < 24) {
            $beforeMidnight = true;
            $totalHoursSleep = 24 - $totalHoursSleep; // 2
        }

        $totalHoursSleep = $totalHoursSleep + $minutes; // 2.38

        $wakeUpTimeArray = explode(':', $wakeUpTime); //7.22
        $totalHoursWakeUp = $wakeUpTimeArray[0];

        if ($totalHoursWakeUp > 12 && $totalHoursWakeUp < 24) {
            $beforeMidDay = true;
        }

        $minutes = round($wakeUpTimeArray[1]/60, 1);
        $totalHoursWakeUp = $totalHoursWakeUp + $minutes;

        if ($beforeMidnight || (!$beforeMidnight && $beforeMidDay)) return $totalHoursSleep + $totalHoursWakeUp;

        return abs($totalHoursSleep - $totalHoursWakeUp);
    }

    /**
     * @OA\Get(
     *      path="/api/sleeps/myStats",
     *      operationId="Get auth user sleep stats",
     *      tags={"Sleep"},
     *      summary="Get auth user sleep stats",
     *      description="Get auth user sleep stats",
     *      @OA\Response(
     *          response=200,
     *          description="return auth user sleep stats"
     *       )
     *     )
     */
    public static function getSleepStatsForAuthUser(Request $request) {
        if($request->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $sleeps = Sleep::where('userId', Auth::guard('api')->id())->get();

        if (!$sleeps || count($sleeps) == 0) {
            return Response::json(['error' => 'Não existem registos de sono.'], 404);
        }

        $valuesToFilter = [];

        foreach($sleeps as $sleep) {
            $parsedDate = explode('/', $sleep->date);
            $valuesToFilter[$parsedDate[2]][$parsedDate[1]] = [];
        }

        foreach($sleeps as $sleep) {
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $parsedDate = explode('/', $sleep->date);
            array_push($valuesToFilter[$parsedDate[2]][$parsedDate[1]], [
                'label' => $parsedDate[0],
                'value' => $diff,
            ]);
        }

        return Response::json(['data' => $valuesToFilter]);
    }

    /**
     * @OA\Get(
     *      path="/api/sleeps/{id}",
     *      operationId="Get sleep by id",
     *      tags={"Sleep"},
     *      summary="Get sleep by id",
     *      description="Get by id",
     *      @OA\Parameter(
     *         description="ID of sleep",
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
     *          description="return sleep"
     *       )
     *     )
     */
    public function show($id)
    {
        $sleeps = Sleep::where('userId', $id)->get();

        if (!$sleeps) {
            return Response::json(['error' => 'O registo nao existe para o utilizador.'], 404);
        }

        foreach($sleeps as $sleep) {
            $diff = SleepControllerAPI::computeTimeInHours($sleep->sleepTime, $sleep->wakeUpTime);
            $sleep->totalHours = round($diff,2);
        }

        return SleepResource::collection($sleeps);
    }

    /**
     * @OA\Get(
     *      path="/api/sleepsByDate",
     *      operationId="Get sleep dates",
     *      tags={"Sleep"},
     *      summary="Get sleep dates",
     *      description="Get sleep dates",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of sleep dates"
     *       )
     *     )
     */
    public function getSleepDates(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $dates = [];

        $sleeps = Sleep::where('userId', Auth::guard('api')->user()->id)->get(['date']);

        foreach($sleeps as $sleep) {
            array_push($dates, $sleep->date);
        }

        return Response::json(['data' => $dates]);
    }

    /**
     * @OA\Get(
     *      path="/api/sleeps/export",
     *      operationId="Export sleep data to xlsx",
     *      tags={"Sleep"},
     *      summary="Export sleep data to xlsx",
     *      description="Export sleep data to xlsx",
     *      @OA\Response(
     *          response=200,
     *          description="downlaod xlsx file"
     *       )
     *     )
     */
    public function export()
    {
        return Excel::download(new SleepsExport, 'sleeps.xlsx');
    }
}
