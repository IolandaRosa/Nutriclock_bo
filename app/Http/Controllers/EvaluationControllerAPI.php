<?php

namespace App\Http\Controllers;

use App\Evaluation;
use App\Http\Resources\Evaluation as EvaluationResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class EvaluationControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/evaluation",
     *      operationId="Creates new evaluation for auth user",
     *      tags={"Evaluation"},
     *      summary="Creates new evaluation",
     *      description="Creates new evaluation",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="question1",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question2",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question3",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question4",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question5",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question6",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question7",
     *                     type="number"
     *                 ),
     *             @OA\Property(
     *                     property="question8",
     *                     type="number"
     *                 ),
     *              @OA\Property(
     *                     property="question9",
     *                     type="number"
     *                 ),
     *              @OA\Property(
     *                     property="question10",
     *                     type="number"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return evaluation"
     *       )
     *     )
     */
    public function store(Request $request)
    {
        $request->validate([
            'question1' => 'required|numeric',
            'question2' => 'required|numeric',
            'question3' => 'required|numeric',
            'question4' => 'required|numeric',
            'question5' => 'required|numeric',
            'question6' => 'required|numeric',
            'question7' => 'required|numeric',
            'question8' => 'required|numeric',
            'question9' => 'required|numeric',
            'question10' => 'required|numeric'
        ]);

        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'Utilizador nao encontrado!'], 404);
        }

        $evaluation = Evaluation::where('userId', $user->id)->get();

        if ($evaluation && count($evaluation) > 0) {
            return Response::json(['error' => 'A sua avaliação já foi submetida!'], 400);
        }

        $evaluation = new Evaluation();
        $evaluation->userId = $user->id;
        $evaluation->question1 = $request->question1;
        $evaluation->question2 = $request->question2;
        $evaluation->question3 = $request->question3;
        $evaluation->question4 = $request->question4;
        $evaluation->question5 = $request->question5;
        $evaluation->question6 = $request->question6;
        $evaluation->question7 = $request->question7;
        $evaluation->question8 = $request->question8;
        $evaluation->question9 = $request->question9;
        $evaluation->question10 = $request->question10;

        $evaluation->save();
        return new EvaluationResource($evaluation);
    }

    /**
     * @OA\Get(
     *      path="/api/average-evaluation",
     *      operationId="Evaluation average",
     *      tags={"Evaluation"},
     *      summary="Return this list of evaluation averages for admin",
     *      description="Return this list of evaluation averages for admin",
     *      @OA\Response(
     *          response=200,
     *          description="Return this list of evaluation averages for admin"
     *       )
     *     )
     */
    public function getAverageEvaluation() {
        $averages = Evaluation::
            select(DB::raw('avg(question1) question1, avg(question2) question2, avg(question3) question3, avg(question4) question4,
            avg(question5) question5, avg(question6) question6, avg(question7) question7, avg(question8) question8, avg(question9) question9, avg(question10) question10'))
            ->first();

        return Response::json(['averages' => $averages]);
    }

    /**
     * @OA\Get(
     *      path="/api/has-evaluation",
     *      operationId="Evaluation check",
     *      tags={"Evaluation"},
     *      summary="Check if auth user has done an evaluation",
     *      description="Check if auth user has done an evaluation",
     *      @OA\Response(
     *          response=200,
     *          description="Check if auth user has done an evaluation"
     *       )
     *     )
     */
    public function getUserEvaluation() {
        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'Utilizador nao encontrado!'], 404);
        }

        $evaluation = Evaluation::where('userId', $user->id)->get();

        if ($evaluation && count($evaluation) > 0) {
            return Response::json(['hasEvaluation' => true]);
        } else {
            return Response::json(['hasEvaluation' => false]);
        }
    }
}
