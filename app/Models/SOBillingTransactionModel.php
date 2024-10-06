<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class SOBillingTransactionModel extends Model
{

	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_billing_so_table';

	protected $fillable = [
		'drivers_name',
		'plate_no',
        'client_idx',
        'order_date',
        'order_time',
        'so_number',
        'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'so_id';
	
	protected static $logName = 'SO';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'order_date',
        'order_time',
		'so_number',
		'client_idx',
		'drivers_name',
		'plate_no',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
		
    ];
	
}
