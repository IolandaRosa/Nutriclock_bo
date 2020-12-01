<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sleep extends Model
{
    protected $fillable = ['date', 'userId', 'wakeUpTime', 'sleepTime', 'hasWakeUp', 'activities', 'motives'];
}
