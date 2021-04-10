<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [ 'userId', 'notificationsSleep', 'notificationsExercise', 'notificationsMealDiary' ];
}
