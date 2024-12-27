<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class HolidayModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_holiday_table';
	
	protected $fillable = [
        'holiday_description',
		'holiday_date',
		'holiday_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'holiday_id';
	
	protected static $logName = 'Holiday Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'holiday_description',
		'holiday_date',
		'holiday_type',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];	
}
