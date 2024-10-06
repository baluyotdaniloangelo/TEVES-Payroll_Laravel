<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class MeterModel extends Model
{

	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'meter_details';
	/*
			$meter->site_idx 						= $request->siteID;		
			$meter->site_code 						= $request->site_code;			
			$meter->meter_name 						= $request->meter_name;
			$meter->meter_name_addressable 			= $request->meter_name_addressable;
			$meter->meter_default_name 				= $request->meter_default_name;
			$meter->customer_name 					= $request->customer_name;
			$meter->config_idx		 				= $request->meter_model_id;
			$meter->meter_type 						= $request->meter_type;
			$meter->meter_brand 					= $request->meter_brand;
			$meter->meter_multiplier 				= $request->meter_multiplier;
			$meter->meter_role 						= $request->meter_role;
			$meter->location_idx 					= $request->location_id;
			$meter->rtu_idx			 				= $request->rtu_sn_number_id;
			$meter->meter_status 					= $request->meter_status;
			$meter->meter_remarks 					= $request->meter_remarks;	
			$meter->created_by_user_idx 			= Session::get('loginID');
	*/
	protected $fillable = [
		'site_idx',
		'rtu_idx',
		'location_idx',
		'config_idx',
		'site_code',
        'meter_name',
		'meter_name_addressable',
		'meter_default_name',
		'meter_type',
		'meter_brand',
		'meter_role',
		'meter_remarks',
        'customer_name',
		'meter_multiplier',
		'meter_status',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
	
    protected $primaryKey = 'meter_id';
	
	protected static $logName = 'Meter Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'site_idx',
		'rtu_idx',
		'location_idx',
		'config_idx',
		'site_code',
        'meter_name',
		'meter_name_addressable',
		'meter_default_name',
		'meter_type',
		'meter_brand',
		'meter_role',
		'meter_remarks',
        'customer_name',
		'meter_multiplier',
		'meter_status',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
