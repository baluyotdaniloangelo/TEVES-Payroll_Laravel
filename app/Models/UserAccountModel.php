<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class UserAccountModel extends Model
{
 	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'user_tb';
	
	protected $fillable = [
        'user_real_name',
		'user_job_title',
        'user_name',
        'user_password',
		'user_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'user_id';
	
	protected static $logName = 'User Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'user_real_name',
		'user_job_title',
        'user_name',
        'user_password',
		'user_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];		
}
