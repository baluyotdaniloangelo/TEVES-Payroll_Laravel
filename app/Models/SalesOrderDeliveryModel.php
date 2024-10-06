<?php
namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDeliveryModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_sales_order_delivery_details';

	protected $fillable = [
        'sales_order_delivery_details_id',
		'sales_order_idx',
		'receivable_idx',
		'sales_order_component_product_idx',
        'sales_order_delivery_quantity',
        'sales_order_dispatch_date',
		'sales_order_departure_date',
		'sales_order_delivery_total_load',
        'sales_order_delivery_withdrawal_reference',
        'sales_order_delivery_hauler_details',
        'sales_order_delivery_remarks',
        'sales_order_branch_delivery',
        'created_at',
		'updated_at'
    ];
	
    protected $primaryKey = 'sales_order_delivery_details_id';

}
