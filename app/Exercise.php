<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [ 'name', 'startTime', 'endTime', 'duration', 'burnedCalories', 'met', 'date', 'userId', 'exerciseId', 'type'];
}
