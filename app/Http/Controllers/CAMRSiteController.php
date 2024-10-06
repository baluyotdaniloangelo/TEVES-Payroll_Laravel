<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterModel;
use App\Models\BuildingModel;
use App\Models\CompanyModel;
use App\Models\DivisionModel;
use App\Models\MeterLocationModel;
use App\Models\ConfigurationFileModel;
use App\Models\GatewayModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
// use Spatie\Activitylog\Models\Activity;

class CAMRSiteController extends Controller
{
	
	/*Load Site Interface*/
	public function site(){
		
		if(Session::has('loginID')){
			
			$title = 'Site Management';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			if(Session::has('site_current_tab')){
				Session::pull('site_current_tab');
				//return redirect('/');
			}
			
			$division_data = DivisionModel::orderby('division_name')->get();
			$company_data = CompanyModel::orderby('company_name')->get();
			return view("amr.site", compact('data', 'title', 'division_data', 'company_data'));
		}
	} 

	/*Fetch Site List using Datatable*/
	public function getSiteForAdmin(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		if($user_data->user_access=='ALL'){
			
			$user_site_access_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
              		->get([
					'meter_site.site_id',
					'meter_site.last_log_update',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
			
		}else{
			
			$user_site_access_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->join('user_access_group', 'user_access_group.site_idx', '=', 'meter_site.site_id')
					->where('user_idx', $user_data->user_id)
              		->get([
					'meter_site.site_id',
					'meter_site.last_log_update',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
			
		}

		return DataTables::of($user_site_access_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_site">
					<a href="' . url('site_details/'.$row->site_id) .'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view"></a>
					<a href="#" data-id="'.$row->site_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editSite"></a>
					<a href="#" data-id="'.$row->site_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteSite"></a>
					</div>';
                    return $actionBtn;
					
                })
				
				->addColumn('status', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);
						
						/*SERVER DATETIME*/
						$_server_time=date('Y-m-d H:i:s');
						$server_time_current = strtotime($_server_time);
						
						$date1=date_create("$_server_time");
						$date2=date_create("$date_format");
								
						$diff					= date_diff($date1,$date2);
						$days_last_active 		= $diff->format("%a");
						
						if($last_log_update == "0000/00/00 00:00"){
							$statusBtn = '<div style="color:black; font-weight:bold; text-align:center;" title="No Meter Connected on the Gateway/Spare">No Data</div>';
						}
						else if($diff->format("%a")<=0){
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-check-fill btn_icon_table btn_icon_table_status_online" title="Last Status Update : '.$last_log_update.'"></a>';
						}else{
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-slash-fill btn_icon_table btn_icon_table_status_offline" title="Offline Since : '.$last_log_update.'"></a>';
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_site">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['status','action'])
                ->make(true);
		}
    }

	/*Fetch Site List using Datatable*/
	public function getSiteForUser(Request $request)
    {

		if ($request->ajax()) {
		
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		if($user_data->user_access=='ALL'){
			
			$user_site_access_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
              		->get([
					'meter_site.site_id',
					'meter_site.last_log_update',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
			
		}else{
			
			$user_site_access_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->join('user_access_group', 'user_access_group.site_idx', '=', 'meter_site.site_id')
					->where('user_idx', $user_data->user_id)
              		->get([
					'meter_site.site_id',
					'meter_site.last_log_update',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
			
		}
		
		return DataTables::of($user_site_access_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_site">
					<a href="' . url('site_details/'.$row->site_id) .'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view"></a>
					</div>';
                    return $actionBtn;
                })
				
				->addColumn('status', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);
						
						/*SERVER DATETIME*/
						$_server_time=date('Y-m-d H:i:s');
						$server_time_current = strtotime($_server_time);
						
						$date1=date_create("$_server_time");
						$date2=date_create("$date_format");
								
						$diff					= date_diff($date1,$date2);
						$days_last_active 		= $diff->format("%a");
						
						if($last_log_update == "0000/00/00 00:00"){
							$statusBtn = '<div style="color:black; font-weight:bold; text-align:center;" title="No Meter Connected on the Gateway/Spare">No Data</div>';
						}
						else if($diff->format("%a")<=0){
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-check-fill btn_icon_table btn_icon_table_status_online" title="Last Status Update : '.$last_log_update.'"></a>';
						}else{
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-slash-fill btn_icon_table btn_icon_table_status_offline" title="Offline Since : '.$last_log_update.'"></a>';
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_site">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['status','action'])
                ->make(true);
		
		}
		
    }

	/*Site Dashboard*/
	public function site_details_2($siteID){

		$title = 'Site Details';
		/*Get User Information*/
		if(Session::has('loginID')){
			$data = User::where('user_id', '=', Session::get('loginID'))
			->first();
			
			/*Get List of Configuration File*/
			$configuration_file_data = ConfigurationFileModel::orderby('config_file')->get();

		$site_current_tab = Session::get('site_current_tab');
		
		if($site_current_tab == 'status' || $site_current_tab == ''){
			
			$status_tab = " active show";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "true";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";
			
		}else if($site_current_tab == 'meter'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = " active show";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "true";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";	
			
		}else if($site_current_tab == 'building'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = " active show";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "true";
			$meterlocation_aria_selected = "false";
			
		}else if($site_current_tab == 'meterlocation'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = " active show";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "true";
			
		}else{
			
			$status_tab = "";
			$gateway_tab = " active show";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "true";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";
	
		}
		
		$raw_query_offline = "SELECT 
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ?
				) AS `total_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) < 0
				) AS `online_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) >= 1 OR (meter_rtu.last_log_update = '0000-00-00 00:00:00' AND `site_idx` = ?)
				) AS `offline_gateway`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ?
				) AS `total_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ? and DATEDIFF(NOW(), meter_details.last_log_update) < 0
				) AS `online_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ? and DATEDIFF(NOW(), meter_details.last_log_update) >= 1 OR (meter_details.last_log_update = '0000-00-00 00:00:00' AND `site_idx` = ?)
				) AS `offline_meter`";	
					   
		$offline_data = DB::select("$raw_query_offline", [$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID]);
		
		$SiteData = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->where('meter_site.site_id', $siteID)
              		->get([
					'meter_site.site_id',
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_division_table.division_name',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
		
		return view("amr.site_main_2",  compact('data','SiteData','title','status_tab','gateway_tab','meter_tab','meterlocation_tab','building_tab','status_aria_selected','gateway_aria_selected','meter_aria_selected','building_aria_selected','meterlocation_aria_selected','site_current_tab','configuration_file_data','offline_data'));
		
		}
		
	}

	/*Site Dashboard*/
	public function site_details($siteID){

		$title = 'Site Details';
		/*Get User Information*/
		if(Session::has('loginID')){
			$data = User::where('user_id', '=', Session::get('loginID'))
			->first();
			
			/*Get List of Configuration File*/
			$configuration_file_data = ConfigurationFileModel::orderby('config_file')->get();

		$site_current_tab = Session::get('site_current_tab');
		
		if($site_current_tab == 'status' || $site_current_tab == ''){
			
			$status_tab = " active show";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "true";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";
			
		}else if($site_current_tab == 'meter'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = " active show";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "true";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";	
			
		}else if($site_current_tab == 'building'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = " active show";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "true";
			$meterlocation_aria_selected = "false";
			
		}else if($site_current_tab == 'meterlocation'){
			
			$status_tab = "";
			$gateway_tab = "";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = " active show";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "false";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "true";
			
		}else{
			
			$status_tab = "";
			$gateway_tab = " active show";
			$meter_tab = "";
			$building_tab = "";
			$meterlocation_tab = "";
			
			$status_aria_selected = "false";
			$gateway_aria_selected = "true";
			$meter_aria_selected = "false";
			$building_aria_selected = "false";
			$meterlocation_aria_selected = "false";
	
		}
		
		$raw_query_offline = "SELECT 
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ?
				) AS `total_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) < 0
				) AS `online_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `site_idx` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) >= 1 OR (meter_rtu.last_log_update = '0000-00-00 00:00:00' AND `site_idx` = ?)
				) AS `offline_gateway`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ?
				) AS `total_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ? and DATEDIFF(NOW(), meter_details.last_log_update) < 0
				) AS `online_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `site_idx` = ? and DATEDIFF(NOW(), meter_details.last_log_update) >= 1 OR (meter_details.last_log_update = '0000-00-00 00:00:00' AND `site_idx` = ?)
				) AS `offline_meter`";	
					   
		$offline_data = DB::select("$raw_query_offline", [$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID]);
		
		$SiteData = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->where('meter_site.site_id', $siteID)
              		->get([
					'meter_site.site_id',
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_division_table.division_name',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
		
		return view("amr.site_main",  compact('data','SiteData','title','status_tab','gateway_tab','meter_tab','meterlocation_tab','building_tab','status_aria_selected','gateway_aria_selected','meter_aria_selected','building_aria_selected','meterlocation_aria_selected','site_current_tab','configuration_file_data','offline_data'));
		
		}
		
	}

	/*Fetch Site Information*/
	public function site_info(Request $request){

		$siteID = $request->siteID;
		
		$data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->where('meter_site.site_id', $siteID)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_id',
					'meter_company_table.company_name',
					'meter_division_table.division_id',
					'meter_division_table.division_code',
					'meter_division_table.division_name',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
		
		return response()->json($data);
		
	}

	/*Delete Site Information*/
	public function delete_site_confirmed(Request $request){

		$siteID = $request->siteID;
		SiteModel::find($siteID)->delete();
		
		/*Delete on Building Table*/	
		BuildingModel::where('site_idx', $siteID)
		->first()
		->delete();
		
		return 'Deleted';
		
	} 

	public function create_site_post(Request $request){

		$request->validate([
          'building_code'      		=> 'required|unique:meter_building_table,building_code',
		  'building_description'    => 'required|unique:meter_building_table,building_description',
		  'division_id'    			=> 'required',
		  'company_id' 				=> 'required'
        ], 
        [
			'building_code.required' 		=> 'Building Code is Required',
			'building_description.required' => 'Building Description is Required',
			'division_id.required' 			=> 'Division is Required',
			'company_id.required' 			=> 'Company is Required',
        ]
		);

			// $data = $request->all();
			#insert
			$site = new SiteModel();
			$site->division_idx 		= $request->division_id;
			$site->company_idx 			= $request->company_id;
			$site->site_code 			= $request->building_code;
			$site->created_by_user_idx 	= Session::get('loginID');
			$result = $site->save();
	
			$site_idx = $site->site_id;
			
			/*Save to Building Details*/
			$Bldg = new BuildingModel();
			$Bldg->site_idx 			= $site_idx;
			$Bldg->building_code 		= $request->building_code;
			$Bldg->building_description = $request->building_description;
			$Bldg->device_ip_range 		= $request->device_ip_range;
			$Bldg->ip_network 			= $request->ip_network;
			$Bldg->ip_netmask 			= $request->ip_netmask;
			$Bldg->ip_gateway 			= $request->ip_gateway;
			$Bldg->created_by_user_idx 	= Session::get('loginID');
			$Bldg->save();
			
			if($result){
				return response()->json(['success'=>'Building Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Building Information']);
			}
	}

	public function update_site_post(Request $request){
		
		$request->validate([
          'building_code'      		=> ['required',Rule::unique('meter_building_table')->where( 
										fn ($query) =>$query
											->where('building_code', $request->building_code)
											->where('building_id', '<>',  $request->building_id )											
										)],
		  'building_description'    => ['required',Rule::unique('meter_building_table')->where( 
										fn ($query) =>$query
											->where('building_description', $request->building_description)
											->where('building_id', '<>',  $request->building_id )
										)],
		  'division_id'    			=> 'required',
		  'company_id' 				=> 'required'
        ], 
        [
			'building_code.required' 		=> 'Building Code is Required',
			'building_description.required' => 'Building Description is Required',
			'division_id.required' 			=> 'Division is Required',
			'company_id.required' 			=> 'Company is Required',
        ]
		);
		
			// $data = $request->all();
			#update
					
			$site = new SiteModel();
			$site = SiteModel::find($request->SiteID);
			$site->division_idx 		= $request->division_id;
			$site->company_idx 			= $request->company_id;
			$site->site_code 			= $request->building_code;
			$site->modified_by_user_idx = Session::get('loginID');
			
			$result = $site->update();

			/*Update Building*/
			$Bldg = BuildingModel::where('site_idx', $request->SiteID)
				->firstOrFail()
				->update([
					'building_code' 		=> $request->building_code,
					'building_description' 	=> $request->building_description,
					'device_ip_range' 		=> $request->device_ip_range,
					'ip_network' 			=> $request->ip_network,
					'ip_netmask' 			=> $request->ip_netmask,
					'ip_gateway' 			=> $request->ip_gateway,
					'modified_by_user_idx' 	=> Session::get('loginID'),
				]);

			/*Update Meter Site Code*/
			$meter_update = MeterModel::where('site_idx', $request->SiteID)
				->update([
					'site_code' => $request->building_code
				]);
			
			/*Update Gateway Site Code*/
			$gateway_update = GatewayModel::where('site_idx', $request->SiteID)
				->update([
					'site_code' => $request->building_code,
					'update_rtu_location' => 1
				]);
				
			if($result){
				return response()->json(['success'=>'Building Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Building Information']);
			}
	}

	public function save_site_tab(Request $request){ 

        $tab = $request->tab;		
		$request->session()->put('site_current_tab', $tab);
				
    }
	
}

