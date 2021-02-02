<?php

namespace App\Http\Controllers;

use App\Disease;
use App\Http\Resources\Disease as DiseaseResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Response;

class DiseaseControllerAPI extends Controller
{
    public function index()
    {
        return DiseaseResource::collection(Disease::all());
    }

    public function update(Request $request, $id) {
        $disease = Disease::find($id);

        if (!$disease) {
            return Response::json(['error' => 'A doença não existe.'], 404);
        }

        $disease->update($request->all());

        return new DiseaseResource($disease);
    }

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
