<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PurchaseOrderModel;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderComponentModel;
use App\Models\PurchaseOrderPaymentModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Photo;
use Illuminate\Validation\Rule;

class PurchaseOrderController_v2 extends Controller
{
	
	/*Load Product Interface*/
	public function purchaseorder(){
		
		$title = 'Purchase Order';
		$data = array();
		if(Session::has('loginID')){
			
			$product_data = ProductModel::all();
			$supplier_data = SupplierModel::all();	
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			if($data->user_branch_access_type=='ALL'){
				
				$teves_branch = TevesBranchModel::all();
			
			}else{
				
				$userID = Session::get('loginID');
				
				$teves_branch = TevesBranchModel::leftJoin('teves_user_branch_access', function($q) use ($userID)
				{
					$q->on('teves_branch_table.branch_id', '=', 'teves_user_branch_access.branch_idx');
				})
							
							->where('teves_user_branch_access.user_idx', '=', $userID)
							->get([
							'teves_branch_table.branch_id',
							'teves_user_branch_access.user_idx',
							'teves_user_branch_access.branch_idx',
							'teves_branch_table.branch_code',
							'teves_branch_table.branch_name'
							]);
				
			}	
			
			$purchase_data_suggestion = PurchaseOrderModel::select('purchase_loading_terminal','hauler_operator','lorry_driver','plate_number','purchase_destination')->distinct()->get();			
			$purchase_payment_suggestion = PurchaseOrderPaymentModel::select('purchase_order_bank')->distinct()->get();
		
		}

		return view("pages.purchaseorder_v2", compact('data','title','product_data','purchase_data_suggestion','purchase_payment_suggestion','supplier_data','teves_branch'));
		
	}   
	
	/*Fetch Product List using Datatable*/
	public function getPurchaseOrderList_v2(Request $request){
		
		$list = PurchaseOrderModel::get();
		if ($request->ajax()) {

		$data = PurchaseOrderModel::leftJoin('teves_supplier_table', 'teves_supplier_table.supplier_id', '=', 'teves_purchase_order_table.purchase_order_supplier_idx')
              	->get([
						'teves_purchase_order_table.purchase_order_id',
						'teves_purchase_order_table.purchase_order_date',
						'teves_purchase_order_table.purchase_order_control_number',
						'teves_purchase_order_table.purchase_order_sales_order_number',
						'teves_supplier_table.supplier_name',
						'teves_purchase_order_table.purchase_order_total_payable',
						'teves_purchase_order_table.purchase_status',
						'teves_purchase_order_table.purchase_order_delivery_status'
				]);
	
		return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('status', function($row){
					
					/*				
				if($row->purchase_status=='Pending'){
					$purchase_status_selected = '<option disabled="" value="">Choose...</option><option selected value="Pending">Pending</option><option value="Paid">Paid</option>';
				}else if($row->purchase_status=='Paid'){
					$purchase_status_selected = '<option disabled="" value="">Choose...</option><option value="Pending">Pending</option><option selected value="Paid">Paid</option>';
				}else{
					$purchase_status_selected = '<option disabled="" selected value="">Choose...</option><option value="Pending">Pending</option><option value="Paid">Paid</option>';
				}*/
					
					$actionBtn = $row->purchase_status;
                    return $actionBtn;
                })
				
				->addColumn('action', function($row){
					$actionBtn = '
					<div align="center" class="action_table_menu_Product">
					<a href="#" data-id="'.$row->purchase_order_id.'" class="btn-warning btn-circle btn-sm bi bi-printer-fill btn_icon_table btn_icon_table_view" id="PrintPurchaseOrder""></a>
					<a href="#" class="btn-circle btn-sm bi bi-images btn_icon_table btn_icon_table_gallery" onclick="ViewGalery('.$row->purchase_order_id.')" id="viewPaymentGalery"></a>
					<a href="purchase_order_form/'.$row->purchase_order_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editCashiersReport"></a>
					<a href="#" data-id="'.$row->purchase_order_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deletePurchaseOrder"></a>
					</div>';
                    return $actionBtn;
                })
				->rawColumns(['action','status'])
                ->make(true);
		}		
    }

