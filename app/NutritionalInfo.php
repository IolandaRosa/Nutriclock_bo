<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NutritionalInfo extends Model
{
    protected $fillable = [ 'label', 'value', 'unit', 'mealId' ];
}
