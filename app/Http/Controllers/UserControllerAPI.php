<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;
use App\Meal;
use App\Medication;
use App\Message;
use App\NutritionalInfo;
use App\Sleep;
use App\User;
use App\UsersUfc;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Response;

class UserControllerAPI extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        return new UserResource($user);
    }

    public function updateProfessional(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $user->update($request->all());

        UsersUfc::where('user_id', $user->id)->delete();

        if ($request->ufcs) {
            foreach ($request->ufcs as $usf_id) {
                $usf = new UsersUfc();
                $usf->ufc_id = $usf_id;
                $usf->user_id = $user->id;
                $usf->save();
            }
        }
        return new UserResource($user);
    }

    public function updateProfessionalProfile(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email',
        ]);

        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        if (Auth::guard('api')->user()->id != $id) {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        if ($request->avatar != null) {
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
            // Storage::disk('public')->delete('avatars/'.$user->avatarUrl);
            $image = $request->file('avatar');
            // $path = basename($image->store('avatars', 'public'));
            $path = basename($image->store('avatars', 's3'));
            $user->avatarUrl = basename($path);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;

        $senderMessages = Message::where('senderId', $id)->get();
        $receiverMessages = Message::where('receiverId', $id)->get();


        if ($senderMessages) {
            foreach ($senderMessages as $m) {
                $m->senderName = $request->name;
                $m->senderPhotoUrl = $user->avatarUrl;
                $m->save();
            }
        }

        if ($receiverMessages) {
            foreach ($receiverMessages as $m) {
                $m->receiverName = $request->name;
                $m->receiverPhotoUrl = $user->avatarUrl;
                $m->save();
            }
        }

        $user->save();

        return new UserResource($user);
    }

    public function getAdminUsers()
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $users = User::where('id', '!=', Auth::guard('api')->user()->id)->where('role', '!=', 'PATIENT')->withTrashed()->get();
        return UserResource::collection($users);
    }

    public function getPatients(Request $request)
    {
        if (Auth::guard('api')->user()->role == 'PATIENT') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        if (count($request->ufcsIds) == 0) {
            $users = User::where('role', 'PATIENT')->get();
        } else {
            $users = User::where('role', 'PATIENT')->whereIn('ufc_id', $request->ufcsIds)->get();
        }

        return UserResource::collection($users);
    }


    public function registerUser(Request $request)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => Rule::in(['ADMIN', 'INTERN', 'PROFESSIONAL']),
        ], [
            'email.unique' => 'O email já existe.',
        ]);

        $user = new User();

        $user->fill(array_merge($request->all(), ['password' => '123']));
        $user->password = Hash::make($user->password);
        $user->gender = 'MALE';

        $user->save();

        foreach ($request->usfIds as $id) {
            UsersUfc::create([
                'user_id' => $user->id,
                'ufc_id' => $id
            ]);
        }

        return new UserResource($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png',
            'gender' => Rule::in(['MALE', 'FEMALE', 'NONE']),
        ]);

        $user = new User();

        if ($request->avatar != null) {
            $image = $request->file('avatar');
            $path = basename($image->store('avatars', 'public'));
            $user->avatarUrl = basename($path);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->gender = $request->gender;
        $user->role = 'PATIENT';
        $user->active = $request->acceptTerms;
        $user->diseases = $request->diseases;
        $user->birthday = $request->birthday;
        $user->ufc_id = $request->ufc_id;
        $user->height = $request->height;
        $user->weight = $request->weight;

        $user->save();

        if ($request->drugs) {
            $drugs = json_decode($request->drugs);
            foreach ($drugs as $drug) {
                $medication = new Medication();
                $medication->user_id = $user->id;
                $medication->name = $drug->name;
                $medication->posology = $drug->posology;
                $medication->type = $drug->type;

                if ($drug->timesAWeek != null) {
                    $medication->timesAWeek = $drug->timesAWeek;
                }

                if ($drug->timesADay != null) {
                    $medication->timesADay = $drug->timesADay;
                }

                $medication->save();
            }
        }

        return new UserResource($user);
    }

    public function activate(Request $request, $id)
    {
        $request->validate([
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'birthday' => 'required|date',
            'active' => 'required',
            'diseases' => 'nullable|string'
        ]);

        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $user->weight = $request->weight;
        $user->height = $request->height;
        $user->birthday = $request->birthday;
        $user->active = $request->active;
        $user->diseases = $request->diseases;

        $user->save();

        return new UserResource($user);
    }

    public function getAuthenticatedUser(Request $request)
    {
        return new UserResource($request->user());
    }

    public function toggleActive(Request $request, $id)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        if ($user->deleted_at === null) {
            $user->delete();
        } else {
            $user->restore();
        }

        return new UserResource($user);
    }

    public function updateAcceptanceTerms(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $user->terms_accepted = $request->terms_accepted;

        $user->save();

        return new UserResource($user);
    }

    public function destroy($id)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        if ($user->avatarUrl) {
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
            // Storage::disk('public')->delete('avatars/'.$user->avatarUrl);
        }

        UsersUfc::where('user_id', $user->id)->forceDelete();
        Message::where('senderId', $user->id)->forceDelete();
        Message::where('receiverId', $user->id)->forceDelete();

        $user->forceDelete();

        return new UserResource($user);
    }

    public function deletePatient($id)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $meals = Meal::where('userId', $user->id)->get();
        Sleep::where('userId', $user->id)->forceDelete();
        Message::where('senderId', $user->id)->forceDelete();
        Message::where('receiverId', $user->id)->forceDelete();

        if ($meals) {
            foreach ($meals as $meal) {
                NutritionalInfo::where('mealId', $meal->id)->forceDelete();
                $meal->forceDelete();
            }
        }

        if ($user->avatarUrl) {
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
            // Storage::disk('public')->delete('avatars/'.$user->avatarUrl);
        }

        $user->forceDelete();

        return new UserResource($user);
    }

    public function getProfessionalByUsf(Request $request, $id)
    {
        $users = [];
        $userIds = UsersUfc::where('ufc_id', $id)->get('user_id');

        if ($userIds) {
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                $unreadMessages = Message::where('senderId', $user->first()->id)->where('receiverId', Auth::guard('api')->user()->id)->where('read', 0)->count();

                if ($user && $user->first()->role === 'PROFESSIONAL') {
                    $user->first()->unreadMessages = $unreadMessages;
                    array_push($users, $user->first());
                }
            }
        }

        return Response::json(['data' => $users,], 200);
    }

    public function updateAvatar(Request $request)
    {
        $user = User::find(Auth::guard('api')->user()->id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        if ($user->avatarUrl) {
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
        }

        $image = $request->file('avatar');
        $path = basename($image->store('avatars', 's3'));
        $user->avatarUrl = basename($path);

        $user->save();

        return new UserResource($user);
    }

    public function updatePatientProfile(Request $request)
    {
        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $request->validate([
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'birthday' => 'required|date',
            'name' => 'required',
            'gender' => Rule::in(['MALE', 'FEMALE', 'NONE']),
        ]);

        $user->weight = $request->weight;
        $user->height = $request->height;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->name = $request->name;

        $user->save();

        return new UserResource($user);
    }

    public function updatePatientDiseases(Request $request)
    {
        $user = User::find(Auth::guard('api')->user()->id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $user->diseases = $request->diseases;

        $user->save();

        return new UserResource($user);
    }
}
