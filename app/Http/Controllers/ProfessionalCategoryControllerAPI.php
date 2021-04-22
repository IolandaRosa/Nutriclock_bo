<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfessionalCategory as ProfessionalCategoryResource;
use App\ProfessionalCategory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class ProfessionalCategoryControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/professionalCategories",
     *      operationId="professionalCategories",
     *      tags={"Professional Category"},
     *      summary="Return this list of professional categories",
     *      description="Return this list of professional categories",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of professional categories"
     *       )
     *     )
     */
    public function index()
    {
        return ProfessionalCategoryResource::collection(ProfessionalCategory::all());
    }

    /**
     * @OA\Post(
     *      path="/api/professionalCategories",
     *      operationId="Creates new professional category",
     *      tags={"Professional Category"},
     *      summary="Creates new professional category",
     *      description="Creates new professional category",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return professional category"
     *       )
     *     )
     */
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

    /**
     * @OA\Put(
     *      path="/api/professionalCategories/{id}",
     *      operationId="Update professional category",
     *      tags={"Professional Category"},
     *      summary="Update professional category",
     *      description="Update professional category",
     *      @OA\Parameter(
     *         description="ID of professional category",
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
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return professional category"
     *       )
     *     )
     */
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

    /**
     * @OA\Delete(
     *      path="/api/professionalCategories/{id}",
     *      operationId="Delete professional category",
     *      tags={"Professional Category"},
     *      summary="Delete professional category",
     *      description="Delete professional category",
     *      @OA\Parameter(
     *         description="ID of professional category",
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
     *          description="return professional category deleted"
     *       )
     *     )
     */
    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $total = User::where('professionalCategoryId', $id)->count();

        if ($total > 0) {
            return Response::json(['error' => 'Não é possível eliminar pois existem utilizadores associados!'], 400);
        }

        $professionalCategory=ProfessionalCategory::findOrFail($id);

        $professionalCategory->forceDelete();
        return new ProfessionalCategoryResource($professionalCategory);
    }
}