	/*Load Purchase Order Interface*/
	public function purchase_order_form($PurchaseOrderID){
		
		$title = 'Purchase Order';
		$data = array();
		if(Session::has('loginID')){
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();/*User Data*/
			$supplier_data = SupplierModel::all();
			$product_data = ProductModel::all();
			
			if($data->user_branch_access_type=='ALL'){
				
				$teves_branch = TevesBranchModel::all();
			
			}else{
				
				$userID = Session::get('loginID');
				
				$teves_branch = TevesBranchModel::leftJoin('teves_user_branch_access', function($q) use ($userID)
				{
					$q->on('teves_branch_table.branch_id', '=', 'teves_user_branch_access.branch_idx');
				})
							
							->where('teves_user_branch_access.user_idx', '=', $userID)
							->get([
							'teves_branch_table.branch_id',
							'teves_user_branch_access.user_idx',
							'teves_user_branch_access.branch_idx',
							'teves_branch_table.branch_code',
							'teves_branch_table.branch_name'
							]);
				
			}	
			
			$purchase_data_suggestion = PurchaseOrderModel::select('purchase_loading_terminal','hauler_operator','lorry_driver','plate_number','contact_number','purchase_destination','purchase_destination_address')->distinct()->get();			
			$purchase_payment_suggestion = PurchaseOrderPaymentModel::select('purchase_order_bank')->distinct()->get();
			
			
					
		return view("pages.purchase_order_form", compact('data','title','product_data','supplier_data','teves_branch', 'purchase_data_suggestion','purchase_payment_suggestion', 'PurchaseOrderID'));
		}
		
	}  	
	
	/*Fetch Product Information*/
	public function purchase_order_info(Request $request){
		
				$data = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
				->leftJoin('teves_supplier_table', 'teves_supplier_table.supplier_id', '=', 'teves_purchase_order_table.purchase_order_supplier_idx')
              	->get([
						'teves_supplier_table.supplier_name',
						'teves_supplier_table.supplier_tin',
						'teves_supplier_table.supplier_address',
						'teves_purchase_order_table.purchase_order_control_number',
						'teves_purchase_order_table.purchase_order_date',
						'teves_purchase_order_table.purchase_order_sales_order_number',
						'teves_purchase_order_table.purchase_order_collection_receipt_no',
						'teves_purchase_order_table.purchase_order_official_receipt_no',
						'teves_purchase_order_table.purchase_order_delivery_receipt_no',
						'teves_purchase_order_table.purchase_order_bank',
						'teves_purchase_order_table.purchase_order_date_of_payment',
						'teves_purchase_order_table.purchase_order_reference_no',
						'teves_purchase_order_table.purchase_order_payment_amount',
						'teves_purchase_order_table.purchase_order_delivery_method',
						'teves_purchase_order_table.purchase_loading_terminal',
						'teves_purchase_order_table.purchase_order_date_of_pickup',
						'teves_purchase_order_table.purchase_order_date_of_arrival',
						'teves_purchase_order_table.purchase_order_gross_amount',
						'teves_purchase_order_table.purchase_order_total_liters',
						'teves_purchase_order_table.purchase_order_net_percentage', 
						'teves_purchase_order_table.purchase_order_net_amount',
						'teves_purchase_order_table.purchase_order_less_percentage',
						'teves_purchase_order_table.purchase_order_total_payable',
						'teves_purchase_order_table.hauler_operator',
						'teves_purchase_order_table.lorry_driver',
						'teves_purchase_order_table.plate_number',
						'teves_purchase_order_table.purchase_destination',
						'teves_purchase_order_table.purchase_order_instructions',
						'teves_purchase_order_table.purchase_order_note',
						'teves_purchase_order_table.company_header'
				]);	
															
					return response()->json($data);	
					
	}

	/*Delete Product Information*/
	public function delete_purchase_order_confirmed(Request $request){

		/*Delete on Purchase Order Table*/
		$purchase_order_id = $request->purchase_order_id;
		PurchaseOrderModel::find($purchase_order_id)->delete();
		
		/*Delete on Purchase Order Product Component*/
		PurchaseOrderComponentModel::where('purchase_order_idx', $purchase_order_id)->delete();
		
		/*Delete on Purchase Order Payment Component*/
		PurchaseOrderPaymentModel::where('purchase_order_idx', $purchase_order_id)->delete();

		return 'Deleted';
		
	} 

