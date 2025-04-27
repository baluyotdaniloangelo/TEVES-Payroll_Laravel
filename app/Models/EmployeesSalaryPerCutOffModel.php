<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class EmployeesSalaryPerCutOffModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_payroll_employee_salary_per_cut_off';
	
	protected $fillable = [
		'employee_idx',
        'branch_idx',
		'cutoff_idx',
		'hourly_rate',
		'daily_rate',
		'employment_type',
		'basic_pay_total',
		'regular_overtime_pay_total',
		'day_off_pay_total',
		'night_differential_pay_total',
		'regular_holiday_pay_total',
		'special_holiday_pay_total',
		'count_days',
		'deduction_amount_total',
		'allowance_amount_total',
		'leave_logs_count',
		'leave_amount_pay_total',
		'gross_salary',
		'net_salary',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'employee_salary_id';
	
	protected static $logName = 'Employee Salary per Cut-Off Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'employee_idx',
        'branch_idx',
		'cutoff_idx',
		'hourly_rate',
		'daily_rate',
		'employment_type',
		'basic_pay_total',
		'regular_overtime_pay_total',
		'day_off_pay_total',
		'night_differential_pay_total',
		'regular_holiday_pay_total',
		'special_holiday_pay_total',
		'count_days',
		'deduction_amount_total',
		'allowance_amount_total',
		'leave_logs_count',
		'leave_amount_pay_total',
		'gross_salary',
		'net_salary',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];	
}
