<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProfessionalCategory;
use App\Http\Resources\ProfessionalCategory as ProfessionalCategoryResource;
use Illuminate\Support\Facades\Auth;
use Response;

class ProfessionalCategoryControllerAPI extends Controller
{
    public function index()
    {
        return ProfessionalCategoryResource::collection(ProfessionalCategory::all());
    }

    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'name'=>'required|unique:professional_categories,name',
        ]);

        $professionalCategory = new ProfessionalCategory();

        $professionalCategory->name = $request->input('name');
        $professionalCategory->save();

        return new ProfessionalCategoryResource($professionalCategory);
    }

    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $validateData=$request->validate([
            'name'=>'required|unique:professional_categories,name',
        ]);

        $professionalCategory=ProfessionalCategory::findOrFail($id);

        $professionalCategory->fill($validateData)->save();

        return new ProfessionalCategoryResource($professionalCategory);
    }

    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $professionalCategory=ProfessionalCategory::findOrFail($id);

        $professionalCategory->delete();
        return new ProfessionalCategoryResource($professionalCategory);
    }
}
