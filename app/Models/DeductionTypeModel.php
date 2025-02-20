<?php
namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class DeductionTypeModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_deduction_type_table';
	
	protected $fillable = [
        'deduction_description',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];
    
	protected $primaryKey = 'deduction_id';
	
	protected static $logName = 'Deduction Type Details';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'deduction_description',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'updated_by_user_idx'
    ];	
}
