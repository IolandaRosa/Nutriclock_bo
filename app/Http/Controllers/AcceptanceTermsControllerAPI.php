<?php

namespace App\Http\Controllers;

use App\AcceptanceTerms;
use App\Http\Resources\AcceptanceTerms as AcceptanceTermsResource;
use App\User;
use Illuminate\Http\Request;

class AcceptanceTermsControllerAPI extends Controller
{
    public function getTerms() {
        $version = AcceptanceTerms::max('version');

        $terms = AcceptanceTerms::where('version', $version)->first();

        if (!$terms) {
            return Response::json(['error' => 'Os termos não existem.'], 404);
        }

        return new AcceptanceTermsResource($terms);
    }

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
