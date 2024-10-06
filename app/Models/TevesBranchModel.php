<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TevesBranchModel extends Model
{


	protected $table = 'teves_branch_table';
	
	protected $fillable = [
        'branch_code',
		'branch_name',
		'branch_tin',
		'branch_address',
		'branch_contact_number',
		'branch_owner',
		'branch_owner_title',
		'branch_logo',
    ];
	
	protected $primaryKey = 'branch_id';
    
}
