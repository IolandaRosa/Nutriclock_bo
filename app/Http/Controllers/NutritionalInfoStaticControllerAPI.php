<?php

namespace App\Http\Controllers;
use App\Http\Resources\NutritionalInfoStatic as NutritionalInfoStaticResource;
use App\NutritionalInfoStatic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NutritionalInfoStaticControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/meal-names",
     *      operationId="Nutritional Info Static",
     *      tags={"Nutritional Info Static"},
     *      summary="Return this list of nutritional info static names",
     *      description="Return this list of nutritional info static names",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of nutritional info static names"
     *       )
     *     )
     */
    public function getNames() {
        return  NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all('name'));
    }

    /**
     * @OA\Get(
     *      path="/api/meals-query/{query}",
     *      operationId="Query nutritional info static",
     *      tags={"Nutritional Info Static"},
     *      summary="Query nutritional info static",
     *      description="Query nutritional info static",
     *     @OA\Parameter(
     *         description="Query the meal by name",
     *         in="path",
     *         name="query",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="query nutritional info static results"
     *       )
     *     )
     */
    public function getByQuery($query) {
        $info = NutritionalInfoStatic::whereRaw('LOWER(name) LIKE ? ',[trim(strtolower($query)).'%'])->get();

        return  NutritionalInfoStaticResource::collection($info);
    }

    /**
     * @OA\Get(
     *      path="/api/nutritional-info-static",
     *      operationId="Nutritional Info Static",
     *      tags={"Nutritional Info Static"},
     *      summary="Return this list of nutritional info static",
     *      description="Return this list of nutritional info static",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of nutritional info static"
     *       )
     *     )
     */
    public function index()
    {
        return NutritionalInfoStaticResource::collection(NutritionalInfoStatic::all());
    }

    /**
     * @OA\Post(
     *      path="/api/nutritional-info-static",
     *      operationId="Creates new nutritional info static",
     *      tags={"Nutritional Info Static"},
     *      summary="Creates new nutritional info static",
     *      description="Creates new nutritional info static",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="energy_kcal",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="energy_kJ",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="water_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="protein_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fats_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="carbo_hidrats_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fiber_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="colesterol_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitA_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitD_pg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="tiamina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="riboflavina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="niacina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitB6_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vit_B12_pg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitC_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="na_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="k_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="ca_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="p_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="mg_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fe_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="zn_mg",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return nutritional info static"
     *       )
     *     )
     */
    public function store (Request $request) {
        $codeExists = NutritionalInfoStatic::where('code', $request->code)->first();
        if ($codeExists) {
            return Response::json(['error' => 'O código já existe.'], 400);
        }

        $name = NutritionalInfoStatic::where('name', $request->name)->first();
        if ($name) {
            return Response::json(['error' => 'O nome já existe.'], 400);
        }

        $info = new NutritionalInfoStatic();
        $info->fill($request->all());
        $info->save();

        return new NutritionalInfoStaticResource($info);
    }

    /**
     * @OA\Delete(
     *      path="/api/nutritional-info-static/{code}",
     *      operationId="Delete nutritional info",
     *      tags={"Nutritional Info Static"},
     *      summary="Delete nutritional info",
     *      description="Delete nutritional info",
     *      @OA\Parameter(
     *         description="ID of nutritional info",
     *         in="path",
     *         name="code",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return nutritional info"
     *       )
     *     )
     */
    public function destroy($code) {
        NutritionalInfoStatic::where('code', $code)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }

    /**
     * @OA\Put(
     *      path="/api/nutritional-info-static/{code}",
     *      operationId="Update nutritional info static",
     *      tags={"Nutritional Info Static"},
     *      summary="Update nutritional info static",
     *      description="Update nutritional info static",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="energy_kcal",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="energy_kJ",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="water_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="protein_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fats_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="carbo_hidrats_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fiber_g",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="colesterol_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitA_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitD_pg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="tiamina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="riboflavina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="niacina_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitB6_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vit_B12_pg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="vitC_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="na_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="k_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="ca_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="p_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="mg_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fe_mg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="zn_mg",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return nutritional info static"
     *       )
     *     )
     */
    public function update (Request $request, $code) {
        $info = NutritionalInfoStatic::where('code', $code)->first();
        if (!$info) {
            return Response::json(['error' => 'nao existe o alimento.'], 400);
        }

        $info->fill($request->all());
        $info->update();

        return new NutritionalInfoStaticResource($info);
    }
}
