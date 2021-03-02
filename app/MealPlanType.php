<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MealPlanType extends Model
{
    protected $fillable = [ 'id', 'type', 'planMealId', 'portion', 'hour'];
}
