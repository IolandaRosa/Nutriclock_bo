<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\Meal as MealResource;
use App\Meal;
use App\User;
use Response;

class MealControllerAPI extends Controller
{
    public function index()
    {
        return MealResource::collection(Meal::all());
    }

    public function userMealsCount(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $count = Meal::where('userId', $id)->count();

        if (!$count) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        return Response::json(['meals' => $count], 200);
     }

    public function getAuthUserMeals(Request $request) {
        $meals = Meal::where('userId', auth()->id())->get();

        if (!$meals) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        return MealResource::collection($meals);
    }

    public function getMealsByUser(Request $request, $id) {
        $meals = Meal::where('userId', $id)->get();

        if (!$meals) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        return MealResource::collection($meals);
    }

    public function show(Request $request, $id) {
        $meal = Meal::find($id);

        if (!$meal) {
            return Response::json(['error' => 'A refeição não existe.'], 404);
        }

        return new MealResource($meal);
    }

    public static function computeNumericUnit($request) {
        $value = 0;

        switch($request->relativeUnit) {
            case "Gramas":
            case "Mililitros": $value = $request->quantity; break;
            case "Colher de sopa": $value = 15; break;
            case "Colher de sobremesa": $value = 7.5; break;
            case "Colher de chá": $value = 5; break;
            case "Colher de café": $value = 2.5; break;
            case "Colher de servir": $value = 30; break;
            case "Copo": $value = 250; break;
            case "Chavena de chá": $value = 240; break;
            case "Pires": $value = 200; break;
            case "Prato": $value = 40; break;
            case "Caneca": $value = 300; break;
            case "Concha de sopa": $value = 160; break;
            case "Tigela média": $value = 350; break;
            case "Chavena de café": $value = 40; break;
            default: $value = 0;
        }

        return $value;
    }

    public function store(Request $request, $id) {
        $request->validate([
            'name' => 'required|min:3',
            'quantity' => 'required|numeric|between:0,9999.99',
            'foodPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'nutritionalInfoPhoto' => 'nullable|image|mimes:jpg,jpeg,png',
            'relativeUnit' => 'required',
            'type' => Rule::in(['P','A','J','S','O','L']),
            'date' => 'required',
            'time' => 'required'
        ]);

        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $meal = new Meal();

        if($request->foodPhoto != null) {
            $image = $request->file('foodPhoto');
            $path = basename($image->store('food', 'public'));
            $meal->foodPhotoUrl = basename($path);
        }

        if($request->nutritionalInfoPhoto != null) {
            $image = $request->file('nutritionalInfoPhoto');
            $path = basename($image->store('nutritionalInfo', 'public'));
            $meal->nutritionalInfoPhotoUrl = basename($path);
        }

        $meal->name = $request->name;
        $meal->quantity = $request->quantity;
        $meal->relativeUnit = $request->relativeUnit;
        $meal->type = $request->type;
        $meal->date = $request->date;
        $meal->time = $request->time;
        $meal->userId = $id;
        $meal->numericUnit = MealControllerAPI::computeNumericUnit($request);

        $meal->save();

        return new MealResource($meal);
    }
}
