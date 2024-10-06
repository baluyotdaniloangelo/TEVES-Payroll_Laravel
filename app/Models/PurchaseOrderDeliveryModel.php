<?php
namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDeliveryModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_purchase_order_delivery_details';

	protected $fillable = [
        'purchase_order_delivery_details_id',
		'purchase_order_idx',
		'receivable_idx',
		'purchase_order_component_product_idx',
        'purchase_order_delivery_quantity',
        'purchase_order_dispatch_date',
		'purchase_order_departure_date',
		'purchase_order_delivery_total_load',
        'purchase_order_delivery_withdrawal_reference',
        'purchase_order_delivery_hauler_details',
        'purchase_order_delivery_remarks',
        'purchase_order_branch_delivery',
        'created_at',
		'updated_at'
    ];
	
    protected $primaryKey = 'purchase_order_delivery_details_id';

}
