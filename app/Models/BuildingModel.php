<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class BuildingModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'meter_building_table';
	
	protected $fillable = [
		'site_idx',
        'building_code',
        'building_description',
		'cut_off',
		'device_ip_range',
		'ip_network',
		'ip_netmask',
		'ip_gateway',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'building_id';
	
	protected static $logName = 'Building Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'site_idx',
        'building_code',
        'building_description',
		'cut_off',
		'device_ip_range',
		'ip_network',
		'ip_netmask',
		'ip_gateway',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
	
}
