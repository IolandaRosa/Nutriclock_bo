<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BiometricCollections extends Model
{
    protected $fillable = [ 'id', 'name', 'date', 'biometric_group_id' ];
}
