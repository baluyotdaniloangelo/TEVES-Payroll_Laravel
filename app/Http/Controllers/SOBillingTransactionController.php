<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SOBillingTransactionModel;
use App\Models\BillingTransactionModel;
use App\Models\ProductModel;
use App\Models\ClientModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
//use Spatie\Activitylog\Models\Activity;

class SOBillingTransactionController extends Controller
{
	
	/*Load Site Interface*/
	public function so(){
		
		$title = 'SO List';
		$data = array();
		if(Session::has('loginID')){
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$client_data = ClientModel::all();
			
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
			
			$drivers_name = SOBillingTransactionModel::select('drivers_name')->distinct()->get();
			$plate_no = SOBillingTransactionModel::select('plate_no')->distinct()->get();
		
		}

		return view("pages.so_billing", compact('data','title','client_data','drivers_name','plate_no','teves_branch'));
		
	}   

	/*Fetch SO List using Datatable*/
	public function getSOBillingTransactionList(Request $request)
    {

		$list = SOBillingTransactionModel::get();
		if ($request->ajax()) {

	if(Session::get('UserType')!="Admin"){
		
    	$data = SOBillingTransactionModel::join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_billing_so_table.client_idx')
					->whereRaw("teves_billing_so_table.branch_idx IN (SELECT branch_idx FROM teves_user_branch_access WHERE user_idx=?)", Session::get('loginID'))
              		->get([
					'teves_billing_so_table.so_id',
					'teves_billing_so_table.so_number',
					'teves_billing_so_table.drivers_name',
					'teves_billing_so_table.plate_no',
					'teves_client_table.client_name',
					'teves_billing_so_table.order_date',
					'teves_billing_so_table.order_time',
					'teves_billing_so_table.created_at',
					'teves_billing_so_table.created_by_user_id']);
					
	}else{
		
		$data = SOBillingTransactionModel::join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_billing_so_table.client_idx')
              		->get([
					'teves_billing_so_table.so_id',
					'teves_billing_so_table.so_number',
					'teves_billing_so_table.drivers_name',
					'teves_billing_so_table.plate_no',
					'teves_client_table.client_name',
					'teves_billing_so_table.order_date',
					'teves_billing_so_table.order_time',
					'teves_billing_so_table.created_at',
					'teves_billing_so_table.created_by_user_id']);
					
	}
		
		return DataTables::of($data)
				
				->addIndexColumn()				
                ->addColumn('action', function($row){
							
						if(Session::get('UserType')=="Admin"){
							$actionBtn = '
							<div align="center" class="action_table_menu_site">
								<a href="so_add_product/'.$row->so_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editSO"></a>
								<a href="#" data-id="'.$row->so_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteSO"></a>
							</div>';
						}
						else{
						
						$startTimeStamp = strtotime($row->created_at);
						$endTimeStamp = strtotime(date('y-m-d'));
						$timeDiff = abs($endTimeStamp - $startTimeStamp);
						$numberDays = $timeDiff/86400;  // 86400 seconds in one day
						// and you might want to convert to integer
						$numberDays = intval($numberDays);
							
							if($numberDays>=3 || Session::get('loginID')!=$row->created_by_user_id){
								/*$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->so_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="viewSO"></a>
								</div>';*/
								$actionBtn = '';
							}else{
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="so_add_product/'.$row->so_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editSO"></a>
								<a href="#" data-id="'.$row->so_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteSO"></a>
								</div>';
							}
						}
                    return $actionBtn;
                })		
				->rawColumns(['action'])
                ->make(true);		
		}
    }

	public function so_info(Request $request){

		$SOId = $request->so_id;
		$data = SOBillingTransactionModel::where('so_id', $request->so_id)
              		->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_billing_so_table.client_idx')	
              		->get([
					'teves_billing_so_table.branch_idx',
					'teves_billing_so_table.so_id',
					'teves_billing_so_table.so_number',
					'teves_billing_so_table.drivers_name',
					'teves_billing_so_table.plate_no',
					'teves_client_table.client_name',
					'teves_billing_so_table.order_date',
					'teves_billing_so_table.order_time',
					'teves_billing_so_table.created_at']);
		return response()->json($data);
		
	}

	/*Delete SO and Product Information*/
	public function delete_so_confirmed(Request $request){

		$SOId = $request->so_id;
		SOBillingTransactionModel::find($SOId)->delete();
		
		/*Delete on Sales Order Product Component*/	
		BillingTransactionModel::where('so_idx', $SOId)->delete();
		
		return 'Deleted';
		
	} 	
	
	
	/*Load Site Interface*/
	public function create_so_billing(){
		
		$title = 'Billing Transaction';
		$data = array();
		if(Session::has('loginID')){
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
		
			$product_data = ProductModel::all();
			
			$client_data = ClientModel::all();
			
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
		
			return view("pages.billing_so_form", compact('data','title','product_data','client_data','drivers_name','plate_no','teves_branch'));
		}

	}  
	
