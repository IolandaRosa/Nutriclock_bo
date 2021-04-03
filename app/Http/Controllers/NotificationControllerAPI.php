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
    public function getUserNotifications()
    {
        $user = User::find(Auth::guard('api')->user()->id);
        $lastSleepDate = null;
        $sleepDays = null;
        $lastExerciseDate = null;
        $exerciseDays = null;
        $mealDiaryConfirm = false;

        if (!$user) {
            return Response::json(['error' => 'O utilizador não existe!'], 400);
        }

        $notification = Notification::where('userId', $user->id)->first();

        if (!$notification) {
            return Response::json(['error' => 'Este utilizador não tem notificações'], 400);
        }

        if ($notification->notificationsSleep) {
            $sleep = Sleep::where('userId', $user->id)->orderBy('date', 'desc')->first('date');

            if ($sleep) {
                $dateParts = explode('/', $sleep->date);
                $now = time();
                $sleepDate = strtotime($dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]);
                $sleepDays = round(($now - $sleepDate) / (60 * 60 * 24));
                if ($sleepDays > 3) {
                    $lastSleepDate = $sleep->date;
                }
            }
        }

        if ($notification->notificationsExercise) {
            $exercise = Exercise::where('userId', $user->id)->orderBy('date', 'desc')->first('date');

            if ($exercise) {
                $now = time();
                $exerciseDate = strtotime($exercise->date);
                $exerciseDays = round(($now - $exerciseDate) / (60 * 60 * 24));
                if ($exerciseDays > 3) {
                    $lastExerciseDate = $exercise->date;
                }
            }
        }

        if ($notification->notificationsMealDiary) {
            $mealDiary = Meal::where('userId', $user->id)->orderBy('date', 'desc')->first('date');

            if ($mealDiary) {
                $now = time();
                $mealDiaryDate = strtotime($mealDiary->date);
                $mealDiaryDays = round(($now - $mealDiaryDate) / (60 * 60 * 24));
                if ($mealDiaryDays > 0 && $mealDiaryDays < 2) {
                    $mealDiaryConfirm = true;
                }
            }
        }

        return Response::json([
            'sleepDate' => $lastSleepDate,
            'sleepDays' => $sleepDays,
            'exerciseDate' => $lastExerciseDate,
            'exerciseDays' => $exerciseDays,
            'mealDiaryConfirm' => $mealDiaryConfirm
        ]);
    }

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
