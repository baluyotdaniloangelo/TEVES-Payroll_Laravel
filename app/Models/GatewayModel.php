<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class GatewayModel extends Model
{
    use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'meter_rtu';
	
	protected $fillable = [
        'gateway_sn',
        'gateway_mac',
        'gateway_ip',
		'rtu_physical_location',
		'update_rtu',
        'update_rtu_location',
		'update_rtu_ssh',
		'update_rtu_force_lp',
        'idf_number',
        'switch_name',
        'idf_port',
        'last_log_update',
        'soft_rev',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
	
	protected $primaryKey = 'rtu_id';
    
	protected static $logName = 'Gateway Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'gateway_sn',
        'gateway_mac',
        'gateway_ip',
		'rtu_physical_location',
		'update_rtu',
        'update_rtu_location',
		'update_rtu_ssh',
		'update_rtu_force_lp',
        'idf_number',
        'switch_name',
        'idf_port',
        'last_log_update',
        'soft_rev',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