	public function create_so_post(Request $request){
		
		if(Session::has('loginID')){
			
			$request->validate([
			  'order_date'      		=> 'required',
			  'order_time'      		=> 'required',
			  'so_number'      			=> 'required|unique:teves_billing_so_table,so_number',
			  'client_idx'      		=> 'required',
			  'plate_no'      			=> 'required',
			  'drivers_name'      		=> 'required',
			], 
			[
				'order_date.required' => 'Order Date is required',
				'order_time.required' => 'Order Time is Required',
				'so_number.required' => 'SO is Required',
				'client_idx.required' => 'Client is Required',
				'plate_no.required' => 'Plate Number is Required',
				'drivers_name.required' => "Driver's Name is Required",
			]
			);
					
						/*insert*/
						$SOBilling = new SOBillingTransactionModel();
						$SOBilling->branch_idx 			= $request->branch_id;
						$SOBilling->order_date 			= $request->order_date;
						$SOBilling->order_time 			= $request->order_time;
						$SOBilling->so_number 			= $request->so_number;	
						$SOBilling->client_idx 			= $request->client_idx;
						$SOBilling->plate_no 			= $request->plate_no;
						$SOBilling->drivers_name 		= $request->drivers_name;
						$SOBilling->created_by_user_id 	= Session::get('loginID');
						$result = $SOBilling->save();
						
						$last_transaction_id = $SOBilling->so_id;
				
						if($result){
							return response()->json(array('success' => "SO Information Successfully Created!", 'so_id' => $last_transaction_id), 200);
						}
						else{
							return response()->json(['success'=>'Error on Insert SO Information']);
						}	
		}
		
	}
	
	/*Load SO Information. This Page will also enables user to add the Product*/
	public function so_add_product($SOId){

		if(Session::has('loginID')){
			
			$title = 'Update SO / Add Product';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$so_data = SOBillingTransactionModel::where('so_id', $SOId)
			->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_billing_so_table.client_idx')
            ->get([				
			'teves_client_table.client_name',
			'teves_client_table.client_id',
			'teves_billing_so_table.so_number',
			'teves_billing_so_table.branch_idx',
			'teves_billing_so_table.order_date',
			'teves_billing_so_table.order_time',
			'teves_billing_so_table.drivers_name',
			'teves_billing_so_table.plate_no']);	
			
			$raw_query_product = "SELECT a.product_id,a.product_name,ifnull(b.branch_price,a.product_price) AS product_price ,c.branch_code FROM teves_product_table AS a
			LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
			WHERE c.branch_id = ?";			
			$product_data = DB::select("$raw_query_product", [$so_data[0]['branch_idx']]);
		
			$client_data = ClientModel::all();
			$teves_branch = TevesBranchModel::all();
			
			$drivers_name = BillingTransactionModel::select('drivers_name')->distinct()->get();
			$plate_no = BillingTransactionModel::select('plate_no')->distinct()->get();
			
			return view("pages.billing_so_form_add_product", compact('data','title','product_data','client_data','drivers_name','plate_no', 'SOId','so_data','teves_branch'));

		}
	}  	
	
	public function update_so_post(Request $request){
		
		$request->validate([
          'order_date'      		=> 'required',
		  'order_time'      		=> 'required',
		  'so_number'      			=> ['required',Rule::unique('teves_billing_so_table')->where( 
									fn ($query) =>$query
										->where('so_id', '<>',  $request->so_id )
									)],
		  'client_idx'      		=> 'required',
		  'plate_no'      			=> 'required',
		  'drivers_name'      		=> 'required',
        ], 
        [
			'order_date.required' => 'Order Date is required',
			'order_time.required' => 'Order Time is Required',
			'so_number.required' => 'SO is Required',
			'client_idx.required' => 'Client is Required',
			'plate_no.required' => 'Plate Number is Required',
			'drivers_name.required' => "Driver's Name is Required",
        ]
		);
				
					/*insert*/
					$SOBilling = new SOBillingTransactionModel();
					$SOBilling = SOBillingTransactionModel::find($request->so_id);
					$SOBilling->branch_idx 			= $request->branch_id;
					$SOBilling->order_date 			= $request->order_date;
					$SOBilling->order_time 			= $request->order_time;
					$SOBilling->so_number 			= $request->so_number;	
					$SOBilling->client_idx 			= $request->client_idx;
					$SOBilling->plate_no 			= $request->plate_no;
					$SOBilling->drivers_name 		= $request->drivers_name;
					$SOBilling->updated_by_user_id 	= Session::get('loginID');
					$result = $SOBilling->update();	

						/*Update Product*/
						$billing_update = BillingTransactionModel::where('so_idx', $request->so_id)
						->update(
							['branch_idx'			=> $request->branch_id,
							'order_date' 			=> $request->order_date,
							'order_time' 			=> $request->order_time,
							'order_po_number' 		=> $request->so_number,
							'client_idx' 			=> $request->client_idx,
							'plate_no' 			=> $request->plate_no,
							'drivers_name' 		=> $request->drivers_name,
							'updated_by_user_idx' 	=> Session::get('loginID')]
						);
					//$billing_update->update();
					
						
			
					if($result){
						return response()->json(array('success' => "SO Information Successfully Updated!", 'so_id' => $request->so_id), 200);
					}
					else{
						return response()->json(['success'=>'Error on Insert SO Information']);
					}	
		
	}
	
