<?php

namespace App\Http\Controllers;

use App\Http\Resources\Ufc as UfcResource;
use App\Ufc;
use App\User;
use App\UsersUfc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class UfcControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/ufcs",
     *      operationId="ufcs",
     *      tags={"USF"},
     *      summary="Return this list of usf",
     *      description="Return this list of usf",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of usf"
     *       )
     *     )
     */
    public function index()
    {
        return UfcResource::collection(Ufc::all());
    }

    /**
     * @OA\Post(
     *      path="/api/ufcs",
     *      operationId="Creates new usf",
     *      tags={"USF"},
     *      summary="Creates new usf",
     *      description="Creates new usf",
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
     *          description="return usf"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'name'=>'required|unique:ufcs,name',
        ]);

        $ufc = new Ufc();

        $ufc->name = $request->input('name');
        $ufc->save();

        return new UfcResource($ufc);
    }

    /**
     * @OA\Put(
     *      path="/api/ufcs/{id}",
     *      operationId="Update usf",
     *      tags={"USF"},
     *      summary="Update usf",
     *      description="Update usf",
     *      @OA\Parameter(
     *         description="ID of usf",
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
     *          description="return usf"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $validateData=$request->validate([
            'name'=>'required|unique:ufcs,name',
        ]);

        $ufc=Ufc::findOrFail($id);

        $ufc->fill($validateData)->save();

        return new UfcResource($ufc);
    }

    /**
     * @OA\Delete(
     *      path="/api/ufcs/{id}",
     *      operationId="Delete usf",
     *      tags={"USF"},
     *      summary="Delete usf",
     *      description="Delete usf",
     *      @OA\Parameter(
     *         description="ID of usf",
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
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $totalUsfs = UsersUfc::where('ufc_id', $id)->count();

        if ($totalUsfs > 0) {
            return Response::json(['error' => 'Não é possível eliminar pois existem utilizadores associados!'], 400);
        }

        $ufc=Ufc::findOrFail($id);
        $ufc->forceDelete();
        return new UfcResource($ufc);
    }

    /**
     * @OA\Get(
     *      path="/api/users/{id}/ufcs",
     *      operationId="Get users from usf",
     *      tags={"USF"},
     *      summary="Get users from usf",
     *      description="Get users from usf",
     *      @OA\Parameter(
     *         description="ID of usf",
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
     *          description="return users from usf"
     *       )
     *     )
     */
    public function getUserUfcs(Request $request, $id) {
        $usersUfcs=[];

        $user = User::find($id);

        if($user->role != 'PROFESSIONAL'){
            return UfcResource::collection(Ufc::all());
        } else {
            $usersUfcs = UsersUfc::where('user_id', $id)->get();
        }

        if (!$usersUfcs) {
            return Response::json(['error' => 'Não há ufcs'], 400);
        }

        $ufcs = [];

        foreach ($usersUfcs as $u) {
            $ufc = Ufc::where('id', $u->ufc_id)->first();
            array_push($ufcs, $ufc);
        }

        return UfcResource::collection($ufcs);
    }

    /**
     * @OA\Get(
     *      path="/api/ufcs/auth/description",
     *      operationId="Get usf names for auth user",
     *      tags={"USF"},
     *      summary="Get usf names for auth user",
     *      description="Get usf names for auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return users from usf"
     *       )
     *     )
     */
    public function getUserUfcsName() {
        $names = "";
        $userUfc = UsersUfc::where('user_id', Auth::guard('api')->id())->get();

        if ($userUfc) {
            foreach ($userUfc as $u) {
                $ufc = Ufc::where('id', $u->ufc_id)->first();
                $names .= $ufc->name.", ";
            }
        }

        return Response::json(['data' => substr(trim($names), 0, -1)], 200);
    }

}
