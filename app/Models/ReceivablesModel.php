<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class ReceivablesModel extends Model
{
	
	use LogsActivity;

	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_receivable_table';
	
	protected $fillable = [
		'receivable_id',
        'client_idx',
		'billing_date',
		'control_number',
		'or_number',
		'payment_term',
		'receivable_description',
		'receivable_amount',
		'receivable_remaining_balance',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'	
    ];
	
	protected $primaryKey = 'receivable_id';
	
	protected static $logName = 'ReceivableInfo';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'receivable_id',
		'client_idx',
		'billing_date',
		'control_number',
		'or_number',
		'payment_term',
		'receivable_description',
		'receivable_amount',
		'receivable_remaining_balance',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'		
    ];
}
