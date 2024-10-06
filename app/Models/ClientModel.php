<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{

	
	protected $table = 'teves_client_table';
	
	protected $fillable = [
        'client_name',
		'client_address',
		'client_tin',
    ];
	
	protected $primaryKey = 'client_id';
    
}
