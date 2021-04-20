<?php

namespace App\Http\Controllers;

use App\Configuration;
use App\Http\Resources\Configuration as ConfigurationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ConfigurationControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/configs",
     *      operationId="configs",
     *      tags={"Configurations"},
     *      summary="Return this list of configurations",
     *      description="Return this list of configurations",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of configurations"
     *       )
     *     )
     */
    public function index()
    {
        return ConfigurationResource::collection(Configuration::all());
    }

    /**
     * @OA\Get(
     *      path="/api/configs/tips",
     *      operationId="get config tip status",
     *      tags={"Configurations"},
     *      summary="Return config tip status",
     *      description="Return config tip status",
     *      @OA\Response(
     *          response=200,
     *          description="return config tip status"
     *       )
     *     )
     */
    public function getTipStatus() {
        $configuration = Configuration::where('key', 'SLEEP_TIP_ENABLED')->first();
        return new ConfigurationResource($configuration);
    }

    /**
     * @OA\Put(
     *      path="/api/configs/{id}",
     *      operationId="Update config status by id",
     *      tags={"Configurations"},
     *      summary="Update config status by id",
     *      description="Update config status by id",
     *      @OA\Parameter(
     *         description="ID of config",
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
     *          description="return usf"
     *       )
     *     )
     */
    public function update(Request $request, $id)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'value' => 'required',
        ]);

        $configuration = Configuration::find($id);

        if (!$configuration) {
            return Response::json(['error' => 'A configuração não existe!'], 404);
        }

        $configuration->value = $request->input('value');
        $configuration->save();
        return new ConfigurationResource($configuration);
    }
}
