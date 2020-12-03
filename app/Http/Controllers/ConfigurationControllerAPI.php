<?php

namespace App\Http\Controllers;

use App\Configuration;
use App\Http\Resources\Configuration as ConfigurationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ConfigurationControllerAPI extends Controller
{
    public function index()
    {
        return ConfigurationResource::collection(Configuration::all());
    }

    public function updateTipEnabled(Request $request)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'value' => 'required',
        ]);

        $configuration = Configuration::where('key', 'SLEEP_TIP_ENABLED')->first();

        if (!$configuration) {
            return Response::json(['error' => 'A configuração não existe!'], 404);
        }

        $configuration->value = $request->input('value');
        $configuration->save();
        return new ConfigurationResource($configuration);
    }
}
