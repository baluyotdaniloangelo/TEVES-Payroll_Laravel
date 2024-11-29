<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\BranchModel;
use App\Models\DepartmentModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class EmployeeManagementController extends Controller
{
	
	/*Load Employee Interface*/
	public function employee(){
		
		if(Session::has('loginID')){
			
			$title = 'Employee List';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			if(Session::has('site_current_tab')){
				Session::pull('site_current_tab');
			}
			
			$department_data = DepartmentModel::orderby('department_name')->get();
			$branch_data = BranchModel::orderby('branch_name')->get();
			return view("payroll.Employee", compact('data', 'title'));
		}
	} 

	/*Fetch Employee List using Datatable*/
	public function getEmployeeList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
			
		$employee_list_data = EmployeeModel::join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
              		->get([
					'teves_employee_table.employee_id',
					'teves_employee_table.employee_last_name',
					'teves_employee_table.employee_first_name',
					'teves_employee_table.employee_middle_name',
					'teves_employee_table.employee_extension_name',
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_position',
					'teves_employee_table.employee_status',
					'teves_branch_table.branch_name',
					'teves_department_table.department_name']);

		return DataTables::of($employee_list_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
										
					$actionBtn = '
					
					<div class="dropdown dropdown-action">
													<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
													<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
														<a class="dropdown-item" href="' . url('site_details/'.$row->employee_id) .'" data-bs-toggle="modal" data-bs-target="#edit_employee"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
														<a class="dropdown-item" href="' . url('site_details/'.$row->employee_id) .'" data-bs-toggle="modal" data-bs-target="#delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
													</div>
												</div>
					
					';
                    return $actionBtn;
					
                })
					
										
				
				
				->rawColumns(['action'])
                ->make(true);
		}
    }

	/*Fetch Site List using Datatable*/
	public function getSiteForUser(Request $request)
    {

		if ($request->ajax()) {
		
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		if($user_data->user_access=='ALL'){
			
			$employee_list_data = EmployeeModel::join('teves_employee_table', 'teves_employee_table.employee_id', '=', 'teves_employee_table.employee_id')
					->join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
              		->get([
					'teves_employee_table.employee_id',
					'teves_employee_table.last_log_update',
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_last_name',
					'teves_branch_table.branch_name',
					'teves_department_table.division_code',
					'teves_employee_table.device_ip_range',
					'teves_employee_table.ip_network',
					'teves_employee_table.ip_netmask',
					'teves_employee_table.ip_gateway',
					'teves_employee_table.cut_off']);
			
		}else{
			
			$employee_list_data = EmployeeModel::join('teves_employee_table', 'teves_employee_table.employee_id', '=', 'teves_employee_table.employee_id')
					->join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
					->join('user_access_group', 'user_access_group.employee_id', '=', 'teves_employee_table.employee_id')
					->where('user_idx', $user_data->user_id)
              		->get([
					'teves_employee_table.employee_id',
					'teves_employee_table.last_log_update',
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_last_name',
					'teves_branch_table.branch_name',
					'teves_department_table.division_code',
					'teves_employee_table.device_ip_range',
					'teves_employee_table.ip_network',
					'teves_employee_table.ip_netmask',
					'teves_employee_table.ip_gateway',
					'teves_employee_table.cut_off']);
			
		}
		
		return DataTables::of($employee_list_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_site">
					<a href="' . url('site_details/'.$row->employee_id) .'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view"></a>
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
				SELECT COUNT(*) from `meter_rtu` where `employee_id` = ?
				) AS `total_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `employee_id` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) < 0
				) AS `online_gateway`,
				(
				SELECT COUNT(*) from `meter_rtu` where `employee_id` = ? and DATEDIFF(NOW(), meter_rtu.last_log_update) >= 1 OR (meter_rtu.last_log_update = '0000-00-00 00:00:00' AND `employee_id` = ?)
				) AS `offline_gateway`,
				(
				SELECT COUNT(*) from `meter_details` where `employee_id` = ?
				) AS `total_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `employee_id` = ? and DATEDIFF(NOW(), meter_details.last_log_update) < 0
				) AS `online_meter`,
				(
				SELECT COUNT(*) from `meter_details` where `employee_id` = ? and DATEDIFF(NOW(), meter_details.last_log_update) >= 1 OR (meter_details.last_log_update = '0000-00-00 00:00:00' AND `employee_id` = ?)
				) AS `offline_meter`";	
					   
		$offline_data = DB::select("$raw_query_offline", [$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID,$siteID]);
		
		$SiteData = EmployeeModel::join('teves_employee_table', 'teves_employee_table.employee_id', '=', 'teves_employee_table.employee_id')
					->join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
					->where('teves_employee_table.employee_id', $siteID)
              		->get([
					'teves_employee_table.employee_id',
					'teves_employee_table.building_id',
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_last_name',
					'teves_branch_table.branch_name',
					'teves_department_table.division_code',
					'teves_department_table.department_name',
					'teves_employee_table.device_ip_range',
					'teves_employee_table.ip_network',
					'teves_employee_table.ip_netmask',
					'teves_employee_table.ip_gateway',
					'teves_employee_table.cut_off']);
		
		return view("amr.site_main_2",  compact('data','SiteData','title','status_tab','gateway_tab','meter_tab','meterlocation_tab','building_tab','status_aria_selected','gateway_aria_selected','meter_aria_selected','building_aria_selected','meterlocation_aria_selected','site_current_tab','configuration_file_data','offline_data'));
		
		}
		
	}


	/*Fetch Site Information*/
	public function site_info(Request $request){

		$siteID = $request->siteID;
		
		$data = EmployeeModel::join('teves_employee_table', 'teves_employee_table.employee_id', '=', 'teves_employee_table.employee_id')
					->join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
					->where('teves_employee_table.employee_id', $siteID)
              		->get([
					'teves_employee_table.building_id',
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_last_name',
					'teves_branch_table.branch_id',
					'teves_branch_table.branch_name',
					'teves_department_table.department_id',
					'teves_department_table.division_code',
					'teves_department_table.department_name',
					'teves_employee_table.device_ip_range',
					'teves_employee_table.ip_network',
					'teves_employee_table.ip_netmask',
					'teves_employee_table.ip_gateway',
					'teves_employee_table.cut_off']);
		
		return response()->json($data);
		
	}

	/*Delete Site Information*/
	public function delete_site_confirmed(Request $request){

		$siteID = $request->siteID;
		EmployeeModel::find($siteID)->delete();
		
		/*Delete on Building Table*/	
		BuildingModel::where('employee_id', $siteID)
		->first()
		->delete();
		
		return 'Deleted';
		
	} 

	public function submit_employee_information(Request $request){


/*'employee_number',
								'employee_last_name',
								'employee_first_name',
								'employee_middle_name',
								'employee_extension_name',
								'employee_birthday',
								'employee_position',
								'employee_picture',
								'employee_phone',
								'employee_email',
								'branch_idx',
								'department_idx',
								'time_in',
								'break_time',
								'time_out',
								'restday_monday',
								'restday_tuesday',
								'restday_wednesday',
								'restday_thursday',
								'restday_friday',
								'restday_saturday',
								'restday_sunday',
								*/


		$request->validate([
          'employee_number'      		=> 'required|unique:teves_employee_table,employee_number',
		  'employee_last_name'    		=> 'required',
		  'employee_first_name'    		=> 'required',
		  'employee_birthday'    		=> 'required',
		  'branch_idx'    				=> 'required',
		  'department_idx' 				=> 'required',
		  'time_in'    					=> 'required',
		  'break_time'    				=> 'required',
		  'time_out'    				=> 'required',
        ], 
       
		);

			// $data = $request->all();
			#insert
			$site = new EmployeeModel();
			$site->department_idx 		= $request->department_id;
			$site->branch_idx 			= $request->branch_id;
			$site->site_code 			= $request->employee_number;
			$site->created_by_user_idx 	= Session::get('loginID');
			$result = $site->save();
	
			$employee_id = $site->employee_id;
			
			/*Save to Building Details*/
			$Bldg = new BuildingModel();
			$Bldg->employee_id 			= $employee_id;
			$Bldg->employee_number 		= $request->employee_number;
			$Bldg->employee_last_name = $request->employee_last_name;
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
          'employee_number'      		=> ['required',Rule::unique('teves_employee_table')->where( 
										fn ($query) =>$query
											->where('employee_number', $request->employee_number)
											->where('building_id', '<>',  $request->building_id )											
										)],
		  'employee_last_name'    => ['required',Rule::unique('teves_employee_table')->where( 
										fn ($query) =>$query
											->where('employee_last_name', $request->employee_last_name)
											->where('building_id', '<>',  $request->building_id )
										)],
		  'department_id'    			=> 'required',
		  'branch_id' 				=> 'required'
        ], 
        [
			'employee_number.required' 		=> 'Building Code is Required',
			'employee_last_name.required' => 'Building Description is Required',
			'department_id.required' 			=> 'Division is Required',
			'branch_id.required' 			=> 'Company is Required',
        ]
		);
		
			// $data = $request->all();
			#update
					
			$site = new EmployeeModel();
			$site = EmployeeModel::find($request->SiteID);
			$site->department_idx 		= $request->department_id;
			$site->branch_idx 			= $request->branch_id;
			$site->site_code 			= $request->employee_number;
			$site->modified_by_user_idx = Session::get('loginID');
			
			$result = $site->update();

			/*Update Building*/
			$Bldg = BuildingModel::where('employee_id', $request->SiteID)
				->firstOrFail()
				->update([
					'employee_number' 		=> $request->employee_number,
					'employee_last_name' 	=> $request->employee_last_name,
					'device_ip_range' 		=> $request->device_ip_range,
					'ip_network' 			=> $request->ip_network,
					'ip_netmask' 			=> $request->ip_netmask,
					'ip_gateway' 			=> $request->ip_gateway,
					'modified_by_user_idx' 	=> Session::get('loginID'),
				]);

			/*Update Meter Site Code*/
			$meter_update = MeterModel::where('employee_id', $request->SiteID)
				->update([
					'site_code' => $request->employee_number
				]);
			
			/*Update Gateway Site Code*/
			$gateway_update = GatewayModel::where('employee_id', $request->SiteID)
				->update([
					'site_code' => $request->employee_number,
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

