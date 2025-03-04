<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BillingTransactionModel;
use App\Models\SOBillingTransactionModel;
use App\Models\ReceivablesModel;
use App\Models\ReceivablesPaymentModel;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderComponentModel;
use App\Models\SalesOrderPaymentModel;

use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderComponentModel;
use App\Models\PurchaseOrderPaymentModel;


use App\Models\ClientModel;
use Session;
use Validator;
//use DataTables;
use Illuminate\Support\Facades\DB;
/*Excel*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/*PDF*/
use PDF;
use DataTables;

class ReportController extends Controller
{
	
	/*Load Billing History Report Interface*/
	public function billing_history(){
		
		if(Session::has('loginID')){
			
			$title = 'Billing History';
			$data = array();
			if(Session::has('loginID')){
				
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
				
				$client_data = ClientModel::all();
				
				$product_data = ProductModel::all();
				//$teves_branch = TevesBranchModel::all();
				
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
			
			}

			return view("pages.billing_history", compact('data','title','client_data','drivers_name','plate_no','product_data','teves_branch'));
		
		}
	}  	
	
	public function generate_report(Request $request){


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

		$client_idx = $request->client_idx;
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		$company_header = $request->company_header;
					
		/*Using Raw Query*/
		$raw_query = "select `teves_billing_table`.`billing_id`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time` from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` >= ? and `teves_billing_table`.`order_date` <= ? and `teves_billing_table`.`branch_idx` = ? order by `teves_billing_table`.`order_date` asc";			
		$data = DB::select("$raw_query", [$client_idx,$start_date,$end_date,$company_header]);

				return DataTables::of($data)
				->addIndexColumn()
                ->make(true);
		
	}	
	
	
	public function soa_summary_history(){
		

		if(Session::has('loginID')){

		$title = 'Statement of Account - Summary';
		$data = array();
		
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
			
		}

		return view("pages.soa_summary_report", compact('data','title','client_data','teves_branch'));
		
	}  	
	
	public function generate_soa_summary(Request $request){

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

		$client_idx = $request->client_idx;
		$start_date = $request->start_date;
		$end_date = $request->end_date;

	   $receivable_data = ReceivablesModel::where('teves_receivable_table.client_idx', $client_idx)
					->where('teves_receivable_table.billing_date', '>=', $start_date)
                    ->where('teves_receivable_table.billing_date', '<=', $end_date)
              	->get([
					'teves_receivable_table.receivable_name',
					'teves_receivable_table.billing_date',
					'teves_receivable_table.control_number',
					'teves_receivable_table.or_number',
					'teves_receivable_table.ar_reference',
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'teves_receivable_table.receivable_remaining_balance'
				]);	


		$result = array();
		
		$total_payment = 0;
		$current_balance = 0;
		
		foreach ($receivable_data as $data_cols){
	   
				@$receivable_name = $data_cols->receivable_name;
				@$billing_date = $data_cols->billing_date;
				@$control_number = $data_cols->control_number;
				@$or_number = $data_cols->or_number;
				@$ar_reference = $data_cols->ar_reference;
				@$payment_term = $data_cols->payment_term;
				@$receivable_description = $data_cols->receivable_description;
				@$receivable_amount = $data_cols->receivable_amount;
				@$receivable_remaining_balance = $data_cols->receivable_remaining_balance;		
							
				$current_balance += $data_cols->receivable_remaining_balance;	
				
					$result[] = array(
					'receivable_name' => $receivable_name,
					'billing_date' => $billing_date,
					'control_number' => $control_number,
					'or_number' => $or_number,
					'ar_reference' => $ar_reference,
					'payment_term' => $payment_term,
					'receivable_description' => $receivable_description,
					'receivable_amount' => $receivable_amount,
					'receivable_remaining_balance' => $receivable_remaining_balance,
					'current_balance' => @$current_balance
					);
					  
		}	
		
		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		
		
	}	
	
