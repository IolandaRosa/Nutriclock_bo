<?php

namespace App\Http\Controllers;

use App\BiometricProcedures;
use App\Notification;
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
            'orderNumber' => 'required|integer|unique:biometric_procedures,orderNumber'
        ]);

        $biometricProcedure = new BiometricProcedures();
        $biometricProcedure->value = $request->value;
        $biometricProcedure->orderNumber = $request->orderNumber;
        $biometricProcedure->save();

        return $this->index();
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

        $biometricProcedures = BiometricProcedures::where('orderNumber', '>', $biometricProcedure->orderNumber)->get();

        if ($biometricProcedures) {
            foreach ($biometricProcedures as $b) {
                $b->orderNumber = $b->orderNumber - 1;
                $b->save();
            }
        }

        $biometricProcedure->forceDelete();
        return $this->index();
    }

    /**
     * @OA\Get(
     *      path="/api/biometric-procedure-up/{id}",
     *      operationId="Downgrade order number",
     *      tags={"Biometric Procedure"},
     *      summary="Downgrade order number",
     *      description="Downgrade order number",
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
     *          description="return biometric list reordered"
     *       )
     *     )
     */
    public function movesUp($id)
    {
        $biometricProcedure = BiometricProcedures::find($id);

        if (!$biometricProcedure) {
            return Response::json(['error' => 'O procedimento não existe.'], 400);
        }

        $biometricProcedureBefore = BiometricProcedures::where('orderNumber', $biometricProcedure->orderNumber - 1)->first();

        if ($biometricProcedureBefore) {
            $biometricProcedureBefore->orderNumber = $biometricProcedure->orderNumber;
            $biometricProcedureBefore->save();
        }

        $biometricProcedure->orderNumber = $biometricProcedure->orderNumber - 1;
        $biometricProcedure->save();
        return $this->index();
    }

    /**
     * @OA\Get(
     *      path="/api/biometric-procedure-down/{id}",
     *      operationId="Upgrade order number",
     *      tags={"Biometric Procedure"},
     *      summary="Upgrade order number",
     *      description="Upgrade order number",
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
     *          description="return biometric list reordered"
     *       )
     *     )
     */
    public function movesDown($id)
    {
        $biometricProcedure = BiometricProcedures::find($id);

        if (!$biometricProcedure) {
            return Response::json(['error' => 'O procedimento não existe.'], 400);
        }

        $biometricProcedureAfter = BiometricProcedures::where('orderNumber', $biometricProcedure->orderNumber + 1)->first();

        if ($biometricProcedureAfter) {
            $biometricProcedureAfter->orderNumber = $biometricProcedure->orderNumber;
            $biometricProcedureAfter->save();
        }

        $biometricProcedure->orderNumber = $biometricProcedure->orderNumber + 1;
        $biometricProcedure->save();
        return $this->index();
    }

    public function test()
    {
        $hour = date("H:i");
        $notifications = Notification::where('userId', '49')->first();
        $today = date("d-m-Y");
        $interval = '10:00';
        $intervalTime = date("H:i", strtotime($interval . ' -1 hour' . ' -15 minutes'));
        return Response::json([
            'hour' => $hour,
            'notificationsBiometric' => $notifications->notificationsBiometric,
            'today' => $today,
            'todayEqualsDate' => ($today == '09-05-2021'),
            'interval' => $interval,
            'intervalTime' => $intervalTime,
            'intervalTimeEqualsHours' => $intervalTime == $hour,
            'hourBigSmall' => $hour >= '12:00' && $hour <= '12:09'
        ]);
    }
}
