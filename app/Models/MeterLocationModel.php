<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class MeterLocationModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'meter_location_table';
	
	protected $fillable = [
		'site_idx',
        'location_code',
        'location_description',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
	
    protected $primaryKey = 'location_id';
	
	protected static $logName = 'Location Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'site_idx',
        'location_code',
        'location_description',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
