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

    public function getTipStatus() {
        $configuration = Configuration::where('key', 'SLEEP_TIP_ENABLED')->first();
        return new ConfigurationResource($configuration);
    }

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
