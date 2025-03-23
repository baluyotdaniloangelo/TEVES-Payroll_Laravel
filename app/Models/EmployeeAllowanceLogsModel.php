<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeAllowanceLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_payroll_employee_allowance_logs';

	protected $fillable = [
        'allowance_idx',
        'employee_idx',
		'branch_idx',
		'allowance_date',
		'allowance_description',
		'allowance_type',
		'allowance_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'allowance_logs_id';
	
	protected static $logName = 'Employee Allowance Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'allowance_idx',
        'employee_idx',
		'branch_idx',
		'allowance_date',
		'allowance_description',
		'allowance_type',
		'allowance_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
}
