<?php

namespace App\Jobs;

use App\BiometricCollectionIntervals;
use App\BiometricCollections;
use App\Exercise;
use App\Meal;
use App\Notification;
use App\Notifications\FCMNotification;
use App\Sleep;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Notifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    public function handle()
    {
        $hour = date("H:i");
        $users = User::all();

        if ($users) {
            foreach ($users as $u) {
                if ($u->fcmToken) {
                    $notifications = Notification::where('userId', $u->id)->first();
                    if ($notifications) {
                        if ($notifications->notificationsBiometric) {
                            $collectionsDates = BiometricCollections::all();
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
                                                $u->notify(new FCMNotification($u->fcmToken, 'Recolha de Saliva', 'Prepare-se para realizar a próxima recolha de saliva às ' . $intervalHour . ' horas.'));
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($hour > '12:00' && $hour < '12:20') {
                            if ($notifications->notificationsSleep) {
                                $sleep = Sleep::where('userId', $u->id)->orderBy('date', 'desc')->first('date');

                                if ($sleep && $sleep->date) {
                                    $dateParts = explode('/', $sleep->date);
                                    $now = time();
                                    $sleepDate = strtotime($dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]);
                                    $sleepDays = round(($now - $sleepDate) / (60 * 60 * 24));
                                    if ($sleepDays > 3) {
                                        $u->notify(new FCMNotification($u->fcmToken, 'Diário do Sono', 'Já não efetua um registo de sono desde o dia ' . $sleep->date . '. Por favor atualize o Diário de Sono.'));
                                    }
                                }
                            }

                            if ($notifications->notificationsExercise) {
                                $exercise = Exercise::where('userId', $u->id)->orderBy('date', 'desc')->first('date');
                                $exerciseDateParts = explode('T', $exercise->date);
                                $exerciseParts = explode('-', $exerciseDateParts[0]);
                                $dateE = $exerciseParts[2] . '-' . $exerciseParts[1] . '-' . $exerciseParts[0];

                                if ($exercise) {
                                    $now = time();
                                    $exerciseDate = strtotime($exercise->date);
                                    $exerciseDays = round(($now - $exerciseDate) / (60 * 60 * 24));
                                    if ($exerciseDays > 3) {
                                        $u->notify(new FCMNotification($u->fcmToken, 'Atividade física', 'Já não efetua um registo de atividade física desde o dia ' . $dateE . '. Por favor atualize a informação.'));
                                    }
                                }
                            }

                            if ($notifications->notificationsMealDiary) {
                                $mealDiary = Meal::where('userId', $u->id)->orderBy('date', 'desc')->first('date');

                                if ($mealDiary) {
                                    $now = time();
                                    $mealDiaryDate = strtotime($mealDiary->date);
                                    $mealDiaryDays = round(($now - $mealDiaryDate) / (60 * 60 * 24));
                                    if ($mealDiaryDays > 0 && $mealDiaryDays < 2) {
                                        $u->notify(new FCMNotification($u->fcmToken, 'Diário Alimentar', 'Não se esqueça de registar todas assuas refeições ao longo do dia'));
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
