<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalCategory extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'name' ];
}
