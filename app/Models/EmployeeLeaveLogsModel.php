<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeLeaveLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_payroll_employee_leave_logs';

	protected $fillable = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'date_of_leave',
		'reason_of_leave',
		'leave_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'employee_leave_logs_id';
	
	protected static $logName = 'Employee Leave Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'date_of_leave',
		'reason_of_leave',
		'leave_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
}
