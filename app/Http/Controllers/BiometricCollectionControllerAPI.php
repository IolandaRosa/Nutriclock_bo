<?php

namespace App\Http\Controllers;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use Illuminate\Http\Request;
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
        $index = 0;
        $biometricCollections = BiometricCollections::orderBy('orderNumber')->get(['id', 'orderNumber', 'name', 'date']);

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
     *                     property="orderNumber",
     *                     type="number"
     *                 ),
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
     *                     type="array"
     *                     @OA\Items(type="string")
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
            'orderNumber' => 'required|integer|unique:biometric_collections,orderNumber',
            'date' => 'required|string',
        ]);

        $biometricCollection = new BiometricCollections();
        $biometricCollection->name = $request->name;
        $biometricCollection->orderNumber = $request->orderNumber;
        $biometricCollection->date = $request->date;
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

        $biometricCollections = BiometricCollections::where('orderNumber', '>', $biometricCollection->orderNumber)->get();

        if ($biometricCollections) {
            foreach ($biometricCollections as $b) {
                $b->orderNumber = $b->orderNumber - 1;
                $b->save();
            }
        }

        $biometricCollection->forceDelete();
        return $this->index();
    }

    /**
     * @OA\Get(
     *      path="/api/biometric-collection-up/{id}",
     *      operationId="Downgrade order number",
     *      tags={"Biometric Collection"},
     *      summary="Downgrade order number",
     *      description="Downgrade order number",
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
     *          description="return biometric list reordered"
     *       )
     *     )
     */
    public function movesUp($id) {
        $biometricCollection = BiometricCollections::find($id);

        if (!$biometricCollection) {
            return Response::json(['error' => 'O procedimento não existe.'], 400);
        }

        $biometricCollectionBefore = BiometricCollections::where('orderNumber', $biometricCollection->orderNumber-1)->first();

        if ($biometricCollectionBefore) {
            $biometricCollectionBefore->orderNumber = $biometricCollection->orderNumber;
            $biometricCollectionBefore->save();
        }

        $biometricCollection->orderNumber = $biometricCollection->orderNumber - 1;
        $biometricCollection->save();
        return $this->index();
    }

    /**
     * @OA\Get(
     *      path="/api/biometric-collection-down/{id}",
     *      operationId="Upgrade order number",
     *      tags={"Biometric Collection"},
     *      summary="Upgrade order number",
     *      description="Upgrade order number",
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
     *          description="return biometric list reordered"
     *       )
     *     )
     */
    public function  movesDown($id) {
        $biometricCollection = BiometricCollections::find($id);

        if (!$biometricCollection) {
            return Response::json(['error' => 'O procedimento não existe.'], 400);
        }

        $biometricCollectionAfter = BiometricCollections::where('orderNumber', $biometricCollection->orderNumber+1)->first();

        if ($biometricCollectionAfter) {
            $biometricCollectionAfter->orderNumber = $biometricCollection->orderNumber;
            $biometricCollectionAfter->save();
        }

        $biometricCollection->orderNumber = $biometricCollection->orderNumber + 1;
        $biometricCollection->save();
        return $this->index();
    }
}
