<?php

namespace App\Http\Controllers;

use App\ExerciseStatic;
use App\Http\Resources\ExerciseStatic as ExerciseStaticResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExerciseStaticControllerAPI extends Controller
{
    public function index()
    {
        return ExerciseStaticResource::collection(ExerciseStatic::all());
    }

    public function store(Request $request)
    {
        $nameExists = ExerciseStatic::where('name', $request->name)->first();
        if ($nameExists) {
            return Response::json(['error' => 'O nome já existe.'], 400);
        }

        $exercise = new ExerciseStatic();
        $exercise->fill($request->all());
        $exercise->save();

        return new ExerciseStaticResource($exercise);
    }

    public function update(Request $request, $id)
    {
        $exercise = ExerciseStatic::find($id);
        if (!$exercise) {
            return Response::json(['error' => 'nao existe o alimento.'], 400);
        }

        $exercise->fill($request->all());
        $exercise->update();

        return new ExerciseStaticResource($exercise);
    }

    public function destroy($id)
    {
        ExerciseStatic::where('id', $id)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }
}
