<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class CashiersReportModel_P3 extends Model
{
	
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	

	protected $table = 'teves_cashiers_report_p3';

	protected $fillable = [
		'cashiers_report_p3_id',
        'user_idx',
		'cashiers_report_id',
        'product_idx',
		'order_quantity',
		'pump_price',
		'unit_price',
		'discounted_price',
		'order_total_amount',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'cashiers_report_p3_id';
	
	protected static $logName = 'Cashiers Report Table Part 3';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'cashiers_report_p3_id',
		'user_idx',
		'cashiers_report_id',
        'product_idx',
		'order_quantity',
		'pump_price',
		'unit_price',
		'discounted_price',
		'order_total_amount',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
}