	public function create_purchase_order_post(Request $request){

		$request->validate([
			'supplier_idx'  	=> 'required'
        ], 
        [
			'supplier_idx.required' 	=> "Supplier's Name is Required"
        ]
		);

			@$last_id = PurchaseOrderModel::latest()->first()->purchase_order_id;
			
			$BranchInfo = TevesBranchModel::where('branch_id', '=', $request->company_header)->first();			
			
			$control_number = $BranchInfo->branch_initial."-PO-".$last_id+2;
			
			$Purchaseorder = new PurchaseOrderModel();	
			$Purchaseorder->purchase_order_control_number 			= $control_number;
			$Purchaseorder->purchase_order_date 					= $request->purchase_order_date;
			$Purchaseorder->purchase_order_supplier_idx				= $request->supplier_idx;
			$Purchaseorder->company_header							= $request->company_header;
			
			$Purchaseorder->purchase_order_net_percentage			= $request->default_net_percentage;
			$Purchaseorder->purchase_order_less_percentage			= $request->default_less_percentage;
			
			$result = $Purchaseorder->save();
			
			/*Get Last ID*/
			$last_transaction_id = $Purchaseorder->purchase_order_id;

			if($result){
				return response()->json(array('success' => "Purchase Order Successfully Created!", 'purchase_order_id' => @$last_transaction_id), 200);
			}
			else{
				return response()->json(['success'=>'Error on Insert Purchase Order Information']);
			}
	}

	public function update_purchase_order_post(Request $request){

		$request->validate([
			'supplier_idx'  	=> 'required'
        ], 
        [
			'supplier_idx.required' 	=> "Supplier's Name is Required"
        ]
		);
		
					
			$BranchInfo = TevesBranchModel::where('branch_id', '=', $request->company_header)->first();			
			
			$control_number = $BranchInfo->branch_initial."-PO-".$request->purchase_order_id+1;
		
			$Purchaseorder = new PurchaseOrderModel();
			$Purchaseorder = PurchaseOrderModel::find($request->purchase_order_id);
			$Purchaseorder->purchase_order_control_number 			= $control_number;
			$Purchaseorder->purchase_order_date 					= $request->purchase_order_date;
			$Purchaseorder->purchase_order_supplier_idx				= $request->supplier_idx;
					
			$Purchaseorder->purchase_order_sales_order_number		= $request->purchase_order_sales_order_number;
			$Purchaseorder->purchase_order_collection_receipt_no	= $request->purchase_order_collection_receipt_no;
			$Purchaseorder->purchase_order_official_receipt_no		= $request->purchase_order_official_receipt_no;
			$Purchaseorder->purchase_order_delivery_receipt_no		= $request->purchase_order_delivery_receipt_no;
				
			$Purchaseorder->purchase_order_delivery_method			= $request->purchase_order_delivery_method;
			$Purchaseorder->purchase_loading_terminal				= $request->purchase_loading_terminal;
					
			$Purchaseorder->purchase_order_net_percentage			= $request->purchase_order_net_percentage;
			$Purchaseorder->purchase_order_less_percentage			= $request->purchase_order_less_percentage;
			
			$Purchaseorder->hauler_operator							= $request->hauler_operator;
			$Purchaseorder->lorry_driver							= $request->lorry_driver;			
			
			$Purchaseorder->plate_number							= $request->plate_number;
					
			$Purchaseorder->purchase_destination					= $request->purchase_destination;
									
			$Purchaseorder->purchase_order_instructions				= $request->purchase_order_instructions;
			$Purchaseorder->purchase_order_note						= $request->purchase_order_note;
			$Purchaseorder->company_header							= $request->company_header;		
			
			$result = $Purchaseorder->update();
			
			
			
				$order_total_amount = PurchaseOrderComponentModel::where('purchase_order_idx', $request->purchase_order_id)
						->sum('order_total_amount');
					
				$gross_amount = $order_total_amount;
		
				$PurchaseOrderInfo = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
						->get([
								'purchase_order_net_percentage', 
								'purchase_order_less_percentage'
						]);	
				
				$net_in_percentage 				= $PurchaseOrderInfo[0]->purchase_order_net_percentage;/*1.12*/
				$less_in_percentage 			= $PurchaseOrderInfo[0]->purchase_order_less_percentage/100;
							
				if($PurchaseOrderInfo[0]->purchase_order_net_percentage==0){
							$purchase_order_net_amount 			= 0;
							$purchase_order_total_due 			=  number_format($gross_amount,4,".","");
				}else{
							$purchase_order_net_amount 			=  number_format($gross_amount/$net_in_percentage,4,".","");
							$purchase_order_total_due 			=  number_format($gross_amount - (($gross_amount/$net_in_percentage)*$less_in_percentage),4,".","");
				}
				
				$PurchaseOrderUpdate = new PurchaseOrderModel();
				$PurchaseOrderUpdate = PurchaseOrderModel::find($request->purchase_order_id);
				$PurchaseOrderUpdate->purchase_order_gross_amount = number_format($gross_amount,4,".","");
				$PurchaseOrderUpdate->purchase_order_net_amount = $purchase_order_net_amount;
				$PurchaseOrderUpdate->purchase_order_total_payable = $purchase_order_total_due;
				$PurchaseOrderUpdate->update();	
			
			
			/*Get Last ID*/
			$last_transaction_id = $Purchaseorder->purchase_order_id;
			
			if($result){
				return response()->json(array('success' => "Purchase Order Successfully Updated!", 'purchase_order_id' => @$last_transaction_id), 200);
			}
			else{
				return response()->json(['success'=>'Error on Update Purchase Order Information']);
			}
			
	}
	
