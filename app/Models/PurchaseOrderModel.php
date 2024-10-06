<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderModel extends Model
{

	protected $table = 'teves_purchase_order_table';
	
	protected $fillable = [
		'purchase_order_date',
		'purchase_order_control_number',
		'purchase_order_supplier_idx',
		'purchase_order_or_number',
		'purchase_order_gross_amount',
		'purchase_order_total_payable',
		'purchase_status'
    ];
	
	protected $primaryKey = 'purchase_order_id';
    
}
