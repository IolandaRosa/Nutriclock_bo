<?php

namespace App\Http\Controllers;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use App\Exercise;
use App\Http\Resources\User as UserResource;
use App\Ingredient;
use App\Meal;
use App\MealPlan;
use App\MealPlanType;
use App\Medication;
use App\Message;
use App\Notification;
use App\Notifications\FCMNotification;
use App\NutritionalInfo;
use App\Plan;
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
    /**
     * @OA\Get(
     *      path="/api/users",
     *      operationId="users",
     *      tags={"Users"},
     *      summary="Return this list of users in app",
     *      description="Return this list of users in app",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of users from app"
     *       )
     *     )
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * @OA\Get(
     *      path="/api/admin/users",
     *      operationId="admin users",
     *      tags={"Users"},
     *      summary="Return this list of backoffice users in app",
     *      description="Return this list of backoffice users in app",
     *      @OA\Response(
     *          response=200,
     *          description="return the list of backoffice users from app"
     *       )
     *     )
     */
    public function getAdminUsers()
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $users = User::where('id', '!=', Auth::guard('api')->user()->id)->where('role', '!=', 'PATIENT')->withTrashed()->get();
        return UserResource::collection($users);
    }

    /**
     * @OA\Post(
     *      path="/api/users/register",
     *      operationId="register backoffice user",
     *      tags={"Users"},
     *      summary="Creates new backoffice user",
     *      description="Creates new backoffice user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="usfIds",
     *                     type="array",
     *                     @OA\Items(type="number")
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Get(
     *      path="/api/users/me",
     *      operationId="auth users",
     *      tags={"Users"},
     *      summary="Return auth user",
     *      description="Return auth user",
     *      @OA\Response(
     *          response=200,
     *          description="return auth user"
     *       )
     *     )
     */
    public function getAuthenticatedUser(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * @OA\Get(
     *      path="/api/users/{id}",
     *      operationId="return user by id",
     *      tags={"Users"},
     *      summary="Return user by id",
     *      description="Return user by id",
     *      @OA\Parameter(
     *         description="ID of user to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function show(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *      path="/api/users",
     *      operationId="register a patient",
     *      tags={"Users"},
     *      summary="Creates new patient",
     *      description="Creates new patient",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="acceptTerms",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="diseases",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="birthday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="ufc_id",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="height",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="weight",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="drugs",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     description="avatar to upload",
     *                     property="avatar",
     *                     type="string",
     *                     format="file",
     *                 ),
     *                 required={"name", "email", "password", "gender"},
     *             )
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
            $image = $request->file('avatar');
            $path = basename($image->store('avatars', 's3'));
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

        $notification = new Notification();
        $notification->userId = $user->id;
        $notification->notificationsSleep = true;
        $notification->notificationsExercise = true;
        $notification->notificationsMealDiary = true;
        $notification->save();

        return new UserResource($user);
    }

    /**
     * @OA\Get(
     *      path="/api/patients",
     *      operationId="patients",
     *      tags={"Users"},
     *      summary="Return list of patients",
     *      description="Return list of patients",
     *      @OA\Response(
     *          response=200,
     *          description="return list patients"
     *       )
     *     )
     */
    public function getPatients(Request $request)
    {
        $users = [];

        switch (Auth::guard('api')->user()->role) {
            case 'ADMIN':
                $users = User::where('role', 'PATIENT')->withTrashed()->get();
                break;
            case 'INTERN':
                $users = User::where('role', 'PATIENT')->where('active', true)->where('requestForget', false)->get();
                break;
            case 'PROFESSIONAL':
                if (count($request->ufcsIds) > 0) {
                    $users = User::where('role', 'PATIENT')->where('active', true)->where('requestForget', false)->whereIn('ufc_id', $request->ufcsIds)->get();
                }
        }

        return UserResource::collection($users);
    }

    /**
     * @OA\Delete(
     *      path="/api/patients/{id}",
     *      operationId="patients",
     *      tags={"Users"},
     *      summary="Delete patient",
     *      description="Delte patient",
     *     @OA\Parameter(
     *         description="ID of user to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return patient deleted"
     *       )
     *     )
     */
    public function deletePatient($id)
    {
        if (Auth::guard('api')->user()->role != 'ADMIN') {
            return Response::json(['error' => 'Accesso proibido!'], 401);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $plans = Plan::where('userId', $user->id)->get();
        $meals = Meal::where('userId', $user->id)->get();
        Sleep::where('userId', $user->id)->forceDelete();
        Message::where('senderId', $user->id)->forceDelete();
        Message::where('receiverId', $user->id)->forceDelete();
        Medication::where('user_id', $user->id)->forceDelete();
        Notification::where('userId', $user->id)->forceDelete();

        if ($meals) {
            foreach ($meals as $meal) {
                NutritionalInfo::where('mealId', $meal->id)->forceDelete();
                $meal->forceDelete();
            }
        }

        if ($plans) {
            foreach ($plans as $plan) {
                $mealPlans = MealPlan::where('planId', $plan->id())->get();
                if ($mealPlans) {
                    foreach ($mealPlans as $mealPlan) {
                        $mealPlanTypes = MealPlanType::where('planMealId', $mealPlan->id)->get();
                        if ($mealPlanTypes) {
                            foreach ($mealPlanTypes as $mealPlanType) {
                                Ingredient::where('mealPlanTypeId', $mealPlanType->id)->forceDelete();
                                $mealPlanType->forceDelete();
                            }
                        }
                        $mealPlan->forceDelete();
                    }
                }
                $plan->forceDelete();
            }
        }

        if ($user->avatarUrl) {
            Storage::disk('s3')->delete('avatars/' . $user->avatarUrl);
        }

        $user->forceDelete();

        return new UserResource($user);
    }

    // todo delete extra endpoint

    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      operationId="users",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Delete user",
     *     @OA\Parameter(
     *         description="ID of user to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user deleted"
     *       )
     *     )
     */
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
        }

        UsersUfc::where('user_id', $user->id)->forceDelete();
        Message::where('senderId', $user->id)->forceDelete();
        Message::where('receiverId', $user->id)->forceDelete();

        $user->forceDelete();

        return new UserResource($user);
    }

    /**
     * @OA\Delete(
     *      path="/api/users/{id}/status",
     *      operationId="users",
     *      tags={"Users"},
     *      summary="Toggle user active status",
     *      description="Toggle user active status",
     *      @OA\Parameter(
     *         description="ID of user to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user toggled"
     *       )
     *     )
     */
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

    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      operationId="update backoffice user",
     *      tags={"Users"},
     *      summary="Update backoffice user",
     *      description="Update backoffice user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="usfIds",
     *                     type="array",
     *                     @OA\Items(type="number")
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Put(
     *      path="/api/users/profile/{id}",
     *      operationId="update profile of a backoffice user",
     *      tags={"Users"},
     *      summary="Updates profile of a backoffice user",
     *      description="Updates profile of a backoffice user",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     description="avatar to upload",
     *                     property="avatar",
     *                     type="string",
     *                     format="file",
     *                 ),
     *                 required={"name", "email", "gender"},
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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
            $image = $request->file('avatar');
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

    /**
     * @OA\Put(
     *      path="/api/users/terms/{id}",
     *      operationId="update user terms",
     *      tags={"Users"},
     *      summary="Update user terms",
     *      description="Update user terms",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="terms_accepted",
     *                     type="boolean"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Post(
     *      path="/api/users/avatar",
     *      operationId="update patient avatar",
     *      tags={"Users"},
     *      summary="Updates patient avatar",
     *      description="Updates patient avatar",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="avatar to upload",
     *                     property="avatar",
     *                     type="string",
     *                     format="file",
     *                 ),
     *                 required={"avatar"},
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Post(
     *      path="/api/users/profile",
     *      operationId="update patient profile",
     *      tags={"Users"},
     *      summary="Update patient profile",
     *      description="Update patient profile",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="weight",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="height",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="birthday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Post(
     *      path="/api/users/diseases",
     *      operationId="update patient diseases",
     *      tags={"Users"},
     *      summary="Update patient diseases",
     *      description="Update patient diseases",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="diseases",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
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

    /**
     * @OA\Get(
     *      path="/api/professionalsByUsf/{id}",
     *      operationId="professionals by usf",
     *      tags={"Users"},
     *      summary="Return list of professionals by usf",
     *      description="Return list of professionals by usf",
     *      @OA\Parameter(
     *         description="ID of usf",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return list users"
     *       )
     *     )
     */
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

    /**
     * @OA\Get(
     *      path="/api/forgot-me",
     *      operationId="Forget user",
     *      tags={"Users"},
     *      summary="Update request forget parameter",
     *      description="Update request forget parameter",
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function forgetUserData()
    {
        $user = User::find(Auth::guard('api')->user()->id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $user->requestForget = true;
        $user->save();

        return new UserResource($user);
    }

    /**
     * @OA\Get(
     *      path="/api/forgot-me-count",
     *      operationId="Forget user requests count",
     *      tags={"Users"},
     *      summary="Forget user requests count",
     *      description="Forget user requests count",
     *      @OA\Response(
     *          response=200,
     *          description="return total user forget requests"
     *       )
     *     )
     */
    public function countForgetUserData()
    {
        $count = 0;
        if (Auth::guard('api')->user()->role == 'ADMIN') {
            $count = User::where('requestForget', true)->count();
        }

        return Response::json(['data' => $count]);
    }

    /**
     * @OA\Get(
     *      path="/api/undo-forgot/{id}",
     *      operationId="Update user forget data",
     *      tags={"Users"},
     *      summary="Update user forget data",
     *      description="Update user forget data",
     *      @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function undoForgot($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $user->requestForget = false;
        $user->save();

        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *      path="/api/fcm",
     *      operationId="Update FCM token",
     *      tags={"Users"},
     *      summary="Update FCM token",
     *      description="Update FCM token",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="fcmToken",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function fcmToken(Request $request)
    {
        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $user->fcmToken = $request->fcmToken;
        $user->save();
        return new UserResource($user);
    }

    /**
     * @OA\Put(
     *      path="/api/users/{id}/nutriclock-group",
     *      operationId="Update nutriclock group",
     *      tags={"Users"},
     *      summary="Update nutriclock group",
     *      description="Update nutriclock group",
     *      @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="nutriclockGroup",
     *                     type="boolean"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="return user"
     *       )
     *     )
     */
    public function updateNutriclockGroup(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe.'], 404);
        }

        $user->nutriclockGroup = $request->nutriclockGroup;
        $user->save();
        return new UserResource($user);
    }

    public function test() {
        $hour = date("H:i");
        $users = User::all();

        $notificationsArray = [];

        if ($users) {
            foreach ($users as $u) {
                if ($u->fcmToken) {
                    $notifications = Notification::where('userId', $u->id)->first();
                    if ($notifications) {
                        if ($hour > date('H:i', strtotime('14:25')) && $hour < date('H:i', strtotime('15:30'))) {
                            if ($notifications->notificationsSleep) {
                                $sleep = Sleep::where('userId', $u->id)->orderBy('date', 'desc')->first('date');

                                if (!$sleep) {
                                    array_push($notificationsArray, 'Sono esta vazio '.$u->email.' id '.$u->id);
                                } else if ($sleep->date) {
                                    $dateParts = explode('/', $sleep->date);
                                    $now = time();
                                    $sleepDate = strtotime($dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]);
                                    $sleepDays = round(($now - $sleepDate) / (60 * 60 * 24));
                                    if ($sleepDays > 3) {
                                        array_push($notificationsArray, 'Notificacao de sono '.$u->email.' id '.$u->id);
                                    }
                                }
                            }

                            if ($notifications->notificationsExercise) {
                                $exercise = Exercise::where('userId', $u->id)->orderBy('date', 'desc')->first('date');

                                if ($exercise && $exercise->date) {
                                    $exerciseDateParts = explode('T', $exercise->date);
                                    $exerciseParts = explode('-', $exerciseDateParts[0]);
                                    $dateE = $exerciseParts[2] . '-' . $exerciseParts[1] . '-' . $exerciseParts[0];
                                    $now = time();
                                    $exerciseDate = strtotime($exercise->date);
                                    $exerciseDays = round(($now - $exerciseDate) / (60 * 60 * 24));
                                    if ($exerciseDays > 3) {
                                        array_push($notificationsArray, 'Notificacao de exercicio '.$u->email.' id '.$u->id);
                                    }
                                }
                            }

                            if ($notifications->notificationsMealDiary) {
                                $mealDiary = Meal::where('userId', $u->id)->orderBy('date', 'desc')->first('date');

                                if (!$mealDiary) {
                                    array_push($notificationsArray, 'O diario alimentar esta vazio '.$u->email.' id '.$u->id);
                                } else {
                                    $now = time();
                                    $mealDiaryDate = strtotime($mealDiary->date);
                                    $mealDiaryDays = round(($now - $mealDiaryDate) / (60 * 60 * 24));
                                    if ($mealDiaryDays > 0 && $mealDiaryDays < 2) {
                                        array_push($notificationsArray, 'Notificacao de alimentar '.$u->email.' id '.$u->id);
                                    }
                                }
                            }
                        }


                        if ($notifications->notificationsBiometric) {
                            $collectionsDates = BiometricCollections::where('biometric_group_id', $u->biometric_group_id)->get();
                            $today = date("d-m-Y");

                            foreach ($collectionsDates as $collection) {

                                if ($collection->date == $today) {
                                    $intervals = BiometricCollectionIntervals::where('collectionId', $collection->id)->get();

                                    foreach ($intervals as $interval) {
                                        $intervalHour = $interval->hour;

                                        for ($i = 0; $i < 7 ; $i++) {
                                            $minAdd = 10 + $i;
                                            $minMinus = 10 - $i;
                                            $intervalTimeMinus = date("H:i", strtotime($intervalHour . ' -1 hour' . ' -'.$minMinus.' minutes'));
                                            $intervalTimeAdd = date("H:i", strtotime($intervalHour . ' -1 hour' . ' -'.$minAdd.' minutes'));
                                            if ($intervalTimeMinus == $hour || $intervalTimeAdd == $hour) {
                                                array_push($notificationsArray, 'Notificacao de biometrica '.$u->email.' id '.$u->id);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return Response::json(['data' => $notificationsArray]);
    }
}
