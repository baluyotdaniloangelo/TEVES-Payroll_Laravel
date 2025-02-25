<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeeDeductionLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_employee_deduction_logs';

	protected $fillable = [
        'deduction_idx',
        'employee_idx',
		'branch_idx',
		'deduction_date',
		'deduction_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'deduction_logs_id';
	
	protected static $logName = 'Employee Deduction Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'deduction_idx',
        'employee_idx',
		'branch_idx',
		'deduction_date',
		'deduction_amount',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
}