	public function get_purchase_order_product_list(Request $request){		
	
					$raw_query_purchase_order_component = "SELECT `teves_purchase_order_component_table`.`purchase_order_component_id`,
					IFNULL(`teves_product_table`.`product_name`,`teves_purchase_order_component_table`.item_description) as product_name,
					IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
					`teves_purchase_order_component_table`.`product_idx`, `teves_purchase_order_component_table`.`product_price`, `teves_purchase_order_component_table`.`order_quantity`,
					`teves_purchase_order_component_table`.`order_total_amount`
					from `teves_purchase_order_component_table`  left join `teves_product_table` on	 
					`teves_product_table`.`product_id` = `teves_purchase_order_component_table`.`product_idx` where `purchase_order_idx` = ?		  
					order by `purchase_order_component_id` asc";	
					
			$data = DB::select("$raw_query_purchase_order_component", [ $request->purchase_order_id]);	
			
			$paymentlist = PurchaseOrderPaymentModel::where('purchase_order_idx', '=', $request->purchase_order_id)->get();
			$paymentcount = $paymentlist->count();
		
			return response()->json(array('productlist'=>$data,'paymentcount'=>$paymentcount));

	}
	
	public function get_purchase_order_payment_list(Request $request){		
	
			$data =  PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $request->purchase_order_id)
				->orderBy('purchase_order_payment_details_id', 'asc')
              	->get([
					'teves_purchase_order_payment_details.purchase_order_payment_details_id',
					'teves_purchase_order_payment_details.purchase_order_bank',
					'teves_purchase_order_payment_details.purchase_order_date_of_payment',
					'teves_purchase_order_payment_details.purchase_order_reference_no',
					'teves_purchase_order_payment_details.purchase_order_payment_amount',
					'teves_purchase_order_payment_details.image_reference'
					]);
		
