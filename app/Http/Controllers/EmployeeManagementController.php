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
								<a class="dropdown-item" href="#" data-id="'.$row->employee_id.'" id="edit_employee"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->employee_id.'" id="delete_employee"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
					
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }

	/*Fetch Site Information*/
	public function employee_info(Request $request){

		$employeeID = $request->employeeID;
		
		$data = EmployeeModel::join('teves_department_table', 'teves_department_table.department_id', '=', 'teves_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_employee_table.branch_idx')
					->where('teves_employee_table.employee_id', $employeeID)
              		->get([
					'teves_employee_table.employee_number',
					'teves_employee_table.employee_last_name',
					'teves_employee_table.employee_first_name',
					'teves_employee_table.employee_middle_name',
					'teves_employee_table.employee_extension_name',
					'teves_employee_table.employee_birthday',
					'teves_employee_table.employee_position',
					'teves_employee_table.employee_picture',
					'teves_employee_table.employee_phone',
					'teves_employee_table.employee_email',
					'teves_branch_table.branch_id',
					'teves_branch_table.branch_name',
					'teves_department_table.department_id',
					'teves_department_table.department_name',
					'teves_employee_table.employee_status',
					'teves_employee_table.time_in',
					'teves_employee_table.break_time',
					'teves_employee_table.time_out',
					'teves_employee_table.restday_monday',
					'teves_employee_table.restday_tuesday',
					'teves_employee_table.restday_wednesday',
					'teves_employee_table.restday_thursday',
					'teves_employee_table.restday_friday',
					'teves_employee_table.restday_saturday',
					'teves_employee_table.restday_sunday',
					]);
		
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

