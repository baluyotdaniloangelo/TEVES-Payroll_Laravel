<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class CompanyDetailsModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_payroll_company_details';
	
	protected $fillable = [
        'sss_number',
		'pagibig_number',
		'pagibig_number',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'company_detail_id';
	
	protected static $logName = 'Company Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'sss_number',
		'pagibig_number',
		'pagibig_number',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];	
}
