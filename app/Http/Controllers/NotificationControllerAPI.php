<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\Http\Resources\Notification as NotificationResource;
use App\Meal;
use App\Notification;
use App\Sleep;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class NotificationControllerAPI extends Controller
{
    public function store(Request $request)
    {
        $user = User::find(Auth::guard('api')->id());

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $notification = Notification::where('userId', $user->id)->first();

        if (!$notification) {
            $notification = $this->createNewNotification($user->id);
        } else {
            $notification->notificationsSleep = $request->notificationsSleep;
            $notification->notificationsExercise = $request->notificationsExercise;
            $notification->notificationsMealDiary = $request->notificationsMealDiary;
        }

        $notification->save();
        return new NotificationResource($notification);
    }

    public function show()
    {
        $user = User::find(Auth::guard('api')->user()->id);

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $notification = Notification::where('userId', $user->id)->first();

        if (!$notification) {
            $notification = $this->createNewNotification($user->id);
            $notification->save();
        }

        return new NotificationResource($notification);
    }

    private function createNewNotification($id)
    {
        $notification = new Notification();
        $notification->userId = $id;
        $notification->notificationsSleep = true;
        $notification->notificationsExercise = true;
        $notification->notificationsMealDiary = true;
        return $notification;
    }
}
