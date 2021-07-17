<?php

namespace App\Http\Controllers;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class BiometricCollectionControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/biometric-collection",
     *      operationId="get all biometric collections",
     *      tags={"Biometric Collection"},
     *      summary="Return a list of all biometric collections",
     *      description="Return a list all biometric collections",
     *      @OA\Response(
     *          response=200,
     *          description="return a list of all biometric collections"
     *       )
     *     )
     */
    public function index()
    {
        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $index = 0;
        $biometricCollections = BiometricCollections::where('biometric_group_id', $user->biometric_group_id)->orderBy('date')->get(['id', 'name', 'date']);

        if (!$biometricCollections) {
            return Response::json(['error' => 'Não existem recolhas'], 400);
        }

        foreach ($biometricCollections as $biometricCollection) {
            $intervals = BiometricCollectionIntervals::where('collectionId', $biometricCollection->id)->get(['id', 'hour']);
            $biometricCollections[$index]['intervals'] = $intervals;
            $index++;
        }
        return Response::json(['data' => $biometricCollections]);
    }


    /**
     * @OA\Post(
     *      path="/api/biometric-collection",
     *      operationId="Creates new biometric collection",
     *      tags={"Biometric Collection"},
     *      summary="Creates new biometric collection",
     *      description="Creates new biometric collection",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="intervals",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="groupId",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return new biometric collection"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|string',
            'groupId' => 'required'
        ]);

        $biometricCollection = new BiometricCollections();
        $biometricCollection->name = $request->name;
        $biometricCollection->date = $request->date;
        $biometricCollection->biometric_group_id = $request->groupId;
        $biometricCollection->save();

        if ($request->intervals) {
            foreach ($request->intervals as $r) {
                $interval = new BiometricCollectionIntervals();
                $interval->collectionId = $biometricCollection->id;
                $interval->hour = $r;
                $interval->save();
            }
        }
        return $this->index();
    }

    /**
     * @OA\Delete(
     *      path="/api/biometric-collection/{id}",
     *      operationId="Delete biometric collection",
     *      tags={"Biometric Collection"},
     *      summary="Delete biometric collection",
     *      description="Delete biometric collection",
     *      @OA\Parameter(
     *         description="ID of biometric collection",
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
     *          description="return biometric collection"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $biometricCollection = BiometricCollections::find($id);

        if (!$biometricCollection) {
            return Response::json(['error' => 'A recolha não existe.'], 400);
        }

        $biometricCollection->forceDelete();
        return $this->index();
    }
}
