<?php

namespace App\Http\Controllers;

use App\Http\Resources\SleepTip as SleepTipResource;
use App\SleepTip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SleepTipControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/tips",
     *      operationId="tips",
     *      tags={"Sleep Tip"},
     *      summary="Return this list of tips",
     *      description="Return this list of tips",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of tips"
     *       )
     *     )
     */
    public function index()
    {
        return SleepTipResource::collection(SleepTip::all());
    }

    /**
     * @OA\Post(
     *      path="/api/tips",
     *      operationId="Creates new tip",
     *      tags={"Sleep Tip"},
     *      summary="Creates new tip",
     *      description="Creates new tip",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return tip"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'description'=>'required',
        ]);

        $tip = new SleepTip();
        $tip->description = $request->input('description');
        $tip->save();
        return new SleepTipResource($tip);
    }

    /**
     * @OA\Put(
     *      path="/api/tips/{id}",
     *      operationId="Update tip",
     *      tags={"Sleep Tip"},
     *      summary="Update tip",
     *      description="Update tip",
     *      @OA\Parameter(
     *         description="ID of tip",
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
     *                     property="description",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return tip"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $validateData=$request->validate([
            'description'=>'required',
        ]);

        $tip=SleepTip::findOrFail($id);

        $tip->fill($validateData)->save();

        return new SleepTipResource($tip);
    }

    /**
     * @OA\Delete(
     *      path="/api/tips/{id}",
     *      operationId="Delete tip",
     *      tags={"Sleep Tip"},
     *      summary="Delete tip",
     *      description="Delete tip",
     *      @OA\Parameter(
     *         description="ID of tip",
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
     *          description="return tip"
     *       )
     *     )
     */
    public function destroy($id)
    {
        if(Auth::guard('api')->user()->role != 'ADMIN'){
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $tip=SleepTip::findOrFail($id);

        $tip->forceDelete();

        return new SleepTipResource($tip);
    }
}
