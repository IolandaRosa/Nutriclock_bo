<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\ExerciseStatic;
use App\HouseholdStatic;
use App\Http\Resources\Exercise as ExerciseResource;
use App\Sleep;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ExerciseControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/exercises",
     *      operationId="Creates new exercise for auth user",
     *      tags={"Exercise"},
     *      summary="Creates new exercise",
     *      description="Creates new exercise",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="endTime",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="startTime",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return exercise"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'endTime' => 'required|numeric',
            'startTime' => 'required|numeric',
            'name' => 'required',
        ]);

        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'Utilizador nao encontrado!'], 404);
        }

        $duration = $this->computeDuration($request->endTime, $request->startTime);

        $exercise = new Exercise();
        $exercise->userId = $user->id;
        $exercise->name = $request->name;
        $exercise->startTime = $request->startTime;
        $exercise->endTime = $request->endTime;
        $exercise->date = $request->date;
        $exercise->type = $request->type;
        $exercise->duration = intval($duration);
        $info = null;

        if ($request->type == 'H') {
            $info = HouseholdStatic::where('name', $request->name)->first();
        } else {
            $info = ExerciseStatic::where('name', $request->name)->first();
        }

        if ($info) {
            $exercise->exerciseId = $info->id;
            $exercise->met = $info->met;
            $exercise->burnedCalories = intval($user->weight * $info->met * ($duration / 60));
        }

        $exercise->save();
        return new ExerciseResource($exercise);
    }

    private function computeDuration($endTime, $startTime)
    {
        return ($endTime - $startTime) / 60000;
    }

    /**
     * @OA\Get(
     *      path="/api/exercises/dates",
     *      operationId="exercises dates",
     *      tags={"Exercise"},
     *      summary="Return this list of exercises dates for auth user",
     *      description="Return this list of exercises dates for auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of exercises dates for auth user"
     *       )
     *     )
     */
    public function getExerciseDates() {
        $dates = Exercise::where('userId', Auth::guard('api')->id())->get('date');

        $parsedDates = [];
        $i = 0;

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $parsedDates = array_unique($parsedDates);
            $i = count($parsedDates);
        }

        return Response::json(['data' => $parsedDates]);
    }

    /**
     * @OA\Get(
     *      path="/api/exercises/detail/{date}",
     *      operationId="get exercises by date for auth user",
     *      tags={"Exercise"},
     *      summary="Return this list of exercises by date for auth user",
     *      description="Return this list of exercises by date for auth user",
     *     @OA\Parameter(
     *         description="date of exercise",
     *         in="path",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return the list of exercises by date for auth user"
     *       )
     *     )
     */
    public function getExerciseByDate($date) {
        $exercises = Exercise::where('userId', Auth::guard('api')->id())->where('date', $date)->get();
        return ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *      path="/api/exercises/stats",
     *      operationId="exercises stats for auth user",
     *      tags={"Exercise"},
     *      summary="Return this list of exercises stats for auth user",
     *      description="Return this list of exercises stats for auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of exercises stats for auth user"
     *       )
     *     )
     */
    public static function getExerciseStats() {
        $exercises = Exercise::where('userId', Auth::guard('api')->id())->get();

        if (!$exercises || count($exercises) == 0) {
            return Response::json(['error' => 'Não existem registos de sono.'], 404);
        }

        $valuesToFilter = [];

        foreach($exercises as $e) {
            $parsedDate = explode('-', $e->date);
            $day = intval(explode('T', $parsedDate[2])[0]);
            $valuesToFilter[$parsedDate[0]][intval($parsedDate[1])][$day] = [
                'calories' => 0,
                'duration' => 0,
            ];
        }

        foreach($exercises as $e) {
            $parsedDate = explode('-', $e->date);
            $day = intval(explode('T', $parsedDate[2])[0]);
            $valuesToFilter[$parsedDate[0]][intval($parsedDate[1])][$day]['calories'] += $e->burnedCalories;
            $valuesToFilter[$parsedDate[0]][intval($parsedDate[1])][$day]['duration'] += $e->duration;
        }

        return Response::json(['data' => $valuesToFilter]);
    }

    /**
     * @OA\Get(
     *      path="/api/exercises/admin/{id}",
     *      operationId="get exercises by user",
     *      tags={"Exercise"},
     *      summary="Return exercises by user",
     *      description="Return exercises by user",
     *     @OA\Parameter(
     *         description="id of user",
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
     *          description="returnexercises by user"
     *       )
     *     )
     */
    public function getExerciseByUser($id) {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'Utilizador nao encontrado!'], 404);
        }

        $exercises = Exercise::where('userId', $id)->get();

        return ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *      path="/api/exercises/admin/stats/{id}",
     *      operationId="get exercises stats by user",
     *      tags={"Exercise"},
     *      summary="Return exercises stats by user",
     *      description="Return exercises stats by user",
     *     @OA\Parameter(
     *         description="id of user",
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
     *          description="return exercise stats by user"
     *       )
     *     )
     */
    public function getStatsByUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'Utilizador nao encontrado!'], 404);
        }

        $exercises = Exercise::where('userId', $id)->get();

        if (!$exercises || count($exercises) == 0) {
            return Response::json(['error' => 'Não existem registos de exercicio.'], 404);
        }

        $durationSum = 0;
        $caloriesSum = 0;
        $exercisesCount = count($exercises);
        $valuesToFilter = [];

        foreach($exercises as $e) {
            $parsedDate = explode('-', $e->date);
            $valuesToFilter[$parsedDate[0]][$parsedDate[1]] = [];
        }

        foreach($exercises as $e) {
            $durationSum += $e->duration;
            $caloriesSum += $e->burnedCalories;
            $parsedDate = explode('-', $e->date);
            $hasLabel = false;
            $day = intval(explode('T', $parsedDate[2])[0]);
            $index = 0;

            foreach ($valuesToFilter[$parsedDate[0]][$parsedDate[1]] as $item){
                if ($item['label'] == $day) {
                    $item['duration'] += $e->duration;
                    $item['calories'] += $e->burnedCalories;
                    $valuesToFilter[$parsedDate[0]][$parsedDate[1]][$index] = $item;
                    $hasLabel = true;
                }
                $index++;
            }

            if(!$hasLabel) {
                array_push($valuesToFilter[$parsedDate[0]][$parsedDate[1]], [
                    'label' => $day,
                    'duration' => $e->duration,
                    'calories' => $e->burnedCalories,
                ]);
            }
        }

        $averageDuration = $durationSum/$exercisesCount;
        $averageCalories = $caloriesSum/$exercisesCount;

        return Response::json(['stats' => [
            'totalRegisters'=>$exercisesCount,
            'averageDuration'=>round($averageDuration, 2),
            'averageCalories'=>round($averageCalories, 2),
            'chartStats' => $valuesToFilter
        ]]);
    }
}
