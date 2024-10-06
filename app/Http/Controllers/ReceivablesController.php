<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ReceivablesModel;
use App\Models\SalesOrderModel;
use App\Models\BillingTransactionModel;
use App\Models\ReceivablesPaymentModel;
use App\Models\ClientModel;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReceivablesController extends Controller
{
	
	/*Load Product Interface*/
	public function receivables(){
		
		if(Session::has('loginID')){
			
			$title = 'Receivable';
			$data = array();
			
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
			
			return view("pages.receivables", compact('data','title','teves_branch'));
			
		}
		
	}   

	/*Load Report Interface*/
	public function create_recievable(){
		
		
		if(Session::has('loginID')){
			
			$title = 'Create Receivable';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$client_data = ClientModel::all();
			
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
			
			$drivers_name = BillingTransactionModel::select('drivers_name')->distinct()->get();
			$plate_no = BillingTransactionModel::select('plate_no')->distinct()->get();
		
			return view("pages.create_recievable", compact('data','title','client_data','drivers_name','plate_no','product_data','teves_branch'));
		
		}
	
	}   
	
	/*Fetch Receivables List using Datatable*/
	/*Recievable from Billing*/
	public function getReceivablesList_billing(Request $request)
    {
		
		if(Session::has('loginID')){
			
			// $list = ReceivablesModel::get();
			if ($request->ajax()) {

			$data = ReceivablesModel::join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
						->WHERE('teves_receivable_table.sales_order_idx', '=', '0')
						->get([
						'teves_receivable_table.receivable_id',
						'teves_receivable_table.sales_order_idx',
						'teves_receivable_table.billing_date',
						'teves_receivable_table.billing_period_start',
						'teves_receivable_table.billing_period_end',
						'teves_client_table.client_name',
						'teves_receivable_table.control_number',		
						'teves_receivable_table.receivable_description',
						'teves_receivable_table.receivable_gross_amount',
						'teves_receivable_table.receivable_vatable_sales',
						'teves_receivable_table.receivable_vat_amount',
						'teves_receivable_table.receivable_withholding_tax',
						'teves_receivable_table.receivable_amount',
						'teves_receivable_table.receivable_remaining_balance',
						'teves_receivable_table.receivable_status',
						'teves_receivable_table.created_at']);
											
					return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){	
					
						if($row->sales_order_idx==0){
							$menu_for_update = 'editReceivables';
						}else{
							$menu_for_update = 'editReceivablesFromSalesOrder';
						}
									
							$actionBtn_admin = '<div align="center" class="action_table_menu_Product">
										<a href="receivable_from_billing_form?receivable_id='.$row->receivable_id.'&tab=payment" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-cash-stack btn_icon_table btn_icon_table_view" title="Add Payment"></a>
										<a href="receivable_from_billing_form?receivable_id='.$row->receivable_id.'&tab=product" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-pencil-fill btn_icon_table btn_icon_table_edit" title="Update"></a>
										<a href="#" data-id="'.$row->receivable_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteReceivables" title="Delete"></a>
									</div>';
							
						$startTimeStamp = strtotime($row->created_at);
						$endTimeStamp = strtotime(date('y-m-d'));
						$timeDiff = abs($endTimeStamp - $startTimeStamp);
						$numberDays = $timeDiff/86400;  // 86400 seconds in one day
						// and you might want to convert to integer
						$numberDays = intval($numberDays);
							
							if($numberDays>=3){

							$actionBtn_user = '<div align="center" class="action_table_menu_Product">
										<a href="receivable_from_billing_form?receivable_id='.$row->receivable_id.'&tab=payment" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-cash-stack btn_icon_table btn_icon_table_view" title="Add Payment"></a>
										</div>';
										
							}else{
							
							$actionBtn_user = '<div align="center" class="action_table_menu_Product">
										<a href="receivable_from_billing_form?receivable_id='.$row->receivable_id.'&tab=payment" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-cash-stack btn_icon_table btn_icon_table_view" title="Add Payment"></a>
										<a href="receivable_from_billing_form?receivable_id='.$row->receivable_id.'&tab=product" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-pencil-fill btn_icon_table btn_icon_table_edit" title="Update"></a>
										</div>';
										
							}
							// if($row->receivable_status == 'Paid' && Session::get('UserType')!="Admin"){
										// return '';
							// }else{
										// return $actionBtn;
							// }
								
							if(Session::get('UserType')=="Admin"){
										return $actionBtn_admin;
							}else{
								
								if($row->receivable_status != 'Paid'){
										return $actionBtn_user;
								}else{
										return '';
								}
										
							}
						
					})
					
					->addColumn('action_print', function($row){
						
						if($row->sales_order_idx==0){
							$print_sales_or_billing = '<option value="PrintBilling">Billing</option>';
						}else{
							$print_sales_or_billing = '<option value="PrintSalesOrder">Sales Order</option>';
						}
						
						$action_print = '
						<div align="center" class="action_table_menu_Product">
						<select class="receivable_print_'.$row->receivable_id.'" name="receivable_print_'.$row->receivable_id.'" id="receivable_print_'.$row->receivable_id.'" onchange="receivable_print('.$row->receivable_id.')">	
							<option disabled="" selected value="">Choose...</option>
							<option value="PrintStatement" title="Statement of Account">SOA</option>
							'.$print_sales_or_billing.'
							<option value="PrintReceivables">Receivable</option>
							</select>
						</div>';
						return $action_print;
					})
					
					
					->rawColumns(['action','action_print'])
					->make(true);
			}	
		}			
    }
	/*Recievable from Billing*/
	public function getReceivablesList_sales_order(Request $request)
    {
					
		if(Session::has('loginID')){
			
			//$list = ReceivablesModel::get();
			if ($request->ajax()) {

			$data = ReceivablesModel::join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
						->join('teves_sales_order_table', 'teves_sales_order_table.sales_order_id', '=', 'teves_receivable_table.sales_order_idx')
						->WHERE('teves_receivable_table.sales_order_idx', '<>', '0')
						->get([
						'teves_receivable_table.receivable_id',
						'teves_receivable_table.sales_order_idx',
						'teves_receivable_table.billing_date',
						'teves_client_table.client_name',
						'teves_receivable_table.control_number',		
						'teves_receivable_table.receivable_description',
						'teves_receivable_table.receivable_gross_amount',
						'teves_receivable_table.receivable_vatable_sales',
						'teves_receivable_table.receivable_vat_amount',
						'teves_receivable_table.receivable_withholding_tax',
						'teves_receivable_table.receivable_amount',
						'teves_receivable_table.receivable_remaining_balance',
						'teves_receivable_table.receivable_status',
						'teves_sales_order_table.sales_order_payment_status',
					    'teves_sales_order_table.sales_order_delivery_status'
						]);
											
					return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){	
				
							$menu_for_update = 'editReceivablesFromSalesOrder';

							$actionBtn_admin = '<div align="center" class="action_table_menu_Product">
										<a href="#" class="btn-circle btn-sm bi bi-images btn_icon_table btn_icon_table_gallery" onclick="ViewGalery('.$row->receivable_id.')" id="viewPaymentGalery"></a>
										<a href="sales_order_form?sales_order_id='.$row->sales_order_idx.'&tab=payment" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-cash-stack btn_icon_table btn_icon_table_view" title="Add Payment"></a>
										<a href="sales_order_form?sales_order_id='.$row->sales_order_idx.'&tab=delivery" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle btn-sm bi bi-truck btn_icon_table btn_icon_table_delivery" title="Delivery"></a>
										<a href="sales_order_form?sales_order_id='.$row->sales_order_idx.'&tab=receivable" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" title="Update"></a>
										<a href="#" data-id="'.$row->receivable_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteReceivables" title="Delete"></a>
									</div>';
					
							$actionBtn_user = '<div align="center" class="action_table_menu_Product">
										<a href="#" class="btn-circle btn-sm bi bi-images btn_icon_table btn_icon_table_gallery" onclick="ViewGalery('.$row->receivable_id.')" id="viewPaymentGalery"></a>
										<a href="sales_order_form?sales_order_id='.$row->sales_order_idx.'&tab=payment" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle bi bi-cash-stack btn_icon_table btn_icon_table_view" title="Add Payment"></a>
										<a href="sales_order_form?sales_order_id='.$row->sales_order_idx.'&tab=delivery" data-id="'.$row->receivable_id.'" class="btn-warning btn-circle btn-sm bi bi-truck btn_icon_table btn_icon_table_delivery" title="Delivery"></a>
									</div>';
					
							// if($row->receivable_status == 'Paid' && Session::get('UserType')!="Admin"){
										// return '';
							// }else{
										// return $actionBtn;
							// }
							
								
							if(Session::get('UserType')=="Admin"){
										return $actionBtn_admin;
							}else{
								
								if($row->receivable_status != 'Paid'){
										return $actionBtn_user;
								}else{
										return '';
								}
										
							}
						
					})
					
					->addColumn('action_print', function($row){
						
						if($row->sales_order_idx==0){
							$print_sales_or_billing = '<option value="PrintBilling">Billing</option>';
						}else{
							$print_sales_or_billing = '<option value="PrintSalesOrder">Sales Order</option>';
						}
						
						$action_print = '
						<div align="center" class="action_table_menu_Product">
						<select class="receivable_print_'.$row->receivable_id.'" name="receivable_print_'.$row->receivable_id.'" id="receivable_print_'.$row->receivable_id.'" onchange="receivable_print('.$row->receivable_id.')">	
							<option disabled="" selected value="">Choose...</option>
							<option value="PrintStatement" title="Statement of Account">SOA</option>
							'.$print_sales_or_billing.'
							<option value="PrintReceivables">Receivable</option>
							</select>
						</div>';
						return $action_print;
					})
					
					
					->rawColumns(['action','action_print'])
					->make(true);
			}	
		}			
    }

	/*Payment List*/
	public function get_receivable_payment_list(Request $request){		
	
		if(Session::has('loginID')){
			
				$data =  ReceivablesPaymentModel::where('teves_receivable_payment.receivable_idx', $request->receivable_id)
					->orderBy('receivable_payment_id', 'asc')
					->get([
						'teves_receivable_payment.receivable_payment_id',
						'teves_receivable_payment.receivable_date_of_payment',
						'teves_receivable_payment.receivable_mode_of_payment',
						'teves_receivable_payment.receivable_reference',
						'teves_receivable_payment.receivable_payment_amount',
						'teves_receivable_payment.image_reference',
						]);
			
				return response()->json($data);
				
		}
			
	}
	
	/*Delete Payment Item*/
	public function delete_receivable_payment_item(Request $request){		
		
		if(Session::has('loginID')){
			
			if(Session::get('UserType')=="Admin"){
				
				$paymentitemID = $request->paymentitemID;
				$receivableID = $request->receivable_id;
			
				ReceivablesPaymentModel::find($paymentitemID)->delete();
			
				/*Remaining Balance*/
				/*Get Receivable Details*/
				$receivable_details = ReceivablesModel::find($receivableID, ['receivable_amount']);							
				$receivable_amount = $receivable_details->receivable_amount;
				
				/*Get Receivable Payment Details*/
				$receivable_payment_amount =  ReceivablesPaymentModel::where('teves_receivable_payment.receivable_idx', $receivableID)
					->sum('receivable_payment_amount');
					
				$remaining_balance = number_format($receivable_amount - $receivable_payment_amount+0,2, '.', '');;
			
				/*IF Fully Paid Automatically Update the Status to Paid*/
				if($remaining_balance <= 0)
				{
					$receivable_status = 'Paid';
				}else{
					$receivable_status = 'Pending';
				}
				
				/*Update Recievable Table*/
				$Receivables_update = new ReceivablesModel();
				$Receivables_update = ReceivablesModel::find($receivableID);
				
				$Receivables_update->receivable_remaining_balance 	= $remaining_balance;
				$Receivables_update->receivable_status 				= $receivable_status;
				
				$result_update = $Receivables_update->update();

				return 'Deleted';
				
			}
			
		}
	}
	


	public function create_receivables_from_sale_order_post(Request $request){		

		$request->validate([
			'receivable_description'  	=> 'required'
        ], 
        [
			'receivable_description.required' 	=> 'Description is Required'
        ]
		);
			
			/*
			if need to use where
			$last_id = ReceivablesModel::where('control_number', '<>', '')->latest()->first()->receivable_id;
			*/
			
			$last_id = ReceivablesModel::latest()->first()->receivable_id;
			
			/*GET SALES ORDER*/
			$sales_order_idx = $request->sales_order_idx;
			
					$SalesOrderData = SalesOrderModel::where('sales_order_id', $sales_order_idx)
					->get([
					'teves_sales_order_table.sales_order_id',
					'teves_sales_order_table.company_header',
					'teves_sales_order_table.sales_order_client_idx',
					'teves_sales_order_table.sales_order_total_due',
					'teves_sales_order_table.sales_order_payment_term']);	
			
			$BranchInfo = TevesBranchModel::where('branch_id', '=', $SalesOrderData[0]->company_header)->first();				
			$control_number = $BranchInfo->branch_initial."-AR-".($last_id+1);			
			
			/*Save to Receivables*/
			$Receivables = new ReceivablesModel();
			$Receivables->client_idx 					= $SalesOrderData[0]->sales_order_client_idx;
			$Receivables->control_number 				= $control_number;
			$Receivables->sales_order_idx 				= $request->sales_order_idx;
			$Receivables->billing_date 					= $request->billing_date;
			$Receivables->payment_term 					= $SalesOrderData[0]->sales_order_payment_term;
			$Receivables->receivable_description 		= $request->receivable_description;
			$Receivables->receivable_status 			= 'Pending';			
			$Receivables->receivable_amount 			= $SalesOrderData[0]->sales_order_total_due;
			$Receivables->receivable_remaining_balance 	= $SalesOrderData[0]->sales_order_total_due;
			$Receivables->company_header 				= $SalesOrderData[0]->company_header;
			$Receivables->created_by_user_id 			= Session::get('loginID');
			$result = $Receivables->save();
			
			/*Update Sales Order to Delivered*/
			$salesOrderUpdate = new SalesOrderModel();
			$salesOrderUpdate = SalesOrderModel::find($sales_order_idx);
			$salesOrderUpdate->sales_order_status = 'Delivered';
			$salesOrderUpdate->update();

			if($result){
				return response()->json(array('success' => true, 'receivable_id' => $Receivables->receivable_id), 200);
			}
			else{
				return response()->json(['success'=>'Error on Insert Receivables Information']);
			}
	}
	
	public function update_receivables_from_sale_order_post(Request $request){		

		$request->validate([
			'receivable_description'  	=> 'required'
        ], 
        [
			'receivable_description.required' 	=> 'Description is Required'
        ]
		);
		
			$receivable_data = ReceivablesModel::where('receivable_id', $request->ReceivableID)
              		->get(['teves_receivable_table.created_at']);
		
			$startTimeStamp = strtotime($receivable_data[0]['created_at']);
			$endTimeStamp = strtotime(date('y-m-d'));
			$timeDiff = abs($endTimeStamp - $startTimeStamp);
			$numberDays = $timeDiff/86400;  // 86400 seconds in one day
			// and you might want to convert to integer
			$numberDays = intval($numberDays);

			if(Session::get('UserType')=="Admin"){
				
					$ReceivableID = $request->ReceivableID;
					/*Update to Receivables*/
					$Receivables = new ReceivablesModel();
					$Receivables = ReceivablesModel::find($ReceivableID);
					$Receivables->billing_date 					= $request->billing_date;
					$Receivables->payment_term 					= $request->payment_term;
					$Receivables->receivable_description 		= $request->receivable_description;
					$Receivables->updated_by_user_id 			= Session::get('loginID');
					$result = $Receivables->update();

					if($result){
						return response()->json(array('success' => 'Receivable Information Successfully Updated!', 'receivable_id' => $request->ReceivableID), 200);
					}
					else{
						return response()->json(['success'=>'Error on Update Receivables Information']);
					}
					
			}else{
			
				if($numberDays>=3){
					return response()->json(['success'=>'You can no longer Edit this Receivables item or Ask the Admin to Edit']);
				}
			
			}
	}
		
	/*Fetch Receivable Information*/
	public function receivable_info(Request $request){

					$receivable_id = $request->receivable_id;
					$data = ReceivablesModel::where('receivable_id', $request->receivable_id)
					->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
              		->get([
					'teves_receivable_table.receivable_id',
					'teves_receivable_table.sales_order_idx',
					'teves_receivable_table.billing_date',
					'teves_client_table.client_address',
					'teves_client_table.client_name',
					'teves_client_table.client_id',
					'teves_receivable_table.control_number',
					'teves_client_table.client_tin',
					'teves_receivable_table.or_number',
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'teves_receivable_table.receivable_status',
					'teves_receivable_table.billing_period_start',
					'teves_receivable_table.billing_period_end',
					'teves_receivable_table.less_per_liter',
					'teves_receivable_table.receivable_net_value_percentage',
					'teves_receivable_table.receivable_vat_value_percentage',
					'teves_receivable_table.receivable_withholding_tax_percentage',
					'teves_receivable_table.company_header',
					'teves_receivable_table.created_at']);
					return response()->json($data);
		
	}

	/*Delete Receivable Information*/
	public function delete_receivable_confirmed(Request $request){

		$receivableID = $request->receivable_id;
		ReceivablesModel::find($receivableID)->delete();
		
		/*Delete Payment*/	
		ReceivablesPaymentModel::where('receivable_idx', $receivableID)->delete();
		
		$billing_update = BillingTransactionModel::where('receivable_idx', '=', $receivableID)
				->update(
					[
					'receivable_idx' => 0
					],
				);
		
		return 'Deleted';
		
	} 

	public function create_receivables_post(Request $request){

		$request->validate([
			'receivable_description'  	=> 'required'
        ], 
        [
			'receivable_description.required' 	=> 'Description is Required'
        ]
		);

			@$last_id = ReceivablesModel::latest()->first()->receivable_id;

			$client_idx = $request->client_idx;
			$start_date = $request->start_date;
			$end_date = $request->end_date;
			
			$less_per_liter = $request->less_per_liter;
			$withholding_tax_percentage = $request->withholding_tax_percentage;
			$net_value_percentage = $request->net_value_percentage;
			$vat_value_percentage = $request->vat_value_percentage;
		
			$receivable_amount = BillingTransactionModel::where('client_idx', $client_idx)
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
				->where('receivable_idx', '=', 0)
				->groupBy('teves_billing_table.client_idx')
				->sum('order_total_amount');
					
			$receivable_total_liter = BillingTransactionModel::where('client_idx', $client_idx)
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
                ->where('teves_product_table.product_unit_measurement', '=', 'L')
				->where('receivable_idx', '=', 0)
				->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
				->groupBy('teves_billing_table.client_idx')
				->groupBy('teves_product_table.product_unit_measurement')
				->sum('order_quantity');

			$gross_amount = $receivable_amount;
			$vatable_sales = $gross_amount / $net_value_percentage;
			$vatable_amount = ($gross_amount / $net_value_percentage) * $vat_value_percentage;
			$withholding_tax = $vatable_sales * $withholding_tax_percentage;
			
			$total_amount_due = $receivable_amount - (($receivable_total_liter*$less_per_liter) + ($withholding_tax));
					
			$BranchInfo = TevesBranchModel::where('branch_id', '=', $request->company_header)->first();				
			$control_number = $BranchInfo->branch_initial."-AR-".($last_id+1);					
					
			$Receivables = new ReceivablesModel();
			$Receivables->client_idx 				= $request->client_idx;
			$Receivables->control_number 			= $control_number;
			$Receivables->billing_date 				= date('Y-m-d');
			$Receivables->payment_term 				= $request->payment_term;
			$Receivables->receivable_description 	= $request->receivable_description;
			
			$Receivables->receivable_gross_amount 		= number_format($gross_amount,2, '.', '');			
			$Receivables->receivable_vatable_sales 		= number_format($vatable_sales,2, '.', '');
			$Receivables->receivable_vat_amount 		= number_format($vatable_amount,2, '.', '');
			$Receivables->receivable_withholding_tax 	= number_format($withholding_tax,2, '.', '');			
			$Receivables->receivable_amount 			= number_format($total_amount_due,2, '.', '');
			$Receivables->receivable_remaining_balance 	= number_format($total_amount_due,2, '.', '');
			
			$Receivables->receivable_net_value_percentage 			=  number_format($net_value_percentage,2, '.', '');
			$Receivables->receivable_withholding_tax_percentage 	=  number_format($withholding_tax_percentage * 100,2, '.', '');
			$Receivables->receivable_vat_value_percentage 			=  number_format($vat_value_percentage * 100,2, '.', '');
					
			$Receivables->receivable_status 			= 'Pending';		
			$Receivables->billing_period_start 			= $start_date;
			$Receivables->billing_period_end 			= $end_date;
			
			$Receivables->less_per_liter 		= $request->less_per_liter;
			$Receivables->company_header 		= $request->company_header;
			$Receivables->created_by_user_id 			= Session::get('loginID');
			$result = $Receivables->save();
			
			/*Update Billing List Affected by the Receivable*/
			/*Included branch_idx February 25, 2024*/
			
			$billing_update = BillingTransactionModel::where('client_idx', $client_idx)
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
				->where('receivable_idx', '=', 0)
				->update([
					'receivable_idx' => $Receivables->receivable_id]);
			
			if($result){
				return response()->json(array('success' => true, 'receivable_id' => $Receivables->receivable_id), 200);
			}
			else{
				return response()->json(['success'=>'Error on Insert Receivables Information']);
			}
	}

	public function update_receivables_post(Request $request){		
		
	$request->validate([
			'receivable_description'  	=> 'required'
        ], 
        [
			'receivable_description.required' 	=> 'Description is Required'
        ]
		);
			
			/*Get Client ID from Receivable*/
			$client_idx = ReceivablesModel::where('receivable_id', $request->ReceivableID)
			  		->get([
					'teves_receivable_table.client_idx',
					'teves_receivable_table.created_at']);		

			$startTimeStamp = strtotime($client_idx[0]['created_at']);
			$endTimeStamp = strtotime(date('y-m-d'));
			$timeDiff = abs($endTimeStamp - $startTimeStamp);
			$numberDays = $timeDiff/86400;  // 86400 seconds in one day
			// and you might want to convert to integer
			$numberDays = intval($numberDays);

			$start_date = $request->start_date;
			$end_date = $request->end_date;
		
			$receivable_amount = BillingTransactionModel::where('client_idx', $client_idx[0]['client_idx'])
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
				->where('receivable_idx', '=', $request->ReceivableID)
				->groupBy('teves_billing_table.client_idx')
				->sum('order_total_amount');
					
			$receivable_total_liter = BillingTransactionModel::where('client_idx', $client_idx[0]['client_idx'])
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
				->where('receivable_idx', '=', $request->ReceivableID)
                ->where('teves_product_table.product_unit_measurement', '=', 'L')
				->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
				->groupBy('teves_billing_table.client_idx')
				->groupBy('teves_product_table.product_unit_measurement')
				->sum('order_quantity');
			
			$less_per_liter = $request->less_per_liter;
			
			/*Sum Rendered Payment*/
			$_receivable_total_payment_amount = ReceivablesPaymentModel::where('receivable_idx', $request->ReceivableID)
				->sum('receivable_payment_amount');
			
			$withholding_tax_percentage = $request->withholding_tax_percentage;
			$net_value_percentage = $request->net_value_percentage;
			$vat_value_percentage = $request->vat_value_percentage;
			
			$gross_amount = $receivable_amount;
			$vatable_sales = $gross_amount / $net_value_percentage;
			$vatable_amount = ($gross_amount / $net_value_percentage) * $vat_value_percentage;
			$withholding_tax = $vatable_sales * $withholding_tax_percentage;
			
			$total_amount_due = $receivable_amount - (($receivable_total_liter*$less_per_liter) + ($withholding_tax));
		
			if($_receivable_total_payment_amount==0){
				$receivable_total_payment_amount = $total_amount_due;
			}else{
				$receivable_total_payment_amount = $total_amount_due - $_receivable_total_payment_amount;
			}
		
			$Receivables = new ReceivablesModel();
			$Receivables = ReceivablesModel::find($request->ReceivableID);
			$Receivables->billing_date 					= $request->billing_date;
			$Receivables->payment_term 					= $request->payment_term;
			$Receivables->receivable_description 		= $request->receivable_description;
			
			$Receivables->receivable_gross_amount 		= number_format($gross_amount,2, '.', '');			
			$Receivables->receivable_vatable_sales 		= number_format($vatable_sales,2, '.', '');
			$Receivables->receivable_vat_amount 		= number_format($vatable_amount,2, '.', '');
			$Receivables->receivable_withholding_tax 	= number_format($withholding_tax,2, '.', '');			
			$Receivables->receivable_amount 			= number_format($total_amount_due,2, '.', '');
			
			$Receivables->receivable_remaining_balance 	= number_format($receivable_total_payment_amount,2, '.', '');
			
			$Receivables->receivable_net_value_percentage 			= number_format($net_value_percentage,2, '.', '');
			$Receivables->receivable_withholding_tax_percentage 	= number_format($withholding_tax_percentage * 100,2, '.', '');
			$Receivables->receivable_vat_value_percentage 			= number_format($vat_value_percentage * 100,2, '.', '');			
			
			$Receivables->billing_period_start 			= $request->start_date;
			$Receivables->billing_period_end 			= $request->end_date;
			
			$Receivables->less_per_liter 				= $request->less_per_liter;
			$Receivables->company_header 				= $request->company_header;
			
			/*Update Billing List Affected by the Receivable*/
			/*Included branch_idx February 25, 2024*/
			
			$billing_update = BillingTransactionModel::where('client_idx', $client_idx[0]['client_idx'])
				->where('order_date', '>=', $start_date)
                ->where('order_date', '<=', $end_date)
				->where('receivable_idx', '=', 0)
				->update([
					'receivable_idx' => $request->ReceivableID]);
			
			if(Session::get('UserType')=="Admin"){
				
					$result = $Receivables->update();
					
					if($result){
						return response()->json(['success'=>'Receivables Information Successfully Updated!']);
					}
					else{
						return response()->json(['success'=>'Error on Update Receivables Information']);
					}
					
			}else{
			
				if($numberDays>=3){
					return response()->json(['success'=>'You can no longer Edit this Receivable item.']);
				}
			
			}
	}
	
	/*Load Sales Order Form Interface*/
	public function receivable_from_billing_form(Request $request){
		
		$ReceivableID = $request->receivable_id;
		$tab = $request->tab;
		
		$title = 'Receivable and Billing Information Form';
		$data = array();
		if(Session::has('loginID')){
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();/*User Data*/
			
			$client_data = ClientModel::all();
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
			
			$receivables_payment_suggestion = ReceivablesPaymentModel::select('receivable_mode_of_payment')->distinct()->get();
			
			$receivables_details = ReceivablesModel::where('receivable_id', '=', $ReceivableID)->first();

			$drivers_name = BillingTransactionModel::select('drivers_name')->distinct()->get();
			$plate_no = BillingTransactionModel::select('plate_no')->distinct()->get();
					
		return view("pages.billing_to_receivable_form", compact('data','title','product_data','client_data','teves_branch','receivables_payment_suggestion','receivables_details','tab','drivers_name','plate_no'));
		
		}
		
	}  	

	/*Generated for receivable after saved*/
	public function billing_to_receivable_product(Request $request){

		$request->validate([
          'client_idx'      		=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'client_idx.required' 	=> 'Please select a Client',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);
		
		$receivable_id = $request->receivable_id;
		$client_idx = $request->client_idx;
		$start_date = $request->start_date;
		$end_date = $request->end_date;
					
		/*Using Raw Query*/
		$raw_query = "select `teves_billing_table`.`billing_id`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time`, `teves_billing_table`.`created_at` from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` BETWEEN ? and ? and `teves_billing_table`.`receivable_idx` = ? order by `teves_billing_table`.`order_date` asc";			
		$billing_data = DB::select("$raw_query", [$client_idx,$start_date,$end_date,$receivable_id]);
		
		//return response()->json($billing_data);
		
		
			$paymentlist = ReceivablesPaymentModel::where('receivable_idx', '=', $request->receivable_id)->get();
			$paymentcount = $paymentlist->count();
		
			return response()->json(array('productlist'=>$billing_data,'paymentcount'=>$paymentcount));		
	}	
	
	
	public function billing_receivable_payment_post(Request $request){
        	 
		$request->validate([
		  //$validator = \Validator::make($request->all(),[
			  'payment_image_reference'			=>'image|mimes:jpg,png,jpeg,svg|max:10048',
			  'receivable_mode_of_payment'      	=> 'required',
			  'receivable_date_of_payment'      	=> 'required',
			  'receivable_reference'      		=> ['required',Rule::unique('teves_receivable_payment')->where( 
												  fn ($query) =>$query
													  ->where('receivable_idx', $request->receivable_idx_payment)
													  ->where('receivable_reference', $request->receivable_reference)
													  ->where('receivable_payment_id', '<>',  $request->receivable_payment_id )
												  )],
			  'receivable_payment_amount'       	=> 'required',
		 ],[
			  'receivable_mode_of_payment.required' 	=> 'Bank Details is Required',
			  'receivable_date_of_payment.required' 	=> 'Date of Payment is Required',
			  'receivable_reference.required' 		=> 'Reference Number Required',
			  'receivable_payment_amount.required' 	=> 'Payment Amount is Required'
		 ]);
			 
			 if ($request->hasFile('payment_image_reference')) {
				 
					 $path = 'files/';
					 $file = $request->file('payment_image_reference');
					 @$file_name = time().'_'.@$file->getClientOriginalName();

					  $upload = $file->storeAs($path, $file_name, 'public');
					  
					  $path = $request->file('payment_image_reference')->getRealPath();    
					  $logo = file_get_contents($path);
					  $image_blob = base64_encode($logo);
			  
						  $receivable_idx = $request->receivable_idx_payment;
						  $receivable_payment_id = $request->receivable_payment_id;
						  
						  if($receivable_payment_id==0){
							  
							  $PaymentComponent = new ReceivablesPaymentModel();
							  
							  $PaymentComponent->receivable_idx 				= $receivable_idx;
							  $PaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
							  $PaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
							  $PaymentComponent->receivable_reference 		= $request->receivable_reference;
							  $PaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
							  $PaymentComponent->image_reference 				= $image_blob;
							  $result = $PaymentComponent->save();
							  $result_type = 'Saved';
						  
						  }
						  else{
						  
							  $PaymentComponent = new ReceivablesPaymentModel();
							  
							  $PaymentComponent = ReceivablesPaymentModel::find($receivable_payment_id);
							  $PaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
							  $PaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
							  $PaymentComponent->receivable_reference 		= $request->receivable_reference;
							  $PaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
							  $PaymentComponent->image_reference 				= $image_blob;
							  $result = $PaymentComponent->update();
							  $result_type = 'Updated';

						  }
				  
			 }else{	
			  
						  $receivable_idx = $request->receivable_idx_payment;
						  $receivable_payment_id = $request->receivable_payment_id;
						  
						  if($receivable_payment_id==0){
							  
							  $PurchaseOrderPaymentComponent = new ReceivablesPaymentModel();
							  
							  $PurchaseOrderPaymentComponent->receivable_idx 				= $receivable_idx;
							  $PurchaseOrderPaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
							  $PurchaseOrderPaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
							  $PurchaseOrderPaymentComponent->receivable_reference 		= $request->receivable_reference;
							  $PurchaseOrderPaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
							  
							  $result = $PurchaseOrderPaymentComponent->save();
							  $result_type = 'Saved';
						  
						  }
						  else{
						  
							  $PurchaseOrderPaymentComponent = new ReceivablesPaymentModel();
							  $PurchaseOrderPaymentComponent = ReceivablesPaymentModel::find($receivable_payment_id);
							  $PurchaseOrderPaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
							  $PurchaseOrderPaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
							  $PurchaseOrderPaymentComponent->receivable_reference 		= $request->receivable_reference;
							  $PurchaseOrderPaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
							  
							  $result = $PurchaseOrderPaymentComponent->update();
							  $result_type = 'Updated';

						  }
						  
			 }

			  /*Get Sales Order ID from Receivable*/
			  #$sales_order_id =  ReceivablesModel::find($receivable_idx, ['sales_order_idx']);

			  /*Update Sales Order to Delivered*/
			  #$salesOrderUpdate_status = new SalesOrderModel();
			  #$salesOrderUpdate_status = SalesOrderModel::find($sales_order_id->sales_order_idx);
			  #$salesOrderUpdate_status->sales_order_status = 'Delivered';
			  #$salesOrderUpdate_status->update();
			  
			  /*Get Recivable Details [receivable_amount]*/
			  $receivable_details = ReceivablesModel::find($receivable_idx, ['receivable_amount']);							
			  $receivable_amount = $receivable_details->receivable_amount;
			  
			  /*Get Payment Details*/
			  $receivable_total_payment_amount =  ReceivablesPaymentModel::where('receivable_idx', $receivable_idx)
				  ->sum('receivable_payment_amount');
			  
			  /*Compute Balance and Status Creation*/
						  $remaining_balance = number_format($receivable_amount - $receivable_total_payment_amount+0,2, '.', '');
						  $_paid_percentage = ($receivable_total_payment_amount / $receivable_amount) * 100;
					  
						  $paid_percentage = number_format($_paid_percentage,2,".","");
					  
						  /*IF Fully Paid Automatically Update the Status to Paid*/
						  if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
						  {		
							  
							  $Receivablestatus = "$paid_percentage% Paid";
							  
						  }else if($paid_percentage >= 100)
						  {	
							  
							  $Receivablestatus = "Paid";
							  
						  }else{
							  
							  $Receivablestatus = "Pending";
							  
						  }
			  
					  /*Update Receivable Status and Remaining Balance*/
					  $Receivables_ACTION = new ReceivablesModel();
					  $Receivables_ACTION = ReceivablesModel::find($receivable_idx);		
					  $Receivables_ACTION->receivable_remaining_balance 	= number_format($remaining_balance,2, '.', '');
					  $Receivables_ACTION->receivable_status 		= $Receivablestatus;		
					  $Receivables_ACTION->update();
			  
					  if($result){
						  return response()->json(array('success' => "Payment Information Successfully $result_type!"), 200);
					  }
					  else{
						  return response()->json(['success'=>'Error on Payment Information']);
					  }	
		 }	
	
	public function billing_receivable_delete_payment(Request $request){		
		  
			$receivable_payment_id = $request->paymentitemID;
			$receivable_idx = $request->receivable_idx;
			
			ReceivablesPaymentModel::find($receivable_payment_id)->delete();
			
					/*Get Sales Order ID from Receivable*/
					$sales_order_id =  ReceivablesModel::find($receivable_idx, ['sales_order_idx']);
					
					/*Get Recivable Details [receivable_amount]*/
					$receivable_details = ReceivablesModel::find($receivable_idx, ['receivable_amount']);							
					$receivable_amount = $receivable_details->receivable_amount;
					
					/*Get Payment Details*/
					$receivable_total_payment_amount =  ReceivablesPaymentModel::where('receivable_idx', $receivable_idx)
						->sum('receivable_payment_amount');
					
					/*Compute Balance and Status Creation*/
								$remaining_balance = number_format($receivable_amount - $receivable_total_payment_amount+0,2, '.', '');
								$_paid_percentage = ($receivable_total_payment_amount / $receivable_amount) * 100;
							
								$paid_percentage = number_format($_paid_percentage,2,".","");
							
								/*IF Fully Paid Automatically Update the Status to Paid*/
								if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
								{		
									
									$Receivablestatus = "$paid_percentage% Paid";
									
								}else if($paid_percentage >= 100)
								{	
									
									$Receivablestatus = "Paid";
									
								}else{
									
									$Receivablestatus = "Pending";
									
								}
					
							/*Update Receivable Status and Remaining Balance*/
							$Receivables_ACTION = new ReceivablesModel();
							$Receivables_ACTION = ReceivablesModel::find($receivable_idx);		
							$Receivables_ACTION->receivable_remaining_balance 	= number_format($remaining_balance,2, '.', '');
							$Receivables_ACTION->receivable_status 		= $Receivablestatus;		
							$Receivables_ACTION->update();		
			
			return 'Deleted';
			
	}
			   

	public function receivable_payment_list(Request $request){		
  
		  $data =  ReceivablesPaymentModel::where('receivable_idx', $request->receivable_idx)
			  ->orderBy('receivable_payment_id', 'asc')
				->get([
				  'receivable_payment_id',
				  'receivable_date_of_payment',
				  'receivable_mode_of_payment',
				  'receivable_reference',
				  'receivable_payment_amount',
				  'created_at',
				  'image_reference',
				  'created_at'
				  ]);
	  
		  return response()->json($data);
  }
	  
	/*Fetch Payment Information*/
	public function receivable_payment_info(Request $request){
				  
				  $data = ReceivablesPaymentModel::where('receivable_payment_id', $request->receivable_payment_id)
					->get([
				  'receivable_payment_id',
				  'receivable_idx',
				  'receivable_date_of_payment',
				  'receivable_mode_of_payment',
				  'receivable_reference',
				  'receivable_payment_amount',
				  'image_reference'
				  ]);
				  return response()->json($data);
	  
  }

  public function sales_order_receivable_payment(Request $request){
        	 
	$request->validate([
		  'payment_image_reference'			=>'image|mimes:jpg,png,jpeg,svg|max:10048',
		  'receivable_mode_of_payment'      	=> 'required',
		  'receivable_date_of_payment'      	=> 'required',
		  'receivable_reference'      		=> ['required',Rule::unique('teves_receivable_payment')->where( 
											  fn ($query) =>$query
												  ->where('receivable_idx', $request->receivable_idx_payment)
												  ->where('receivable_reference', $request->receivable_reference)
												  ->where('receivable_payment_id', '<>',  $request->receivable_payment_id )
											  )],
		  'receivable_payment_amount'       	=> 'required',
	 ],[
		  'receivable_mode_of_payment.required' 	=> 'Bank Details is Required',
		  'receivable_date_of_payment.required' 	=> 'Date of Payment is Required',
		  'receivable_reference.required' 		=> 'Reference Number Required',
		  'receivable_payment_amount.required' 	=> 'Payment Amount is Required'
	 ]);
		 
		 if ($request->hasFile('payment_image_reference')) {
			 
				 $path = 'files/';
				 $file = $request->file('payment_image_reference');
				 @$file_name = time().'_'.@$file->getClientOriginalName();

				  $upload = $file->storeAs($path, $file_name, 'public');
				  
				  $path = $request->file('payment_image_reference')->getRealPath();    
				  $logo = file_get_contents($path);
				  $image_blob = base64_encode($logo);
		  
					  $receivable_idx = $request->receivable_idx_payment;
					  $receivable_payment_id = $request->receivable_payment_id;
					  
					  if($receivable_payment_id==0){
						  
						  $PaymentComponent = new ReceivablesPaymentModel();
						  
						  $PaymentComponent->receivable_idx 				= $receivable_idx;
						  $PaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
						  $PaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
						  $PaymentComponent->receivable_reference 		= $request->receivable_reference;
						  $PaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
						  $PaymentComponent->image_reference 				= $image_blob;
						  $result = $PaymentComponent->save();
						  $result_type = 'Saved';
					  
					  }
					  else{
					  
						  $PaymentComponent = new ReceivablesPaymentModel();
						  
						  $PaymentComponent = ReceivablesPaymentModel::find($receivable_payment_id);
						  $PaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
						  $PaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
						  $PaymentComponent->receivable_reference 		= $request->receivable_reference;
						  $PaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
						  $PaymentComponent->image_reference 				= $image_blob;
						  $result = $PaymentComponent->update();
						  $result_type = 'Updated';

					  }
			  
		 }else{	
		  
					  $receivable_idx = $request->receivable_idx_payment;
					  $receivable_payment_id = $request->receivable_payment_id;
					  
					  if($receivable_payment_id==0){
						  
						  $PurchaseOrderPaymentComponent = new ReceivablesPaymentModel();
						  
						  $PurchaseOrderPaymentComponent->receivable_idx 				= $receivable_idx;
						  $PurchaseOrderPaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
						  $PurchaseOrderPaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
						  $PurchaseOrderPaymentComponent->receivable_reference 			= $request->receivable_reference;
						  $PurchaseOrderPaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
						  
						  $result = $PurchaseOrderPaymentComponent->save();
						  $result_type = 'Saved';
					  
					  }
					  else{
					  
						  $PurchaseOrderPaymentComponent = new ReceivablesPaymentModel();
						  $PurchaseOrderPaymentComponent = ReceivablesPaymentModel::find($receivable_payment_id);
						  $PurchaseOrderPaymentComponent->receivable_mode_of_payment 	= $request->receivable_mode_of_payment;
						  $PurchaseOrderPaymentComponent->receivable_date_of_payment 	= $request->receivable_date_of_payment;
						  $PurchaseOrderPaymentComponent->receivable_reference 		= $request->receivable_reference;
						  $PurchaseOrderPaymentComponent->receivable_payment_amount 	= $request->receivable_payment_amount;
						  
						  $result = $PurchaseOrderPaymentComponent->update();
						  $result_type = 'Updated';

					  }
					  
		 }

		  /*Get Sales Order ID from Receivable*/
		  $sales_order_id =  ReceivablesModel::find($receivable_idx, ['sales_order_idx']);
		  
		  /*Get Recivable Details [receivable_amount]*/
		  $receivable_details = ReceivablesModel::find($receivable_idx, ['receivable_amount']);							
		  $receivable_amount = $receivable_details->receivable_amount;
		  
		  /*Get Payment Details*/
		  $receivable_total_payment_amount =  ReceivablesPaymentModel::where('receivable_idx', $receivable_idx)
			  ->sum('receivable_payment_amount');
		  
		  /*Compute Balance and Status Creation*/
					  $remaining_balance = number_format($receivable_amount - $receivable_total_payment_amount+0,2, '.', '');
					  $_paid_percentage = ($receivable_total_payment_amount / $receivable_amount) * 100;
				  
					  $paid_percentage = number_format($_paid_percentage,2,".","");
				  
					  /*IF Fully Paid Automatically Update the Status to Paid*/
					  if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
					  {		
						  
						  $Receivablestatus = "$paid_percentage%";
						  
					  }else if($paid_percentage >= 100)
					  {	
						  
						  $Receivablestatus = "$paid_percentage%";
						  
					  }else{
						  
						  $Receivablestatus = "Pending";
						  
					  }
					  
		  		  /*Update Sales Order to Delivered*/
				  $salesOrderUpdate_status = new SalesOrderModel();
				  $salesOrderUpdate_status = SalesOrderModel::find($sales_order_id->sales_order_idx);
				  $salesOrderUpdate_status->sales_order_payment_status = $Receivablestatus;
				  $salesOrderUpdate_status->update();
		  
				  /*Update Receivable Status and Remaining Balance*/
				  $Receivables_ACTION = new ReceivablesModel();
				  $Receivables_ACTION = ReceivablesModel::find($receivable_idx);		
				  $Receivables_ACTION->receivable_remaining_balance 	= number_format($remaining_balance,2, '.', '');
				  $Receivables_ACTION->receivable_status 		= $Receivablestatus;		
				  $Receivables_ACTION->update();
		  
				  if($result){
					  return response()->json(array('success' => "Payment Information Successfully $result_type!"), 200);
				  }
				  else{
					  return response()->json(['success'=>'Error on Payment Information']);
				  }	
	 }	

  public function sales_order_receivable_delete_payment(Request $request){		
		  
	  $receivable_payment_id = $request->paymentitemID;
	  $receivable_idx = $request->receivable_idx;
	  
	  ReceivablesPaymentModel::find($receivable_payment_id)->delete();
	  
			  /*Get Sales Order ID from Receivable*/
			  $sales_order_id =  ReceivablesModel::find($receivable_idx, ['sales_order_idx']);
			  
			  /*Get Recivable Details [receivable_amount]*/
			  $receivable_details = ReceivablesModel::find($receivable_idx, ['receivable_amount']);							
			  $receivable_amount = $receivable_details->receivable_amount;
			  
			  /*Get Payment Details*/
			  $receivable_total_payment_amount =  ReceivablesPaymentModel::where('receivable_idx', $receivable_idx)
				  ->sum('receivable_payment_amount');
			  
			  
						/*Compute Balance and Status Creation*/
						  $remaining_balance = number_format($receivable_amount - $receivable_total_payment_amount+0,2, '.', '');
						  $_paid_percentage = ($receivable_total_payment_amount / $receivable_amount) * 100;
					  
						  $paid_percentage = number_format($_paid_percentage,2,".","");
					  
						  /*IF Fully Paid Automatically Update the Status to Paid*/
						  if($paid_percentage >= 0.01 && $paid_percentage <= 99.99)
						  {		
							  
							  $Receivablestatus = "$paid_percentage%";
						  
						  }else if($paid_percentage >= 100)
						  {	
						  
						  $Receivablestatus = "$paid_percentage%";
							  
						  }else{
							  
							  $Receivablestatus = "Pending";
							  
						  }
						  
					  /*Update Sales Order to Delivered Pending by checking the count of Payment if payment is Zero then update the status to Pending*/
					  /*
					  if($receivable_total_payment_amount==0){
						  
						  $salesOrderUpdate_status = new SalesOrderModel();
						  $salesOrderUpdate_status = SalesOrderModel::find($sales_order_id->sales_order_idx);
						  $salesOrderUpdate_status->sales_order_status = 'Pending';
						  $salesOrderUpdate_status->update();
						  
					  }else{
					  
						  $salesOrderUpdate_status = new SalesOrderModel();
						  $salesOrderUpdate_status = SalesOrderModel::find($sales_order_id->sales_order_idx);
						  $salesOrderUpdate_status->sales_order_status = 'Delivered';
						  $salesOrderUpdate_status->update();
					  
					  }*/
			  		  		  
					  /*Update Sales Order to Delivered*/
					  $salesOrderUpdate_status = new SalesOrderModel();
					  $salesOrderUpdate_status = SalesOrderModel::find($sales_order_id->sales_order_idx);
					  $salesOrderUpdate_status->sales_order_payment_status = $Receivablestatus;
					  $salesOrderUpdate_status->update();
				  
					  /*Update Receivable Status and Remaining Balance*/
					  $Receivables_ACTION = new ReceivablesModel();
					  $Receivables_ACTION = ReceivablesModel::find($receivable_idx);		
					  $Receivables_ACTION->receivable_remaining_balance 	= number_format($remaining_balance,2, '.', '');
					  $Receivables_ACTION->receivable_status 		= $Receivablestatus;		
					  $Receivables_ACTION->update();		
	  
	  return 'Deleted';
	  
  }


	
}
