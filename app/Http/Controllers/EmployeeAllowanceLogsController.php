<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\EmployeeAllowanceLogsModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeAllowanceLogsController extends Controller
{
	
	public function employee_allowance_logs(){
		
		if(Session::has('loginID')){
			
			$title = 'Employee Allowances';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$active_link = 'employee_allowance_logs';
			
			return view("payroll.employee_allowance_logs", compact('data', 'title', 'active_link'));
		}
	} 

	/*Fetch Employee Regular Log List using Datatable*/
	public function getEmployeeAllowanceLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeAllowanceLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_allowance_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_employee_table.department_idx', '=', 'teves_payroll_department_table.department_id')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->select([
			'teves_payroll_employee_allowance_logs.*',
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
								<a class="dropdown-item" href="#" data-id="'.$row->allowance_logs_id.'" id="edit_employee_allowance_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->allowance_logs_id.'" id="delete_employee_allowance_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }

	/*Fetch EmployeeDetails Information*/
	public function employee_allowance_info(Request $request){

		$employeeallowancelogsID = $request->employeeallowancelogsID;

		$data = EmployeeAllowanceLogsModel::join('teves_payroll_employee_table', 'teves_payroll_employee_allowance_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					//->join('teves_payroll_allowance_type_table', 'teves_payroll_employee_allowance_logs.allowance_idx', '=', 'teves_payroll_allowance_type_table.allowance_id')
					//->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_allowance_logs.allowance_logs_id', $employeeallowancelogsID)
              		->get([
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_last_name',
					'teves_payroll_employee_table.employee_first_name',
					'teves_payroll_employee_table.employee_middle_name',
					'teves_payroll_employee_table.employee_extension_name',
                    'teves_payroll_employee_table.employee_full_name',
					'teves_branch_table.branch_id',
					'teves_branch_table.branch_name',
					'teves_branch_table.branch_code',
					'teves_payroll_employee_allowance_logs.allowance_date',
					'teves_payroll_employee_allowance_logs.allowance_description',
					'teves_payroll_employee_allowance_logs.allowance_type',
					'teves_payroll_employee_allowance_logs.allowance_amount',
					]);
		return response()->json($data);
		
	}

	/*Delete Employee Information*/
	public function delete_employee_allowance_log_confirmed(Request $request){

		$employeeallowancelogsID = $request->employeeallowancelogsID;
		EmployeeAllowanceLogsModel::find($employeeallowancelogsID)->delete();
		
		return 'Deleted';
		
	} 

	/*Create/Update AllowanceType Information*/
	public function submit_employee_allowance_logs_information(Request $request){
			
			$branch_idx						= $request->branch_idx;
			$employee_idx 					= $request->employee_idx;
			//$allowance_idx 					= $request->allowance_idx;
			$allowance_logs_id 				= $request->allowance_logs_id;
			$allowance_date 				= $request->allowance_date;
			$allowance_description				= $request->allowance_description;
			$allowance_type					= $request->allowance_type;
			$allowance_amount 				= $request->allowance_amount;
			
			if($branch_idx!=0 && $employee_idx!=0 && $allowance_amount!=0){
			/*Query Employee Information*/
			$employee_data = EmployeeModel::where('teves_payroll_employee_table.employee_id', $employee_idx)
						->get([
						'teves_payroll_employee_table.branch_idx',
						'teves_payroll_employee_table.department_idx',
						'teves_payroll_employee_table.employee_rate'
						]);
			
			$branch_idx 						= $employee_data[0]->branch_idx;
			$department_idx 					= $employee_data[0]->department_idx;
			
			if($allowance_logs_id==0){
			
				$EmployeeAllowanceLogs = new EmployeeAllowanceLogsModel();
			
				$EmployeeAllowanceLogs->employee_idx 					= $employee_idx;
				$EmployeeAllowanceLogs->branch_idx 						= $branch_idx;
				$EmployeeAllowanceLogs->department_idx 					= $department_idx;
				$EmployeeAllowanceLogs->allowance_date 					= $allowance_date;
				$EmployeeAllowanceLogs->allowance_description 			= $allowance_description;
				$EmployeeAllowanceLogs->allowance_type 					= $allowance_type;
				$EmployeeAllowanceLogs->allowance_amount 				= $allowance_amount;
				
				$EmployeeAllowanceLogs->created_by_user_idx 		= Session::get('loginID');
				
				$result = $EmployeeAllowanceLogs->save();

				 if($result){
					 return response()->json(['success'=>'Created']);
				 }
				 else{
					 return response()->json(['success'=>'Error on Insert Employee Allowance Information']);
				 }
			
			}else{
			
				$EmployeeAllowanceLogs = new EmployeeAllowanceLogsModel();
				$EmployeeAllowanceLogs = EmployeeAllowanceLogsModel::find($allowance_logs_id);
				
				$EmployeeAllowanceLogs->employee_idx 					= $employee_idx;
				$EmployeeAllowanceLogs->branch_idx 						= $branch_idx;
				$EmployeeAllowanceLogs->department_idx 					= $department_idx;
				$EmployeeAllowanceLogs->allowance_date 					= $allowance_date;
				$EmployeeAllowanceLogs->allowance_description 			= $allowance_description;
				$EmployeeAllowanceLogs->allowance_type 					= $allowance_type;
				$EmployeeAllowanceLogs->allowance_amount 				= $allowance_amount;
				
				$EmployeeAllowanceLogs->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeAllowanceLogs->update();
				
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
				  'allowance_date'    		=> 'required',
				  'allowance_amount'    	=> 'required',
				  'allowance_idx'    	=> 'required'
				]);
		
	}
	}

}

