<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterModel;
use App\Models\BuildingModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
/*Excel*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReportController extends Controller
{
	
	/*Load Billing History Report Interface*/
	public function sap_report(){
		
		
		if(Session::has('loginID')){
			
			$title = 'SAP Report';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			if($data->user_type='Admin'){
				
				$site_data = SiteModel::all();/*for Administrator*/
			
			}else{
			
				$raw_query_user_access = "SELECT 
				meter_site.id as id,
				meter_site.site_code,
				meter_site.site_name,
				meter_site.business_entity
				FROM meter_site
				JOIN user_access_group ON meter_site.id=user_access_group.meter_site_id
				and user_access_group.user_id = ?";	
							   
				$site_data = DB::select("$raw_query_user_access", [$data->user_id]);
		
			}
					
			return view("amr.sap_report", compact('data','title','site_data'));
		
		}
		
	}  	
	
	public function generate_building_list(Request $request){
		
		$site_id 	= $request->site_id;
					
		$building_data = BuildingModel::where('meter_site_id', $site_id)->orderby('building_code')->get();			
					
		return response()->json($building_data);
		
	}
	
	public function generate_sap_report(Request $request){

		$request->validate([
          'site_id'      		=> 'required',
		  'meter_role'      		=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site',
			'meter_role.required' 	=> 'Please select a Meter Role',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		$building_id = $request->building_id;
		$_start_date = $request->start_date;
		$_end_date 	= $request->end_date;
		
	/*CURRENT DATE*/
    $dt_from = ("$_end_date 10:00:00");
	$dt_from_w_time = "$dt_from";
	$date1=date_create($dt_from_w_time);
	date_add($date1,date_interval_create_from_date_string("14 minutes"));
	$start_date = date_format($date1,"Y-m-d H:i:59");
	
	/*Prev Month Base of date*/
	$prev_month = date('Y-m-d', strtotime($_end_date. ' - 1 months'));
	$prev_month_date = "$prev_month 10:14:59";

	/*Prev Month Base of date*/
	$past_two_months = date('Y-m-d', strtotime($_end_date. ' - 2 months'));
	$past_two_months_date = "$past_two_months 10:14:59";

		/*Query Select using Raw*/
		$data = MeterModel::selectRaw("meter_name,`customer_name`,`building`,`meter_site_name`,`meter_type`,`meter_multiplier`")	
		->selectRaw("(select `wh_total` from `meter_data` where `location` = 'ROBINSONS' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` where `location` = 'ROBINSONS' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")		
		->selectRaw("(select `wh_total` from `meter_data` where `location` = 'ROBINSONS' and `meter_id` = meter_name and `datetime` <= '$prev_month_date' order by `datetime` desc limit 0, 1 ) as `prev_reading`")		
		->selectRaw("(select `wh_total` from `meter_data` where `location` = 'ROBINSONS' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading`")	
		->selectRaw("(select `datetime` from `meter_data` where `location` = 'ROBINSONS' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading_datetime`")
		->where('meter_site_id', $site_id)
		->where("meter_role",'=', $meter_role)
		->where("meter_status", 'Active')
		->where(function ($q) use($building_id) {
			if ($building_id) {
			   $q->where('building_id', $building_id);
			}
			})
       ->get();
		
		return response()->json($data);
		
	}	
	
	/*Generated for receivable but not save*/
	public function generate_report_recievable(Request $request){

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
		
		$data = BillingTransactionModel::where('client_idx', $client_idx)
					->where('teves_billing_table.order_date', '>=', $start_date)
                    ->where('teves_billing_table.order_date', '<=', $end_date)
					->where('teves_billing_table.receivable_idx', '=', 0)
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
					'teves_billing_table.order_date',
					'teves_billing_table.order_time']);
		
		return response()->json($data);
		
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
		
		$data = BillingTransactionModel::where('client_idx', $client_idx)
					->where('teves_billing_table.order_date', '>=', $start_date)
                    ->where('teves_billing_table.order_date', '<=', $end_date)
					->where('teves_billing_table.receivable_idx', '=', $receivable_id)
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
					'teves_billing_table.order_date',
					'teves_billing_table.order_time']);
		
		return response()->json($data);
		
	}	

}