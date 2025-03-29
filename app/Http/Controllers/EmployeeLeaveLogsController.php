<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\EmployeeLeaveLogsModel;
use App\Models\HolidayModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeLeaveLogsController extends Controller
{
	
	/*Load Employee Interface*/
	public function employee_attendance_logs(){
		
		if(Session::has('loginID')){
			
			$title = 'Employee Logs';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$active_link = 'employee_attendance_logs';
			
			return view("payroll.employee_attendance_logs", compact('data', 'title', 'active_link'));
		}
	} 

	/*Fetch Employee Regular Log List using Datatable*/
	public function getEmployeeLeaveLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLeaveLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_leave_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		//->where('teves_payroll_employee_leave_logs.reason_of_leave', 'Regular')
		->select([
			'teves_payroll_employee_leave_logs.*',
			'teves_branch_table.branch_name',
			'teves_payroll_department_table.department_name',
			'teves_payroll_employee_table.employee_number',
			DB::raw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,''), ' ') as employee_full_name"),
		]);
	
		return DataTables::of($regular_logs)
				->addIndexColumn()
                ->addColumn('action', function($row){				
					$actionBtn = '
					<div class="dropdown dropdown-action">
						<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								<a class="dropdown-item" href="#" data-id="'.$row->employee_leave_logs_id.'" id="edit_employee_leave_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->employee_leave_logs_id.'" id="delete_employee_leave_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }
	

	/*Fetch EmployeeDetails Information*/
	public function employee_leave_logs_info(Request $request){

		$employeeLeavelogsID = $request->employeeLeavelogsID;

		$data = EmployeeLeaveLogsModel::join('teves_payroll_employee_table', 'teves_payroll_employee_leave_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_leave_logs.employee_leave_logs_id', $employeeLeavelogsID)
              		->get([
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_last_name',
					'teves_payroll_employee_table.employee_first_name',
					'teves_payroll_employee_table.employee_middle_name',
					'teves_payroll_employee_table.employee_extension_name',
					'teves_branch_table.branch_id',
					'teves_branch_table.branch_name',
					'teves_branch_table.branch_code',
					'teves_payroll_department_table.department_id',
					'teves_payroll_department_table.department_name',
					'teves_payroll_employee_leave_logs.date_of_leave',
					'teves_payroll_employee_leave_logs.leave_amount',
					'teves_payroll_employee_leave_logs.reason_of_leave'
					]);
		return response()->json($data);
		
	}

	/*Delete Employee Information*/
	public function delete_employee_leave_log_confirmed(Request $request){

		$employeelogID = $request->employeelogID;
		EmployeeLeaveLogsModel::find($employeelogID)->delete();
		
		return 'Deleted';
		
	} 

	public function submit_employee_leave_logs_information(Request $request){
			
			$branch_idx						= $request->branch_idx;
			$employee_idx 					= $request->employee_idx;
			$employee_leave_logs_id 		= $request->employee_leave_logs_id;
			$date_of_leave 					= $request->date_of_leave;
			$reason_of_leave 				= $request->reason_of_leave;
			
			
			if($branch_idx!=0 && $employee_idx!=0){
			/*Query Employee Information*/
			$employee_data = EmployeeModel::where('teves_payroll_employee_table.employee_id', $employee_idx)
						->get([
						'teves_payroll_employee_table.branch_idx',
						'teves_payroll_employee_table.department_idx',
						'teves_payroll_employee_table.employee_rate'
						]);
			
			$branch_idx 						= $employee_data[0]->branch_idx;
			$department_idx 					= $employee_data[0]->department_idx;
			
			
			$employee_current_rate = $employee_data[0]->employee_rate;
			
			$leave_amount = 8 * $employee_current_rate;

			$request->validate([
				  'branch_idx'				=> ['required'],
				  'employee_idx'      		=> ['required',Rule::unique('teves_payroll_employee_leave_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('date_of_leave', $date_of_leave)
														->where('reason_of_leave', '<>',  $reason_of_leave )
														->where(function ($q) use($employee_leave_logs_id) {
															if ($employee_leave_logs_id!=0) {
															   $q->where('teves_payroll_employee_leave_logs.employee_leave_logs_id', '<>', $employee_leave_logs_id);
															}
														})
														/*->where('employee_leave_logs_id', '<>',  $employee_leave_logs_id )	*/										
													)],
				  'date_of_leave'    		=> ['required',Rule::unique('teves_payroll_employee_leave_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('date_of_leave', $date_of_leave)
														->where('reason_of_leave', '<>',  $reason_of_leave )
														->where(function ($q) use($employee_leave_logs_id) {
															if ($employee_leave_logs_id!=0) {
															   $q->where('teves_payroll_employee_leave_logs.employee_leave_logs_id', '<>', $employee_leave_logs_id);
															}
														})
														/*->where('employee_leave_logs_id', '<>',  $employee_leave_logs_id )	*/										
													)],
				  'reason_of_leave'    	 	=> 'required',
				]);
							
		
		
			if($employee_leave_logs_id==0){
			
				$EmployeeLeaveLogs = new EmployeeLeaveLogsModel();
			
				$EmployeeLeaveLogs->employee_idx 			= $employee_idx;
				$EmployeeLeaveLogs->branch_idx 				= $branch_idx;
				$EmployeeLeaveLogs->department_idx 			= $department_idx;
				$EmployeeLeaveLogs->current_rate			= $employee_current_rate;
				$EmployeeLeaveLogs->date_of_leave 			= $date_of_leave;
				$EmployeeLeaveLogs->reason_of_leave			= $reason_of_leave;
				$EmployeeLeaveLogs->leave_amount			= $leave_amount;
				
				$EmployeeLeaveLogs->created_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeLeaveLogs->save();

				 if($result){
					 return response()->json(['success'=>'Created']);
				 }
				 else{
					 return response()->json(['success'=>'Error on Insert Employee Information']);
				 }
			
			}else{
				
				$EmployeeLeaveLogs = new EmployeeLeaveLogsModel();
				$EmployeeLeaveLogs = EmployeeLeaveLogsModel::find($employee_leave_logs_id);
				
				$EmployeeLeaveLogs->employee_idx 			= $employee_idx;
				$EmployeeLeaveLogs->branch_idx 				= $branch_idx;
				$EmployeeLeaveLogs->department_idx 			= $department_idx;
				$EmployeeLeaveLogs->current_rate			= $employee_current_rate;
				$EmployeeLeaveLogs->date_of_leave 			= $date_of_leave;
				$EmployeeLeaveLogs->reason_of_leave			= $reason_of_leave;
				$EmployeeLeaveLogs->leave_amount			= $leave_amount;
				$EmployeeLeaveLogs->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeLeaveLogs->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Update Employee Information']);
				}
			
			}
			
			
	}else{

				$request->validate([
				  'branch_idx'				=> 'required',
				  'employee_idx'      		=> 'required',
				  'date_of_leave'    		=> 'required',
				  'reason_of_leave'    		=> 'required'
				
				]);
		
	}
	}

}

