<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseStatic extends Model
{
    protected $table = 'exercises_static';
    protected $fillable = [ 'name', 'met'];
}
