<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class CutOffModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_payroll_cutoff_table';
	
	protected $fillable = [
        'branch_idx',
        'cut_off_month',
		'cut_off_period_start',
		'cut_off_period_end',
        'cut_off_gross_salary',
        'cut_off_net_salary',
		'reviewed_by',
		'approved_by',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'cutoff_id';
	
	protected static $logName = 'Cut-Off Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'branch_idx',
        'cut_off_month',
		'cut_off_period_start',
		'cut_off_period_end',
        'cut_off_gross_salary',
        'cut_off_net_salary',
		'reviewed_by',
		'approved_by',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];	
}
