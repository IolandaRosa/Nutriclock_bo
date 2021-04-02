<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// define('SERVER_URL', 'http://nutriclock.test:81');
define('SERVER_URL', 'https://nutriclock.herokuapp.com');
define('CLIENT_ID', '2');
define('CLIENT_SECRET', 'Duc2Pacdpd2C3IrSt2rx1gIDANlk8VaTk5RJOozd');

class LoginControllerAPI extends Controller
{
    public function login(Request $request) {
        $http = new \GuzzleHttp\Client;

        $response = $http->post(SERVER_URL.'/oauth/token', [
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
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json(['error'=>'Credenciais inválidas.'], $errorCode);
        }
    }

    public function logout(Request $request) {
        \Auth::guard('api')->user()->token()->revoke();
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg'=>'Token revoked'], 200);
    }
}
