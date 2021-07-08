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
    public function getAverageEvaluation()
    {
        $questions = Evaluation::all();

        $totalZero1 = 0;
        $totalQuestion1 = 0;
        $totalZero2 = 0;
        $totalQuestion2 = 0;
        $totalZero3 = 0;
        $totalQuestion3 = 0;
        $totalZero4 = 0;
        $totalQuestion4 = 0;
        $totalZero5 = 0;
        $totalQuestion5 = 0;
        $totalZero6 = 0;
        $totalQuestion6 = 0;
        $totalZero7 = 0;
        $totalQuestion7 = 0;
        $totalZero8 = 0;
        $totalQuestion8 = 0;
        $totalZero9 = 0;
        $totalQuestion9 = 0;
        $totalZero10 = 0;
        $totalQuestion10 = 0;

        foreach ($questions as $q) {
            if ($q->question1 == 0) $totalZero1++;
            $totalQuestion1 += $q->question1;

            if ($q->question2 == 0) $totalZero2++;
            $totalQuestion2 += $q->question2;

            if ($q->question3 == 0) $totalZero3++;
            $totalQuestion3 += $q->question3;

            if ($q->question4 == 0) $totalZero4++;
            $totalQuestion4 += $q->question4;

            if ($q->question5 == 0) $totalZero5++;
            $totalQuestion5 += $q->question5;

            if ($q->question6 == 0) $totalZero6++;
            $totalQuestion6 += $q->question6;

            if ($q->question7 == 0) $totalZero7++;
            $totalQuestion7 += $q->question7;

            if ($q->question8 == 0) $totalZero8++;
            $totalQuestion8 += $q->question8;

            if ($q->question9 == 0) $totalZero9++;
            $totalQuestion9 += $q->question9;

            if ($q->question10 == 0) $totalZero10++;
            $totalQuestion10 += $q->question10;
        }

        return Response::json([
            'question1' => $totalQuestion1 / (count($questions) - $totalZero1),
            'answer1' => count($questions) - $totalZero1,
            'question2' => $totalQuestion2 / (count($questions) - $totalZero2),
            'answer2' => count($questions) - $totalZero2,
            'question3' => $totalQuestion3 / (count($questions) - $totalZero3),
            'answer3' => count($questions) - $totalZero3,
            'question4' => $totalQuestion4 / (count($questions) - $totalZero4),
            'answer4' => count($questions) - $totalZero4,
            'question5' => $totalQuestion5 / (count($questions) - $totalZero5),
            'answer5' => count($questions) - $totalZero5,
            'question6' => $totalQuestion6 / (count($questions) - $totalZero6),
            'answer6' => count($questions) - $totalZero6,
            'question7' => $totalQuestion7 / (count($questions) - $totalZero7),
            'answer7' => count($questions) - $totalZero7,
            'question8' => $totalQuestion8 / (count($questions) - $totalZero8),
            'answer8' => count($questions) - $totalZero8,
            'question9' => $totalQuestion9 / (count($questions) - $totalZero9),
            'answer9' => count($questions) - $totalZero9,
            'question10' => $totalQuestion10 / (count($questions) - $totalZero10),
            'answer10' => count($questions) - $totalZero10
        ]);
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
    public function getUserEvaluation()
    {
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
