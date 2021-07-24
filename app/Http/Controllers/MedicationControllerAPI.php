<?php

namespace App\Http\Controllers;

use App\Http\Resources\Medication as MedicationResource;
use App\Medication;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/medications",
     *      operationId="Medication",
     *      tags={"Medication"},
     *      summary="Return this list of medications",
     *      description="Return this list of medications",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of medications"
     *       )
     *     )
     */
    public function index()
    {
        return MedicationResource::collection(Medication::all());
    }

    /**
     * @OA\Get(
     *      path="/api/medications/{userId}",
     *      operationId="Get medication by user",
     *      tags={"Medication"},
     *      summary="Get medication by user",
     *      description="Get medication by user",
     *      @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return list of medication from user"
     *       )
     *     )
     */
    public function getMedicationByUser(Request $request, $id) {
        $medication = Medication::where('user_id', $id)->get();

        return MedicationResource::collection($medication);
    }

    /**
     * @OA\Get(
     *      path="/api/medication/{id}",
     *      operationId="Get medication by user",
     *      tags={"Medication"},
     *      summary="Get medication by id",
     *      description="Get medication by id",
     *      @OA\Parameter(
     *         description="ID of medication",
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
     *          description="return medication"
     *       )
     *     )
     */
    public function getMedication (Request $request, $id) {
        $med = Medication::find($id);

        if (!$med) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        return new MedicationResource($med);
    }

    /**
     * @OA\Post(
     *      path="/api/medications/{userId}",
     *      operationId="Creates new medication for user",
     *      tags={"Medication"},
     *      summary="Creates new medication for user",
     *      description="Creates new medication for user",
     *      @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="userId",
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
     *                 @OA\Property(
     *                     property="timesAWeek",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="timesADay",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="posology",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return medication"
     *       )
     *     )
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $user=User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $medication = new Medication();

        $medication->name = $request->name;
        $medication->timesAWeek = $request->timesAWeek;
        $medication->timesADay = $request->timesADay;
        $medication->user_id = $id;
        $medication->posology = $request->posology;
        $medication->type = $request->type;

        $medication->save();

        return new MedicationResource($medication);
    }

    /**
     * @OA\Put(
     *      path="/api/medications/{id}",
     *      operationId="Update medication for user",
     *      tags={"Medication"},
     *      summary="Update medication for user",
     *      description="Update medication for user",
     *      @OA\Parameter(
     *         description="ID of medication",
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
     *                 @OA\Property(
     *                     property="timesAWeek",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="timesADay",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="posology",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return medication"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'posology' => 'required|integer',
        ]);
        $medication=Medication::findOrFail($id);

        if (!$medication) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        $medication->name = $request->name;
        $medication->timesAWeek = $request->timesAWeek;
        $medication->timesADay = $request->timesADay;
        $medication->posology = $request->posology;
        $medication->type = $request->type;
        $medication->update();
        return new MedicationResource($medication);
    }

    /**
     * @OA\Delete(
     *      path="/api/medications/{id}",
     *      operationId="Delete medication",
     *      tags={"Medication"},
     *      summary="Delete medication",
     *      description="Delete medication",
     *      @OA\Parameter(
     *         description="ID of medication",
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
     *          description="return medication"
     *       )
     *     )
     */
    public function destroy($id)
    {
        $medication=Medication::findOrFail($id);

        if (!$medication) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        $medication->forceDelete();

        return new MedicationResource($medication);
    }

    /**
     * @OA\Get(
     *      path="/api/medications-auth",
     *      operationId="Get medication by auth user",
     *      tags={"Medication"},
     *      summary="Get medication by auth user",
     *      description="Get medication by auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return list of medication"
     *       )
     *     )
     */
    public function getAuthMedication() {
        $user = User::find(Auth::guard('api')->user()->id);

        if(!$user){
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $medication = Medication::where('user_id', $user->id)->get();

        return MedicationResource::collection($medication);
    }

    /**
     * @OA\Post(
     *      path="/api/medications-auth",
     *      operationId="Creates new medication for auth user",
     *      tags={"Medication"},
     *      summary="Creates new medication for auth user",
     *      description="Creates new medication for auth user",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="timesAWeek",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="timesADay",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="posology",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return medication"
     *       )
     *     )
     */
    public function storeAuth(Request $request) {
        return $this->store($request, Auth::guard('api')->user()->id);
    }
}
