<?php

namespace App\Http\Controllers;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use App\BiometricGroup;
use App\User;
use Illuminate\Http\Request;
use \App\Http\Resources\BiometricGroup as BiometricGroupResource;
use \App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Response;

class BiometricGroupControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/biometric-group",
     *      operationId="Creates new biometric group",
     *      tags={"Biometric Collection"},
     *      summary="Creates new biometric group",
     *      description="Creates new biometric group",
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
     *          description="return new biometric group"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $group = new BiometricGroup();
        $group->name = $request->name;
        $group->save();

        return new BiometricGroupResource($group);
    }

    /**
     * @OA\Delete(
     *      path="/api/biometric-group/{id}",
     *      operationId="Delete biometric group",
     *      tags={"Biometric Group"},
     *      summary="Delete biometric group",
     *      description="Delete biometric group",
     *      @OA\Parameter(
     *         description="ID of biometric group",
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
     *          description="return biometric group"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $group = BiometricGroup::find($id);

        if (!$group) {
            return Response::json(['error' => 'O grupo de recolha não existe.'], 400);
        }

        $collections = BiometricCollections::where('biometric_group_id', $id)->get();

        foreach ($collections as $collection) {
            BiometricCollectionIntervals::where('collectionId', $collection->id)->delete();
            $collection->delete();
        }

        $users = User::where('biometric_group_id', $id)->get();

        foreach ($users as $user) {
            $user->biometric_group_id = null;
            $user->save();
        }

        $group->delete();

        return new BiometricGroupResource($group);
    }

    /**
     * @OA\Post(
     *      path="/api/biometric-group-user-add",
     *      operationId="Associates a biometric group with user",
     *      tags={"Biometric Collection"},
     *      summary="Associates a biometric group with user",
     *      description="Associates a biometric group with user",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="userId",
     *                     type="number"
     *                 ),
     *             ),
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="groupId",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return biometric group"
     *       )
     *     )
     */
    public function addUserToGroup(Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'groupId' => 'required',
        ]);

        $group = BiometricGroup::find($request->groupId);

        if (!$group) {
            return Response::json(['error' => 'O grupo de recolha não existe.'], 400);
        }

        $user = User::find($request->userId);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 400);
        }

        $user->biometric_group_id = $request->groupId;
        $user->save();

        return new BiometricGroupResource($group);
    }

    /**
     * @OA\Post(
     *      path="/api/biometric-group-user-remove",
     *      operationId="Dissociates a biometric group with user",
     *      tags={"Biometric Collection"},
     *      summary="Dissociates a biometric group with user",
     *      description="Dissociates a biometric group with user",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="userId",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function removeUserFromGroup(Request $request)
    {
        $request->validate([
            'userId' => 'required',
        ]);

        $user = User::find($request->userId);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 400);
        }

        $user->biometric_group_id = null;
        $user->save();

        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *      path="/api/biometric-group-biometric-collection-add",
     *      operationId="Associates a biometric group with biometric collection",
     *      tags={"Biometric Collection"},
     *      summary="Associates a biometric group with biometric collection",
     *      description="Associates a biometric group with biometric collection",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="collectionId",
     *                     type="number"
     *                 ),
     *             ),
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="groupId",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return biometric group"
     *       )
     *     )
     */
    public function addBiometricCollectionToGroup(Request $request)
    {
        $request->validate([
            'collectionId' => 'required',
            'groupId' => 'required',
        ]);

        $group = BiometricGroup::find($request->groupId);

        if (!$group) {
            return Response::json(['error' => 'O grupo de recolha não existe.'], 400);
        }

        $collection = BiometricCollections::find($request->collectionId);

        if (!$collection) {
            return Response::json(['error' => 'A recolha não existe.'], 400);
        }

        $collection->biometric_group_id = $request->groupId;
        $collection->save();

        return new BiometricGroupResource($group);
    }

    /**
     * @OA\Post(
     *      path="/api/biometric-group-biometric-collection-remove",
     *      operationId="Dissociates a biometric group with biometric collection",
     *      tags={"Biometric Collection"},
     *      summary="Dissociates a biometric group with biometric collection",
     *      description="Dissociates a biometric group with biometric collection",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="collectionId",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return biometric collection"
     *       )
     *     )
     */
    public function removeBiometricCollectionToGroup(Request $request)
    {
        $request->validate([
            'collectionId' => 'required'
        ]);

        $collection = BiometricCollections::find($request->collectionId);

        if (!$collection) {
            return Response::json(['error' => 'A recolha não existe.'], 400);
        }

        BiometricCollectionIntervals::where('collectionId', $collection->id)->delete();
        $collection->delete();

        return new \App\Http\Resources\BiometricCollections($collection);
    }
}
