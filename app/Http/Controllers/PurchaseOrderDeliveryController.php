<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PurchaseOrderModel;
use App\Models\ProductModel;
use App\Models\ClientModel;
use App\Models\TevesBranchModel;
use App\Models\PurchaseOrderComponentModel;
use App\Models\PurchaseOrderDeliveryModel;
use App\Models\ReceivablesModel;
use App\Models\ReceivablesPaymentModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderDeliveryController extends Controller
{
	
		/*Get List Of Product to Deliver*/
	public function get_purchase_order_product_list_delivery(Request $request){		
	
			$raw_query_purchase_order_component = "SELECT `teves_purchase_order_delivery_details`.`purchase_order_delivery_details_id`,
						`teves_product_table`.`product_name`,
						`teves_product_table`.`product_unit_measurement`,
						`teves_purchase_order_delivery_details`.`purchase_order_component_idx`,
						`teves_purchase_order_delivery_details`.`purchase_order_component_product_idx`, 
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_quantity`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_date`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_withdrawal_reference`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_hauler_details`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_remarks`
						
						from `teves_purchase_order_delivery_details`  left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_purchase_order_delivery_details`.`purchase_order_component_product_idx` where `teves_purchase_order_delivery_details`.`purchase_order_idx` = ?		  
						order by `teves_purchase_order_delivery_details`.`purchase_order_delivery_date` asc";	
						
			$data = DB::select("$raw_query_purchase_order_component", [ $request->purchase_order_id]);		
			
			return response()->json(array('productlist_delivery'=>$data));	

	}
	
	/*ROUTES:PurchaseOrderDeliveryInfo*/
	/*Fetch Product Delivery Information*/
	public function purchase_order_component_delivery_info(Request $request){
		
			$raw_query_purchase_order_component = "SELECT `teves_purchase_order_delivery_details`.`purchase_order_delivery_details_id`,
						`teves_product_table`.`product_name`,
						`teves_product_table`.`product_unit_measurement`,
						`teves_purchase_order_delivery_details`.`purchase_order_component_idx`,
						`teves_purchase_order_delivery_details`.`purchase_order_component_product_idx`, 
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_quantity`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_date`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_withdrawal_reference`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_hauler_details`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_remarks`
						from `teves_purchase_order_delivery_details`  left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_purchase_order_delivery_details`.`purchase_order_component_product_idx` 
						
						where `teves_purchase_order_delivery_details`.`purchase_order_delivery_details_id` = ?		  
						order by `teves_purchase_order_delivery_details`.`purchase_order_delivery_date` asc";	
						
			$data = DB::select("$raw_query_purchase_order_component", [ $request->purchase_order_delivery_details_id]);	
			
			return response()->json($data);
		
	}	
	
	/*Delete Product Information*/
	public function delete_purchase_order_product_delivery_confirmed(Request $request){

		/*Delete on Sales Order Table*/
		$purchase_order_delivery_details_id = $request->purchase_order_delivery_details_id;
		PurchaseOrderDeliveryModel::find($purchase_order_delivery_details_id)->delete();
		
		
				   /*Get Total Sales Order Quantity*/
					$sales_total_order_quantity =  PurchaseOrderComponentModel::where('purchase_order_idx', $request->purchase_order_id)
					->sum('order_quantity');
					
				  /*Get Delivery Total Quantity*/
					$sales_total_order_delivery_quantity =  PurchaseOrderDeliveryModel::where('purchase_order_idx', $request->purchase_order_id)
					->sum('purchase_order_delivery_quantity');
			  
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
				  $PurchaseOrderUpdate_status = new PurchaseOrderModel();
				  $PurchaseOrderUpdate_status = PurchaseOrderModel::find($request->purchase_order_id);
				  $PurchaseOrderUpdate_status->purchase_order_delivery_status = $DeliveryStatus;
				  $PurchaseOrderUpdate_status->update();
		
		
		
		return 'Deleted';
		
	} 	

	public function purchase_order_component_delivery_compose(Request $request){

		$request->validate([
		  'purchase_order_delivery_date'   => 'required',
		  'purchase_order_component_product_idx'   => 'required',
		  'purchase_order_delivery_quantity'     => 'required',
        ], 
        [
			'purchase_order_delivery_date.required' 	=> 'Delivery Date is Required',
			'purchase_order_component_product_idx.required' 	=> 'Product is Required',
			'purchase_order_delivery_quantity.required' 		=> 'Quantity is Required'
        ]
		);
			
					if($request->purchase_order_delivery_details_id==0){
						
						/*SAVE*/
						$PurchaseOrderDeliveryComponent = new PurchaseOrderDeliveryModel();
						$PurchaseOrderDeliveryComponent->purchase_order_idx 								= $request->purchase_order_id;
						$PurchaseOrderDeliveryComponent->receivable_idx 								= $request->receivable_id;
						
						$PurchaseOrderDeliveryComponent->purchase_order_component_idx 					= $request->purchase_order_component_idx+0;
						$PurchaseOrderDeliveryComponent->purchase_order_component_product_idx 			= $request->purchase_order_component_product_idx+0;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_quantity 				= $request->purchase_order_delivery_quantity;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_date 					= $request->purchase_order_delivery_date;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_withdrawal_reference 	= $request->purchase_order_delivery_withdrawal_reference;
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_hauler_details 			= $request->purchase_order_delivery_hauler_details;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_remarks 			= $request->purchase_order_delivery_remarks;
						
						$result = $PurchaseOrderDeliveryComponent->save();	
				
						$action_taken = "Created";						
						
					}else{
						
						/*UPDATE*/
						$PurchaseOrderDeliveryComponent = new PurchaseOrderDeliveryModel();
						$PurchaseOrderDeliveryComponent = PurchaseOrderDeliveryModel::find($request->purchase_order_delivery_details_id);
						$PurchaseOrderDeliveryComponent->purchase_order_component_idx 					= $request->purchase_order_component_idx+0;
						$PurchaseOrderDeliveryComponent->purchase_order_component_product_idx 			= $request->purchase_order_component_product_idx;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_quantity 				= $request->purchase_order_delivery_quantity;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_date 					= $request->purchase_order_delivery_date;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_withdrawal_reference 	= $request->purchase_order_delivery_withdrawal_reference;
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_hauler_details 			= $request->purchase_order_delivery_hauler_details;
						
						$PurchaseOrderDeliveryComponent->purchase_order_delivery_remarks 			= $request->purchase_order_delivery_remarks;
						
						$result = $PurchaseOrderDeliveryComponent->update();
				
						$action_taken = "Updated";
							
					}
				
				  /*Get Total Sales Order Quantity*/
					$sales_total_order_quantity =  PurchaseOrderComponentModel::where('purchase_order_idx', $request->purchase_order_id)
					->sum('order_quantity');
					
				  /*Get Delivery Total Quantity*/
					$sales_total_order_delivery_quantity =  PurchaseOrderDeliveryModel::where('purchase_order_idx', $request->purchase_order_id)
					->sum('purchase_order_delivery_quantity');
			  
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
				  $PurchaseOrderUpdate_status = new PurchaseOrderModel();
				  $PurchaseOrderUpdate_status = PurchaseOrderModel::find($request->purchase_order_id);
				  $PurchaseOrderUpdate_status->purchase_order_delivery_status = $DeliveryStatus;
				  $PurchaseOrderUpdate_status->update();
					
						/*Response*/
						if($result){
							return response()->json(array('success' => "Product Information Successfully $action_taken!"), 200);
						}
						else{
							return response()->json(['success'=>'Error on $action_taken Product Information']);
						}		
		
	}		
	
}
