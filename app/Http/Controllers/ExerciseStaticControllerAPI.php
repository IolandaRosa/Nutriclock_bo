<?php

namespace App\Http\Controllers;

use App\ExerciseStatic;
use App\Http\Resources\ExerciseStatic as ExerciseStaticResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExerciseStaticControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exercises/list",
     *      operationId="exercises static list",
     *      tags={"Exercises Static"},
     *      summary="Return this list exercises static",
     *      description="Return this list exercises static",
     *      @OA\Response(
     *          response=200,
     *          description="return the list exercises static"
     *       )
     *     )
     */
    public function index()
    {
        return ExerciseStaticResource::collection(ExerciseStatic::all());
    }

    /**
     * @OA\Post(
     *      path="/api/exercises-static",
     *      operationId="Creates new exercise static",
     *      tags={"Exercises Static"},
     *      summary="Creates new exercise static",
     *      description="Creates new exercise static",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="met",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return exercise static"
     *       )
     *     )
     */
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

    /**
     * @OA\Put(
     *      path="/api/exercises-static/{id}",
     *      operationId="Update exercise static",
     *      tags={"Exercises Static"},
     *      summary="Update exercise static",
     *      description="Update exercise static",
     *      @OA\Parameter(
     *         description="ID of exercise static",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="met",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return exercise static"
     *       )
     *     )
     */
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

    /**
     * @OA\Delete(
     *      path="/api/exercises-static/{id}",
     *      operationId="Delete exercise static",
     *      tags={"Exercises Static"},
     *      summary="Delete exercise static",
     *      description="Delete exercise static",
     *      @OA\Parameter(
     *         description="ID of exercise static",
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
     *          description="return exercise static"
     *       )
     *     )
     */
    public function destroy($id)
    {
        ExerciseStatic::where('id', $id)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }
}
