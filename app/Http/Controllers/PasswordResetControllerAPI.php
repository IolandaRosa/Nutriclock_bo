<?php

namespace App\Http\Controllers;

use App\Http\Resources\PasswordReset as PasswordResetResource;
use App\Http\Resources\User as UserResource;
use App\Notifications\PasswordResetRequest;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Response;

class PasswordResetControllerAPI extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/password",
     *      operationId="Reset Password",
     *      tags={"Authentication"},
     *      summary="Reset Password",
     *      description="Reset Password",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="email password reset"
     *       )
     *     )
     */
    public function store (Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
           return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $passwordReset = new PasswordReset();
        $passwordReset->email = $request->email;
        $passwordReset->token = Str::random(60);

        $passwordReset->save();

        if ($request->register) {
            $user->notify(new PasswordResetRequest($passwordReset->token, $user, 'Confirmação de Registo', 'Clique no link abaixo para  finalizar o processo de autenticação.'));
        } else {
            $user->notify(new PasswordResetRequest($passwordReset->token, $user, 'Recuperação de Password', 'Clique no link abaixo para recuperar a sua password.'));
        }

        return new PasswordResetResource($passwordReset);
    }

    /**
     * @OA\Post(
     *      path="/api/reset-password",
     *      operationId="Reset Password",
     *      tags={"Authentication"},
     *      summary="Reset user password",
     *      description="Reset user password",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="token",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="reset user password"
     *       )
     *     )
     */
    public function reset (Request $request) {
        $request->validate([
            'password' => 'required|string',
            'token' => 'required|string',
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->first();

         if (!$passwordReset) {
              return Response::json(['error' => 'O Token não existe.'], 404);
         }

         if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()){
            $passwordReset->forceDelete();
            return Response::json(['error' => 'O Token expirou.'], 404);
         }

         $user = User::where('email', $passwordReset->email)->first();

         if (!$user) {
              return Response::json(['error' => 'O utilizador não existe.'], 404);
         }

         $user->password = Hash::make($request->password);
         $user->save();
         $passwordReset->forceDelete();
         return new UserResource($user);
    }

    /**
     * @OA\Put(
     *      path="/api/password/{id}",
     *      operationId="Update user password by id",
     *      tags={"Authentication"},
     *      summary="Update user password by id",
     *      description="Update user password by id",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="newPassword",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="changes the actual user password"
     *       )
     *     )
     */
    public function update (Request $request, $id) {
        $request->validate([
            'newPassword' => 'required|string',
            'password' => 'required|string|different:newPassword',
        ]);

        $user=User::findOrFail($id);

        if(Auth::guard('api')->user()->id != $user->id){
            return Response::json(['error' => 'Access forbidden!'], 401);
        }

        if (!(Hash::check($request->input('password'), $user->password))) {
            return Response::json(['error' => 'Insere a password correta'], 422);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();
        return new UserResource($user);
    }

    /**
     * @OA\Put(
     *      path="/api/email/{id}",
     *      operationId="Update user email by id",
     *      tags={"Authentication"},
     *      summary="Update user email by id",
     *      description="Update user email by id",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="newEmail",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="changes the actual user email"
     *       )
     *     )
     */
    public function updateEmail (Request $request, $id) {
        $request->validate([
            'newEmail' => 'required|email',
            'email' => 'required|email|different:newEmail',
        ]);

        $user=User::findOrFail($id);

        if(Auth::guard('api')->user()->id != $user->id){
            return Response::json(['error' => 'Access forbidden!'], 401);
        }

        if ($request->email != $user->email) {
            return Response::json(['error' => 'Insere o email correcto'], 422);
        }

        $user->email = $request->newEmail;
        $user->save();
        return new UserResource($user);
    }
}
