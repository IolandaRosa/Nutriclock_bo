<?php

namespace App\Http\Controllers;

use App\HouseholdStatic;
use App\Http\Resources\HouseholdStatic as HouseholdStaticResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HouseholdStaticControllerAPI extends Controller
{
    public function index()
    {
        return HouseholdStaticResource::collection(HouseholdStatic::all());
    }

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

    public function destroy($id)
    {
        HouseholdStatic::where('id', $id)->delete();
        return Response::json(['data' => 'Elemento eliminado']);
    }
}
