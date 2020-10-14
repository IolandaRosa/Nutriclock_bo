<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ufc extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'name' ];
}
