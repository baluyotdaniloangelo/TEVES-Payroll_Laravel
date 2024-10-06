<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccountModel extends Model
{
    //use HasFactory;
	protected $table = 'user_tb';
	
	protected $fillable = [
        'user_real_name',
		'user_job_title',
        'user_name',
        'user_password',
		'user_type',
		'created_at',
		'updated_at'
    ];
    
	protected $primaryKey = 'user_id';
	
}
