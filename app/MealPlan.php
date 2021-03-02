<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    protected $fillable = [ 'id', 'planId', 'date', 'dayOfWeek'];
}
