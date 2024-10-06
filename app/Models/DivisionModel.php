<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class DivisionModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'meter_division_table';
	
	protected $fillable = [
        'division_code',
		'division_name',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
    
	protected $primaryKey = 'division_id';
	
	protected static $logName = 'Division Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'division_code',
		'division_name',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];	
}