			return response()->json($data);
	}

	public function delete_purchase_order_product(Request $request){		
			
				$productitemID = $request->purchase_order_component_id;
				PurchaseOrderComponentModel::find($productitemID)->delete();
		
				$order_total_amount = PurchaseOrderComponentModel::where('purchase_order_idx', $request->purchase_order_id)
						->sum('order_total_amount');
					
				$gross_amount = $order_total_amount;
		
				$PurchaseOrderInfo = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
						->get([
								'purchase_order_net_percentage', 
								'purchase_order_less_percentage'
						]);	
				
				$net_in_percentage 				= $PurchaseOrderInfo[0]->purchase_order_net_percentage;/*1.12*/
				$less_in_percentage 			= $PurchaseOrderInfo[0]->purchase_order_less_percentage/100;
							
				if($PurchaseOrderInfo[0]->purchase_order_net_percentage==0){
							$purchase_order_net_amount 			= 0;
							$purchase_order_total_due 			=  number_format($gross_amount,4,".","");
				}else{
							$purchase_order_net_amount 			=  number_format($gross_amount/$net_in_percentage,4,".","");
							$purchase_order_total_due 			=  number_format($gross_amount - (($gross_amount/$net_in_percentage)*$less_in_percentage),4,".","");
				}
				
				$PurchaseOrderUpdate = new PurchaseOrderModel();
				$PurchaseOrderUpdate = PurchaseOrderModel::find($request->purchase_order_id);
				$PurchaseOrderUpdate->purchase_order_gross_amount = number_format($gross_amount,4,".","");
				$PurchaseOrderUpdate->purchase_order_net_amount = $purchase_order_net_amount;
				$PurchaseOrderUpdate->purchase_order_total_payable = $purchase_order_total_due;
				$PurchaseOrderUpdate->update();	
		
				return 'Deleted';
				
	}
	
	public function purchase_order_delete_payment(Request $request){		
			
		$paymentitemID = $request->paymentitemID;
		$purchase_order_idx = $request->purchase_order_idx;
		
		PurchaseOrderPaymentModel::find($paymentitemID)->delete();
		
		
							/*Update Status*/
							/*Remaining Balance*/
							/*Get Purchase Order Total Payable*/
							$purchase_order_details = PurchaseOrderModel::find($purchase_order_idx, ['purchase_order_total_payable']);							
							$purchase_order_total_payable = $purchase_order_details->purchase_order_total_payable;
							
							/*Get Payment Details*/
							$purchase_order_total_payment_amount =  PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $purchase_order_idx)
								->sum('purchase_order_payment_amount');
								
							$remaining_balance = number_format($purchase_order_total_payable - $purchase_order_total_payment_amount+0,4, '.', '');
							$_paid_percentage = ($purchase_order_total_payment_amount / $purchase_order_total_payable) * 100;
						
							$paid_percentage = number_format($_paid_percentage,2,".","");
						
							/*IF Fully Paid Automatically Update the Status to Paid*/
							if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
							{		
								
								$PurchaseOrderstatus = "$paid_percentage% Paid";
								
							}else if($paid_percentage >= 100)
							{	
								
								$PurchaseOrderstatus = "Paid";
								
							}else{
								
								$PurchaseOrderstatus = "Pending";
								
							}
							
							/*Update Recievable Table*/
							$PurchaseOrderUpdate = new PurchaseOrderModel();
							$PurchaseOrderUpdate = PurchaseOrderModel::find($purchase_order_idx);
							
							$PurchaseOrderUpdate->purchase_order_remaining_balance 	= $remaining_balance;
							$PurchaseOrderUpdate->purchase_status 			= $PurchaseOrderstatus;
							
							$result_update = $PurchaseOrderUpdate->update();
		return 'Deleted';
		
		
	}
	
	public function update_purchase_status(Request $request){		
			
		$purchase_order_id = $request->purchase_order_id;
		$purchase_status = $request->purchase_status;
				
				$PurchaseOrderUpdate = new PurchaseOrderModel();
				$PurchaseOrderUpdate = PurchaseOrderModel::find($purchase_order_id);
				$PurchaseOrderUpdate->purchase_status = $purchase_status;
				$PurchaseOrderUpdate->update();
		
	}

	public function create_purchase_order_product_item(Request $request){

		$request->validate([
		  'item_description'      	=> 'required',
		  'order_quantity'      	=> 'required',
        ], 
        [
			'item_description.required' => 'Item Description or Product is Required',
			'order_quantity.required' => 'Quantity is Required'
        ]
		);
				
					/*Check if Price is From Manual Price*/
					if($request->product_manual_price!=0){
						
						$product_price = $request->product_manual_price;
						
					}else{
		
						/*Product Details*/
						$raw_query_product = "SELECT a.product_id, ifnull(b.branch_price,a.product_price) AS product_price FROM teves_product_table AS a
						LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
						WHERE b.branch_idx = ? and b.product_idx = ?";			
						$product_info = DB::select("$raw_query_product", [$request->branch_idx,$request->product_idx]);		
						
						$product_price = $product_info[0]->product_price;
						
					}
					
					$order_total_amount = $request->order_quantity * $product_price;	
					
					$purchase_order_data = PurchaseOrderModel::where('purchase_order_id', $request->purchase_order_id)
					->get([
					'teves_purchase_order_table.purchase_order_id',
					'teves_purchase_order_table.company_header',
					'teves_purchase_order_table.purchase_order_date',
					'teves_purchase_order_table.purchase_order_supplier_idx',
					'teves_purchase_order_table.purchase_order_net_percentage',
					'teves_purchase_order_table.purchase_order_less_percentage'
					]);
					
					$purchase_order_date = $purchase_order_data[0]->purchase_order_date;
					$supplier_idx = $purchase_order_data[0]->purchase_order_supplier_idx;
					
					if(($request->product_idx+0)==0){
						$item_description = $request->item_description;
					}else{
						$item_description = '';
					}
					
					$purchase_order_component_id = $request->purchase_order_component_id + 0;

					if($purchase_order_component_id==0){
						
						/*SAVE*/
						$PurchaseOrderComponent = new PurchaseOrderComponentModel();
						$PurchaseOrderComponent->purchase_order_idx 	= $request->purchase_order_id;
						$PurchaseOrderComponent->product_idx 			= $request->product_idx+0;
						$PurchaseOrderComponent->item_description 		= $item_description;
						$PurchaseOrderComponent->product_price 			= $product_price;
						$PurchaseOrderComponent->order_quantity 		= $request->order_quantity;
						$PurchaseOrderComponent->purchase_order_date 	= $purchase_order_date;
						$PurchaseOrderComponent->supplier_idx 			= $supplier_idx;
						$PurchaseOrderComponent->order_total_amount 	= $order_total_amount;
						
						$result = $PurchaseOrderComponent->save();
						
						$action = 'Insert';
						
					}
					else{
						
						/*UPDATE*/
						$PurchaseOrderComponent = new PurchaseOrderComponentModel();
						$PurchaseOrderComponent = PurchaseOrderComponentModel::find($request->purchase_order_component_id);
						$PurchaseOrderComponent->product_idx 			= $request->product_idx;
						$PurchaseOrderComponent->item_description 		= $item_description;
						$PurchaseOrderComponent->product_price 			= $product_price;
						$PurchaseOrderComponent->order_quantity 		= $request->order_quantity;
						$PurchaseOrderComponent->purchase_order_date 	= $purchase_order_date;
						$PurchaseOrderComponent->supplier_idx 			= $supplier_idx;
						$PurchaseOrderComponent->order_total_amount 	= $order_total_amount;
						
						$result = $PurchaseOrderComponent->update();

						$action = 'Update';
										
					}
				
					$sum_of_total_order_amount = PurchaseOrderComponentModel::where('purchase_order_idx', $request->purchase_order_id)
						->sum('order_total_amount');
						
					 $gross_amount = $sum_of_total_order_amount;
					
					$net_in_percentage 				= $purchase_order_data[0]->purchase_order_net_percentage;/*1.12*/
					$less_in_percentage 			= $purchase_order_data[0]->purchase_order_less_percentage/100;
								
					if($purchase_order_data[0]->purchase_order_net_percentage==0){
								$purchase_order_net_amount 		= 0;
								$purchase_order_total_due 		=  number_format($gross_amount,4,".","");
					}else{
								$purchase_order_net_amount 		=  number_format($gross_amount/$net_in_percentage,4,".","");
								$purchase_order_total_due 		=  number_format($gross_amount - (($gross_amount/$net_in_percentage)*$less_in_percentage),4,".","");
					}
					
					$PurchaseOrderUpdate = new PurchaseOrderModel();
					$PurchaseOrderUpdate = PurchaseOrderModel::find($request->purchase_order_id);
					$PurchaseOrderUpdate->purchase_order_gross_amount = number_format($gross_amount,4,".","");
					$PurchaseOrderUpdate->purchase_order_net_amount = $purchase_order_net_amount;
					$PurchaseOrderUpdate->purchase_order_total_payable = $purchase_order_total_due;
					$PurchaseOrderUpdate->update();		
				
				if($action=='Insert'){
					
						if($result){
							return response()->json(array('success' => "Product Information Successfully Created!"), 200);
						}
						else{
							return response()->json(['success'=>'Error on Creation Product Information']);
						}	
				}else{
						
						if($result){
							return response()->json(array('success' => "Product Information Successfully Updated!"), 200);
						}
						else{
							return response()->json(['success'=>'Error on Update Product Information']);
						}	
				}
				
	}

	public function purchase_order_component_info(Request $request){

			$raw_query_sales_order_component = "select IFNULL(`teves_product_table`.`product_name`,`teves_purchase_order_component_table`.item_description) as product_name,
			IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
			 `teves_purchase_order_component_table`.`product_price`,
			  `teves_purchase_order_component_table`.`order_quantity`,
			   `teves_purchase_order_component_table`.`order_total_amount`
				 from `teves_purchase_order_component_table` left join `teves_product_table`
				  on `teves_product_table`.`product_id` = `teves_purchase_order_component_table`.`product_idx`
				   where `teves_purchase_order_component_table`.`purchase_order_component_id` = ?";	
						
			$data = DB::select("$raw_query_sales_order_component", [ $request->purchase_order_component_id]);		

		return response()->json($data);
		
	}

	public function purchase_order_payment_info(Request $request){

		$PurchaseOrderPaymentInfo = PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_payment_details_id', $request->purchase_order_payment_details_id)
              	->get([
						'purchase_order_payment_details_id',
						'purchase_order_idx', 
						'purchase_order_bank',
						'purchase_order_date_of_payment',
						'purchase_order_reference_no',
						'purchase_order_payment_amount',
						'image_reference'
				]);	

		return response()->json($PurchaseOrderPaymentInfo);
		
	}
	
	public function purchase_order_product_info(Request $request){

			$raw_query_sales_order_component = "select IFNULL(`teves_product_table`.`product_name`,`teves_purchase_order_component_table`.item_description) as product_name,
			IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
			 `teves_purchase_order_component_table`.`product_price`,
			  `teves_purchase_order_component_table`.`order_quantity`,
			   `teves_purchase_order_component_table`.`order_total_amount`
				 from `teves_purchase_order_component_table` left join `teves_product_table`
				  on `teves_product_table`.`product_id` = `teves_purchase_order_component_table`.`product_idx`
				   where `teves_purchase_order_component_table`.`purchase_order_component_id` = ?";	
						
			$data = DB::select("$raw_query_sales_order_component", [ $request->purchase_order_component_id]);		

		return response()->json($data);
		
	}	
	
	
	public function save_purchase_order_payment(Request $request){

			 $request->validate([
			//$validator = \Validator::make($request->all(),[
				'payment_image_reference'			=>'image|mimes:jpg,png,jpeg,svg|max:10048',
				'purchase_order_bank'      			=> 'required',
				'purchase_order_date_of_payment'    => 'required',
				'purchase_order_reference_no'      	=> ['required',Rule::unique('teves_purchase_order_payment_details')->where( 
													fn ($query) =>$query
														->where('purchase_order_idx', $request->purchase_order_id_payment)
														->where('purchase_order_reference_no', $request->purchase_order_reference_no)
														->where('purchase_order_payment_details_id', '<>',  $request->purchase_order_payment_details_id )														
													)],
				'purchase_order_payment_amount'     => 'required',
           ],[
				'purchase_order_bank.required' 				=> 'Bank Details is Required',
				'purchase_order_date_of_payment.required' 	=> 'Date of Payment is Required',
				'purchase_order_reference_no.required' 		=> 'Reference Number Required',
				'purchase_order_payment_amount.required' 	=> 'Payment Amount is Required'
           ]);
		   
			   if ($request->hasFile('payment_image_reference')) {
				   
					   $path = 'files/';
					   $file = $request->file('payment_image_reference');
					   @$file_name = time().'_'.@$file->getClientOriginalName();

						//$upload = $file->storeAs($path, $file_name);
						$upload = $file->storeAs($path, $file_name, 'public');
						
						//$name = $request->file('payment_image_reference')->getClientOriginalName();			
						//$path = $request->file('image')->store('public/images');

						$path = $request->file('payment_image_reference')->getRealPath();    
						$logo = file_get_contents($path);
						$image_blob = base64_encode($logo);
				
							$purchase_order_idx = $request->purchase_order_id_payment;
							$purchase_order_payment_id = $request->purchase_order_payment_details_id;
							
							if($purchase_order_payment_id==0){
								
							$PurchaseOrderPaymentComponent = new PurchaseOrderPaymentModel();
							
							$PurchaseOrderPaymentComponent->purchase_order_idx 				= $purchase_order_idx;
							$PurchaseOrderPaymentComponent->purchase_order_bank 			= $request->purchase_order_bank;
							$PurchaseOrderPaymentComponent->purchase_order_date_of_payment 	= $request->purchase_order_date_of_payment;
							$PurchaseOrderPaymentComponent->purchase_order_reference_no 	= $request->purchase_order_reference_no;
							$PurchaseOrderPaymentComponent->purchase_order_payment_amount 	= $request->purchase_order_payment_amount;
							$PurchaseOrderPaymentComponent->image_reference 				= $image_blob;
							$result = $PurchaseOrderPaymentComponent->save();
							$result_type = 'Saved';
							}
							else{
							
							$PurchaseOrderPaymentComponent = new PurchaseOrderPaymentModel();
							
							$PurchaseOrderPaymentComponent = PurchaseOrderPaymentModel::find($purchase_order_payment_id);
							$PurchaseOrderPaymentComponent->purchase_order_bank 			= $request->purchase_order_bank;
							$PurchaseOrderPaymentComponent->purchase_order_date_of_payment 	= $request->purchase_order_date_of_payment;
							$PurchaseOrderPaymentComponent->purchase_order_reference_no 	= $request->purchase_order_reference_no;
							$PurchaseOrderPaymentComponent->purchase_order_payment_amount 	= $request->purchase_order_payment_amount;
							$PurchaseOrderPaymentComponent->image_reference 				= $image_blob;
							$result = $PurchaseOrderPaymentComponent->update();
							$result_type = 'Updated';

							}
					
			   }else{	
				
							$purchase_order_idx = $request->purchase_order_id_payment;
							$purchase_order_payment_id = $request->purchase_order_payment_details_id;
							
							if($purchase_order_payment_id==0){
								
							$PurchaseOrderPaymentComponent = new PurchaseOrderPaymentModel();
							
							$PurchaseOrderPaymentComponent->purchase_order_idx 				= $purchase_order_idx;
							$PurchaseOrderPaymentComponent->purchase_order_bank 			= $request->purchase_order_bank;
							$PurchaseOrderPaymentComponent->purchase_order_date_of_payment 	= $request->purchase_order_date_of_payment;
							$PurchaseOrderPaymentComponent->purchase_order_reference_no 	= $request->purchase_order_reference_no;
							$PurchaseOrderPaymentComponent->purchase_order_payment_amount 	= $request->purchase_order_payment_amount;
							
							$result = $PurchaseOrderPaymentComponent->save();
							$result_type = 'Saved';
							
							}
							else{
							
							$PurchaseOrderPaymentComponent = new PurchaseOrderPaymentModel();
							
							$PurchaseOrderPaymentComponent = PurchaseOrderPaymentModel::find($purchase_order_payment_id);
							$PurchaseOrderPaymentComponent->purchase_order_bank 			= $request->purchase_order_bank;
							$PurchaseOrderPaymentComponent->purchase_order_date_of_payment 	= $request->purchase_order_date_of_payment;
							$PurchaseOrderPaymentComponent->purchase_order_reference_no 	= $request->purchase_order_reference_no;
							$PurchaseOrderPaymentComponent->purchase_order_payment_amount 	= $request->purchase_order_payment_amount;
							
							$result = $PurchaseOrderPaymentComponent->update();
							$result_type = 'Updated';

							}
							
			   }

							/*Update Status*/
							/*Remaining Balance*/
							/*Get Purchase Order Total Payable*/
							$purchase_order_details = PurchaseOrderModel::find($purchase_order_idx, ['purchase_order_total_payable']);							
							$purchase_order_total_payable = $purchase_order_details->purchase_order_total_payable;
							
							/*Get Payment Details*/
							$purchase_order_total_payment_amount =  PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $purchase_order_idx)
								->sum('purchase_order_payment_amount');
								
							$remaining_balance = number_format($purchase_order_total_payable - $purchase_order_total_payment_amount+0,2, '.', '');
							$_paid_percentage = ($purchase_order_total_payment_amount / $purchase_order_total_payable) * 100;
						
							$paid_percentage = number_format($_paid_percentage,2,".","");
						
							/*IF Fully Paid Automatically Update the Status to Paid*/
							if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
							{	
								
								$PurchaseOrderstatus = "$paid_percentage%";
								
							}else if($paid_percentage >= 100)
							{	
								
								$PurchaseOrderstatus = "$paid_percentage%";
								
							}else{
								
								$PurchaseOrderstatus = "Pending";
								
							}
							
							/*Update Recievable Table*/
							$PurchaseOrderUpdate = new PurchaseOrderModel();
							$PurchaseOrderUpdate = PurchaseOrderModel::find($purchase_order_idx);
							
							$PurchaseOrderUpdate->purchase_order_remaining_balance 	= $remaining_balance;
							$PurchaseOrderUpdate->purchase_status 			= $PurchaseOrderstatus;
							
							$result_update = $PurchaseOrderUpdate->update();
			

						if($result){
							return response()->json(array('success' => "Payment Information Successfully!"), 200);
						}
						else{
							return response()->json(['success'=>'Error on Payment Information']);
						}	

              // }
           }
     
	 
}
