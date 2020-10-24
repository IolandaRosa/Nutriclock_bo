<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [ 'name', 'quantity', 'date', 'time', 'foodPhotoUrl', 'nutritionalInfoPhotoUrl', 'type', 'relativeUnit', 'numericUnit', 'observations', 'userId' ];
    protected $casts = [ 'date' => 'datetime'];
}
