<?php

namespace App\Http\Controllers;

use App\BiometricProcedures;
use App\Http\Resources\BiometricProcedures as BiometricProceduresResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BiometricProcedureControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/biometric-procedure",
     *      operationId="get all biometric procedures",
     *      tags={"Biometric Procedure"},
     *      summary="Return a list of all biometric procedures",
     *      description="Return a list all biometric procedures",
     *      @OA\Response(
     *          response=200,
     *          description="return a list of all biometric procedures"
     *       )
     *     )
     */
    public function index()
    {
        $biometricProcedures = BiometricProcedures::orderBy('orderNumber')->get(['id', 'value', 'orderNumber']);
        return Response::json(['data' => $biometricProcedures]);
    }


    /**
     * @OA\Post(
     *      path="/api/biometric-procedure",
     *      operationId="Creates new biometric procedure",
     *      tags={"Biometric Procedure"},
     *      summary="Creates new biometric procedure",
     *      description="Creates new biometric procedure",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="orderNumber",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="value",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return new biometric procedure"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|string',
            'orderNumber' => 'required|integer|unique:biometric_collections,orderNumber'
        ]);

        $biometricProcedure = new BiometricProcedures();
        $biometricProcedure->value = $request->value;
        $biometricProcedure->orderNumber = $request->orderNumber;
        $biometricProcedure->save();

        return new BiometricProceduresResource($biometricProcedure);
    }


    /**
     * @OA\Put(
     *      path="/api/biometric-procedure/{id}",
     *      operationId="Update biometric procedure",
     *      tags={"Biometric Procedure"},
     *      summary="Update biometric procedure",
     *      description="Update biometric procedure",
     *      @OA\Parameter(
     *         description="ID of biometric procedure",
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
     *                     property="value",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return biometric procedure"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        $biometricProcedure = BiometricProcedures::find($id);

        if (!$biometricProcedure) {
            return Response::json(['error' => 'O procedimento não existe.'], 404);
        }

        $biometricProcedure->update($request->all());

        return new BiometricProceduresResource($biometricProcedure);
    }

    /**
     * @OA\Delete(
     *      path="/api/biometric-procedure/{id}",
     *      operationId="Delete biometric procedure",
     *      tags={"Biometric Collection"},
     *      summary="Delete biometric procedure",
     *      description="Delete biometric procedure",
     *      @OA\Parameter(
     *         description="ID of biometric procedure",
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
     *          description="return biometric procedure"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $biometricProcedure = BiometricProcedures::find($id);

        if (!$biometricProcedure) {
            return Response::json(['error' => 'O procedimento não existe.'], 400);
        }

        $biometricProcedure->forceDelete();
        return new BiometricProceduresResource($biometricProcedure);
    }
}
