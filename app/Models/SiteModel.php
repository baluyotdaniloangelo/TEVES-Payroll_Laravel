<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_payroll_employee_table';
	
	protected $fillable = [
        'employee_number',
        'employee_name',
        'employee_birthday',
		'employee_position',
		'employee_picture',
		'branch_idx',
		'department_idx',
		'time_in',
		'break_time',
		'time_out',
		'restday_monday',
		'restday_tuesday',
		'restday_wednesday',
		'restday_thursday',
		'restday_friday',
		'restday_saturday',
		'restday_sunday',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'employee_id';
	
	protected static $logName = 'Employee Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'employee_number',
        'employee_name',
        'employee_birthday',
		'employee_position',
		'employee_picture',
		'branch_idx',
		'department_idx',
		'time_in',
		'break_time',
		'time_out',
		'restday_monday',
		'restday_tuesday',
		'restday_wednesday',
		'restday_thursday',
		'restday_friday',
		'restday_saturday',
		'restday_sunday',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
