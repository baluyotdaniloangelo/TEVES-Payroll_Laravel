<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class BillingTransactionModel extends Model
{
    //use HasFactory;
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_billing_table';

	protected $fillable = [
        'product_idx',
		'drivers_name',
		'plate_no',
        'product_price',
        'client_idx',
		'order_quantity',
		'order_total_amount',
        'order_date',
        'order_time',
        'order_po_number',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'billing_id';
	
	protected static $logName = 'Billing Transaction';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'order_date',
        'order_time',
		'order_po_number',
		'client_idx',
		'drivers_name',
		'plate_no',
        'product_idx',
        'product_price',
		'order_quantity',
		'order_total_amount',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
}
