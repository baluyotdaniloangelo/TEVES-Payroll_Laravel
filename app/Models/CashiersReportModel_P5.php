<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class CashiersReportModel_P5 extends Model
{
	
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
	protected $table = 'teves_cashiers_report_p5';

	protected $fillable = [
		'cashiers_report_p5_id',
        'user_idx',
		'cashiers_report_id',
		'one_thousand_deno',
		'five_hundred_deno',
		'two_hundred_deno',
		'one_hundred_deno',
		'fifty_deno',
		'twenty_deno',
		'ten_deno',
		'five_deno',
		'one_deno',
		'twenty_five_cent_deno',
		'cash_drop',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'cashiers_report_p5_id';
	
	protected static $logName = 'Cashiers Report Table Part 5';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'cashiers_report_p5_id',
        'user_idx',
		'cashiers_report_id',
		'one_thousand_deno',
		'five_hundred_deno',
		'two_hundred_deno',
		'one_hundred_deno',
		'fifty_deno',
		'twenty_deno',
		'ten_deno',
		'five_deno',
		'one_deno',
		'twenty_five_cent_deno',
		'cash_drop',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
}
