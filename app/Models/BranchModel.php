<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

use Session;

class BranchModel extends Model
{
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}

	protected $table = 'teves_branch_table';
	
	protected $fillable = [
        'branch_code',
		'branch_name',
		'branch_initial',
		'branch_tin',
		'branch_address',
		'branch_contact_number',
		'branch_owner',
		'branch_owner_title',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
	
	protected $primaryKey = 'branch_id';
    
	protected static $logName = 'Branch Information';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'branch_code',
		'branch_name',
		'branch_initial',
		'branch_tin',
		'branch_address',
		'branch_contact_number',
		'branch_owner',
		'branch_owner_title',
		'created_at',
		'created_by_user_idx',
		'updated_at',
		'modified_by_user_idx'
    ];
}
