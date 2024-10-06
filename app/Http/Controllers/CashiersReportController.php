<?php
namespace App\Http\Controllers;
//use Request;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CashiersReportModel;
use App\Models\CashiersReportModel_P1;
use App\Models\CashiersReportModel_P2;
use App\Models\CashiersReportModel_P3;
use App\Models\CashiersReportModel_P4;
use App\Models\CashiersReportModel_P5;
use App\Models\CashiersReportModel_P6;
//use App\Models\CashiersReportModel_P7;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
/*PDF*/
use PDF;
use Illuminate\Validation\Rule;
class CashiersReportController extends Controller
{
	
	/*Load client Interface*/
	public function cashierReport(){
		
		$title = "Cashier' Report";
		$data = array();
		if(Session::has('loginID')){
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
			
		}
		return view("pages.cashiers_report", compact('data','title','teves_branch'));
		
	}   
	
	/*Fetch client List using Datatable*/
	public function getCashierReport(Request $request)
    {
		
		$list = CashiersReportModel::get();
		if ($request->ajax()) {
			
			/*
			July 25, 2024
			Sa Non - Admin User Pwede nya makita lahat ng Chashier's Report Pero hindi niya pwede i-edit ang hindi niya report. 
			After One Day sa Paggawa nya ng Report, Admin na lang ang pwede mag edit.
			*/
			
			if(Session::get('UserType')!="Admin"){
				$data = CashiersReportModel::join('user_tb', 'user_tb.user_id', '=', 'teves_cashiers_report.user_idx')
				 ->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_cashiers_report.teves_branch')				
              	 /*->where('teves_cashiers_report.user_idx', '=', Session::get('loginID'))|*/
				 ->whereRaw("teves_cashiers_report.teves_branch IN (SELECT branch_idx FROM teves_user_branch_access WHERE user_idx=?)", Session::get('loginID'))
				 ->get([				
					'teves_cashiers_report.cashiers_report_id',
					'teves_cashiers_report.user_idx',
					'user_tb.user_real_name',
					'teves_branch_table.branch_code',
					'teves_cashiers_report.cashiers_name',
					'teves_cashiers_report.forecourt_attendant',
					'teves_cashiers_report.report_date',
					'teves_cashiers_report.shift',
					'teves_cashiers_report.created_at',
					'teves_cashiers_report.updated_at']);
			}else{
				$data = CashiersReportModel::join('user_tb', 'user_tb.user_id', '=', 'teves_cashiers_report.user_idx')	
				->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_cashiers_report.teves_branch')				
				->get([				
					'teves_cashiers_report.cashiers_report_id',
					'teves_cashiers_report.user_idx',
					'user_tb.user_real_name',
					'teves_branch_table.branch_code',
					'teves_cashiers_report.cashiers_name',
					'teves_cashiers_report.forecourt_attendant',
					'teves_cashiers_report.report_date',
					'teves_cashiers_report.shift',
					'teves_cashiers_report.created_at',
					'teves_cashiers_report.updated_at']);
			}
	
			return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){
						
						$startTimeStamp = strtotime($row->created_at);
						$endTimeStamp = strtotime(date('y-m-d'));
						$timeDiff = abs($endTimeStamp - $startTimeStamp);
						$numberDays = $timeDiff/86400;  // 86400 seconds in one day
						// and you might want to convert to integer
						$numberDays = intval($numberDays);
						
						if(Session::get('UserType')=="Admin"){
							$actionBtn = '
							<div align="center" class="action_table_menu_client">
							<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-printer-fill btn_icon_table btn_icon_table_view" onclick="printCashierReportPDF('.$row->cashiers_report_id.')"></a>
							<a href="cashiers_report_form/'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editCashiersReport"></a>
							<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteCashiersReport"></a>
							</div>';
						}else{
							
							if($numberDays>=3){
								$actionBtn = '
								<div align="center" class="action_table_menu_client">
								<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-printer-fill btn_icon_table btn_icon_table_view" onclick="printCashierReportPDF('.$row->cashiers_report_id.')"></a>
								</div>';
							}
							else{
								
								if(Session::get('loginID')==$row->user_idx){
								
									$actionBtn = '
									<div align="center" class="action_table_menu_client">
									<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-printer-fill btn_icon_table btn_icon_table_view" onclick="printCashierReportPDF('.$row->cashiers_report_id.')"></a>
									<a href="cashiers_report_form/'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editCashiersReport"></a>
									<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteCashiersReport"></a>
									</div>';
								
								}else{
									
									$actionBtn = '
									<div align="center" class="action_table_menu_client">
									<a href="#" data-id="'.$row->cashiers_report_id.'" class="btn-warning btn-circle btn-sm bi bi-printer-fill btn_icon_table btn_icon_table_view" onclick="printCashierReportPDF('.$row->cashiers_report_id.')"></a>
									</div>';
								
								}
								
							}
							
						}
						
						
						return $actionBtn;
						
						
					})
					->rawColumns(['action'])
					->make(true);
		}
		
    }

	public function create_cashier_report_post(Request $request){
		
		$request->validate([
			'report_date'   => 'required',
			'forecourt_attendant'   => 'required',
			'cashiers_name'   => 'required',
			'shift'   => 'required'
        ], 
        [
			'report_date.required' => 'Report Date is required',
			'forecourt_attendant.required' => "Empoyee's on Duty is required",
			'cashiers_name.required' => "Cashier's Name is required",
			'shift.required' => "Shift is required"
        ]
		);
		
			$CashiersReportCreate = new CashiersReportModel();
			$CashiersReportCreate->user_idx 				= Session::get('loginID');
			$CashiersReportCreate->teves_branch 			= $request->teves_branch;
			$CashiersReportCreate->cashiers_name 			= $request->cashiers_name;
			$CashiersReportCreate->forecourt_attendant 		= $request->forecourt_attendant;
			$CashiersReportCreate->report_date 				= $request->report_date;
			$CashiersReportCreate->shift 				= $request->shift;
			$result = $CashiersReportCreate->save();
			
			/*Get Last ID*/
			$last_transaction_id = $CashiersReportCreate->cashiers_report_id;
			
			/*Initialized Cash On Hand*/
			$CashOnHand = new CashiersReportModel_P5();
			$CashOnHand->cashiers_report_id 	= $last_transaction_id;
			$CashOnHand->user_idx 				= Session::get('loginID');
			$CashOnHand->one_thousand_deno 		= 0;
			$CashOnHand->five_hundred_deno	 	= 0;
			$CashOnHand->two_hundred_deno 		= 0;
			$CashOnHand->one_hundred_deno 		= 0;
			$CashOnHand->fifty_deno 			= 0;
			$CashOnHand->twenty_deno 			= 0;
			$CashOnHand->ten_deno 				= 0;
			$CashOnHand->five_deno 				= 0;
			$CashOnHand->one_deno 				= 0;
			$CashOnHand->twenty_five_cent_deno 	= 0;
			$CashOnHand->cash_drop 				= 0;
			$result = $CashOnHand->save();
			
			if($result){
				return response()->json(array('success' => "Cashier's Report Information Successfully Created!", 'cashiers_report_id' => $last_transaction_id), 200);
			}
			else{
				return response()->json(['success'=>"Error on Insert Cashier's Report Information"]);
			}
			
	}

	public function update_cashier_report_post(Request $request){
		
		$request->validate([
			'report_date'   => 'required',
			'forecourt_attendant'   => 'required',
			'cashiers_name'   => 'required',
			'shift'   => 'required'
        ], 
        [
			'report_date.required' => 'Report Date is required',
			'forecourt_attendant.required' => "Empoyee's on Duty is required",
			'cashiers_name.required' => "Cashier's Name is required",
			'shift.required' => "Shift is required"
        ]
		);
		
			$CashiersReportCreate = new CashiersReportModel();
			$CashiersReportCreate = CashiersReportModel::find($request->CashiersReportId);
			$CashiersReportCreate->teves_branch 			= $request->teves_branch;
			$CashiersReportCreate->forecourt_attendant 		= $request->forecourt_attendant;
			$CashiersReportCreate->report_date 				= $request->report_date;
			$CashiersReportCreate->shift 					= $request->shift;
			$result = $CashiersReportCreate->update();
			
			if($result){
				return response()->json(array('success' => "Cashier's Report Information Successfully Updated!", 'cashiers_report_id' => $request->CashiersReportId), 200);
			}
			else{
				return response()->json(['success'=>"Error on Insert Cashier's Report Information"]);
			}
			
	}

	/*Fetch client Information*/
	public function cashiers_report_info(Request $request){
		
		$CashiersReportID = $request->CashiersReportID;
		
		$data = CashiersReportModel::where('cashiers_report_id', $request->CashiersReportID)
			->join('user_tb', 'user_tb.user_id', '=', 'teves_cashiers_report.user_idx')
            ->get([				
			'teves_cashiers_report.cashiers_report_id',
			'user_tb.user_real_name',
			'teves_cashiers_report.teves_branch',
			'teves_cashiers_report.forecourt_attendant',
			'teves_cashiers_report.report_date',
			'teves_cashiers_report.shift',
			'teves_cashiers_report.created_at',
			'teves_cashiers_report.updated_at']);
		
		return response()->json($data);
					
	}

	public function delete_cashiers_report_info(Request $request){		
			
		$CashiersReportID = $request->CashiersReportID;
		CashiersReportModel::find($CashiersReportID)->delete();
		return 'Deleted';
		
	}
	
	/*Fetch client Information*/
	public function cashiers_report_form($CashiersReportId){
		
		
		if(Session::has('loginID')){
			
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
			
		$title = "Cashier' Report";
		$product_data = ProductModel::all();
		$CashiersReportData = CashiersReportModel::where('cashiers_report_id', $CashiersReportId)
			->join('user_tb', 'user_tb.user_id', '=', 'teves_cashiers_report.user_idx')
            ->get([				
			'teves_cashiers_report.cashiers_report_id',
			'user_tb.user_real_name',
			'teves_cashiers_report.teves_branch',
			'teves_cashiers_report.cashiers_name',
			'teves_cashiers_report.forecourt_attendant',
			'teves_cashiers_report.report_date',
			'teves_cashiers_report.shift',
			'teves_cashiers_report.created_at',
			'teves_cashiers_report.updated_at']);
			
		return view("pages.cashiers_report_form_main", compact('data','title','CashiersReportData','product_data','CashiersReportId','teves_branch'));	
		
		}
		
	}

	public function cashiers_report_p1_info(Request $request){

		$CashiersReportId = $request->CashiersReportId;
		$product_id = $request->product_id;
		
		if($CashiersReportId!=0){
			
			$data =  CashiersReportModel_P1::where('cashiers_report_id', $CashiersReportId)
				->where('product_idx', $product_id)
				->skip(0)
				->take(1)
					->get([
						'teves_cashiers_report_p1.product_price'
						]);			
						
			return response()->json($data);
			
		}else{
		
			$CHPH1_ID = $request->CHPH1_ID;
			
			$data =  CashiersReportModel_P1::where('cashiers_report_p1_id', $CHPH1_ID)
				->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p1.product_idx')
					->get([
						'teves_product_table.product_name',
						'teves_product_table.product_id',
						'teves_cashiers_report_p1.cashiers_report_p1_id',
						'teves_cashiers_report_p1.beginning_reading',
						'teves_cashiers_report_p1.closing_reading',
						'teves_cashiers_report_p1.calibration',
						'teves_cashiers_report_p1.order_quantity',
						'teves_cashiers_report_p1.product_price',
						'teves_cashiers_report_p1.order_total_amount'
						]);			
						
			return response()->json($data);
			
		}
	}
	
	public function save_product_cashiers_report_p1(Request $request){	

		$CHPH1_ID 				= $request->CHPH1_ID;
		
		$request->validate([
			'product_idx'  			=> 'required',
			'beginning_reading'  	=> ['required',Rule::unique('teves_cashiers_report_p1')->where( 
									fn ($query) =>$query
										->where('cashiers_report_id', $request->CashiersReportId)
										->where('product_idx', $request->product_idx) 
										->where('beginning_reading', $request->beginning_reading)
										->where(function ($r) use($CHPH1_ID) {
													if ($CHPH1_ID) {
													   $r->where('cashiers_report_p1_id', '<>', $CHPH1_ID);
													}
										})										
									)],
			'closing_reading'  		=> ['required',Rule::unique('teves_cashiers_report_p1')->where( 
									fn ($query) =>$query
										->where('cashiers_report_id', $request->CashiersReportId)
										->where('product_idx', $request->product_idx) 
										->where('closing_reading', $request->closing_reading) 
										->where(function ($r) use($CHPH1_ID) {
													if ($CHPH1_ID) {
													   $r->where('cashiers_report_p1_id', '<>', $CHPH1_ID);
													}
										})
									)],	
        ], 
        [
			'product_idx.required' 	=> 'Product is Required',
			'beginning_reading.required' 	=> 'Beginning Reading is Required',
			'closing_reading.required' 	=> 'Closing Reading is Required',
        ]
		);
			
			/*Get Last ID*/
			$CashiersReportId = $request->CashiersReportId;
			
			$product_idx				= $request->product_idx;
			$beginning_reading 			= $request->beginning_reading;
			$closing_reading 			= $request->closing_reading;
			$calibration 				= $request->calibration;
			$product_manual_price 		= $request->product_manual_price;

					/*Check if Price is From Manual Price*/
					if($product_manual_price!=0){
						
						$product_price = $request->product_manual_price;
						
					}else{
	
						/*Product Details*/
						$raw_query_product = "SELECT a.product_id, ifnull(b.branch_price,a.product_price) AS product_price FROM teves_product_table AS a
						LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
						WHERE b.branch_idx = ? and b.product_idx = ?";			
						$product_info = DB::select("$raw_query_product", [$request->branch_idx,$request->product_idx]);		
						
						$product_price = $product_info[0]->product_price;

					}
						
								$order_quantity = ($closing_reading - $beginning_reading) - $calibration;
								$peso_sales = ($order_quantity * $product_price);
								
								if($CHPH1_ID=='' || $CHPH1_ID ==0){	
								
									$CashiersReportModel_P1 = new CashiersReportModel_P1();
									
									$CashiersReportModel_P1->user_idx 					= Session::get('loginID');
									$CashiersReportModel_P1->cashiers_report_id 		= $CashiersReportId;
									$CashiersReportModel_P1->product_idx 				= $product_idx;
									$CashiersReportModel_P1->beginning_reading 			= $beginning_reading;
									$CashiersReportModel_P1->closing_reading 			= $closing_reading;
									$CashiersReportModel_P1->calibration 				= $calibration+0;
									$CashiersReportModel_P1->order_quantity 			= $order_quantity;
									$CashiersReportModel_P1->product_price 				= $product_price;
									$CashiersReportModel_P1->order_total_amount 		= $peso_sales;
									
									$result = $CashiersReportModel_P1->save();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}else{
																	
									$CashiersReportModel_P1 = new CashiersReportModel_P1();
									$CashiersReportModel_P1 = CashiersReportModel_P1::find($CHPH1_ID);
									
									$CashiersReportModel_P1->cashiers_report_id 		= $CashiersReportId;
									$CashiersReportModel_P1->product_idx 				= $product_idx;
									$CashiersReportModel_P1->beginning_reading 			= $beginning_reading;
									$CashiersReportModel_P1->closing_reading 			= $closing_reading;
									$CashiersReportModel_P1->calibration 				= $calibration+0;
									$CashiersReportModel_P1->order_quantity 			= $order_quantity;
									$CashiersReportModel_P1->product_price 				= $product_price;
									$CashiersReportModel_P1->order_total_amount 		= $peso_sales;
									
									$result = $CashiersReportModel_P1->update();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Updated!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}
								
	}	
	
	public function get_cashiers_report_product_p1(Request $request){		
	
			$data =  CashiersReportModel_P1::where('cashiers_report_id', $request->CashiersReportId)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p1.product_idx')
				->orderBy('cashiers_report_p1_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p1.product_price',
					'teves_cashiers_report_p1.cashiers_report_p1_id',
					'teves_cashiers_report_p1.cashiers_report_id',
					'teves_cashiers_report_p1.beginning_reading',
					'teves_cashiers_report_p1.closing_reading',
					'teves_cashiers_report_p1.calibration',
					'teves_cashiers_report_p1.order_quantity',
					'teves_cashiers_report_p1.order_total_amount'
					]);
		
			return response()->json($data);
	}
	
	public function delete_cashiers_report_product_p1(Request $request){		
			
		$CHPH1_ID = $request->CHPH1_ID;
		CashiersReportModel_P1::find($CHPH1_ID)->delete();
		return 'Deleted';
		
	}
	
	
	/*Part Two (Other Reports)*/
	public function save_product_cashiers_report_PH2(Request $request){	

		$request->validate([
			'product_idx'  		=> 'required',
			'order_quantity'  	=> 'required'		
        ], 
        [
			'product_idx.required' 	=> 'Product is Required',
			'order_quantity.required' 	=> 'Order Quantity is Required',
        ]
		);
			
			/*Get Cashier Report ID*/
			$CashiersReportId = $request->CashiersReportId;
			
			$product_idx				= $request->product_idx;
			$order_quantity 			= $request->order_quantity;
			$product_manual_price 		= $request->product_manual_price;
			
			$CHPH2_ID 				= $request->CHPH2_ID;

					/*Check if Price is From Manual Price*/
					if($product_manual_price!=0){
						
						$product_price = $request->product_manual_price;
						
					}else{
	
						/*Product Details*/
						$raw_query_product = "SELECT a.product_id, ifnull(b.branch_price,a.product_price) AS product_price FROM teves_product_table AS a
						LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
						WHERE b.branch_idx = ? and b.product_idx = ?";			
						$product_info = DB::select("$raw_query_product", [$request->branch_idx,$request->product_idx]);		
						
						$product_price = $product_info[0]->product_price;

					}
						
								$peso_sales = ($order_quantity * $product_price);
								
								if($CHPH2_ID=='' || $CHPH2_ID ==0){	
								
									$CashiersReportModel_P2 = new CashiersReportModel_P2();
									
									$CashiersReportModel_P2->user_idx 					= Session::get('loginID');
									$CashiersReportModel_P2->cashiers_report_id 		= $CashiersReportId;
									$CashiersReportModel_P2->product_idx 				= $product_idx;
									$CashiersReportModel_P2->order_quantity 			= $order_quantity;
									$CashiersReportModel_P2->product_price 				= $product_price;
									$CashiersReportModel_P2->order_total_amount 		= $peso_sales;
									
									$result = $CashiersReportModel_P2->save();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}else{
																	
									$CashiersReportModel_P2 = new CashiersReportModel_P2();
									$CashiersReportModel_P2 = CashiersReportModel_P2::find($CHPH2_ID);
									
									$CashiersReportModel_P2->product_idx 				= $product_idx;
									$CashiersReportModel_P2->order_quantity 			= $order_quantity;
									$CashiersReportModel_P2->product_price 				= $product_price;
									$CashiersReportModel_P2->order_total_amount 		= $peso_sales;
									
									$result = $CashiersReportModel_P2->update();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Updated!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}
								
	}	
	
	public function get_cashiers_report_product_p2(Request $request){		
	
			$data =  CashiersReportModel_P2::where('cashiers_report_id', $request->CashiersReportId)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p2.product_idx')
				->orderBy('cashiers_report_p2_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p2.product_price',
					'teves_cashiers_report_p2.cashiers_report_p2_id',
					'teves_cashiers_report_p2.cashiers_report_id',
					'teves_cashiers_report_p2.order_quantity',
					'teves_cashiers_report_p2.order_total_amount'
					]);
		
			return response()->json($data);
	}

	public function cashiers_report_p2_info(Request $request){

		$CHPH2_ID = $request->CHPH2_ID;
		
		$data =  CashiersReportModel_P2::where('cashiers_report_p2_id', $CHPH2_ID)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p2.product_idx')
				->get([
					'teves_product_table.product_name',
					'teves_product_table.product_id',
					'teves_cashiers_report_p2.cashiers_report_p2_id',
					'teves_cashiers_report_p2.order_quantity',
					'teves_cashiers_report_p2.product_price',
					'teves_cashiers_report_p2.order_total_amount'
					]);			
					
		return response()->json($data);
		
	}
	
	public function delete_cashiers_report_product_p2(Request $request){		
			
		$CHPH2_ID = $request->CHPH2_ID;
		CashiersReportModel_P2::find($CHPH2_ID)->delete();
		return 'Deleted';
		
	}

	/*Part Three (MSC Reports)*/
	public function save_product_cashiers_report_PH3(Request $request){	

		$miscellaneous_items_type	= $request->miscellaneous_items_type;
		
		if($miscellaneous_items_type=='OTHERS' || $miscellaneous_items_type=='CASHOUT'){
			
			$request->validate([
				'item_description'      	=> 'required',
				'product_manual_price' 	=> 'required',	
			], 
			[
				'item_description'      	=> 'required',
				'product_manual_price.required' 	=> 'Required',
			]
			);
			
		}else{
			
			$request->validate([
				'miscellaneous_items_type' 	=> 'required',
				'item_description'      	=> 'required',
				'order_quantity'  			=> 'required'		
			], 
			[
				'miscellaneous_items_type.required' 	=> 'Type is Required',
				'item_description'      	=> 'required',
				'order_quantity.required' 	=> 'Order Quantity is Required',
			]
			);	
			
		}
			
			/*Get Cashier Report ID*/
			$CashiersReportId = $request->CashiersReportId;

            $reference_no				= $request->reference_no;

			$product_idx				= $request->product_idx;
			$order_quantity 			= $request->order_quantity;
			$product_manual_price 		= $request->product_manual_price + 0;
			
			$CHPH3_ID 			= $request->CHPH3_ID;
			$pump_price_data =  CashiersReportModel_P1::where('cashiers_report_id', $CashiersReportId)
				->where('product_idx', $product_idx)
				->skip(0)
				->take(1)
					->get([
						'teves_cashiers_report_p1.cashiers_report_p1_id',
						'teves_cashiers_report_p1.product_price'
						]);	
					
			$pump_price = @$pump_price_data[0]['product_price']+0;
			$cashiers_report_p1_id = @$pump_price_data[0]['cashiers_report_p1_id']+0;


            if($miscellaneous_items_type=='SALES_CREDIT'){

                    if($pump_price==0){
				
				        $discounted_price 	= 0;
				        $product_price 		= $product_manual_price;
					
					}else{
			
				        /*Check if Price is From Manual Price*/
				        if($product_manual_price!=0){
					        $product_price = $pump_price;
					        $discounted_price 	= $pump_price;
				        }else{
					        $discounted_price 	= 0;
					        $product_price = $pump_price;
				        }
						
			        }
					
					$peso_sales = ($order_quantity * $product_price);
					
            }else if($miscellaneous_items_type=='DISCOUNTS'){

                    if($pump_price==0){
				
				        $discounted_price 	= 0;
				        $product_price 		= $product_manual_price;
				
			        }else{
			
				        /*Check if Price is From Manual Price*/
				        if($product_manual_price!=0){
					        $product_price = $pump_price - $product_manual_price;
					        $discounted_price 	= $pump_price - $product_manual_price;
				        }else{
					        $discounted_price 	= 0;
					        $product_price = $pump_price;
				        }
			
			        }
					$peso_sales = ($order_quantity * $product_price);

            }else if($miscellaneous_items_type=='OTHERS'){

                   
				        $discounted_price 	= 0;
				        $product_price 		= $product_manual_price;
				
			       
					$peso_sales = ($order_quantity * $product_price);

            }else{

			        if($pump_price==0){
				
				        $discounted_price 	= 0;
				        $product_price 		= $product_manual_price;
				
			        }else{
			
				        /*Check if Price is From Manual Price*/
				        if($product_manual_price!=0){
					        $product_price = $pump_price - $product_manual_price;
					        $discounted_price 	= $pump_price - $product_manual_price;
				        }else{
					        $discounted_price 	= 0;
					        $product_price = $pump_price;
				        }
			
			        }
					 $peso_sales = ($product_price);
             }
			 	
								if($CHPH3_ID=='' || $CHPH3_ID ==0){	
								
									$CashiersReportModel_P3 = new CashiersReportModel_P3();
									
									$CashiersReportModel_P3->user_idx 					= Session::get('loginID');
									$CashiersReportModel_P3->cashiers_report_id 		= $CashiersReportId;
                                    $CashiersReportModel_P3->miscellaneous_items_type 	= $miscellaneous_items_type;
                                    $CashiersReportModel_P3->reference_no 				= $reference_no;
									$CashiersReportModel_P3->product_idx 				= $product_idx;
									$CashiersReportModel_P3->item_description 			= $request->item_description;
									$CashiersReportModel_P3->order_quantity 			= $order_quantity;
                                    $CashiersReportModel_P3->pump_price 				= $pump_price;
									$CashiersReportModel_P3->unit_price 				= $product_manual_price;
									$CashiersReportModel_P3->discounted_price 			= $discounted_price;
									$CashiersReportModel_P3->order_total_amount 		= $peso_sales;
									$result = $CashiersReportModel_P3->save();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}else{
																	
									$CashiersReportModel_P3 = new CashiersReportModel_P3();
									$CashiersReportModel_P3 = CashiersReportModel_P3::find($CHPH3_ID);
                                    $CashiersReportModel_P3->miscellaneous_items_type 	= $miscellaneous_items_type;
                                    $CashiersReportModel_P3->reference_no 				= $reference_no;
									$CashiersReportModel_P3->product_idx 				= $product_idx;
									$CashiersReportModel_P3->item_description 			= $request->item_description;
									$CashiersReportModel_P3->order_quantity 			= $order_quantity;
                                    $CashiersReportModel_P3->pump_price 			    = $pump_price;
									$CashiersReportModel_P3->unit_price 				= $product_manual_price;
									$CashiersReportModel_P3->discounted_price 			= $discounted_price;
									$CashiersReportModel_P3->order_total_amount 		= $peso_sales;
									$CashiersReportModel_P3->miscellaneous_items_type 	= $miscellaneous_items_type;
									
									$result = $CashiersReportModel_P3->update();
									
									if($result){
										return response()->json(['success'=>'Product Successfully Updated!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Information']);
									}
									
								}
								
	}	
	
	public function get_cashiers_report_product_p3_DISCOUNTS(Request $request){		
	
			$data =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
            ->where('miscellaneous_items_type','DISCOUNTS')
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->orderBy('cashiers_report_p3_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p3.reference_no',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.cashiers_report_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.order_total_amount'
					]);
		
			return response()->json($data);
	}
	
    public function get_cashiers_report_product_p3_SALES_CREDIT(Request $request){		
	
			$data =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
            ->where('miscellaneous_items_type','SALES_CREDIT')
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->orderBy('cashiers_report_p3_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.cashiers_report_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.order_total_amount'
					]);
		
			return response()->json($data);
	}

    public function get_cashiers_report_product_p3_OTHERS(Request $request){		
	
			$data =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
				->whereIn('miscellaneous_items_type', array('CASHOUT', 'OTHERS'))
				->orderBy('cashiers_report_p3_id', 'asc')
              	->get([
					'teves_cashiers_report_p3.reference_no',
					'teves_cashiers_report_p3.item_description',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.cashiers_report_id',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.order_total_amount'
					]);
		
			return response()->json($data);
	}

	public function cashiers_report_p3_info_SALES_CREDIT(Request $request){

		$CHPH3_ID = $request->CHPH3_ID;
		
		$data =  CashiersReportModel_P3::where('cashiers_report_p3_id', $CHPH3_ID)
		->where('miscellaneous_items_type','=','SALES_CREDIT')
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->get([
					'teves_product_table.product_name',
					'teves_product_table.product_id',
					'teves_cashiers_report_p3.miscellaneous_items_type',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.order_total_amount'
					]);			
					
		return response()->json($data);
		
	}
	
	public function cashiers_report_p3_info_DISCOUNT(Request $request){

		$CHPH3_ID = $request->CHPH3_ID;
		
		$data =  CashiersReportModel_P3::where('cashiers_report_p3_id', $CHPH3_ID)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->get([
					'teves_product_table.product_name',
					'teves_product_table.product_id',
					'teves_cashiers_report_p3.reference_no',
					'teves_cashiers_report_p3.miscellaneous_items_type',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.order_total_amount'
					]);			
					
		return response()->json($data);
		
	}
	
	public function cashiers_report_p3_info_OTHERS(Request $request){

		$CHPH3_ID = $request->CHPH3_ID;
		
		$data =  CashiersReportModel_P3::where('cashiers_report_p3_id', $CHPH3_ID)
				->get([
					'teves_cashiers_report_p3.miscellaneous_items_type',
					'teves_cashiers_report_p3.item_description',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.reference_no'
					]);			
					
		return response()->json($data);
		
	}
	
	public function delete_cashiers_report_product_p3(Request $request){		
			
		$CHPH3_ID = $request->CHPH3_ID;
		CashiersReportModel_P3::find($CHPH3_ID)->delete();
		return 'Deleted';
		
	}

	/*Part Four (MSC Reports)*/
	public function save_cashiers_report_PH4(Request $request){	

		$request->validate([
			'description_p4'  	=> 'required',
			'amount_p4'  		=> 'required'		
        ], 
        [
			'description_p4.required' 	=> 'Description is Required',
			'amount_p4.required' 		=> 'Amount is Required',
        ]
		);
			
			/*Get Cashier Report ID*/
			$CashiersReportId = $request->CashiersReportId;
			
			$description_p4		= $request->description_p4;
			$amount_p4 			= $request->amount_p4;
			
			$CHPH4_ID 			= $request->CHPH4_ID;

								if($CHPH4_ID=='' || $CHPH4_ID ==0){	
								
									$CashiersReportModel_P4 = new CashiersReportModel_P4();
									
									$CashiersReportModel_P4->user_idx 				= Session::get('loginID');
									$CashiersReportModel_P4->cashiers_report_id 	= $CashiersReportId;
									$CashiersReportModel_P4->description_p4 		= $description_p4;
									$CashiersReportModel_P4->amount_p4 				= $amount_p4;
									
									$result = $CashiersReportModel_P4->save();
									
									if($result){
										return response()->json(['success'=>'Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Creation']);
									}
									
								}else{
																	
									$CashiersReportModel_P4 = new CashiersReportModel_P4();
									$CashiersReportModel_P4 = CashiersReportModel_P4::find($CHPH4_ID);
									$CashiersReportModel_P4->description_p4 		= $description_p4;
									$CashiersReportModel_P4->amount_p4 				= $amount_p4;
									
									$result = $CashiersReportModel_P4->update();
									
									if($result){
										return response()->json(['success'=>'Successfully Updated!']);
									}
									else{
										return response()->json(['success'=>'Error on Updated']);
									}
									
								}
								
	}	
	
	public function get_cashiers_report_p4(Request $request){		
	
			$data =  CashiersReportModel_P4::where('cashiers_report_id', $request->CashiersReportId)
				->orderBy('cashiers_report_p4_id', 'asc')
              	->get([
					'teves_cashiers_report_p4.cashiers_report_p4_id',
					'teves_cashiers_report_p4.description_p4',
					'teves_cashiers_report_p4.amount_p4'
					]);
		
			return response()->json($data);
	}

	public function cashiers_report_p4_info(Request $request){

		$CHPH4_ID = $request->CHPH4_ID;
		
		$data =  CashiersReportModel_P4::where('cashiers_report_p4_id', $CHPH4_ID)
				->get([
					'teves_cashiers_report_p4.cashiers_report_p4_id',
					'teves_cashiers_report_p4.description_p4',
					'teves_cashiers_report_p4.amount_p4'
					]);			
					
		return response()->json($data);
		
	}
	
	public function delete_cashiers_report_p4(Request $request){		
			
		$CHPH4_ID = $request->CHPH4_ID;
		CashiersReportModel_P4::find($CHPH4_ID)->delete();
		return 'Deleted';
		
	}

	/*Part 5 - Cash on Hand*/
	public function cashiers_report_p5_info(Request $request){
		
		$CashiersReportID = $request->CashiersReportId;
		
		$data = CashiersReportModel_P5::where('cashiers_report_id', $CashiersReportID)
            ->get([	
			'teves_cashiers_report_p5.cashiers_report_p5_id',			
			'teves_cashiers_report_p5.one_thousand_deno',
			'teves_cashiers_report_p5.five_hundred_deno',
			'teves_cashiers_report_p5.two_hundred_deno',
			'teves_cashiers_report_p5.one_hundred_deno',
			'teves_cashiers_report_p5.fifty_deno',
			'teves_cashiers_report_p5.twenty_deno',
			'teves_cashiers_report_p5.ten_deno',
			'teves_cashiers_report_p5.five_deno',
			'teves_cashiers_report_p5.one_deno',
			'teves_cashiers_report_p5.twenty_five_cent_deno',
			'teves_cashiers_report_p5.cash_drop'
			]);
		
		return response()->json($data);
					
	}

	public function save_cashiers_report_PH5(Request $request){
		
		$request->validate([
			'cash_drop'   => 'required'
        ], 
        [
			'cash_drop.required' => 'Cash Drop is required'
        ]
		);
		
			$CashOnHand = new CashiersReportModel_P5();
			$CashOnHand = CashiersReportModel_P5::find($request->CHPH5_ID);
			$CashOnHand->one_thousand_deno 		= $request->one_thousand_deno;
			$CashOnHand->five_hundred_deno	 	= $request->five_hundred_deno;
			$CashOnHand->two_hundred_deno 		= $request->two_hundred_deno;
			$CashOnHand->one_hundred_deno 		= $request->one_hundred_deno;
			$CashOnHand->fifty_deno 			= $request->fifty_deno;
			$CashOnHand->twenty_deno 			= $request->twenty_deno;
			$CashOnHand->ten_deno 				= $request->ten_deno;
			$CashOnHand->five_deno 				= $request->five_deno;
			$CashOnHand->one_deno 				= $request->one_deno;
			$CashOnHand->twenty_five_cent_deno  = $request->twenty_five_cent_deno;;
			$CashOnHand->cash_drop 				= $request->cash_drop;
			
			$result = $CashOnHand->update();
			
			if($result){
				return response()->json(array('success' => "Cash On Hand Successfully Updated!"));
			}
			else{
				return response()->json(['success'=>"Error on Insert Cashier's Cash on Hand Report Information"]);
			}
			
	}	
	
	
	public function save_product_cashiers_report_p6(Request $request){	

		$request->validate([
			'product_idx'  			=> 'required',
			'tank_idx'  			=> 'required',
			'beginning_inventory'  	=> 'required',
			'sales_in_liters_inventory'  		=> 'required',		
			'delivery_inventory'  			=> 'required',
			'ending_inventory' 		=> 'required'
        ], 
        [
			'product_idx.required' 			=> 'Product is Required',
			'tank_idx.required' 			=> 'Tank is Required',
			'beginning_inventory.required' 	=> 'Beginning Inventory is Required',
			'sales_in_liters_inventory.required' 		=> 'Sales in Liters is Required',
			'delivery_inventory.required' 			=> 'Delivery is Required',
			'ending_inventory.required' 	=> 'Ending Inventory is Required',
        ]
		);
			
			/*Get Last ID*/
			$CashiersReportId = $request->CashiersReportId;
			
			$product_idx				= $request->product_idx;
			$tank_idx					= $request->tank_idx;
			$beginning_inventory 		= $request->beginning_inventory;
			$sales_in_liters_inventory 	= $request->sales_in_liters_inventory;
			$delivery_inventory 		= $request->delivery_inventory;
			$ending_inventory 			= $request->ending_inventory;
			
			
			$book_stock = ($beginning_inventory - $sales_in_liters_inventory) + $delivery_inventory;
			$variance = $book_stock - $ending_inventory;
			
			$CRPH6_ID 				= $request->CRPH6_ID;
								
								if($CRPH6_ID=='' || $CRPH6_ID ==0){	
								
									$CashiersReportModel_p6 = new CashiersReportModel_p6();
									
									$CashiersReportModel_p6->user_idx 					= Session::get('loginID');
									$CashiersReportModel_p6->cashiers_report_idx 		= $CashiersReportId;
									$CashiersReportModel_p6->product_idx 				= $product_idx;
									$CashiersReportModel_p6->tank_idx	 				= $tank_idx;
									$CashiersReportModel_p6->beginning_inventory 		= $beginning_inventory;
									$CashiersReportModel_p6->sales_in_liters 			= $sales_in_liters_inventory;
									$CashiersReportModel_p6->delivery 					= $delivery_inventory;
									$CashiersReportModel_p6->ending_inventory 			= $ending_inventory;
									$CashiersReportModel_p6->book_stock 				= $book_stock;
									$CashiersReportModel_p6->variance 					= $variance;
									
									$result = $CashiersReportModel_p6->save();
									
									if($result){
										return response()->json(['success'=>'Product Inventory Successfully Created!']);
									}
									else{
										return response()->json(['success'=>'Error on Product Inventory Information']);
									}
									
								}else{
																	
									$CashiersReportModel_p6 = new CashiersReportModel_p6();
									$CashiersReportModel_p6 = CashiersReportModel_p6::find($CRPH6_ID);
									
									$CashiersReportModel_p6->cashiers_report_idx 		= $CashiersReportId;
									$CashiersReportModel_p6->product_idx 				= $product_idx;
									$CashiersReportModel_p6->tank_idx	 				= $tank_idx;
									$CashiersReportModel_p6->beginning_inventory 		= $beginning_inventory;
									$CashiersReportModel_p6->sales_in_liters 			= $sales_in_liters_inventory;
									$CashiersReportModel_p6->delivery 					= $delivery_inventory;
									$CashiersReportModel_p6->ending_inventory 			= $ending_inventory;
									$CashiersReportModel_p6->book_stock 				= $book_stock;
									$CashiersReportModel_p6->variance 					= $variance;
									
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
	public function get_product_inventory_list(Request $request){		

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
						'teves_cashiers_report_p6.delivery',
						'teves_cashiers_report_p6.ending_inventory',
						'teves_cashiers_report_p6.book_stock',
						'teves_cashiers_report_p6.variance'
					]);
		
			return response()->json($data);			
	}

	
	public function cashiers_report_p6_info(Request $request){

		$CHPH6_ID = $request->CHPH6_ID;
		
		$data =  CashiersReportModel_p6::Join('teves_product_tank_table', 'teves_product_tank_table.tank_id', '=', 'teves_cashiers_report_p6.tank_idx')
					->Join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p6.product_idx')
					->where('teves_cashiers_report_p6.cashiers_report_p6_id', $CHPH6_ID)
					
					->get([
						'teves_product_table.product_id',
						'teves_product_table.product_name',
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_cashiers_report_p6.cashiers_report_p6_id',
						'teves_cashiers_report_p6.beginning_inventory',
						'teves_cashiers_report_p6.sales_in_liters',
						'teves_cashiers_report_p6.delivery',
						'teves_cashiers_report_p6.ending_inventory',
						'teves_cashiers_report_p6.book_stock',
						'teves_cashiers_report_p6.variance'
					]);
					
		return response()->json($data);
		
	}
	
	public function delete_cashiers_report_p6(Request $request){		
			
		$CHPH6_ID = $request->CHPH6_ID;
		CashiersReportModel_p6::find($CHPH6_ID)->delete();
		return 'Deleted';
		
	}
	
	/*OLD cashiers_report_p6_info*/
	public function cashiers_report_summary_info(Request $request){

		$CashiersReportId = $request->CashiersReportId;
		
		$PH1_SUM =  CashiersReportModel_P1::where('teves_cashiers_report_p1.cashiers_report_id', $CashiersReportId)
        ->sum('order_total_amount');

		$PH2_SUM =  CashiersReportModel_P2::where('teves_cashiers_report_p2.cashiers_report_id', $CashiersReportId)
        ->sum('order_total_amount');
		
		$PH3_SUM =  CashiersReportModel_P3::where('teves_cashiers_report_p3.cashiers_report_id', $CashiersReportId)
        ->sum('order_total_amount');
		
		$PH4_SUM =  CashiersReportModel_P4::where('teves_cashiers_report_p4.cashiers_report_id', $CashiersReportId)
        ->sum('amount_p4');
		
		$_PH5_SUM = CashiersReportModel_P5::where('cashiers_report_id', $CashiersReportId)
            ->get([	
			'teves_cashiers_report_p5.cashiers_report_p5_id',			
			'teves_cashiers_report_p5.one_thousand_deno',
			'teves_cashiers_report_p5.five_hundred_deno',
			'teves_cashiers_report_p5.two_hundred_deno',
			'teves_cashiers_report_p5.one_hundred_deno',
			'teves_cashiers_report_p5.fifty_deno',
			'teves_cashiers_report_p5.twenty_deno',
			'teves_cashiers_report_p5.ten_deno',
			'teves_cashiers_report_p5.five_deno',
			'teves_cashiers_report_p5.one_deno',
			'teves_cashiers_report_p5.twenty_five_cent_deno',
			'teves_cashiers_report_p5.cash_drop'
			]);
		
		$one_thousand_deno 		= $_PH5_SUM[0]->one_thousand_deno * 1000;
		
		$five_hundred_deno 		= $_PH5_SUM[0]->five_hundred_deno * 500;
		$two_hundred_deno 		= $_PH5_SUM[0]->two_hundred_deno * 200;
		$one_hundred_deno 		= $_PH5_SUM[0]->one_hundred_deno * 100;
		
		$fifty_deno 			= $_PH5_SUM[0]->fifty_deno * 50;
		$twenty_deno 			= $_PH5_SUM[0]->twenty_deno * 20;
		$ten_deno 				= $_PH5_SUM[0]->ten_deno * 10;
		
		$five_deno 				= $_PH5_SUM[0]->five_deno * 5;
		$one_deno 				= $_PH5_SUM[0]->one_deno * 1;
		
		$twenty_five_cent_deno 	= $_PH5_SUM[0]->twenty_five_cent_deno * 0.25;
		
		$cash_drop 	= $_PH5_SUM[0]->cash_drop;
		
		$PH5_SUM = $cash_drop + $one_thousand_deno + $five_hundred_deno + $two_hundred_deno + $one_hundred_deno + $fifty_deno + $twenty_deno + $ten_deno + $five_deno + $one_deno + $twenty_five_cent_deno;
        		
		if($CashiersReportId!=0){
				return response()->json(array('success' => "Success", 'fuel_sales_total' => $PH1_SUM, 'other_sales_total' => $PH2_SUM, 'miscellaneous_total' => $PH3_SUM, 'theoretical_sales' => $PH4_SUM, 'cash_on_hand' => $PH5_SUM), 200);
		}
		else{
				return response()->json(['success'=>"Error on Load"]);
		}
			
	}
	
	
	/*Print Via PDF*/
	public function generate_cashier_report_pdf(Request $request){

		$request->validate([
		  'CashiersReportId'      			=> 'required'
        ], 
        [
			'CashiersReportId.required' 	=> 'Please select a Client',
        ]
		);
		$CashiersReportId = $request->CashiersReportId;
		
		$CashiersReportData = CashiersReportModel::where('cashiers_report_id', $CashiersReportId)
			->join('user_tb', 'user_tb.user_id', '=', 'teves_cashiers_report.user_idx')
            ->get([				
			'teves_cashiers_report.cashiers_report_id',
			'user_tb.user_real_name',
			'teves_cashiers_report.teves_branch',
			'teves_cashiers_report.cashiers_name',
			'teves_cashiers_report.forecourt_attendant',
			'teves_cashiers_report.report_date',
			'teves_cashiers_report.shift',
			'teves_cashiers_report.created_at',
			'teves_cashiers_report.updated_at']);
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
          
		$title = 'Cashier Report';
		  
		$data_P1_premium_95 =  CashiersReportModel_P1::where('cashiers_report_id', $request->CashiersReportId)
			->where('product_idx', 13)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p1.product_idx')
				->orderBy('cashiers_report_p1_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p1.product_price',
					'teves_cashiers_report_p1.cashiers_report_p1_id',
					'teves_cashiers_report_p1.cashiers_report_id',
					'teves_cashiers_report_p1.beginning_reading',
					'teves_cashiers_report_p1.closing_reading',
					'teves_cashiers_report_p1.calibration',
					'teves_cashiers_report_p1.order_quantity',
					'teves_cashiers_report_p1.order_total_amount'
					]); 
					
		$data_P1_super_regular =  CashiersReportModel_P1::where('cashiers_report_id', $request->CashiersReportId)
			->where('product_idx', 11)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p1.product_idx')
				->orderBy('cashiers_report_p1_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p1.product_price',
					'teves_cashiers_report_p1.cashiers_report_p1_id',
					'teves_cashiers_report_p1.cashiers_report_id',
					'teves_cashiers_report_p1.beginning_reading',
					'teves_cashiers_report_p1.closing_reading',
					'teves_cashiers_report_p1.calibration',
					'teves_cashiers_report_p1.order_quantity',
					'teves_cashiers_report_p1.order_total_amount'
					]); 		

		$data_P1_diesel =  CashiersReportModel_P1::where('cashiers_report_id', $request->CashiersReportId)
			->where('product_idx', 12)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p1.product_idx')
				->orderBy('cashiers_report_p1_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p1.product_price',
					'teves_cashiers_report_p1.cashiers_report_p1_id',
					'teves_cashiers_report_p1.cashiers_report_id',
					'teves_cashiers_report_p1.beginning_reading',
					'teves_cashiers_report_p1.closing_reading',
					'teves_cashiers_report_p1.calibration',
					'teves_cashiers_report_p1.order_quantity',
					'teves_cashiers_report_p1.order_total_amount'
					]);

    	$data_P2 =  CashiersReportModel_P2::where('cashiers_report_id', $request->CashiersReportId)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p2.product_idx')
			->orderBy('cashiers_report_p2_id', 'asc')
            ->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p2.product_price',
					'teves_cashiers_report_p2.cashiers_report_p2_id',
					'teves_cashiers_report_p2.cashiers_report_id',
					'teves_cashiers_report_p2.order_quantity',
					'teves_cashiers_report_p2.order_total_amount'
					]);

		$data_SALES_CREDIT =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
			->where('miscellaneous_items_type','=','SALES_CREDIT')
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->get([
					'teves_product_table.product_name',
					'teves_product_table.product_id',
					'teves_cashiers_report_p3.miscellaneous_items_type',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.order_total_amount'
					]);		
					
		$data_DISCOUNTS =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
            ->where('miscellaneous_items_type','DISCOUNTS')
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p3.product_idx')
				->orderBy('cashiers_report_p3_id', 'asc')
              	->get([
					'teves_product_table.product_id as product_idx',
					'teves_product_table.product_name',
					'teves_cashiers_report_p3.reference_no',
					'teves_cashiers_report_p3.pump_price',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.discounted_price',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.cashiers_report_id',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.order_total_amount'
					]);
		
		$data_OTHERS =  CashiersReportModel_P3::where('cashiers_report_id', $request->CashiersReportId)
				->whereIn('miscellaneous_items_type', array('CASHOUT', 'OTHERS'))
				->orderBy('cashiers_report_p3_id', 'asc')
              	->get([
					'teves_cashiers_report_p3.reference_no',
					'teves_cashiers_report_p3.cashiers_report_p3_id',
					'teves_cashiers_report_p3.cashiers_report_id',
					'teves_cashiers_report_p3.unit_price',
					'teves_cashiers_report_p3.order_quantity',
					'teves_cashiers_report_p3.order_total_amount'
					]);
		
		$data_theoretical_sales =  CashiersReportModel_P4::where('cashiers_report_id', $request->CashiersReportId)
			->orderBy('cashiers_report_p4_id', 'asc')
            ->get([
				'teves_cashiers_report_p4.cashiers_report_p4_id',
				'teves_cashiers_report_p4.description_p4',
				'teves_cashiers_report_p4.amount_p4'
				]);
		
		$data_Cash_on_hand = CashiersReportModel_P5::where('cashiers_report_id', $request->CashiersReportId)
            ->get([	
			'teves_cashiers_report_p5.cashiers_report_p5_id',			
			'teves_cashiers_report_p5.one_thousand_deno',
			'teves_cashiers_report_p5.five_hundred_deno',
			'teves_cashiers_report_p5.two_hundred_deno',
			'teves_cashiers_report_p5.one_hundred_deno',
			'teves_cashiers_report_p5.fifty_deno',
			'teves_cashiers_report_p5.twenty_deno',
			'teves_cashiers_report_p5.ten_deno',
			'teves_cashiers_report_p5.five_deno',
			'teves_cashiers_report_p5.one_deno',
			'teves_cashiers_report_p5.twenty_five_cent_deno',
			'teves_cashiers_report_p5.cash_drop'
			]);
		
		$data_PH6_inventory =  CashiersReportModel_p6::Join('teves_product_tank_table', 'teves_product_tank_table.tank_id', '=', 'teves_cashiers_report_p6.tank_idx')
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
						'teves_cashiers_report_p6.ugt_pumping',
						'teves_cashiers_report_p6.sales_in_liters',
						'teves_cashiers_report_p6.delivery',
						'teves_cashiers_report_p6.ending_inventory',
						'teves_cashiers_report_p6.book_stock',
						'teves_cashiers_report_p6.variance'
					]);
		
				/*
				$data_PH7_inventory =  CashiersReportModel_p7::Join('teves_product_tank_table', 'teves_product_tank_table.tank_id', '=', 'teves_cashiers_report_p7.tank_idx')
					->Join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_cashiers_report_p7.product_idx')
					->where('teves_cashiers_report_p7.cashiers_report_idx', $request->CashiersReportId)
					->orderBy('teves_cashiers_report_p7.product_idx', 'asc')
					->get([
						'teves_product_table.product_id',
						'teves_product_table.product_name',
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_cashiers_report_p7.cashiers_report_p7_id',
						'teves_cashiers_report_p7.beginning_inventory',
						'teves_cashiers_report_p7.sales_in_liters',
						'teves_cashiers_report_p7.ugt_pumping',
						'teves_cashiers_report_p7.delivery',
						'teves_cashiers_report_p7.ending_inventory',
						'teves_cashiers_report_p7.book_stock',
						'teves_cashiers_report_p7.variance'
					]);
				*/	
		$branch_header = TevesBranchModel::find($CashiersReportData[0]['teves_branch'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
        $pdf = PDF::loadView('printables.cashier_report_pdf', compact('title', 'CashiersReportData', 'data_P1_premium_95', 'data_P1_super_regular', 'data_P1_diesel', 'data_P2', 'data_SALES_CREDIT', 'data_DISCOUNTS', 'data_OTHERS', 'data_theoretical_sales', 'data_Cash_on_hand','branch_header','data_PH6_inventory'));
		
		/*Download Directly*/
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'portrait');/*Set to Landscape*/
		$pdf->render();
		return $pdf->stream($CashiersReportData[0]['cashiers_name'].".pdf");

	}

}
