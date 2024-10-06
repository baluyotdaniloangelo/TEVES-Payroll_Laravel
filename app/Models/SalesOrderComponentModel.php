<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderComponentModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_sales_order_component_table';

	protected $fillable = [
        'sales_order_component_id',
		'sales_order_idx',
		'sales_order_date',
        'product_idx',
        'client_idx',
		'order_quantity',
		'product_price',
        'order_quantity',
        'order_total_amount',
        'created_at',
		'updated_at'
    ];
    protected $primaryKey = 'sales_order_component_id';
}
