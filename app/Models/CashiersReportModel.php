<?php
// namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class CashiersReportModel extends Model
{
	
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	
    //use HasFactory;
	protected $table = 'teves_cashiers_report';

	protected $fillable = [
		'cashiers_report_id',
        'user_idx',
		'teves_branch',
        'cashiers_name',
		'forecourt_attendant',
        'report_date',
        'shift',
        'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
	
    protected $primaryKey = 'cashiers_report_id';
	
	protected static $logName = 'Cashiers Report Table';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'cashiers_report_id',
        'user_idx',
		'teves_branch',
        'cashiers_name',
		'forecourt_attendant',
        'report_date',
        'shift',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
		
    ];
}
