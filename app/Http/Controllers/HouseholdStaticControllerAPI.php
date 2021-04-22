<?php

namespace App\Http\Controllers;

use App\HouseholdStatic;
use App\Http\Resources\HouseholdStatic as HouseholdStaticResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HouseholdStaticControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/households",
     *      operationId="households static list",
     *      tags={"Households Static"},
     *      summary="Return this list households static",
     *      description="Return this list households static",
     *      @OA\Response(
     *          response=200,
     *          description="return the list households static"
     *       )
     *     )
     */
    public function index()
    {
        return HouseholdStaticResource::collection(HouseholdStatic::all());
    }

    /**
     * @OA\Post(
     *      path="/api/households",
     *      operationId="Creates new household static",
     *      tags={"Households Static"},
     *      summary="Creates new household static",
     *      description="Creates new household static",
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
     *          description="return household static"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $nameExists = HouseholdStatic::where('name', $request->name)->first();
        if ($nameExists) {
            return Response::json(['error' => 'O nome já existe.'], 400);
        }

        $household = new HouseholdStatic();
        $household->fill($request->all());
        $household->save();

        return new HouseholdStaticResource($household);
    }

    /**
     * @OA\Put(
     *      path="/api/households/{id}",
     *      operationId="Update household static",
     *      tags={"Households Static"},
     *      summary="Update household static",
     *      description="Update household static",
     *      @OA\Parameter(
     *         description="ID of household static",
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
     *          description="return household static"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        $household = HouseholdStatic::find($id);
        if (!$household) {
            return Response::json(['error' => 'nao existe o alimento.'], 400);
        }

        $household->fill($request->all());
        $household->update();

        return new HouseholdStaticResource($household);
    }

    /**
     * @OA\Delete(
     *      path="/api/households/{id}",
     *      operationId="Delete household static",
     *      tags={"Households Static"},
     *      summary="Delete household static",
     *      description="Delete household static",
     *      @OA\Parameter(
     *         description="ID of household static",
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
     *          description="return household static"
     *       )
     *     )
     */
    public function destroy($id)
    {
        HouseholdStatic::where('id', $id)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }
}