	public function so_add_product_post(Request $request){

		$request->validate([
		  'product_idx'      		=> 'required',
		  'order_quantity'      	=> 'required',
        ], 
        [
			'product_idx.required' => 'Product is Required',
			'order_quantity.required' => 'Quantity is Required'
        ]
		);
				
					/*Product Details*/
					$raw_query_product = "SELECT a.product_id, ifnull(b.branch_price,a.product_price) AS product_price FROM teves_product_table AS a
					LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
					WHERE b.branch_idx = ? and b.product_idx = ?";			
					$product_info = DB::select("$raw_query_product", [$request->branch_idx,$request->product_idx]);
									
					/*SO Details*/
					$so_info = SOBillingTransactionModel::find($request->so_id, ['branch_idx','so_number','order_date','order_time','client_idx','drivers_name','plate_no','plate_no']);	
					
					/*Check if Price is From Manual Price*/
					if($request->product_manual_price!=0){
						$product_price = $request->product_manual_price;
					}else{
						$product_price = $product_info[0]->product_price;
					}
					
					$order_total_amount = $request->order_quantity * $product_price;	
					
					/*insert*/
					$Billing = new BillingTransactionModel();
					$Billing->so_idx 				= $request->so_id;
					$Billing->branch_idx			= $request->branch_idx;
					$Billing->order_date 			= $so_info->order_date;
					$Billing->order_time 			= $so_info->order_time;
					$Billing->order_po_number 		= $so_info->so_number                                                                                                                                                                     ;	
					$Billing->client_idx 			= $so_info->client_idx;
					$Billing->plate_no 				= $so_info->plate_no;
					$Billing->drivers_name 			= $so_info->drivers_name;
					$Billing->product_idx 			= $request->product_idx;
					$Billing->product_price 		= $product_price;
					$Billing->order_quantity 		= $request->order_quantity;
					$Billing->order_total_amount 	= $order_total_amount;
					
					$result = $Billing->save();
			
					if($result){
						return response()->json(array('success' => "Bill Information Successfully Created!"), 200);
					}
					else{
						return response()->json(['success'=>'Error on Insert Bill Information']);
					}	
		
	}

	public function get_so_product(Request $request){		
	
			$data =  BillingTransactionModel::where('so_idx', $request->so_id)
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
				->orderBy('billing_id', 'asc')
              	->get([
					'teves_product_table.product_name',
					'teves_billing_table.product_price',
					'teves_billing_table.order_quantity',
					'teves_billing_table.order_total_amount',
					'teves_billing_table.billing_id'
					]);
		
			return response()->json($data);
	}

	public function so_update_product_post(Request $request){

		$request->validate([
		  'product_idx'      		=> 'required',
		  'order_quantity'      	=> 'required',
        ], 
        [
			'product_idx.required' => 'Product is Required',
			'order_quantity.required' => 'Quantity is Required'
        ]
		);
				
					/*Product Details*/
					$raw_query_product = "SELECT a.product_id, ifnull(b.branch_price,a.product_price) AS product_price FROM teves_product_table AS a
					LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
					WHERE b.branch_idx = ? and b.product_idx = ?";			
					$product_info = DB::select("$raw_query_product", [$request->branch_idx,$request->product_idx]);				
					
					/*SO Details*/
					$so_info = SOBillingTransactionModel::find($request->so_id, ['branch_idx','so_number','order_date','order_time','client_idx','drivers_name','plate_no','plate_no']);	
					
					/*Check if Price is From Manual Price*/
					if($request->product_manual_price!=0){
						$product_price = $request->product_manual_price;
					}else{
						$product_price = $product_info[0]->product_price;
					}
					
					$order_total_amount = $request->order_quantity * $product_price;	
					
					/*insert*/
					$Billing = new BillingTransactionModel();
					$Billing = BillingTransactionModel::find($request->billing_id);
					$Billing->branch_idx			= $request->branch_idx;
					$Billing->order_date 			= $so_info->order_date;
					$Billing->order_time 			= $so_info->order_time;
					$Billing->order_po_number 		= $so_info->so_number                                                                                                                                                                     ;	
					$Billing->client_idx 			= $so_info->client_idx;
					$Billing->plate_no 				= $so_info->plate_no;
					$Billing->drivers_name 			= $so_info->drivers_name;
					$Billing->product_idx 			= $request->product_idx;
					$Billing->product_price 		= $product_price;
					$Billing->order_quantity 		= $request->order_quantity;
					$Billing->order_total_amount 	= $order_total_amount;
					
					$result = $Billing->update();
			
					if($result){
						return response()->json(array('success' => "Bill Information Successfully Updated!"), 200);
					}
					else{
						return response()->json(['success'=>'Error on Insert Bill Information']);
					}				
	}		
}
