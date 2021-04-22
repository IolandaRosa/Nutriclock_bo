<?php

namespace App\Http\Controllers;

use App\Ingredient;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class IngredientControllerAPI extends Controller
{
    /**
     * @OA\Delete(
     *      path="/api/ingredient/{id}",
     *      operationId="Delete ingredient",
     *      tags={"Ingredient"},
     *      summary="Delete ingredient",
     *      description="Delete ingredient",
     *      @OA\Parameter(
     *         description="ID of ingredient",
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
     *          description="return ingredient"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return Response::json(['error' => 'O ingrediente não existe!'], 400);
        }

        $ingredient->forceDelete();

        return Response::json(['data' => 'Ingrediente eliminado com sucesso!']);
    }

    /**
     * @OA\Post(
     *      path="/api/ingredient/{id}",
     *      operationId="Creates new ingredient in meal plan",
     *      tags={"Ingredient"},
     *      summary="Creates new ingredient in meal plan",
     *      description="Creates new ingredient in meal plan",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="ingredients",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return ingredient"
     *       )
     *     )
     */
    public function store(Request $request, $id)  {
        $list = [];
        foreach ($request->ingredients as $ingredient) {
            $ingredient = Ingredient::where('mealPlanTypeId', $id)->where('code', $ingredient['code'])->first();
            if($ingredient) return Response::json(['error' => 'O ingrediente ja esta registado nesta refeicao!'], 400);
        }

        foreach ($request->ingredients as $ingredient) {
            $i = new Ingredient();
            $i->code = $ingredient['code'];
            $i->name = $ingredient['name'];
            $i->quantity = $ingredient['quantity'];
            $i->unit = $ingredient['unit'];
            $i->mealPlanTypeId = $id;
            $i->description = $ingredient['description'];
            $i->grams = self::computeNumericUnit(
                $ingredient['unit'], $ingredient['quantity']
            );
            $i->save();

            array_push(
                $list, $i
            );
        }

        return Response::json(['data' => $list]);
    }

    public static function computeNumericUnit($unit, $quantity) {
        switch($unit) {
            case "Gramas":
            case "Mililitros": $value = $quantity; break;
            case "Colher de sopa": $value = 15 * $quantity; break;
            case "Colher de sobremesa": $value = 7.5 * $quantity; break;
            case "Colher de chá": $value = 5 * $quantity; break;
            case "Colher de café": $value = 2.5 * $quantity; break;
            case "Colher de servir": $value = 30 * $quantity; break;
            case "Pires":
            case "Copo": $value = 200 * $quantity; break;
            case "Chavena de chá": $value = 240 * $quantity; break;
            case "Prato": $value = 400 * $quantity; break;
            case "Caneca": $value = 300 * $quantity; break;
            case "Concha de sopa": $value = 160 * $quantity; break;
            case "Tigela média": $value = 350 * $quantity; break;
            case "Chavena de café": $value = 40 * $quantity; break;
            default: $value = 0;
        }

        return $value;
    }
}
