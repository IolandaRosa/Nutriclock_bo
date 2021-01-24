<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sleep extends Model
{
    protected $fillable = ['date', 'userId', 'wakeUpTime', 'sleepTime',
        'hasWakeUp', 'activities', 'motives'];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    // protected $appends = ['totalSleepHours'];

    public function user()
    {
        return $this->belongsTo('App\User','userId','id');
    }

    /*public function getTotalSleepHoursAttribute() {
        $w = SleepControllerAPI::computeTimeInHours($this->wakeUpTime);
        $s = SleepControllerAPI::computeTimeInHours($this->sleepTime);
        return abs($s - $w);
    }*/
}