	public function generate_soa_summary_pdf(Request $request){

		if(Session::has('loginID')){
			
		$request->validate([
			'client_idx'      		=> 'required',
			'client_idx'      		=> 'required',
			'start_date'      		=> 'required',
			'end_date'      			=> 'required'
        ], 
        [
			'client_idx.required' 	=> 'Please select a Client',
			'company_header.required' 	=> 'Please select a Branch',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);

		$client_idx = $request->client_idx;
		$company_header = $request->company_header;
		$start_date = $request->start_date;
		$end_date = $request->end_date;

	   /*SOA Data*/
	   $receivable_data = ReceivablesModel::where('teves_receivable_table.client_idx', $client_idx)
					->where('teves_receivable_table.billing_date', '>=', $start_date)
                    ->where('teves_receivable_table.billing_date', '<=', $end_date)
              	->get([
					'teves_receivable_table.receivable_name',
					'teves_receivable_table.billing_date',
					'teves_receivable_table.control_number',
					'teves_receivable_table.or_number',
					'teves_receivable_table.ar_reference',
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'receivable_remaining_balance'
				]);	
		
		/*Client Information*/
		$client_data = ClientModel::find($client_idx, ['client_name','client_address','client_tin']);			
					
		$receivable_header = TevesBranchModel::find($company_header, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'STATEMENT OF ACCOUNT - Summary';
		  
        $pdf = PDF::loadView('printables.report_receivables_summary_soa_pdf', compact('title', 'receivable_data', 'user_data','receivable_header', 'client_data','start_date','end_date'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($client_data['client_name']."_RECEIVABLE_SOA.pdf");
		
		}
	}		
	
	
	/*Generated for receivable but not save*/
	public function generate_report_recievable (Request $request){

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

		$client_idx = $request->client_idx;
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		
		$company_header = $request->company_header;
		$all_branches = $request->all_branches;	
		
			$data = BillingTransactionModel::where('client_idx', $client_idx)
						->where(function ($r) use($all_branches,$company_header) {
							if ($all_branches=='NO') {
							   $r->where('teves_billing_table.branch_idx', $company_header);
							}
						})
						->whereBetween('teves_billing_table.order_date', ["$start_date", "$end_date"])
						->where('teves_billing_table.receivable_idx', '=', 0)
						->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_billing_table.branch_idx')
						->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
						->orderBy('teves_billing_table.order_date', 'asc')
						->get([
						'teves_billing_table.billing_id',
						'teves_billing_table.receivable_idx',
						'teves_billing_table.drivers_name',
						'teves_billing_table.plate_no',
						'teves_product_table.product_name',
						'teves_product_table.product_unit_measurement',
						'teves_billing_table.product_price',
						'teves_billing_table.order_quantity',					
						'teves_billing_table.order_total_amount',
						'teves_billing_table.order_po_number',
						'teves_billing_table.order_date',
						'teves_billing_table.order_time',
						'teves_billing_table.created_at',
						'teves_billing_table.created_by_user_idx',
						'teves_branch_table.branch_initial']);
				return DataTables::of($data)
				
				->addIndexColumn()				
                ->addColumn('action', function($row){
						
					if($row->receivable_idx==0){
						
						$startTimeStamp = strtotime($row->created_at);
						$endTimeStamp = strtotime(date('y-m-d'));
						$timeDiff = abs($endTimeStamp - $startTimeStamp);
						$numberDays = $timeDiff/86400;  // 86400 seconds in one day
						// and you might want to convert to integer
						$numberDays = intval($numberDays);
						
						$numberHours = $timeDiff/3600;  // 3600 1 hr
						// and you might want to convert to integer
						$numberHours = intval($numberHours);
						
						if(Session::get('UserType')=="Admin"){
							$actionBtn = '
							<div align="center" class="action_table_menu_site">
							<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
							<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editBill"></a>
							<a href="#" data-id="'.$row->billing_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteBill"></a>
							</div>';
						}
						else if(Session::get('UserType')=="Accounting_Staff"){
							
							if($numberDays>=1){
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
								</div>';
							}else{
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editBill"></a>
								<a href="#" data-id="'.$row->billing_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteBill"></a>
								</div>';
							}
							
						}
						else if(Session::get('UserType')=="Supervisor"){
							
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
								</div>';
			
						}
						else{
						
							if($numberHours>=1 || Session::get('loginID')!=$row->created_by_user_idx){
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
								</div>';
							}else{
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editBill"></a>
								<a href="#" data-id="'.$row->billing_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteBill"></a>
								</div>';
							}

						}

					}else{
						
						$actionBtn = '
						<div align="center" class="action_table_menu_site">
						<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_edit" id="viewBill"></a>
						</div>';
					
					}
                    return $actionBtn;
                })
				
				
				
				->rawColumns(['action'])
                ->make(true);	
		
	}	
	
	/*Generated for receivable after saved*/
	public function generate_report_recievable_after_saved(Request $request){

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
		$company_header = $request->company_header;
		$all_branches = $request->all_branches;			
		/*Using Raw Query*/
		
/*
		if($all_branches=="NO"){
			$raw_query = "select `teves_billing_table`.`billing_id`,`teves_billing_table`.`receivable_idx`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time`,'teves_billing_table.created_at', 'teves_billing_table.created_by_user_idx' from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` BETWEEN ? and ? and `teves_billing_table`.`receivable_idx` = ? and `teves_billing_table`.`branch_idx` = ? order by `teves_billing_table`.`order_date` asc";			
			$billing_data = DB::select("$raw_query", [$client_idx,$start_date,$end_date,$receivable_id,$company_header]);
		}
		else{
			$raw_query = "select `teves_billing_table`.`billing_id`,`teves_billing_table`.`receivable_idx`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time`,'teves_billing_table.created_at', 'teves_billing_table.created_by_user_idx' from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` BETWEEN ? and ? and `teves_billing_table`.`receivable_idx` = ? order by `teves_billing_table`.`order_date` asc";			
			$billing_data = DB::select("$raw_query", [$client_idx,$start_date,$end_date,$receivable_id]);
		}
*/		
		
		$billing_data = BillingTransactionModel::where('client_idx', $client_idx)
						->where(function ($r) use($all_branches,$company_header) {
							if ($all_branches=='NO') {
							   $r->where('teves_billing_table.branch_idx', $company_header);
							}
						})
						->whereBetween('teves_billing_table.order_date', ["$start_date", "$end_date"])
						->where('teves_billing_table.receivable_idx', '=', $receivable_id)
						->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_billing_table.branch_idx')
						->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
						->orderBy('teves_billing_table.order_date', 'asc')
						->get([
						'teves_billing_table.billing_id',
						'teves_billing_table.receivable_idx',
						'teves_billing_table.drivers_name',
						'teves_billing_table.plate_no',
						'teves_product_table.product_name',
						'teves_product_table.product_unit_measurement',
						'teves_billing_table.product_price',
						'teves_billing_table.order_quantity',					
						'teves_billing_table.order_total_amount',
						'teves_billing_table.order_po_number',
						'teves_billing_table.order_date',
						'teves_billing_table.order_time',
						'teves_billing_table.created_at',
						'teves_billing_table.created_by_user_idx',
						'teves_branch_table.branch_initial']);
						
				return DataTables::of($billing_data)
				
				->addIndexColumn()				
                ->addColumn('action', function($row){
						
					if($row->receivable_idx==0){
						
						if(Session::get('UserType')=="Admin"){
							$actionBtn = '
							<div align="center" class="action_table_menu_site">
							<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editBill"></a>
							<a href="#" data-id="'.$row->billing_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteBill"></a>
							</div>';
						}
						else{
						
						$startTimeStamp = strtotime($row->created_at);
						$endTimeStamp = strtotime(date('y-m-d'));
						$timeDiff = abs($endTimeStamp - $startTimeStamp);
						$numberDays = $timeDiff/86400;  // 86400 seconds in one day
						// and you might want to convert to integer
						$numberDays = intval($numberDays);
							
							if($numberDays>=3 || Session::get('loginID')!=$row->created_by_user_idx){
								$actionBtn = '';
							}else{
								$actionBtn = '
								<div align="center" class="action_table_menu_site">
								<a href="#" data-id="'.$row->billing_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editBill"></a>
								<a href="#" data-id="'.$row->billing_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteBill"></a>
								</div>';
							}

						}

					}else{
						
						$actionBtn = '';
					
					}
                    return $actionBtn;
                })
				
				
				
				->rawColumns(['action'])
                ->make(true);
		
	}	

	public function generate_receivable_covered_bill_pdf(Request $request){

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
		$less_per_liter = $request->less_per_liter;
		$company_header = $request->company_header;
		
		$withholding_tax_percentage = $request->withholding_tax_percentage;
		$net_value_percentage = $request->net_value_percentage;
		$vat_value_percentage = $request->vat_value_percentage;
		
		
		/*Using Raw Query*/
		//$raw_query = "select `teves_billing_table`.`billing_id`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time` from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` BETWEEN ? and ? and `teves_billing_table`.`receivable_idx` = ? order by `teves_billing_table`.`order_date` asc";			
		//$billing_data = DB::select("$raw_query", [$client_idx,$start_date,$end_date,$receivable_id]);
		$billing_data = BillingTransactionModel::where('client_idx', $client_idx)
						/*->where(function ($r) use($all_branches,$company_header) {
							if ($all_branches=='NO') {
							   $r->where('teves_billing_table.branch_idx', $company_header);
							}
						})*/
						->whereBetween('teves_billing_table.order_date', ["$start_date", "$end_date"])
						->where('teves_billing_table.receivable_idx', '=', $receivable_id)
						->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_billing_table.branch_idx')
						->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
						->orderBy('teves_billing_table.order_date', 'asc')
						->get([
						'teves_billing_table.billing_id',
						'teves_billing_table.receivable_idx',
						'teves_billing_table.drivers_name',
						'teves_billing_table.plate_no',
						'teves_product_table.product_name',
						'teves_product_table.product_unit_measurement',
						'teves_billing_table.product_price',
						'teves_billing_table.order_quantity',					
						'teves_billing_table.order_total_amount',
						'teves_billing_table.order_po_number',
						'teves_billing_table.order_date',
						'teves_billing_table.order_time',
						'teves_billing_table.created_at',
						'teves_billing_table.created_by_user_idx',
						'teves_branch_table.branch_initial']);
		
		
		/*Recievable Data*/
				
		$receivable_data = ReceivablesModel::find($receivable_id, ['payment_term','sales_order_idx','billing_date','ar_reference','or_number','control_number','company_header','created_by_user_id']);
		
		$receivable_header = TevesBranchModel::find($receivable_data['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', $receivable_data['created_by_user_id'])->first();
		
		/*Client Information*/
		$client_data = ClientModel::find($client_idx, ['client_name','client_address','client_tin']);
          
		$title = 'BILLING STATEMENT';
		  
        $pdf = PDF::loadView('printables.report_billing_receivable_pdf', compact('title', 'client_data', 'user_data', 'billing_data', 'start_date', 'end_date', 'less_per_liter', 'company_header', 'receivable_data','withholding_tax_percentage','net_value_percentage','vat_value_percentage','receivable_header'));
		
		/*Download Directly*/
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		$pdf->render();
		return $pdf->stream($client_data['client_name'].".pdf");
	}

	
	public function generate_report_pdf(Request $request){
		
		/*Set Memory Limit to Infinite*/
		ini_set("memory_limit",-1);
		/*Set Maximum Execution Time*/
		ini_set('max_execution_time', 0); //5 minutes
		//ini_set('max_execution_time', 0);
		//ini_set('memory_limit', '4000M');
		/*Form Validation*/
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

		$client_idx = $request->client_idx;
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		$less_per_liter = $request->less_per_liter;
		$company_header = $request->company_header;
		
		$withholding_tax_percentage = $request->withholding_tax_percentage;
		$net_value_percentage = $request->net_value_percentage;
		$vat_value_percentage = $request->vat_value_percentage;
		
		/*Using Raw Query*/
		//$raw_query = "select `teves_billing_table`.`billing_id`, `teves_billing_table`.`drivers_name`, `teves_billing_table`.`plate_no`, `teves_product_table`.`product_name`, `teves_product_table`.`product_unit_measurement`, `teves_billing_table`.`product_price`, `teves_billing_table`.`order_quantity`, `teves_billing_table`.`order_total_amount`, `teves_billing_table`.`order_po_number`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_date`, `teves_billing_table`.`order_time` from `teves_billing_table` USE INDEX (billing_index) inner join `teves_product_table` on `teves_product_table`.`product_id` = `teves_billing_table`.`product_idx` where `client_idx` = ? and `teves_billing_table`.`order_date` BETWEEN ? and ? order by `teves_billing_table`.`order_date` asc";			
		//$billing_data = DB::select("$raw_query", [$client_idx,$start_date,$end_date]);
		
		$all_branches = $request->all_branches;	
		
			$billing_data = BillingTransactionModel::where('client_idx', $client_idx)
						->where(function ($r) use($all_branches,$company_header) {
							if ($all_branches=='NO') {
							   $r->where('teves_billing_table.branch_idx', $company_header);
							}
						})
						->whereBetween('teves_billing_table.order_date', ["$start_date", "$end_date"])
						->where('teves_billing_table.receivable_idx', '=', 0)
						->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_billing_table.branch_idx')
						->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_billing_table.product_idx')
						->orderBy('teves_billing_table.order_date', 'asc')
						->get([
						'teves_billing_table.billing_id',
						'teves_billing_table.receivable_idx',
						'teves_billing_table.drivers_name',
						'teves_billing_table.plate_no',
						'teves_product_table.product_name',
						'teves_product_table.product_unit_measurement',
						'teves_billing_table.product_price',
						'teves_billing_table.order_quantity',					
						'teves_billing_table.order_total_amount',
						'teves_billing_table.order_po_number',
						'teves_billing_table.order_date',
						'teves_billing_table.order_time',
						'teves_billing_table.created_at',
						'teves_billing_table.created_by_user_idx',
						'teves_branch_table.branch_initial']);


		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		/*Recievable Data*/
		$receivable_id = $request->receivable_id;		
		$receivable_data = ReceivablesModel::find($receivable_id, ['payment_term']);
		
		$receivable_header = TevesBranchModel::find($request->company_header, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		/*Client Information*/
		$client_data = ClientModel::find($client_idx, ['client_name','client_address','client_tin']);
          
		$title = 'Billing History';
		  
        $pdf = PDF::loadView('printables.report_billing_pdf', compact('title', 'client_data', 'user_data', 'billing_data', 'start_date', 'end_date', 'less_per_liter', 'company_header', 'receivable_data','withholding_tax_percentage','net_value_percentage','vat_value_percentage','receivable_header'));
		
		/*Download Directly*/
        /*return $pdf->download($client_data['client_name'].".pdf");*/
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		$pdf->render();
		return $pdf->stream($client_data['client_name'].".pdf");
		//return view('printables.report_billing_pdf', compact('title', 'client_data', 'user_data', 'billing_data', 'start_date', 'end_date', 'less_per_liter', 'company_header', 'receivable_data','withholding_tax_percentage','net_value_percentage','vat_value_percentage','receivable_header'));
	}
	
	public function generate_receivable_pdf(Request $request){

		$request->validate([
			'receivable_id'      		=> 'required'
        ], 
        [
			'receivable_id.required' 	=> 'Please select a receivable_id'
        ]
		);

		$receivable_id = $request->receivable_id;
					
				$receivable_data = ReceivablesModel::where('receivable_id', $request->receivable_id)
				->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
              	->get([
					'teves_receivable_table.receivable_id',
					'teves_receivable_table.billing_date',
					'teves_client_table.client_name',
					'teves_client_table.client_address',
					'teves_receivable_table.control_number',
					'teves_client_table.client_tin',
					'teves_receivable_table.or_number',
					'teves_receivable_table.ar_reference',					
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'teves_receivable_table.company_header',
					'teves_receivable_table.created_by_user_id',
					'billing_period_start',
					'billing_period_end'
				]);
		
		$receivable_amount_amt =  number_format($receivable_data[0]['receivable_amount'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$receivable_amount_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;		
	
		$receivable_header = TevesBranchModel::find($receivable_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', $receivable_data[0]['created_by_user_id'])->first();
		
		$title = 'RECEIVABLE';
		  
        $pdf = PDF::loadView('printables.report_receivables_pdf', compact('title', 'receivable_data', 'user_data', 'amount_in_words', 'receivable_header'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($receivable_data[0]['client_name']."_RECEIVABLE.pdf");
		
		//return view('printables.report_receivables_pdf', compact('title', 'receivable_data', 'user_data', 'amount_in_words','receivable_header'));
		
	}
	
	public function generate_receivable_soa_pdf(Request $request){

		$request->validate([
			'receivable_id'      		=> 'required'
        ], 
        [
			'receivable_id.required' 	=> 'Please select a receivable_id'
        ]
		);

		$receivable_id = $request->receivable_id;
					
				$receivable_data = ReceivablesModel::where('receivable_id', $request->receivable_id)
				->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
              	->get([
					'teves_receivable_table.receivable_id',
					'teves_receivable_table.sales_order_idx',
					'teves_receivable_table.receivable_name',
					'teves_receivable_table.billing_date',
					'teves_client_table.client_name',
					'teves_client_table.client_address',
					'teves_receivable_table.control_number',
					'teves_client_table.client_tin',
					'teves_receivable_table.or_number',
					'teves_receivable_table.ar_reference',
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'teves_receivable_table.created_by_user_id',
					'billing_period_start',
					'billing_period_end',
					'company_header'
				]);
		
		$receivable_payment_data =  ReceivablesPaymentModel::where('teves_receivable_payment.receivable_idx', $request->receivable_id)
				->orderBy('receivable_payment_id', 'asc')
              	->get([
					'teves_receivable_payment.receivable_payment_id',
					'teves_receivable_payment.receivable_date_of_payment',
					'teves_receivable_payment.receivable_mode_of_payment',
					'teves_receivable_payment.receivable_reference',
					'teves_receivable_payment.receivable_payment_amount',
					]);
		
		$receivable_header = TevesBranchModel::find($receivable_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		$receivable_amount_amt =  number_format($receivable_data[0]['receivable_amount'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$receivable_amount_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;		
	
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', $receivable_data[0]['created_by_user_id'])->first();
		
		$title = 'STATEMENT OF ACCOUNT';
		  
        $pdf = PDF::loadView('printables.report_receivables_soa_pdf', compact('title', 'receivable_data', 'user_data', 'amount_in_words', 'receivable_payment_data','receivable_header'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($receivable_data[0]['client_name']."_RECEIVABLE_SOA.pdf");
	}

	public function generate_sales_order_pdf(Request $request){

		$request->validate([
			'sales_order_id'      		=> 'required'
        ], 
        [
			'sales_order_id.required' 	=> 'Please select a receivable_id'
        ]
		);

		$sales_order_id = $request->sales_order_id;
					
				$sales_order_data = SalesOrderModel::where('teves_sales_order_table.sales_order_id', $sales_order_id)
				->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_sales_order_table.sales_order_client_idx')
              	->get([
					'teves_sales_order_table.sales_order_id',
					'teves_sales_order_table.sales_order_date',
					'teves_sales_order_table.sales_order_client_idx',
					'teves_client_table.client_name',
					'teves_client_table.client_address',
					'teves_client_table.client_tin',
					'teves_sales_order_table.sales_order_control_number',
					'teves_sales_order_table.sales_order_dr_number',
					'teves_sales_order_table.sales_order_or_number',		
					'teves_sales_order_table.sales_order_po_number',
					'teves_sales_order_table.sales_order_charge_invoice',
					'teves_sales_order_table.sales_order_collection_receipt',					
					'teves_sales_order_table.sales_order_payment_term',
					'teves_sales_order_table.sales_order_delivered_to',
					'teves_sales_order_table.sales_order_delivered_to_address',
					'teves_sales_order_table.sales_order_delivery_method',
					'teves_sales_order_table.sales_order_gross_amount',
					'teves_sales_order_table.sales_order_net_amount',
					'teves_sales_order_table.sales_order_total_due',
					'teves_sales_order_table.sales_order_hauler',
					'teves_sales_order_table.sales_order_required_date',
					'teves_sales_order_table.sales_order_instructions',
					'teves_sales_order_table.sales_order_note',
					'teves_sales_order_table.sales_order_net_percentage',
					'teves_sales_order_table.sales_order_withholding_tax',
					'teves_sales_order_table.company_header',
					'teves_sales_order_table.created_by_user_id'
				]);
			
		$branch_header = TevesBranchModel::find($sales_order_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		$sales_order_amt =  number_format($sales_order_data[0]['sales_order_total_due'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$sales_order_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;
		
		$raw_query_sales_order_component = "SELECT `teves_sales_order_component_table`.`sales_order_component_id`,
						IFNULL(`teves_product_table`.`product_name`,`teves_sales_order_component_table`.item_description) as product_name,
						IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
						`teves_sales_order_component_table`.`product_idx`, `teves_sales_order_component_table`.`product_price`, `teves_sales_order_component_table`.`order_quantity`,
						`teves_sales_order_component_table`.`order_total_amount`
						from `teves_sales_order_component_table`  left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_sales_order_component_table`.`product_idx` where `sales_order_idx` = ?		  
						order by `sales_order_component_id` asc";	
						
		$sales_order_component = DB::select("$raw_query_sales_order_component", [ $sales_order_id]);	
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', $sales_order_data[0]['created_by_user_id'])->first();
		
		$title = 'SALES ORDER';
		  
        $pdf = PDF::loadView('printables.report_sales_order_pdf_v3', compact('title', 'sales_order_data', 'user_data', 'amount_in_words', 'sales_order_component','branch_header'));
		//return view('printables.report_sales_order_pdf', compact('title', 'sales_order_data', 'user_data', 'amount_in_words', 'sales_order_component'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($sales_order_data[0]['client_name']."_SALES_ORDER.pdf");
		
	}

	public function generate_sales_order_delivery_status_pdf(Request $request){

		$request->validate([
			'sales_order_id'      		=> 'required'
        ], 
        [
			'sales_order_id.required' 	=> 'Please select a receivable_id'
        ]
		);

		$sales_order_id = $request->sales_order_id;
					
				$sales_order_data = SalesOrderModel::where('teves_sales_order_table.sales_order_id', $sales_order_id)
				->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_sales_order_table.sales_order_client_idx')
              	->get([
					'teves_sales_order_table.sales_order_id',
					'teves_sales_order_table.sales_order_date',
					'teves_sales_order_table.sales_order_client_idx',
					'teves_client_table.client_name',
					'teves_client_table.client_address',
					'teves_client_table.client_tin',
					'teves_sales_order_table.sales_order_control_number',
					'teves_sales_order_table.sales_order_dr_number',
					'teves_sales_order_table.sales_order_or_number',		
					'teves_sales_order_table.sales_order_po_number',
					'teves_sales_order_table.sales_order_charge_invoice',
					'teves_sales_order_table.sales_order_collection_receipt',					
					'teves_sales_order_table.sales_order_payment_term',
					'teves_sales_order_table.sales_order_delivered_to',
					'teves_sales_order_table.sales_order_delivered_to_address',
					'teves_sales_order_table.sales_order_delivery_method',
					'teves_sales_order_table.sales_order_gross_amount',
					'teves_sales_order_table.sales_order_net_amount',
					'teves_sales_order_table.sales_order_total_due',
					'teves_sales_order_table.sales_order_hauler',
					'teves_sales_order_table.sales_order_required_date',
					'teves_sales_order_table.sales_order_instructions',
					'teves_sales_order_table.sales_order_note',
					'teves_sales_order_table.sales_order_net_percentage',
					'teves_sales_order_table.sales_order_withholding_tax',
					'teves_sales_order_table.company_header'
				]);
			
		$branch_header = TevesBranchModel::find($sales_order_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
	
	   \DB::statement("SET SQL_MODE=''");
	
		$raw_query_sales_order_delivery_total_component = "SELECT 
						`teves_sales_order_component_table`.`sales_order_component_id`,
						IFNULL(`teves_product_table`.`product_name`,`teves_sales_order_component_table`.item_description) as product_name,
						IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
						IFNULL((`teves_sales_order_component_table`.`order_quantity`),0) as total_order_quantity,
						IFNULL(sum(`teves_sales_order_delivery_details`.`sales_order_delivery_quantity`),0) as total_delivered_quantity
						from `teves_sales_order_component_table` left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_sales_order_component_table`.`product_idx`
						LEFT JOIN teves_sales_order_delivery_details ON `teves_sales_order_component_table`.`sales_order_component_id` = teves_sales_order_delivery_details.sales_order_component_idx
						 where `teves_sales_order_component_table`.`sales_order_idx` = ?		  
						group by `teves_sales_order_delivery_details`.`sales_order_component_idx` 
						order by `teves_sales_order_component_table`.`product_idx` asc";	
						
		$sales_order_delivery_total_component = DB::select("$raw_query_sales_order_delivery_total_component", [ $sales_order_id]);	
		
		$raw_query_sales_order_delivery_component = "SELECT 
						`teves_product_table`.`product_name`,
						IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
						`teves_sales_order_delivery_details`.`sales_order_delivery_date`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_quantity`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_withdrawal_reference`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_hauler_details`,
						`teves_sales_order_delivery_details`.`sales_order_delivery_remarks`
						from `teves_sales_order_component_table` left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_sales_order_component_table`.`product_idx`
						LEFT JOIN teves_sales_order_delivery_details ON `teves_sales_order_component_table`.`sales_order_component_id` = teves_sales_order_delivery_details.sales_order_component_idx
						 where `teves_sales_order_delivery_details`.`sales_order_idx` = ?	
						order by `teves_sales_order_delivery_details`.`sales_order_delivery_date` asc";	
						
		$sales_order_delivery_component = DB::select("$raw_query_sales_order_delivery_component", [ $sales_order_id]);	
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'SALES ORDER';
		  
        $pdf = PDF::loadView('printables.report_sales_order_delivery_status_pdf', compact('title', 'sales_order_data', 'user_data', 'sales_order_delivery_total_component', 'sales_order_delivery_component', 'branch_header'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($sales_order_data[0]['client_name']."_SALES_ORDER_DELIVERY_STATUS.pdf");
	}
	
	public function generate_purchase_order_pdf(Request $request){

		$purchase_order_id = $request->purchase_order_id;
				
				$purchase_order_data = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
				->join('teves_supplier_table', 'teves_supplier_table.supplier_id', '=', 'teves_purchase_order_table.purchase_order_supplier_idx')
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
						'teves_purchase_order_table.contact_number',
						'teves_purchase_order_table.purchase_destination',
						'teves_purchase_order_table.purchase_destination_address',
						'teves_purchase_order_table.purchase_date_of_departure',
						'teves_purchase_order_table.purchase_date_of_arrival',
						'teves_purchase_order_table.purchase_order_instructions',
						'teves_purchase_order_table.purchase_order_note',
						'teves_purchase_order_table.company_header',
						'teves_purchase_order_table.created_by_user_id'
				]);

		$purchase_order_amt =  number_format($purchase_order_data[0]['purchase_order_total_payable'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$purchase_order_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;
		
		$purchase_order_component = PurchaseOrderComponentModel::where('teves_purchase_order_component_table.purchase_order_idx', $purchase_order_id)	
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_purchase_order_component_table.product_idx')	
			->orderBy('purchase_order_component_id', 'asc')
              	->get([
					'teves_purchase_order_component_table.purchase_order_component_id',
					'teves_purchase_order_component_table.product_idx',
					'teves_product_table.product_name',
					'teves_product_table.product_unit_measurement',
					'teves_purchase_order_component_table.product_price',
					'teves_purchase_order_component_table.order_quantity',
					'teves_purchase_order_component_table.order_total_amount'
					]);

		$purchase_payment_component = PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $purchase_order_id)
			->orderBy('purchase_order_payment_details_id', 'asc')
              	->get([
					'teves_purchase_order_payment_details.purchase_order_bank',
					'teves_purchase_order_payment_details.purchase_order_date_of_payment',
					'teves_purchase_order_payment_details.purchase_order_reference_no',
					'teves_purchase_order_payment_details.purchase_order_payment_amount',
					]);

		$branch_header = TevesBranchModel::find($purchase_order_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		/*USER INFO*/
		//$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		$user_data = User::where('user_id', '=', $purchase_order_data[0]['created_by_user_id'])->first();
		$title = 'PURCHASE ORDER';
		  
        $pdf = PDF::loadView('printables.report_purchase_order_pdf_v3', compact('title', 'purchase_order_data', 'user_data', 'amount_in_words', 'purchase_order_component', 'purchase_payment_component','branch_header'));
		
		/*Download Directly*/
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($purchase_order_data[0]['supplier_name']."_PURCHASE_ORDER.pdf");
	
	}
	
	
	public function generate_purchase_order_payment_pdf(Request $request){

		$purchase_order_id = $request->purchase_order_id;
				
				$purchase_order_data = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
				->join('teves_supplier_table', 'teves_supplier_table.supplier_id', '=', 'teves_purchase_order_table.purchase_order_supplier_idx')
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
						'teves_purchase_order_table.contact_number',
						'teves_purchase_order_table.purchase_destination',
						'teves_purchase_order_table.purchase_destination_address',
						'teves_purchase_order_table.purchase_date_of_departure',
						'teves_purchase_order_table.purchase_date_of_arrival',
						'teves_purchase_order_table.purchase_order_instructions',
						'teves_purchase_order_table.purchase_order_note',
						'teves_purchase_order_table.company_header'
				]);

		$purchase_order_amt =  number_format($purchase_order_data[0]['purchase_order_total_payable'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$purchase_order_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;
		
		$purchase_order_component = PurchaseOrderComponentModel::where('teves_purchase_order_component_table.purchase_order_idx', $purchase_order_id)	
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_purchase_order_component_table.product_idx')	
			->orderBy('purchase_order_component_id', 'asc')
              	->get([
					'teves_purchase_order_component_table.purchase_order_component_id',
					'teves_purchase_order_component_table.product_idx',
					'teves_product_table.product_name',
					'teves_product_table.product_unit_measurement',
					'teves_purchase_order_component_table.product_price',
					'teves_purchase_order_component_table.order_quantity',
					'teves_purchase_order_component_table.order_total_amount'
					]);

		$purchase_payment_component = PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $purchase_order_id)
			->orderBy('purchase_order_payment_details_id', 'asc')
              	->get([
					'teves_purchase_order_payment_details.purchase_order_bank',
					'teves_purchase_order_payment_details.purchase_order_date_of_payment',
					'teves_purchase_order_payment_details.purchase_order_reference_no',
					'teves_purchase_order_payment_details.purchase_order_payment_amount',
					]);

		$branch_header = TevesBranchModel::find($purchase_order_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'PURCHASE ORDER';
		  
        $pdf = PDF::loadView('printables.report_purchase_order_payment_pdf', compact('title', 'purchase_order_data', 'user_data', 'amount_in_words', 'purchase_order_component', 'purchase_payment_component','branch_header'));
		
		/*Download Directly*/
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($purchase_order_data[0]['supplier_name']."_PURCHASE_ORDER_PAYMENT.pdf");
	
	}	
	
	
	public function generate_purchase_order_delivery_status_pdf(Request $request){

		$purchase_order_id = $request->purchase_order_id;
				
				$purchase_order_data = PurchaseOrderModel::where('teves_purchase_order_table.purchase_order_id', $request->purchase_order_id)
				->join('teves_supplier_table', 'teves_supplier_table.supplier_id', '=', 'teves_purchase_order_table.purchase_order_supplier_idx')
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
						'teves_purchase_order_table.contact_number',
						'teves_purchase_order_table.purchase_destination',
						'teves_purchase_order_table.purchase_destination_address',
						'teves_purchase_order_table.purchase_date_of_departure',
						'teves_purchase_order_table.purchase_date_of_arrival',
						'teves_purchase_order_table.purchase_order_instructions',
						'teves_purchase_order_table.purchase_order_note',
						'teves_purchase_order_table.company_header'
				]);

		$purchase_order_amt =  number_format($purchase_order_data[0]['purchase_order_total_payable'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$purchase_order_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;
		
		/*
		$purchase_order_component = PurchaseOrderComponentModel::where('teves_purchase_order_component_table.purchase_order_idx', $purchase_order_id)	
			->join('teves_product_table', 'teves_product_table.product_id', '=', 'teves_purchase_order_component_table.product_idx')	
			->orderBy('purchase_order_component_id', 'asc')
              	->get([
					'teves_purchase_order_component_table.purchase_order_component_id',
					'teves_purchase_order_component_table.product_idx',
					'teves_product_table.product_name',
					'teves_product_table.product_unit_measurement',
					'teves_purchase_order_component_table.product_price',
					'teves_purchase_order_component_table.order_quantity',
					'teves_purchase_order_component_table.order_total_amount'
					]);

		$purchase_payment_component = PurchaseOrderPaymentModel::where('teves_purchase_order_payment_details.purchase_order_idx', $purchase_order_id)
			->orderBy('purchase_order_payment_details_id', 'asc')
              	->get([
					'teves_purchase_order_payment_details.purchase_order_bank',
					'teves_purchase_order_payment_details.purchase_order_date_of_payment',
					'teves_purchase_order_payment_details.purchase_order_reference_no',
					'teves_purchase_order_payment_details.purchase_order_payment_amount',
					]);
		*/
		
		$branch_header = TevesBranchModel::find($purchase_order_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		
			   \DB::statement("SET SQL_MODE=''");
	
		$raw_query_purchase_order_delivery_total_component = "SELECT 
						`teves_purchase_order_component_table`.`purchase_order_component_id`,
						IFNULL(`teves_product_table`.`product_name`,`teves_purchase_order_component_table`.item_description) as product_name,
						IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
						IFNULL((`teves_purchase_order_component_table`.`order_quantity`),0) as total_order_quantity,
						IFNULL(sum(`teves_purchase_order_delivery_details`.`purchase_order_delivery_quantity`),0) as total_delivered_quantity
						from `teves_purchase_order_component_table` left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_purchase_order_component_table`.`product_idx`
						LEFT JOIN teves_purchase_order_delivery_details ON `teves_purchase_order_component_table`.`purchase_order_component_id` = teves_purchase_order_delivery_details.purchase_order_component_idx
						 where `teves_purchase_order_component_table`.`purchase_order_idx` = ?		  
						group by `teves_purchase_order_component_table`.`product_idx` 
						order by `teves_purchase_order_component_table`.`product_idx` asc";	
						
		$purchase_order_delivery_total_component = DB::select("$raw_query_purchase_order_delivery_total_component", [ $purchase_order_id]);	
		
		$raw_query_purchase_order_delivery_component = "SELECT 
						`teves_product_table`.`product_name`,
						IFNULL(`teves_product_table`.`product_unit_measurement`,'PC') as product_unit_measurement,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_date`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_quantity`,
						`teves_purchase_order_component_table`.`product_price` AS ordered_price,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_withdrawal_reference`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_hauler_details`,
						`teves_purchase_order_delivery_details`.`purchase_order_delivery_remarks`
						from `teves_purchase_order_component_table` left join `teves_product_table` on	 
						`teves_product_table`.`product_id` = `teves_purchase_order_component_table`.`product_idx`
						LEFT JOIN teves_purchase_order_delivery_details ON `teves_purchase_order_component_table`.`purchase_order_component_id` = teves_purchase_order_delivery_details.purchase_order_component_idx
						 where `teves_purchase_order_delivery_details`.`purchase_order_idx` = ?	
						order by `teves_purchase_order_delivery_details`.`purchase_order_delivery_date` asc";	
						
		$purchase_order_delivery_component = DB::select("$raw_query_purchase_order_delivery_component", [ $purchase_order_id]);	
		
		
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'PURCHASE ORDER';
		  
        $pdf = PDF::loadView('printables.report_purchase_order_delivery_status_pdf', compact('title', 'purchase_order_data', 'user_data', 'amount_in_words', 'purchase_order_delivery_component', 'purchase_order_delivery_total_component','branch_header'));
		
		/*Download Directly*/
		/*Stream for Saving/Printing*/
		$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($purchase_order_data[0]['supplier_name']."_PURCHASE_ORDER_DELIVERY_STATUS.pdf");
	
	}	
	
	public function generate_test_pdf(Request $request){
	
		$SOId = 1;
					
		$so_data = SOBillingTransactionModel::where('so_id', $SOId)
			->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_billing_so_table.client_idx')
            ->get([				
			'teves_client_table.client_name',
			'teves_client_table.client_id',
			'teves_billing_so_table.so_number',
			'teves_billing_so_table.order_date',
			'teves_billing_so_table.order_time',
			'teves_billing_so_table.drivers_name',
			'teves_billing_so_table.plate_no']);
			
			$so_header = TevesBranchModel::find(1, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		//->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_sales_order_table.sales_order_client_idx')
		
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'SALES ORDER';
		  
        $pdf = PDF::loadView('printables.print_so_pdf', compact('title', 'so_header','so_data'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		$pdf->set_paper(array(0, 0, 288, 432), 'portrait');
		//$customPaper = array(0,0,288,432);
		//$pdf->setPaper($customPaper);
		return $pdf->stream($so_data[0]['client_name']."_SO.pdf");
	}

	
	public function numberToWord($num = '')
    {
        $num    = ( string ) ( ( int ) $num );
        
        if( ( int ) ( $num ) && ctype_digit( $num ) )
        {
            $words  = array( );
             
            $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
             
            $list1  = array('','one','two','three','four','five','six','seven',
                'eight','nine','ten','eleven','twelve','thirteen','fourteen',
                'fifteen','sixteen','seventeen','eighteen','nineteen');
             
            $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
                'seventy','eighty','ninety','hundred');
             
            $list3  = array('','thousand','million','billion','trillion',
                'quadrillion','quintillion','sextillion','septillion',
                'octillion','nonillion','decillion','undecillion',
                'duodecillion','tredecillion','quattuordecillion',
                'quindecillion','sexdecillion','septendecillion',
                'octodecillion','novemdecillion','vigintillion');
             
            $num_length = strlen( $num );
            $levels = ( int ) ( ( $num_length + 2 ) / 3 );
            $max_length = $levels * 3;
            $num    = substr( '00'.$num , -$max_length );
            $num_levels = str_split( $num , 3 );
             
            foreach( $num_levels as $num_part )
            {
                $levels--;
                $hundreds   = ( int ) ( $num_part / 100 );
				//$hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
                $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : '' ) . ' ' : '' );
                $tens       = ( int ) ( $num_part % 100 );
                $singles    = '';
                 
                if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
            {
                $commas = $commas - 1;
            }
             
            $words  = implode( ', ' , $words );
             
            $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
            if( $commas )
            {
                //$words  = str_replace( ',' , ' and' , $words );
				$words  = str_replace( ',' , ' ' , $words );
            }
             
            return $words;
        }
        else if( ! ( ( int ) $num ) )
        {
            return 'Zero';
        }
        return '';
    }
}