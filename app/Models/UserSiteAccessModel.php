<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSiteAccessModel extends Model
{
    //use HasFactory;
	protected $table = 'user_access_group';
	
	protected $fillable = [
        'user_idx',
        'site_idx',
    ];
    
}
