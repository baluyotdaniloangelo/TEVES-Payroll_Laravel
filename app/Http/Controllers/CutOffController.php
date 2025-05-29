<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\CutOffModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CutOffController extends Controller
{
	
	/*Load Employee Interface*/
	public function cut_off(){
		
		if(Session::has('loginID')){
			
			$title = 'Cut Off';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$active_link = 'cut_off';
			
			return view("payroll.cut_off", compact('data', 'title', 'active_link'));
		}
	} 

	/*Fetch Employee Regular Log List using Datatable*/
	public function getCutOffList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
        /*
		$regular_logs = EmployeeLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->where('teves_payroll_employee_logs.log_type', 'Regular')
		->select([
			'teves_payroll_employee_logs.*',
			'teves_branch_table.branch_name',
			'teves_payroll_department_table.department_name',
			'teves_payroll_employee_table.employee_number',
			DB::raw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,''), ' ') as employee_full_name"),
		]);*/
        
        $data = CutOffModel::query()
            ->leftJoin('teves_payroll_user_table as prepared_by', 'teves_payroll_cutoff_table.created_by_user_idx', '=', 'prepared_by.user_id')
            ->leftJoin('teves_payroll_user_table as reviewed_by', 'teves_payroll_cutoff_table.reviewed_by_user_idx', '=', 'reviewed_by.user_id')
            ->leftJoin('teves_payroll_user_table as approved_by', 'teves_payroll_cutoff_table.approved_by_user_idx', '=', 'approved_by.user_id')
            ->leftJoin('teves_branch_table as branch_details', 'teves_payroll_cutoff_table.branch_idx', '=', 'branch_details.branch_id')
            ->select(
                'teves_payroll_cutoff_table.*',
                'prepared_by.user_real_name as prepared_by_name',
                'reviewed_by.user_real_name as reviewed_by_name',
                'approved_by.user_real_name as approved_by_name',
                'branch_details.branch_code as branch_code'
            )
           ->get();
	
		return DataTables::of($data)
        //<i class="fa-solid fa-print"></i>
				->addIndexColumn()
                ->addColumn('action', function($row){				
					$actionBtn = '
					<div class="dropdown dropdown-action">
						<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								<a class="dropdown-item" href="#" onclick="print_payroll('.$row->cutoff_id.')"><i class="fa-solid fa-print m-r-5"></i> Print</a>
                                <!--<a class="dropdown-item" href="#" onclick="reviewed_payroll('.$row->cutoff_id.')"><i class="fa-solid fa-print m-r-5"></i> Assessed</a>
                                <a class="dropdown-item" href="#" onclick="print_payroll('.$row->cutoff_id.')"><i class="fa-solid fa-print m-r-5"></i> Approved</a>-->
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }
	

	/*Fetch EmployeeDetails Information*/
	public function employee_log_info(Request $request){

		$employeelogsID = $request->employeelogsID;

		$data = EmployeeLogsModel::join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_logs.cutoff_id', $employeelogsID)
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
					'teves_payroll_department_table.department_id',
					'teves_payroll_department_table.department_name',
					'teves_payroll_employee_logs.attendance_date',
					'teves_payroll_employee_logs.override_shift',
					'teves_payroll_employee_logs.log_type',
					'teves_payroll_employee_logs.log_in',
					'teves_payroll_employee_logs.log_out',
					'teves_payroll_employee_logs.breaktime_start',
					'teves_payroll_employee_logs.breaktime_end'
					]);
		return response()->json($data);
		
	}

	/*Delete Employee Information*/
	public function delete_employee_log_confirmed(Request $request){

		$employeelogID = $request->employeelogID;
		EmployeeLogsModel::find($employeelogID)->delete();
		
		return 'Deleted';
		
	} 

}

