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
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportSettingsController extends Controller
{
	
	public function generate_building_list(Request $request){
		
		$site_id 	= $request->site_id;
					
		$building_data = BuildingModel::
			where('meter_site_id', $site_id)
			->orderby('building_code')->get();			
					
		return response()->json($building_data);
		
	}
	
	public function generate_building_list1(Request $request){
		
		$site_id 	= $request->site_id;
					
		$building_data = BuildingModel::
			where('meter_site_id', $site_id)
			->orderby('building_code')->get();			
					
		return response()->json($building_data);
		
		
		return response()->json(array('success' => "Report Information Generated Succesfully", 'cashiers_report_id' => $last_transaction_id), 200);
		
	}
	
	public function generate_meter_list(Request $request){
		
		$site_id 	= $request->site_id;
		
		$raw_query_meter_info = "SELECT
						`meter_details`.`meter_id`,
						`meter_details`.`meter_name`,
						`meter_details`.`customer_name`,
						`meter_details`.`meter_multiplier`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						where `meter_details`.`site_idx` = ?";	
					   
		$meter_details_data = DB::select("$raw_query_meter_info", [$site_id]);

		/*$meter_details_data = MeterModel::
			where('site_idx', $site_id)
			->orderby('customer_name')->get();			*/
					
		return response()->json($meter_details_data);
		
	}

}