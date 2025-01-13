<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class EmployeeRegularLogsController extends Controller
{

	/*Fetch Employee List using Datatable*/
	public function getEmployeeRegularLogsList(Request $request)
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

	/*Fetch EmployeeDetails Information*/
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
					'teves_employee_table.employee_status',
					'teves_employee_table.employee_picture',
					'teves_employee_table.employee_phone',
					'teves_employee_table.employee_email',
					'teves_branch_table.branch_id',
					'teves_branch_table.branch_name',
					'teves_department_table.department_id',
					'teves_department_table.department_name',
					'teves_employee_table.employee_status',
					'teves_employee_table.time_in',
					'teves_employee_table.break_time_in',
					'teves_employee_table.break_time_out',
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

	/*Delete Employee Information*/
	public function delete_employee_confirmed(Request $request){

		$employeeID = $request->employeeID;
		EmployeeModel::find($employeeID)->delete();
		
		return 'Deleted';
		
	} 

	public function submit_employee_information(Request $request){
		
			$employee_id = $request->employee_id;
			$request->validate([
			  'employee_number'      		=> ['required',Rule::unique('teves_employee_table')->where( 
												fn ($query) =>$query
													->where('employee_number', $request->employee_number)
													->where('employee_id', '<>',  $employee_id )											
												)],
			  'employee_last_name'    		=> 'required',
			  'employee_first_name'    		=> 'required',
			  'employee_birthday'    		=> 'required',
			  'branch_idx'    				=> 'required',
			  'department_idx' 				=> 'required',
			  'time_in'    					=> 'required',
			  'break_time_in'    			=> 'required',
			  'break_time_out'    			=> 'required',
			  'time_out'    				=> 'required',
			]);
			
			if($employee_id==0){
			
				$EmployeeDetails = new EmployeeModel();
				
				$EmployeeDetails->employee_number 			= $request->employee_number;
				
				$EmployeeDetails->employee_last_name 		= $request->employee_last_name;
				$EmployeeDetails->employee_first_name 		= $request->employee_first_name;
				$EmployeeDetails->employee_middle_name 		= $request->employee_middle_name;
				$EmployeeDetails->employee_extension_name 	= $request->employee_extension_name;
				
				$EmployeeDetails->employee_birthday 		= $request->employee_birthday;
				$EmployeeDetails->employee_position 		= $request->employee_position;
				$EmployeeDetails->employee_status	 		= $request->employee_status;
				//$EmployeeDetails->employee_picture 		= $request->employee_picture;
				$EmployeeDetails->employee_phone 			= $request->employee_phone;
				$EmployeeDetails->employee_email 			= $request->employee_email;
				
				$EmployeeDetails->branch_idx 				= $request->branch_idx;
				$EmployeeDetails->department_idx 			= $request->department_idx;
				
				$EmployeeDetails->time_in 					= $request->time_in;
				$EmployeeDetails->break_time_in 			= $request->break_time_in;
				$EmployeeDetails->break_time_out 			= $request->break_time_out;
				$EmployeeDetails->time_out 					= $request->time_out;
				
				$EmployeeDetails->restday_monday 			= $request->restday_monday;
				$EmployeeDetails->restday_tuesday 			= $request->restday_tuesday;
				$EmployeeDetails->restday_wednesday 		= $request->restday_wednesday;
				$EmployeeDetails->restday_thursday 			= $request->restday_thursday;
				$EmployeeDetails->restday_friday 			= $request->restday_friday;
				$EmployeeDetails->restday_saturday 			= $request->restday_saturday;
				$EmployeeDetails->restday_sunday 			= $request->restday_sunday;
				
				$EmployeeDetails->created_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeDetails->save();
				
				if($result){
					return response()->json(['success'=>'Created']);
				}
				else{
					return response()->json(['success'=>'Error on Insert Employee Information']);
				}
			
			}else{
			
				$EmployeeDetails = new EmployeeModel();
				
				$EmployeeDetails = EmployeeModel::find($employee_id);
				
				$EmployeeDetails->employee_number 			= $request->employee_number;
				
				$EmployeeDetails->employee_last_name 		= $request->employee_last_name;
				$EmployeeDetails->employee_first_name 		= $request->employee_first_name;
				$EmployeeDetails->employee_middle_name 		= $request->employee_middle_name;
				$EmployeeDetails->employee_extension_name 	= $request->employee_extension_name;
				
				$EmployeeDetails->employee_birthday 		= $request->employee_birthday;
				$EmployeeDetails->employee_position 		= $request->employee_position;
				$EmployeeDetails->employee_status	 		= $request->employee_status;
				//$EmployeeDetails->employee_picture 		= $request->employee_picture;
				$EmployeeDetails->employee_phone 			= $request->employee_phone;
				$EmployeeDetails->employee_email 			= $request->employee_email;
				
				$EmployeeDetails->branch_idx 				= $request->branch_idx;
				$EmployeeDetails->department_idx 			= $request->department_idx;
				
				$EmployeeDetails->time_in 					= $request->time_in;
				$EmployeeDetails->break_time_in 			= $request->break_time_in;
				$EmployeeDetails->break_time_out 			= $request->break_time_out;
				$EmployeeDetails->time_out 					= $request->time_out;
				
				$EmployeeDetails->restday_monday 			= $request->restday_monday;
				$EmployeeDetails->restday_tuesday 			= $request->restday_tuesday;
				$EmployeeDetails->restday_wednesday 		= $request->restday_wednesday;
				$EmployeeDetails->restday_thursday 			= $request->restday_thursday;
				$EmployeeDetails->restday_friday 			= $request->restday_friday;
				$EmployeeDetails->restday_saturday 			= $request->restday_saturday;
				$EmployeeDetails->restday_sunday 			= $request->restday_sunday;
				
				$EmployeeDetails->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeDetails->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Update Employee Information']);
				}
			
			}
			
			
	}


}

