<?php

namespace App\Http\Controllers;

use App\Disease;
use App\Http\Resources\Disease as DiseaseResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Response;

class DiseaseControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/diseases",
     *      operationId="diseases",
     *      tags={"Disease"},
     *      summary="Return this list of diseases",
     *      description="Return this list of diseases",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of diseases"
     *       )
     *     )
     */
    public function index()
    {
        return DiseaseResource::collection(Disease::all()->sortBy('name'));
    }

    /**
     * @OA\Put(
     *      path="/api/diseases/{id}",
     *      operationId="Update disease",
     *      tags={"Disease"},
     *      summary="Update disease",
     *      description="Update disease",
     *      @OA\Parameter(
     *         description="ID of disease",
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
     *                     property="type",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return disease"
     *       )
     *     )
     */
    public function update(Request $request, $id) {
        $disease = Disease::find($id);

        if (!$disease) {
            return Response::json(['error' => 'A doença não existe.'], 404);
        }

        $disease->update($request->all());

        return new DiseaseResource($disease);
    }

    /**
     * @OA\Post(
     *      path="/api/diseases",
     *      operationId="Creates new disease",
     *      tags={"Disease"},
     *      summary="Creates new disease",
     *      description="Creates new disease",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return disease"
     *       )
     *     )
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|min:3|unique:diseases',
            'type' => Rule::in(['A', 'D']),
        ]);

        $disease = new Disease();

        $disease->name = $request->name;
        $disease->type = $request->type;

        $disease->save();

        return new DiseaseResource($disease);
    }

    /**
     * @OA\Delete(
     *      path="/api/diseases/{id}",
     *      operationId="Delete disease",
     *      tags={"Disease"},
     *      summary="Delete disease",
     *      description="Delete disease",
     *      @OA\Parameter(
     *         description="ID of disease",
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
     *          description="return disease"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return Response::json(['error' => 'A doença não existe.'], 404);
        }

        $disease->forceDelete();
        return new DiseaseResource($disease);
    }
}
