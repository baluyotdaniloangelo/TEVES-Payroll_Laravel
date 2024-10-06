<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
	
	protected $table = 'teves_product_table';
	
	protected $fillable = [
        'product_name',
        'product_price',
		'product_unit_measurement',
        'created_at',
		'updated_at'
    ];
	
	protected $primaryKey = 'product_id';
    
}
