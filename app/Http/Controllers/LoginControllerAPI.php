<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
define('SERVER_URL', 'http://nutriclock.test:81');
// define('SERVER_URL', 'https://nutriclock.herokuapp.com');
define('CLIENT_ID', '2');
define('CLIENT_SECRET', 'Duc2Pacdpd2C3IrSt2rx1gIDANlk8VaTk5RJOozd');

class LoginControllerAPI extends Controller
{
    /**
     * @OA\Info(title="Nutriclock API", version="0.1")
     */

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Authetication"},
     *      summary="Login user in app",
     *      description="Login user in app",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return access token"
     *       )
     *     )
     */
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->active || $user->requestForget) {
            return response()->json(['error' => 'Utilizador inativo.', 'active' => $user,], 400);
        }

        $response = $http->post(SERVER_URL . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => ''
            ],
            'exceptions' => false,
        ]);

        $errorCode = $response->getStatusCode();

        if ($errorCode == 200) {
            return json_decode((string)$response->getBody(), true);
        } else {
            return response()->json(['error' => 'Credenciais inválidas.'], $errorCode);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Authetication"},
     *      summary="Logout user in app",
     *      description="Logout user in app",
     *      @OA\Response(
     *          response=200,
     *          description="logout"
     *       )
     *     )
     */
    public function logout(Request $request)
    {
        \Auth::guard('api')->user()->token()->revoke();
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg' => 'Token revoked'], 200);
    }
}
