<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductTankModel extends Model
{
	
	protected $table = 'teves_product_tank_table';
	
	protected $fillable = [
		'product_idx',
		'branch_idx',
        'tank_name',
        'tank_capacity',
		'tank_unit_of_measurement',
        'created_at',
		'updated_at'
    ];
	
	protected $primaryKey = 'tank_id';
    
}
