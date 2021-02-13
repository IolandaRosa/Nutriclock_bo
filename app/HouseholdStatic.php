<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HouseholdStatic extends Model
{
    protected $table = 'households_static';
    protected $fillable = [ 'name', 'met'];
}
