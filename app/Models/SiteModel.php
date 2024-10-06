<?php
namespace App\Models;
// use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class SiteModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'meter_site';
	
	protected $fillable = [
        'division_idx',
        'company_idx',
        'building_idx',
		'site_code',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'site_id';
	
	protected static $logName = 'Site Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'division_idx',
        'company_idx',
        'building_idx',
		'site_code',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
