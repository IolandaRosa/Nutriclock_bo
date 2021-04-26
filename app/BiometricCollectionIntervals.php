<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BiometricCollectionIntervals extends Model
{
    protected $fillable = [ 'id', 'collectionId', 'hour' ];
}
