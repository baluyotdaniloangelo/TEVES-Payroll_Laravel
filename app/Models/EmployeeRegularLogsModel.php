<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeRegularLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_employee_regular_logs';
	
	protected $fillable = [
        'employee_idx',
        'log_in',
        'breaktime_start',
        'breaktime_end',
        'log_out',
        'total_hours',
		'total_regular_hours',
		'total_breaktime_hours',
		'total_tardiness_hours',
		'total_undertime_hours',
		'total_night_differential_hours',
		'holiday_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'regular_logs_id';
	
	protected static $logName = 'Employee Regular Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'employee_idx',
        'log_in',
        'breaktime_start',
        'breaktime_end',
        'log_out',
        'total_hours',
		'total_regular_hours',
		'total_breaktime_hours',
		'total_tardiness_hours',
		'total_undertime_hours',
		'total_night_differential_hours',
		'holiday_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
