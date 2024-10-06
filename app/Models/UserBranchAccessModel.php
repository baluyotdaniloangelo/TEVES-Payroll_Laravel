<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBranchAccessModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_user_branch_access';
	
	protected $fillable = [
        'user_idx',
        'branch_idx',
    ];
    
}
