<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\MealPlan;
use App\MealPlanType;
use App\NutritionalInfoStatic;
use App\Plan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;

class MealPlanTypeControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/meal-type-stats/{id}",
     *      operationId="meal type stats by plan id",
     *      tags={"Meal Plan"},
     *      summary="Return stats by plan id",
     *      description="Return stats by plan id",
     *      @OA\Parameter(
     *         description="ID of plan",
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
     *          description="return stats by plan id"
     *       )
     *     )
     */
    public function statsByPlanDay($id)
    {
        $totalProtein = 0;
        $totalFat = 0;
        $totalCarbs = 0;
        $totalFiber = 0;
        $totalEnergyKcal = 0;
        $totalWater = 0;
        $mealStats = [];

        $mealTypes = MealPlanType::where('planMealId', $id)->orderBy('hour')->get(['id', 'type', 'hour']);
        if (!$mealTypes) {
            return Response::json(['error' => 'Não existem refeições associadas aos plano'], 400);
        }

        foreach ($mealTypes as $mealType) {
            $ingredients = Ingredient::where('mealPlanTypeId', $mealType['id'])->get(['code', 'grams']);
            $mealTotalProtein = 0;
            $mealTotalFat = 0;
            $mealTotalCarbs = 0;
            $mealTotalFiber = 0;
            $mealTotalEnergyKcal = 0;
            $mealTotalWater = 0;

            if ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $nutriInfo = NutritionalInfoStatic::where('code', $ingredient['code'])->first(['protein_g', 'fats_g', 'carbo_hidrats_g', 'fiber_g', 'energy_kcal', 'water_g']);

                    if ($nutriInfo) {
                        $totalProtein += (($nutriInfo->protein_g * $ingredient['grams']) / 100);
                        $totalFat += (($nutriInfo->fats_g * $ingredient['grams']) / 100);
                        $totalCarbs += (($nutriInfo->carbo_hidrats_g * $ingredient['grams']) / 100);
                        $totalFiber += (($nutriInfo->fiber_g * $ingredient['grams']) / 100);
                        $totalEnergyKcal += (($nutriInfo->energy_kcal * $ingredient['grams']) / 100);
                        $totalWater += (($nutriInfo->water_g * $ingredient['grams']) / 100);

                        $mealTotalProtein += (($nutriInfo->protein_g * $ingredient['grams']) / 100);
                        $mealTotalFat += (($nutriInfo->fats_g * $ingredient['grams']) / 100);
                        $mealTotalCarbs += (($nutriInfo->carbo_hidrats_g * $ingredient['grams']) / 100);
                        $mealTotalFiber += (($nutriInfo->fiber_g * $ingredient['grams']) / 100);
                        $mealTotalEnergyKcal += (($nutriInfo->energy_kcal * $ingredient['grams']) / 100);
                        $mealTotalWater += (($nutriInfo->water_g * $ingredient['grams']) / 100);
                    }
                }

                $totalMeal = $mealTotalProtein + $mealTotalFat + $mealTotalCarbs + $mealTotalFiber;

                if ($mealTotalProtein > 0) $mealTotalProtein = ($mealTotalProtein * 100) / $totalMeal;
                if ($mealTotalFat > 0) $mealTotalFat = ($mealTotalFat * 100) / $totalMeal;
                if ($mealTotalCarbs > 0) $mealTotalCarbs = ($mealTotalCarbs * 100) / $totalMeal;
                if ($mealTotalFiber > 0) $mealTotalFiber = ($mealTotalFiber * 100) / $totalMeal;

                array_push($mealStats, [
                    'name' => $mealType['type'],
                    'hour' => $mealType['hour'],
                    'stats' => [
                        round($mealTotalProtein, 2),
                        round($mealTotalFat, 2),
                        round($mealTotalCarbs, 2),
                        round($mealTotalFiber, 2),
                    ],
                    'energy' => round($mealTotalEnergyKcal, 2),
                    'water' => round($mealTotalWater, 2),
                ]);
            }
        }

        $total = $totalProtein + $totalFat + $totalCarbs + $totalFiber;

        if ($totalProtein > 0) $totalProtein = ($totalProtein * 100) / $total;
        if ($totalFat > 0) $totalFat = ($totalFat * 100) / $total;
        if ($totalCarbs > 0) $totalCarbs = ($totalCarbs * 100) / $total;
        if ($totalFiber > 0) $totalFiber = ($totalFiber * 100) / $total;

        return Response::json([
            'data' => ['stats' => [round($totalProtein, 2), round($totalFat, 2), round($totalCarbs, 2), round($totalFiber, 2)],
                'water' => round(($totalWater / 1000), 2),
                'kcal' => round($totalEnergyKcal, 2),
                'days' => $mealStats]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/meal-type-stats",
     *      operationId="Update plan stat when add ingredients",
     *      tags={"Meal Plan"},
     *      summary="Update plan stat when add ingredients",
     *      description="Update plan stat when add ingredients",
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
     *          description="return meal type stats"
     *       )
     *     )
     */
    public function statsMealType(Request $request)
    {
        $totalProtein = 0;
        $totalFat = 0;
        $totalCarbo = 0;
        $totalFiber = 0;
        $totalEnergyKcal = 0;
        $totalWater = 0;

        if (!$request->ingredients) {
            return Response::json(['error' => 'A refeição não tem ingredientes associados'], 400);
        }

        foreach ($request->ingredients as $ingredient) {
            $nutriInfo = NutritionalInfoStatic::where('code', $ingredient['code'])->first(['protein_g', 'fats_g', 'carbo_hidrats_g', 'fiber_g', 'energy_kcal', 'water_g']);

            $quantity = self::computeNumericUnit($ingredient);
            if ($nutriInfo) {
                $totalProtein += (($nutriInfo->protein_g * $quantity) / 100);
                $totalFat += (($nutriInfo->fats_g * $quantity) / 100);
                $totalCarbo += (($nutriInfo->carbo_hidrats_g * $quantity) / 100);
                $totalFiber += (($nutriInfo->fiber_g * $quantity) / 100);
                $totalEnergyKcal += (($nutriInfo->energy_kcal * $quantity) / 100);
                $totalWater += (($nutriInfo->water_g * $quantity) / 100);
            }
        }

        $total = $totalProtein + $totalFat + $totalCarbo + $totalFiber;

        if ($totalProtein > 0) $totalProtein = ($totalProtein * 100) / $total;
        if ($totalFat > 0) $totalFat = ($totalFat * 100) / $total;
        if ($totalCarbo > 0) $totalCarbo = ($totalCarbo * 100) / $total;
        if ($totalFiber > 0) $totalFiber = ($totalFiber * 100) / $total;

        return Response::json([
            'data' => ['stats' => [round($totalProtein, 2), round($totalFat, 2), round($totalCarbo, 2), round($totalFiber, 2)],
                'water' => round(($totalWater / 1000), 2),
                'kcal' => round($totalEnergyKcal, 2)]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/meal-plans",
     *      operationId="Creates new meal plan",
     *      tags={"Meal Plan"},
     *      summary="Creates new meal plan",
     *      description="Creates new meal plan",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="userId",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="dayOfWeek",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="meals",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return meal plan"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'date' => 'required',
            'dayOfWeek' => Rule::in(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN']),
            'meals' => 'required|array'
        ]);

        $plan = Plan::where('userId', $request->userId)->first();

        if (!$plan) {
            $plan = new Plan();
            $plan->userId = $request->userId;
            $plan->save();
        }

        $mealPlan = MealPlan::where('planId', $plan->id)->where('date', $request->date)->first();

        if (!$mealPlan) {
            $mealPlan = new MealPlan();
            $mealPlan->planId = $plan->id;
            $mealPlan->date = $request->date;
            $mealPlan->dayOfWeek = $request->dayOfWeek;
            $mealPlan->save();
        }

        foreach ($request->meals as $meal) {
            $mealPlanType = MealPlanType::where('planMealId', $mealPlan->id)->where('type', $meal['name'])->first();
            if ($mealPlanType) {
                return Response::json(['error' => 'Já existe uma refeição com o mesmo nome registada na mesma data'], 400);
            }

            $mealPlanType = MealPlanType::where('planMealId', $mealPlan->id)->where('hour', $meal['time'])->first();
            if ($mealPlanType) {
                return Response::json(['error' => 'Já existe uma refeição com o mesmo horário registada na mesma data'], 400);
            }

            $mealPlanType = new MealPlanType();
            $mealPlanType->planMealId = $mealPlan->id;
            $mealPlanType->type = $meal['name'];
            $mealPlanType->portion = $meal['portion'];
            $mealPlanType->hour = $meal['time'];
            $mealPlanType->save();

            foreach ($meal['ingredients'] as $i) {
                $ingredient = new Ingredient();
                $ingredient->code = $i['code'];
                $ingredient->name = $i['name'];
                $ingredient->quantity = $i['quantity'];
                $ingredient->unit = $i['unit'];
                $ingredient->description = $i['description'];
                $ingredient->grams = self::computeNumericUnit($ingredient);
                $ingredient->mealPlanTypeId = $mealPlanType->id;
                $ingredient->save();
            }
        }

        return Response::json(['data' => 'A refeição foi criada com sucesso']);
    }

    /**
     * @OA\Post(
     *      path="/api/meal-type/{id}",
     *      operationId="Add meal type to plan",
     *      tags={"Meal Plan"},
     *      summary="Add meal type to plan",
     *      description="Add meal type to plan",
     *     @OA\Parameter(
     *         description="ID of plan",
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
     *                     property="portion",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="time",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return meal plan"
     *       )
     *     )
     */
    public function storeMealType(Request $request, $id)
    {
        $mealPlanType = MealPlanType::where('planMealId', $id)->where('type', $request->name)->first();
        if ($mealPlanType) {
            return Response::json(['error' => 'Já existe uma refeição com o mesmo nome registada'], 400);
        }

        $mealPlanType = MealPlanType::where('planMealId', $id)->where('hour', $request->time)->first();
        if ($mealPlanType) {
            return Response::json(['error' => 'Já existe uma refeição com o mesmo horário registada'], 400);
        }

        $mealPlanType = new MealPlanType();
        $mealPlanType->planMealId = $id;
        $mealPlanType->type = $request->name;
        $mealPlanType->portion = $request->portion;
        $mealPlanType->hour = $request->time;
        $mealPlanType->save();

        return new \App\Http\Resources\MealPlanType($mealPlanType);
    }

    public static function computeNumericUnit($request)
    {
        switch ($request['unit']) {
            case "Gramas":
            case "Mililitros":
                $value = $request['quantity'];
                break;
            case "Colher de sopa":
                $value = 15 * $request['quantity'];
                break;
            case "Colher de sobremesa":
                $value = 7.5 * $request['quantity'];
                break;
            case "Colher de chá":
                $value = 5 * $request['quantity'];
                break;
            case "Colher de café":
                $value = 2.5 * $request['quantity'];
                break;
            case "Colher de servir":
                $value = 30 * $request['quantity'];
                break;
            case "Pires":
            case "Copo":
                $value = 200 * $request['quantity'];
                break;
            case "Chavena de chá":
                $value = 240 * $request['quantity'];
                break;
            case "Prato":
                $value = 400 * $request['quantity'];
                break;
            case "Caneca":
                $value = 300 * $request['quantity'];
                break;
            case "Concha de sopa":
                $value = 160 * $request['quantity'];
                break;
            case "Tigela média":
                $value = 350 * $request['quantity'];
                break;
            case "Chavena de café":
                $value = 40 * $request['quantity'];
                break;
            default:
                $value = 0;
        }

        return $value;
    }

    /**
     * @OA\Get(
     *      path="/api/meal-plans-dates/{id}",
     *      operationId="get meal plan dates list",
     *      tags={"Meal Plan"},
     *      summary="Return meal plan dates list",
     *      description="Return meal plan dates list",
     *     @OA\Parameter(
     *         description="ID of plan",
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
     *          description="return meal plan dates list"
     *       )
     *     )
     */
    public function getMealPlanDates($id)
    {
        $plan = Plan::where('userId', $id)->first();
        if (!$plan) {
            return Response::json(['data' => []]);
        }
        $dates = MealPlan::where('planId', $plan->id)->get('date');

        return Response::json(['data' => $dates]);
    }

    public function formatDate($dateStr)
    {
        $d = DateTime::createFromFormat('d-m-Y', $dateStr);
        if ($d) {
            return $d->format('Y-m-d');
        }

        return null;
    }

    /**
     * @OA\Get(
     *      path="/api/meal-plans/{id}/{date}",
     *      operationId="get meal plan by id by date",
     *      tags={"Meal Plan"},
     *      summary="Return meal plan by id by date",
     *      description="Return meal plan by id by date",
     *      @OA\Parameter(
     *         description="ID of plan",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Parameter(
     *         description="date",
     *         in="path",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return meal plan"
     *       )
     *     )
     */
    public function show($id, $date)
    {
        $plan = Plan::where('userId', $id)->first();

        if (!$plan) {
            return Response::json(['data' => []]);
        }

        $meal_plans = MealPlan::where('planId', $plan->id)->where('date', '=', $date)->get();

        if ($meal_plans) {
            foreach ($meal_plans as $mealPlan) {
                $mealPlanTypes = MealPlanType::where('planMealId', $mealPlan->id)->orderBy('hour')->get();
                if ($mealPlanTypes) {
                    foreach ($mealPlanTypes as $mealType) {
                        $ingredients = Ingredient::where('mealPlanTypeId', $mealType->id)->get();

                        if ($ingredients) {
                            $mealType['ingredients'] = $ingredients;
                        }
                    }

                    $mealPlan['mealTypes'] = $mealPlanTypes;
                }
            }
        }

        return Response::json(['data' => $meal_plans]);
    }

    function rangeWeek($datestr)
    {
        $myDate = strtotime($datestr);

        return array(
            'start' => date('N', $myDate) == 1 ? date('d-m-Y', $myDate) : date('d-m-Y', strtotime('last monday', $myDate)),
            'end' => date('d-m-Y', strtotime('next sunday', $myDate))
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/meal-type/{id}",
     *      operationId="Delete meal plan type",
     *      tags={"Meal Plan"},
     *      summary="Delete meal plan type",
     *      description="Delete meal plan type",
     *      @OA\Parameter(
     *         description="ID of meal plan type",
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
     *          description="return usf"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $mealPlanType = MealPlanType::find($id);

        if (!$mealPlanType) {
            return Response::json(['error' => 'A refeição não existe!'], 400);
        }

        $mealPlanType->forceDelete();

        return Response::json(['data' => 'Refeição eliminada com sucesso!']);
    }

    /**
     * @OA\Get(
     *      path="/api/meal-types-patient/{date}",
     *      operationId="get patient plan by date",
     *      tags={"Meal Plan"},
     *      summary="Return patient plan by date",
     *      description="Return patient plan by date",
     *      @OA\Parameter(
     *         description="date",
     *         in="path",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return meal plan from date"
     *       )
     *     )
     */
    public function getPatientDailyPlan($date)
    {
        $plan = Plan::where('userId', auth()->id())->first('id');
        if (!$plan) {
            return Response::json(['error' => 'Plano alimentar vazio.'], 400);
        }

        $mealPlan = MealPlan::where('planId', $plan->id)->where('date', $date)->first('id');
        if (!$mealPlan) {
            return Response::json(['error' => 'Não existe um plano nesta data'], 400);
        }

        $mealTypes = MealPlanType::where('planMealId', $mealPlan->id)->orderBy('hour')->get();
        if (!$mealTypes) {
            return Response::json(['error' => 'O plano não tem refeições'], 400);
        }

        foreach ($mealTypes as $m) {
            $ingredients = Ingredient::where('mealPlanTypeId', $m->id)->get();
            $m->ingredients = $ingredients;
        }

        return Response::json(['data' => $mealTypes]);
    }

    /**
     * @OA\Get(
     *      path="/api/meal-history-patient/{date}",
     *      operationId="get patient history time",
     *      tags={"Meal Plan"},
     *      summary="Return patient history time",
     *      description="Return patient history time",
     *      @OA\Parameter(
     *         description="date",
     *         in="path",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return patient history time"
     *       )
     *     )
     */
    public function getPatientHistory($date)
    {
        $plan = Plan::where('userId', auth()->id())->first('id');
        if (!$plan) {
            return Response::json(['error' => 'Plano alimentar vazio.'], 400);
        }

        $mealPlans = MealPlan::where('planId', $plan->id)->where('date', '<', $date)->orderBy('date')->get();
        if (!$mealPlans) {
            return Response::json(['error' => 'Não existe um plano nesta data'], 400);
        }

        foreach ($mealPlans as $mealPlan) {
            $mealTypes = MealPlanType::where('planMealId', $mealPlan->id)->orderBy('hour')->get();
            if ($mealTypes) {
                foreach ($mealTypes as $m) {
                    $ingredients = Ingredient::where('mealPlanTypeId', $m->id)->get();
                    $m->ingredients = $ingredients;
                }

                $mealPlan->mealTypes = $mealTypes;
            }
        }

        return Response::json(['data' => $mealPlans]);
    }

    /**
     * @OA\Post(
     *      path="/api/meal-plan-type-confirm/{id}",
     *      operationId="Auth user confirm meal type",
     *      tags={"Meal Plan"},
     *      summary="Auth user confirm meal type",
     *      description="Auth user confirm meal type",
     *     @OA\Parameter(
     *         description="ID of meal type plan",
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
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="confirmedHours",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     description="meal photo to upload",
     *                     property="photo",
     *                     type="string",
     *                     format="file",
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return meal plan"
     *       )
     *     )
     */
    public function confirmAuthMealPlanType(Request $request, $id)
    {
        $mealPlanType = MealPlanType::find($id);
        if (!$mealPlanType) {
            return Response::json(['error' => 'A refeição não existe'], 400);
        }

        $request->validate([
            'confirmedHours' => 'required|string|size:5',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($mealPlanType->photoUrl) {
            Storage::disk('s3')->delete('mealPlans/' . $mealPlanType->photoUrl);
            Storage::disk('s3')->delete('mealPlans/thumb_' . $mealPlanType->photoUrl);
        }

        $mealPlanType->confirmed = true;
        $mealPlanType->confirmedHours = $request->confirmedHours;

        if ($request->file('photo') != null) {
            $image = $request->file('photo');
            $thumbnail = Image::make($image);
            $thumbnail->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path = basename($image->store('mealPlans/', 's3'));
            Storage::disk('s3')->put('mealPlans/thumb_' . $path, $thumbnail->stream());
            $mealPlanType->photoUrl = basename($path);
        }

        $mealPlanType->save();

        return new \App\Http\Resources\MealPlanType($mealPlanType);
    }
}
