<?php

namespace App\Http\Controllers;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use App\BiometricGroup;
use App\Http\Resources\BiometricGroup as BiometricGroupResource;
use App\Http\Resources\User as UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BiometricGroupControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/biometric-group",
     *      operationId="get all biometric groups",
     *      tags={"Biometric Group"},
     *      summary="Return a list of all biometric groups and collections",
     *      description="Return a list all biometric groups and collections",
     *      @OA\Response(
     *          response=200,
     *          description="return a list of all biometric groups and collections"
     *       )
     *     )
     */
    public function index()
    {
        $groups = BiometricGroup::all();

        if (!$groups) {
            return Response::json(['error' => 'Não existem gruops de recolhas'], 400);
        }

        foreach ($groups as $group) {
            $index = 0;
            $biometricCollections = BiometricCollections::where('biometric_group_id', $group->id)->get();
            $group['collections'] = $biometricCollections;

            foreach ($biometricCollections as $biometricCollection) {
                $intervals = BiometricCollectionIntervals::where('collectionId', $biometricCollection->id)->get(['id', 'hour']);
                $biometricCollections[$index]['intervals'] = $intervals;
                $index++;
            }
        }
        return Response::json(['data' => $groups]);
    }

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

        $usersFromGroup = User::where('biometric_group_id', $group->id)->where('role', 'PATIENT')->get();
        $usersWithoutGroup = User::where('biometric_group_id', null)->where('role', 'PATIENT')->get();
        return Response::json(['usersFromGroup' => $usersFromGroup, 'usersWithoutGroup' => $usersWithoutGroup]);
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

        $id = $user->biometric_group_id;

        $user->biometric_group_id = null;
        $user->save();

        $usersFromGroup = User::where('biometric_group_id', $id)->where('role', 'PATIENT')->get();
        $usersWithoutGroup = User::where('biometric_group_id', null)->where('role', 'PATIENT')->get();
        return Response::json(['usersFromGroup' => $usersFromGroup, 'usersWithoutGroup' => $usersWithoutGroup]);
    }

    /**
     * @OA\Get(
     *      path="/api/biometric-group/users/{id}",
     *      operationId="get all biometric users by group",
     *      tags={"Biometric Group"},
     *      summary="Return a list of all users from biometric group and without a group",
     *      description="Return a list of all users from biometric group and without a group",
     *      @OA\Response(
     *          response=200,
     *          description="Return a list of all users from biometric group and without a group"
     *       )
     *     )
     */
    public function usersByBiometricGroup($id)
    {
        $group = BiometricGroup::where('id', $id);

        if (!$group) {
            return Response::json(['error' => 'Não existe o grupo de recolha'], 400);
        }

        $usersFromGroup = User::where('biometric_group_id', $id)->where('role', 'PATIENT')->get();
        $usersWithoutGroup = User::where('biometric_group_id', null)->where('role', 'PATIENT')->get();
        return Response::json(['usersFromGroup' => $usersFromGroup, 'usersWithoutGroup' => $usersWithoutGroup]);
    }
}
