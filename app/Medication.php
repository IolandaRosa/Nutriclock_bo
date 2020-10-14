<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = ['timesAWeek', 'name', 'timesADay', 'user_id', 'posology'];
}
