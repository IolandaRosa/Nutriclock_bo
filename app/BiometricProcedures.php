<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BiometricProcedures extends Model
{
    protected $fillable = [ 'id', 'orderNumber', 'value' ];
}
