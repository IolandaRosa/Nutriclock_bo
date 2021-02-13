<?php

namespace App\Http\Controllers;

use App\ExerciseStatic;
use App\Http\Resources\ExerciseStatic as ExerciseStaticResource;
use Illuminate\Http\Request;

class ExerciseStaticControllerAPI extends Controller
{
    public function index()
    {
        return ExerciseStaticResource::collection(ExerciseStatic::all());
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
