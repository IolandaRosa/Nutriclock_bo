<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Medication as MedicationResource;
use App\Medication;
use App\User;

class MedicationControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MedicationResource::collection(Medication::all());
    }

    public function getMedicationByUser(Request $request, $id) {
        $medication = Medication::where('user_id', $id)->get();

        return MedicationResource::collection($medication);
    }

    public function getMedication (Request $request, $id) {
        $med = Medication::find($id);

        if (!$med) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        return new MedicationResource($med);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $user=User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $medication = new Medication();

        $medication->name = $request->name;
        $medication->timesAWeek = $request->timesAWeek;
        $medication->timesADay = $request->timesADay;
        $medication->user_id = $id;
        $medication->posology = $request->posology;
        $medication->type = $request->type;

        $medication->save();

        return new MedicationResource($medication);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'posology' => 'required|integer',
        ]);
        $medication=Medication::findOrFail($id);

        if (!$medication) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        $medication->name = $request->name;
        $medication->timesAWeek = $request->timesAWeek;
        $medication->timesADay = $request->timesADay;
        $medication->posology = $request->posology;
        $medication->type = $request->type;
        $medication->update();
        return new MedicationResource($medication);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medication=Medication::findOrFail($id);

        if (!$medication) {
            return Response::json(['error' => 'O medicamento não existe.'], 404);
        }

        $medication->forceDelete();

        return new MedicationResource($medication);
    }
}
