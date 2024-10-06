<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderComponentModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_purchase_order_component_table';

	protected $fillable = [
        'purchase_order_component_id',
		'purchase_order_idx',
        'product_idx',
		'order_quantity',
		'product_price',
        'order_quantity',
        'order_total_amount',
        'created_at',
		'updated_at'
    ];
    protected $primaryKey = 'purchase_order_component_id';
}
