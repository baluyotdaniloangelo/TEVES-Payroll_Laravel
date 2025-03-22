<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_payroll_employee_logs';

	protected $fillable = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'attendance_date',
		'override_shift',
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
		'holiday_idx',
		'holiday_type',
		'log_type',
		'basic_pay',
		'overtime_pay',
		'day_off_pay',
		'night_differential_pay',
		'regular_holiday_pay',
		'special_holiday_pay',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'employee_logs_id';
	
	protected static $logName = 'Employee Regular Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'attendance_date',
		'override_shift',
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
		'holiday_idx',
		'holiday_type',
		'log_type',
		'basic_pay',
		'overtime_pay',
		'day_off_pay',
		'night_differential_pay',
		'regular_holiday_pay',
		'special_holiday_pay',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
}
