<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class CashiersReportModel_P7 extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_cashiers_report_p7';

	protected $fillable = [
        'cashiers_report_p7_id',
        'user_idx',
		'cashiers_report_id',
		'product_idx',
		'tank_idx',
		'beginning_inventory',
		'sales_in_liters',
		'ugt_pumping',
		'delivery',
		'ending_inventory',
		'book_stock',
		'variance',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
    protected $primaryKey = 'cashiers_report_p7_id';
	
	protected static $logName = 'Cashiers Report Table Part 7';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'cashiers_report_p7_id',
        'user_idx',
		'cashiers_report_id',
		'product_idx',
		'tank_idx',
		'beginning_inventory',
		'sales_in_liters',
		'ugt_pumping',
		'delivery',
		'ending_inventory',
		'book_stock',
		'variance',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
}
