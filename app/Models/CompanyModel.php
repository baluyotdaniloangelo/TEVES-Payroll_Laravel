<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class CompanyModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'meter_company_table';
	
	protected $fillable = [
        'company_code',
		'company_name',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'company_id';
	
	protected static $logName = 'Company Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'company_code',
		'company_name',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];	
}
