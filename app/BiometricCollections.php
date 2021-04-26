<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BiometricCollections extends Model
{
    protected $fillable = [ 'id', 'orderNumber', 'name', 'date' ];
}
