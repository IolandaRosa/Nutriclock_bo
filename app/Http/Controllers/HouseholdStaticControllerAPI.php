<?php

namespace App\Http\Controllers;

use App\HouseholdStatic;
use App\Http\Resources\HouseholdStatic as HouseholdStaticResource;
use Illuminate\Http\Request;

class HouseholdStaticControllerAPI extends Controller
{
    public function index()
    {
        return HouseholdStaticResource::collection(HouseholdStatic::all());
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
