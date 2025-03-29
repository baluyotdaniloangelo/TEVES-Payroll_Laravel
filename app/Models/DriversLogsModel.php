<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class DriversLogsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_payroll_drivers_logs';

	protected $fillable = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'travel_date',
		'plate_number',
		'loading_terminal',
		'destination',
		'volume',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'drivers_logs_id';
	
	protected static $logName = 'Drivers Logs';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
        'employee_idx',
		'branch_idx',
		'department_idx',
		'current_rate',
		'travel_date',
		'plate_number',
		'loading_terminal',
		'destination',
		'volume',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
}
/*


CREATE TABLE `teves_payroll_drivers_logs` (
	`employee_idx` INT(11) NULL DEFAULT NULL,
	`branch_idx` INT(11) NULL DEFAULT NULL COMMENT 'Employee\'s Branch Assignment',
	`department_idx` INT(11) NULL DEFAULT NULL COMMENT 'Employee\'s Department Assignment',
	`current_rate` DOUBLE NULL DEFAULT NULL,
	`travel_date` DATE NULL DEFAULT NULL,
	`plate_number` VARCHAR(255) NULL DEFAULT 'YES' COLLATE 'latin1_swedish_ci',
	`loading_terminal` VARCHAR(255) NULL DEFAULT 'YES' COLLATE 'latin1_swedish_ci',
	`destination` VARCHAR(255) NULL DEFAULT 'YES' COLLATE 'latin1_swedish_ci',
	`volume` DOUBLE NULL DEFAULT '0',
	`rate_per_liter` DOUBLE NULL DEFAULT '0',
	`gross_amount` DOUBLE NULL DEFAULT '0',
	`trip_pay` DOUBLE NULL DEFAULT '0',
	`log_type` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Regular Log(1st 8 hrs), Regular Overtime, Restdat Overtime' COLLATE 'latin1_swedish_ci',
	`basic_pay` DOUBLE NULL DEFAULT '0',
	`created_at` DATETIME NULL DEFAULT NULL,
	`created_by_user_idx` INT(11) NULL DEFAULT NULL,
	`updated_at` DATETIME NULL DEFAULT NULL,
	`updated_by_user_idx` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`employee_logs_id`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=DYNAMIC
;


*/