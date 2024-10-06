<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CashiersReportModel;
use App\Models\CashiersReportModel_p6;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

class CashiersReport_Dipstick_Inventory_Controller extends Controller
{
	
	public function save_product_cashiers_report_p6(Request $request){	

		$request->validate([
			'product_idx'  					=> 'required',
			'tank_idx'  					=> 'required',
			'beginning_inventory'  			=> 'required',
			'sales_in_liters_inventory'  	=> 'required',	
			'ugt_pumping_inventory'			=> 'required',	
			'delivery_inventory'  			=> 'required',
			'ending_inventory' 				=> 'required'
        ], 
        [
			'product_idx.required' 					=> 'Product is Required',
			'tank_idx.required' 					=> 'Tank is Required',
			'beginning_inventory.required' 			=> 'Beginning Inventory is Required',
			'sales_in_liters_inventory.required' 	=> 'Sales in Liters is Required',
			'ugt_pumping_inventory.required' 		=> 'UGT Pumping is Required',
			'delivery_inventory.required' 			=> 'Delivery is Required',
			'ending_inventory.required' 			=> 'Ending Inventory is Required',
        ]
		);
			
			/*Get Last ID*/
			$CashiersReportId = $request->CashiersReportId;
			
			$product_idx				= $request->product_idx;
			$tank_idx					= $request->tank_idx;
			$beginning_inventory 		= $request->beginning_inventory;
			$sales_in_liters_inventory 	= $request->sales_in_liters_inventory;
			$ugt_pumping_inventory 		= $request->ugt_pumping_inventory;
			$delivery_inventory 		= $request->delivery_inventory;
			$ending_inventory 			= $request->ending_inventory;
			
			
			$book_stock = ($beginning_inventory - $sales_in_liters_inventory - $ugt_pumping_inventory) + $delivery_inventory;
			$variance = $book_stock - $ending_inventory;
			
			$CRPH7_ID 				= $request->CRPH7_ID;
								
								if($CRPH7_ID=='' || $CRPH7_ID ==0){	
								
									$CashiersReportModel_p6 = new CashiersReportModel_p6();
									
									$CashiersReportModel_p6->user_idx 					= Session::get('loginID');
									$CashiersReportModel_p6->cashiers_report_idx 		= $CashiersReportId;
									$CashiersReportModel_p6->product_idx 				= $product_idx;
									$CashiersReportModel_p6->tank_idx	 				= $tank_idx;
									$CashiersReportModel_p6->beginning_inventory 		= $beginning_inventory;
									$CashiersReportModel_p6->sales_in_liters 			= $sales_in_liters_inventory;
									$CashiersReportModel_p6->ugt_pumping	 			= $ugt_pumping_inventory;
									$CashiersReportModel_p6->delivery 					= $delivery_inventory;
									$CashiersReportModel_p6->ending_inventory 			= $ending_inventory;
									$CashiersReportModel_p6->book_stock 				= $book_stock;
									$CashiersReportModel_p6->variance 					= $variance;
									$CashiersReportModel_p6->created_by_user_id 		= Session::get('loginID');
						
									$result = $CashiersReportModel_p6->save();
									
									if($result){
										return response()->json(['success'=>'Product Inventory Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Inventory Information']);
									}
									
								}else{
																	
									$CashiersReportModel_p6 = new CashiersReportModel_p6();
									$CashiersReportModel_p6 = CashiersReportModel_p6::find($CRPH7_ID);
									
									$CashiersReportModel_p6->cashiers_report_idx 		= $CashiersReportId;
									$CashiersReportModel_p6->product_idx 				= $product_idx;
									$CashiersReportModel_p6->tank_idx	 				= $tank_idx;
									$CashiersReportModel_p6->beginning_inventory 		= $beginning_inventory;
									$CashiersReportModel_p6->sales_in_liters 			= $sales_in_liters_inventory;
									$CashiersReportModel_p6->ugt_pumping	 			= $ugt_pumping_inventory;
									$CashiersReportModel_p6->delivery 					= $delivery_inventory;
									$CashiersReportModel_p6->ending_inventory 			= $ending_inventory;
									$CashiersReportModel_p6->book_stock 				= $book_stock;
									$CashiersReportModel_p6->variance 					= $variance;
									$CashiersReportModel_p6->updated_by_user_id 		= Session::get('loginID');
									$result = $CashiersReportModel_p6->update();
									
									if($result){
										return response()->json(['success'=>'Product Inventory Successfully Updated!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Inventory Information']);
									}
									
								}
								
	}		
	
	/**/
	public function get_product_dipstick_inventory_list(Request $request){		

			$data =  CashiersReportModel_p6::Join('teves_product_tank_table', 'teves_product_tank_table.tank_id', '=', 'teves_cashiers_report_p6.tank_idx')
					->Join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p6.product_idx')
					->where('teves_cashiers_report_p6.cashiers_report_idx', $request->CashiersReportId)
					->orderBy('teves_cashiers_report_p6.product_idx', 'asc')
					->get([
						'teves_product_table.product_id',
						'teves_product_table.product_name',
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_cashiers_report_p6.cashiers_report_p6_id',
						'teves_cashiers_report_p6.beginning_inventory',
						'teves_cashiers_report_p6.sales_in_liters',
						'teves_cashiers_report_p6.ugt_pumping',
						'teves_cashiers_report_p6.delivery',
						'teves_cashiers_report_p6.ending_inventory',
						'teves_cashiers_report_p6.book_stock',
						'teves_cashiers_report_p6.variance'
					]);
		
			return response()->json($data);			
	}

	
	public function cashiers_report_p6_info(Request $request){

		$CHPH7_ID = $request->CHPH7_ID;
		
		$data =  CashiersReportModel_p6::Join('teves_product_tank_table', 'teves_product_tank_table.tank_id', '=', 'teves_cashiers_report_p6.tank_idx')
					->Join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p6.product_idx')
					->where('teves_cashiers_report_p6.cashiers_report_p6_id', $CHPH7_ID)
					
					->get([
						'teves_product_table.product_id',
						'teves_product_table.product_name',
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_cashiers_report_p6.cashiers_report_p6_id',
						'teves_cashiers_report_p6.beginning_inventory',
						'teves_cashiers_report_p6.sales_in_liters',
						'teves_cashiers_report_p6.ugt_pumping',
						'teves_cashiers_report_p6.delivery',
						'teves_cashiers_report_p6.ending_inventory',
						'teves_cashiers_report_p6.book_stock',
						'teves_cashiers_report_p6.variance'
					]);
					
		return response()->json($data);
		
	}
	
	public function delete_cashiers_report_p6(Request $request){		
			
		$CHPH7_ID = $request->CHPH7_ID;
		CashiersReportModel_p6::find($CHPH7_ID)->delete();
		return 'Deleted';
		
	}
	
	
}
