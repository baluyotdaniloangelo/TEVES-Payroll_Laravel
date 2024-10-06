<?php
namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Database\Eloquent\Model;

use Session;

class SalesOrderModel extends Model
{

	//use HasFactory;
	use LogsActivity;
	
	public function tapActivity(Activity $activity, string $eventName)
	{
    $activity->causer_id = Session::get('loginID');
	}
	

	protected $table = 'teves_sales_order_table';
	
	protected $fillable = [
        'sales_order_client_idx',
		'sales_order_control_number',
		'sales_order_date',
		'sales_order_dr_number',
		'sales_order_or_number',
		'sales_order_po_number',
		'sales_order_charge_invoice',
		'sales_order_collection_receipt',
		'sales_order_payment_term',
		'sales_order_delivered_to',
		'sales_order_delivered_to_address',
		'sales_order_delivery_method',
		'sales_order_gross_amount',
		'sales_order_net_percentage',
		'sales_order_withholding_tax',
		'sales_order_total_due',
		'sales_order_hauler',
		'sales_order_required_date',
		'sales_order_instructions',
		'sales_order_note',
		'sales_order_mode_of_payment',
		'sales_order_date_of_payment',
		'sales_order_reference_no',
		'sales_order_payment_status',
		'sales_order_delivery_status',
		'company_header',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'	
    ];
	
	
	
	/*
	
	$Salesorder->sales_order_client_idx 				= $request->client_idx;
				$Salesorder->sales_order_control_number 			= $control_number;
				$Salesorder->company_header 						= $request->company_header;
				$Salesorder->sales_order_date 						= $request->sales_order_date;
				$Salesorder->sales_order_delivered_to 				= $request->delivered_to;
				$Salesorder->sales_order_delivered_to_address 		= $request->delivered_to_address;
				$Salesorder->sales_order_dr_number 					= $request->dr_number;
			
				$Salesorder->sales_order_or_number 					= $request->sales_order_or_number;
				$Salesorder->sales_order_po_number 					= $request->sales_order_po_number;
				$Salesorder->sales_order_charge_invoice 			= $request->sales_order_charge_invoice;
				$Salesorder->sales_order_collection_receipt 		= $request->sales_order_collection_receipt;
				
				$Salesorder->sales_order_payment_term 				= $request->payment_term;
				$Salesorder->sales_order_delivery_method 			= $request->delivery_method;
				$Salesorder->sales_order_hauler 					= $request->hauler;
				$Salesorder->sales_order_required_date 				= $request->required_date;
				$Salesorder->sales_order_instructions 				= $request->instructions;
				$Salesorder->sales_order_note 						= $request->note;
				
				$Salesorder->sales_order_net_percentage 			= $request->sales_order_net_percentage;
				$Salesorder->sales_order_withholding_tax 			= $request->sales_order_withholding_tax;
				
				$Salesorder->sales_order_payment_type 				= $request->sales_order_payment_type;
	
	
	*/
	
	
	protected $primaryKey = 'sales_order_id';
	
	protected static $logName = 'Sales Order';
	
	protected static $logOnlyDirty = true;
	
	protected static $logAttributes = [
		'sales_order_client_idx',
		'sales_order_control_number',
		'sales_order_date',
		'sales_order_dr_number',
		'sales_order_or_number',
		'sales_order_po_number',
		'sales_order_charge_invoice',
		'sales_order_collection_receipt',
		'sales_order_payment_term',
		'sales_order_delivered_to',
		'sales_order_delivered_to_address',
		'sales_order_delivery_method',
		'sales_order_gross_amount',
		'sales_order_net_percentage',
		'sales_order_withholding_tax',
		'sales_order_total_due',
		'sales_order_hauler',
		'sales_order_required_date',
		'sales_order_instructions',
		'sales_order_note',
		'sales_order_mode_of_payment',
		'sales_order_date_of_payment',
		'sales_order_reference_no',
		'sales_order_payment_status',
		'sales_order_delivery_status',
		'company_header',
		'created_at',
		'created_by_user_id',
		'updated_at',
		'updated_by_user_id'
    ];
    
}
