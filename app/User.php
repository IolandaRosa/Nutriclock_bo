<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\UsersUfc;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
              'name', 'gender', 'weight', 'height', 'email', 'password', 'role', 'active', 'avatarUrl', 'birthday', 'professionalCategoryId', 'diseases', 'ufc_id', 'terms_accepted'
          ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'datetime',
        'ufcs' => 'array',
    ];

    public function ufcs()
    {
        return $this->hasMany(UsersUfc::class, 'user_id');
    }
}