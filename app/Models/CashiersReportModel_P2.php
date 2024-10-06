<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class CashiersReportModel_P2 extends Model
{
	
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_cashiers_report_p2';

	protected $fillable = [
		'cashiers_report_p2_id',
        'user_idx',
		'cashiers_report_id',
        'product_idx',
		'order_quantity',
		'product_price',
		'order_total_amount',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'cashiers_report_p2_id';

	protected static $logName = 'Cashiers Report Table Part 2';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'cashiers_report_p2_id',
        'user_idx',
		'cashiers_report_id',
        'product_idx',
		'order_quantity',
		'product_price',
		'order_total_amount',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
}
