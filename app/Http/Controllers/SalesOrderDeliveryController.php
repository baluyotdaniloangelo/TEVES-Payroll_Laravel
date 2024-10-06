<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SalesOrderModel;
use App\Models\ProductModel;
use App\Models\ClientModel;
use App\Models\TevesBranchModel;
use App\Models\SalesOrderComponentModel;
use App\Models\SalesOrderDeliveryModel;
use App\Models\ReceivablesModel;
use App\Models\ReceivablesPaymentModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesOrderDeliveryController extends Controller
{
	
		/*Get List Of Product to Deliver*/
	public function get_sales_order_product_list_delivery(Request $request){		
	
			$raw_query_sales_order_component = "SELECT `teves_sales_order_delivery_details`.`sales_order_delivery_details_id`,
						`teves_product_table`.`product_name`,
						`teves_product_table`.`product_unit_measurement`,
						`teves_sales_order_delivery_details`.`sales_order_component_idx`,
						`teves_sales_order_delivery_details`.`sales_order_component_product_idx`, 
						`teves_sales_order_delivery_details`.`sales_order_delivery_quantity`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_date`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_withdrawal_reference`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_hauler_details`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_remarks`,
						`teves_sales_order_delivery_details`.`created_at`
						from `teves_sales_order_delivery_details`  left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_sales_order_delivery_details`.`sales_order_component_product_idx` where `teves_sales_order_delivery_details`.`sales_order_idx` = ?		  
						order by `teves_sales_order_delivery_details`.`sales_order_delivery_date` asc";	
						
			$data = DB::select("$raw_query_sales_order_component", [ $request->sales_order_id]);		
			
			return response()->json(array('productlist_delivery'=>$data));	

	}
	
	/*ROUTES:SalesOrderDeliveryInfo*/
	/*Fetch Product Delivery Information*/
	public function sales_order_component_delivery_info(Request $request){
		
			$raw_query_sales_order_component = "SELECT `teves_sales_order_delivery_details`.`sales_order_delivery_details_id`,
						`teves_product_table`.`product_name`,
						`teves_product_table`.`product_unit_measurement`,
						`teves_sales_order_delivery_details`.`sales_order_component_idx`,
						`teves_sales_order_delivery_details`.`sales_order_component_product_idx`, 
						`teves_sales_order_delivery_details`.`sales_order_delivery_quantity`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_date`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_withdrawal_reference`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_hauler_details`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_remarks`
						from `teves_sales_order_delivery_details`  left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_sales_order_delivery_details`.`sales_order_component_product_idx` 
						
						where `teves_sales_order_delivery_details`.`sales_order_delivery_details_id` = ?		  
						order by `teves_sales_order_delivery_details`.`sales_order_delivery_date` asc";	
						
			$data = DB::select("$raw_query_sales_order_component", [ $request->sales_order_delivery_details_id]);	
			
			return response()->json($data);
		
	}	
	
	/*Delete Product Information*/
	public function delete_sales_order_product_delivery_confirmed(Request $request){

		/*Delete on Sales Order Table*/
		$sales_order_delivery_details_id = $request->sales_order_delivery_details_id;
		SalesOrderDeliveryModel::find($sales_order_delivery_details_id)->delete();
		
		
				   /*Get Total Sales Order Quantity*/
					$sales_total_order_quantity =  SalesOrderComponentModel::where('sales_order_idx', $request->sales_order_id)
					->sum('order_quantity');
					
				  /*Get Delivery Total Quantity*/
					$sales_total_order_delivery_quantity =  SalesOrderDeliveryModel::where('sales_order_idx', $request->sales_order_id)
					->sum('sales_order_delivery_quantity');
			  
					$remaining_balance = number_format($sales_total_order_quantity - $sales_total_order_delivery_quantity+0,2, '.', '');
					$_delivery_percentage = ($sales_total_order_delivery_quantity / $sales_total_order_quantity) * 100;
				  
					  $delivery_percentage = number_format($_delivery_percentage,2,".","");
				  
					  /*IF Fully Paid Automatically Update the Status to Paid*/
					  if($delivery_percentage >= 0.01 && $delivery_percentage <= 99.99)
					  {		
						  
						  $DeliveryStatus = "$delivery_percentage%";
						  
					  }else if($delivery_percentage >= 100)
					  {	
						  
						  $DeliveryStatus = "$delivery_percentage%";
						  
					  }else{
						  
						  $DeliveryStatus = "Pending";
						  
					  }
			  
				  /*Update Sales Order to Delivered*/
				  $salesOrderUpdate_status = new SalesOrderModel();
				  $salesOrderUpdate_status = SalesOrderModel::find($request->sales_order_id);
				  $salesOrderUpdate_status->sales_order_delivery_status = $DeliveryStatus;
				  $salesOrderUpdate_status->update();
		
		
		
		return 'Deleted';
		
	} 	

	public function sales_order_component_delivery_compose(Request $request){

		$request->validate([
		  'sales_order_delivery_date'   => 'required',
		  'sales_order_component_product_idx'   => 'required',
		  'sales_order_delivery_quantity'     => 'required',
        ], 
        [
			'sales_order_delivery_date.required' 	=> 'Delivery Date is Required',
			'sales_order_component_product_idx.required' 	=> 'Product is Required',
			'sales_order_delivery_quantity.required' 		=> 'Quantity is Required'
        ]
		);
			
					if($request->sales_order_delivery_details_id==0){
						
						/*SAVE*/
						$SalesOrderDeliveryComponent = new SalesOrderDeliveryModel();
						$SalesOrderDeliveryComponent->sales_order_idx 								= $request->sales_order_id;
						$SalesOrderDeliveryComponent->receivable_idx 								= $request->receivable_id;
						
						$SalesOrderDeliveryComponent->sales_order_component_idx 					= $request->sales_order_component_idx+0;
						$SalesOrderDeliveryComponent->sales_order_component_product_idx 			= $request->sales_order_component_product_idx+0;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_quantity 				= $request->sales_order_delivery_quantity;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_date 					= $request->sales_order_delivery_date;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_withdrawal_reference 	= $request->sales_order_delivery_withdrawal_reference;
						$SalesOrderDeliveryComponent->sales_order_delivery_hauler_details 			= $request->sales_order_delivery_hauler_details;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_remarks 			= $request->sales_order_delivery_remarks;
						
						$result = $SalesOrderDeliveryComponent->save();	
				
						$action_taken = "Created";						
						
					}else{
						
						/*UPDATE*/
						$SalesOrderDeliveryComponent = new SalesOrderDeliveryModel();
						$SalesOrderDeliveryComponent = SalesOrderDeliveryModel::find($request->sales_order_delivery_details_id);
						$SalesOrderDeliveryComponent->sales_order_component_idx 					= $request->sales_order_component_idx+0;
						$SalesOrderDeliveryComponent->sales_order_component_product_idx 			= $request->sales_order_component_product_idx;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_quantity 				= $request->sales_order_delivery_quantity;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_date 					= $request->sales_order_delivery_date;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_withdrawal_reference 	= $request->sales_order_delivery_withdrawal_reference;
						$SalesOrderDeliveryComponent->sales_order_delivery_hauler_details 			= $request->sales_order_delivery_hauler_details;
						
						$SalesOrderDeliveryComponent->sales_order_delivery_remarks 			= $request->sales_order_delivery_remarks;
						
						$result = $SalesOrderDeliveryComponent->update();
				
						$action_taken = "Updated";
							
					}
				
				  /*Get Total Sales Order Quantity*/
					$sales_total_order_quantity =  SalesOrderComponentModel::where('sales_order_idx', $request->sales_order_id)
					->sum('order_quantity');
					
				  /*Get Delivery Total Quantity*/
					$sales_total_order_delivery_quantity =  SalesOrderDeliveryModel::where('sales_order_idx', $request->sales_order_id)
					->sum('sales_order_delivery_quantity');
			  
					$remaining_balance = number_format($sales_total_order_quantity - $sales_total_order_delivery_quantity+0,2, '.', '');
					$_delivery_percentage = ($sales_total_order_delivery_quantity / $sales_total_order_quantity) * 100;
				  
					  $delivery_percentage = number_format($_delivery_percentage,2,".","");
				  
					  /*IF Fully Paid Automatically Update the Status to Paid*/
					  if($delivery_percentage >= 0.01 && $delivery_percentage <= 99.99)
					  {		
						  
						  $DeliveryStatus = "$delivery_percentage%";
						  
					  }else if($delivery_percentage >= 100)
					  {	
						  
						  $DeliveryStatus = "$delivery_percentage%";
						  
					  }else{
						  
						  $DeliveryStatus = "Pending";
						  
					  }
			  
				  /*Update Sales Order to Delivered*/
				  $salesOrderUpdate_status = new SalesOrderModel();
				  $salesOrderUpdate_status = SalesOrderModel::find($request->sales_order_id);
				  $salesOrderUpdate_status->sales_order_delivery_status = $DeliveryStatus;
				  $salesOrderUpdate_status->update();
					
						/*Response*/
						if($result){
							return response()->json(array('success' => "Product Information Successfully $action_taken!"), 200);
						}
						else{
							return response()->json(['success'=>'Error on $action_taken Product Information']);
						}		
		
	}		
	
}
