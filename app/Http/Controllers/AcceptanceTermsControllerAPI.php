<?php

namespace App\Http\Controllers;

use App\AcceptanceTerms;
use App\Http\Resources\AcceptanceTerms as AcceptanceTermsResource;
use App\User;
use Illuminate\Http\Request;

class AcceptanceTermsControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/terms",
     *      operationId="terms",
     *      tags={"Acceptance Terms"},
     *      summary="Return the last version acceptance terms",
     *      description="Return the last version acceptance terms",
     *      @OA\Response(
     *          response=200,
     *          description="return the last version acceptance terms"
     *       )
     *     )
     */
    public function getTerms() {
        $version = AcceptanceTerms::max('version');

        $terms = AcceptanceTerms::where('version', $version)->first();

        if (!$terms) {
            return Response::json(['error' => 'Os termos não existem.'], 404);
        }

        return new AcceptanceTermsResource($terms);
    }

    /**
     * @OA\Put(
     *      path="/api/terms/{id}",
     *      operationId="Update terms",
     *      tags={"Acceptance Terms"},
     *      summary="Update usf",
     *      description="Update usf",
     *      @OA\Parameter(
     *         description="ID of terms",
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
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return acceptance term"
     *       )
     *     )
     */
    public function update(Request $request, $id) {
        $terms = AcceptanceTerms::where('version', $id)->first();

        $request->validate([
            'title' => 'string|required',
            'description' => 'string|required',
        ]);

        if (!$terms) {
            return Response::json(['error' => 'Os termos não existem.'], 404);
        }

        $terms->title = $request->title;
        $terms->description = $request->description;
        $terms->version = $id+1;
        $terms->update();

        User::where('role', 'PATIENT')->update(array('terms_accepted'=>0));

        return new AcceptanceTermsResource($terms);
    }
}
