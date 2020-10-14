<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Ufc;

class UsersUfc extends Model
{
    protected $table = 'users_ufc';
    protected $fillable = ['user_id', 'ufc_id'];
}
