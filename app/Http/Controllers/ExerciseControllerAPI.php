<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\ExerciseStatic;
use App\HouseholdStatic;
use App\Http\Resources\Exercise as ExerciseResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ExerciseControllerAPI extends Controller
{
    public function index()
    {
        //
    }

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
        $exercise->duration = $duration;
        $info = null;

        if ($request->type == 'H') {
            $info = HouseholdStatic::where('name', $request->name)->first();
        } else {
            $info = ExerciseStatic::where('name', $request->name)->first();
        }

        if ($info) {
            $exercise->exerciseId = $info->id;
            $exercise->met = $info->met;
            $exercise->burnedCalories = $user->weight * $info->met * ($duration / 60);
        }

        $exercise->save();
        return new ExerciseResource($exercise);
    }

    private function computeDuration($endTime, $startTime)
    {
        return ($endTime - $startTime) / 60000;
    }

    public function getExerciseDates() {
        $dates = Exercise::where('userId', Auth::guard('api')->id())->get('date');

        $parsedDates = [];
        $i = 0;

        foreach ($dates as $d) {
            $parsed = Carbon::parse($d->date)->format('d/m/Y');
            $parsedDates[$i] = $parsed;
            $i++;
        }

        $parsedDates = array_unique($parsedDates);
        return Response::json(['data' => $parsedDates]);
    }

    public function getExerciseByDate($date) {
        $exercises = Exercise::where('userId', Auth::guard('api')->id())->where('date', $date)->get();
        return ExerciseResource::collection($exercises);
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
