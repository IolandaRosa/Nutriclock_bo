<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [ 'id', 'code', 'name', 'quantity', 'unit', 'grams', 'mealPlanTypeId', 'description'];
}
