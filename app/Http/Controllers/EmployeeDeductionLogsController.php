<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\EmployeeDeductionLogsModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeDeductionLogsController extends Controller
{

	/*Fetch Employee Regular Log List using Datatable*/
	public function getEmployeeDeductionLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeDeductionLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_deduction_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_deduction_type_table', 'teves_payroll_employee_deduction_logs.deduction_idx', '=', 'teves_payroll_deduction_type_table.deduction_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->select([
			'teves_payroll_employee_deduction_logs.*',
			'teves_payroll_deduction_type_table.deduction_description',
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
								<a class="dropdown-item" href="#" data-id="'.$row->deduction_logs_id.'" id="edit_employee_deduction_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->deduction_logs_id.'" id="delete_employee_deduction_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }

	/*Fetch EmployeeDetails Information*/
	public function employee_deduction_info(Request $request){

		$employeedeductionlogsID = $request->employeedeductionlogsID;

		$data = EmployeeDeductionLogsModel::join('teves_payroll_employee_table', 'teves_payroll_employee_deduction_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					->join('teves_payroll_deduction_type_table', 'teves_payroll_employee_deduction_logs.deduction_idx', '=', 'teves_payroll_deduction_type_table.deduction_id')
					->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_deduction_logs.deduction_logs_id', $employeedeductionlogsID)
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
					'teves_payroll_employee_deduction_logs.deduction_date',
					'teves_payroll_employee_deduction_logs.deduction_amount',
					'teves_payroll_deduction_type_table.deduction_description'
					]);
		return response()->json($data);
		
	}

	/*Delete Employee Information*/
	public function delete_employee_deduction_log_confirmed(Request $request){

		$employeedeductionlogsID = $request->employeedeductionlogsID;
		EmployeeDeductionLogsModel::find($employeedeductionlogsID)->delete();
		
		return 'Deleted';
		
	} 

	/*Create/Update DeductionType Information*/
	public function submit_employee_deduction_logs_information(Request $request){
			
			$branch_idx						= $request->branch_idx;
			$employee_idx 					= $request->employee_idx;
			$deduction_idx 					= $request->deduction_idx;
			$deduction_logs_id 				= $request->deduction_logs_id;
			$deduction_date 				= $request->deduction_date;
			$deduction_amount 				= $request->deduction_amount;
			
			if($branch_idx!=0 && $employee_idx!=0 && $deduction_amount!=0){
			/*Query Employee Information*/
			$employee_data = EmployeeModel::where('teves_payroll_employee_table.employee_id', $employee_idx)
						->get([
						'teves_payroll_employee_table.branch_idx',
						'teves_payroll_employee_table.department_idx',
						'teves_payroll_employee_table.employee_rate',
						'teves_payroll_employee_table.time_in',
						'teves_payroll_employee_table.break_time_in',
						'teves_payroll_employee_table.break_time_out',
						'teves_payroll_employee_table.time_out',
						'teves_payroll_employee_table.total_shift_hours',
						'teves_payroll_employee_table.total_breaktime_hours',
						'teves_payroll_employee_table.restday_monday',
						'teves_payroll_employee_table.restday_tuesday',
						'teves_payroll_employee_table.restday_wednesday',
						'teves_payroll_employee_table.restday_thursday',
						'teves_payroll_employee_table.restday_friday',
						'teves_payroll_employee_table.restday_saturday',
						'teves_payroll_employee_table.restday_sunday',
						]);
			
			$branch_idx 						= $employee_data[0]->branch_idx;
			$department_idx 					= $employee_data[0]->department_idx;
			
			if($deduction_logs_id==0){
			
				$EmployeeDeductionLogs = new EmployeeDeductionLogsModel();
			
				$EmployeeDeductionLogs->employee_idx 					= $employee_idx;
				$EmployeeDeductionLogs->branch_idx 						= $branch_idx;
				$EmployeeDeductionLogs->department_idx 					= $department_idx;
				$EmployeeDeductionLogs->deduction_idx 					= $deduction_idx;
				$EmployeeDeductionLogs->deduction_date 					= $deduction_date;
				$EmployeeDeductionLogs->deduction_amount 				= $deduction_amount;
				
				$EmployeeDeductionLogs->created_by_user_idx 		= Session::get('loginID');
				
				$result = $EmployeeDeductionLogs->save();

				 if($result){
					 return response()->json(['success'=>'Created']);
				 }
				 else{
					 return response()->json(['success'=>'Error on Insert Employee Deduction Information']);
				 }
			
			}else{
			
				$EmployeeDeductionLogs = new EmployeeDeductionLogsModel();
				$EmployeeDeductionLogs = EmployeeDeductionLogsModel::find($deduction_logs_id);
				
				$EmployeeDeductionLogs->employee_idx 					= $employee_idx;
				$EmployeeDeductionLogs->branch_idx 						= $branch_idx;
				$EmployeeDeductionLogs->department_idx 					= $department_idx;
				$EmployeeDeductionLogs->deduction_idx 					= $deduction_idx;
				$EmployeeDeductionLogs->deduction_date 					= $deduction_date;
				$EmployeeDeductionLogs->deduction_amount 				= $deduction_amount;
				
				$EmployeeDeductionLogs->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeDeductionLogs->update();
				
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
				  'deduction_date'    		=> 'required',
				  'deduction_amount'    	=> 'required',
				  'deduction_idx'    	=> 'required'
				]);
		
	}
	}

}

