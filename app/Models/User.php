<?php

namespace App\Models;

#use Illuminate\Contracts\Auth\MustVerifyEmail;
#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
#use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    //use HasApiTokens, Notifiable;
	#use HasApiTokens, HasFactory, Notifiable;
	
	protected $table = 'user_tb';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_real_name',
        'user_name',
        'user_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
	 /**/
    protected $casts = [
        'InputUsername' => 'user_name',
    ];
	
}
