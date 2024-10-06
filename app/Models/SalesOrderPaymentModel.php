<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesOrderPaymentModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_sales_order_payment_details';

	protected $fillable = [
        'sales_order_payment_details_id',
		'sales_order_idx',
        'sales_order_mode_of_payment',
		'sales_order_date_of_payment',
		'sales_order_reference_no',
        'sales_order_payment_amount',
        'created_at',
		'updated_at'
    ];
    protected $primaryKey = 'sales_order_payment_details_id';
	
}
