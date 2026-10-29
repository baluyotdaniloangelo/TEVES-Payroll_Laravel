<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\HolidayModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeLogsController extends Controller
{
	
	/*Load Employee Interface*/
	public function employee_attendance_logs(){
		
		if(Session::has('loginID')){
			
			$title = 'Employee Logs';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$active_link = 'employee_attendance_logs';
			
			return view("payroll.employee_attendance_logs_main", compact('data', 'title', 'active_link'));
		}
	} 

	/*Fetch Employee Regular Log List using Datatable*/
	public function getEmployeeRegularLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLogsModel::join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->where('teves_payroll_employee_logs.log_type', 'Regular')
		->select([
            'teves_payroll_employee_logs.employee_logs_id',
			'teves_payroll_employee_logs.attendance_date as attendance_date',
			'teves_payroll_employee_logs.total_regular_hours as total_regular_hours',
			'teves_payroll_employee_logs.total_night_differential_hours as total_night_differential_hours',
			'teves_payroll_employee_logs.log_in as log_in',
			'teves_payroll_employee_logs.log_out as log_out',
			'teves_payroll_employee_logs.breaktime_start as breaktime_start',
			'teves_payroll_employee_logs.breaktime_end as breaktime_end',
			'teves_payroll_employee_logs.regular_pay as regular_pay',
			'teves_payroll_employee_logs.night_differential_pay as night_differential_pay',
			'teves_payroll_employee_logs.regular_holiday_pay as regular_holiday_pay',
			'teves_payroll_employee_logs.special_holiday_pay as special_holiday_pay',
			'teves_branch_table.branch_name as branch_name',
			'teves_payroll_department_table.department_name as department_name',
			'teves_payroll_employee_table.employee_number as employee_number',
			DB::raw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,''), ' ') as employee_full_name"),
		]);
	
		return DataTables::of($regular_logs)
				->addIndexColumn()
                ->addColumn('attendance_source', function ($row) {
                    if (!\Illuminate\Support\Facades\Schema::hasTable('attendance_import_rows')) return 'Manual / Legacy';
                    $imported = \Illuminate\Support\Facades\DB::table('attendance_import_rows')->where('saved_log_id', $row->employee_logs_id)->exists();
                    return $imported ? 'Imported' : 'Manual / Legacy';
                })
                ->addColumn('action', function($row){				
					$actionBtn = '
					<div class="dropdown dropdown-action">
						<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="edit_employee_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="delete_employee_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                /*SEARCH FILTER*/
                ->filterColumn('employee_number', function($query, $keyword) {
                    $query->where('teves_payroll_employee_table.employee_number', 'like', "%{$keyword}%");
                })
                ->filterColumn('employee_full_name', function($query, $keyword) {
                    $query->whereRaw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,'')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('branch_name', function($query, $keyword) {
                    $query->where('teves_branch_table.branch_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('department_name', function($query, $keyword) {
                    $query->where('teves_payroll_department_table.department_name', 'like', "%{$keyword}%");
                })
                /*SEARCH FILTER*/
                ->make(true);
		}
    }
	
	/*Fetch Employee Regular Overtime Log List using Datatable*/
	public function getEmployeeOvertimeLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		//->whereIn('teves_payroll_employee_logs.log_type', 'RegularOT')
        ->whereIn('teves_payroll_employee_logs.log_type', ['RegularOT', 'RestDayOT']) 
		->select([
             DB::raw("
                CASE 
                    WHEN log_type = 'RegularOT' THEN 'Regular Overtime'
                    WHEN log_type = 'RestDayOT' THEN 'Rest Day Overtime'
                    ELSE log_type
                END as log_type
             "),
			'teves_payroll_employee_logs.attendance_date as attendance_date',
            DB::raw("
            CASE 
                WHEN teves_payroll_employee_logs.log_type = 'RegularOT' 
                    THEN teves_payroll_employee_logs.total_regular_overtime_hours
                WHEN teves_payroll_employee_logs.log_type = 'RestDayOT' 
                    THEN teves_payroll_employee_logs.total_restday_overtime_hours
                ELSE 0
            END as total_overtime_hours
            "),
            'teves_payroll_employee_logs.employee_logs_id',
			'teves_payroll_employee_logs.total_night_differential_hours as total_night_differential_hours',
			'teves_payroll_employee_logs.log_in as log_in',
			'teves_payroll_employee_logs.log_out as log_out',
			'teves_payroll_employee_logs.breaktime_start as breaktime_start',
			'teves_payroll_employee_logs.breaktime_end as breaktime_end',
			DB::raw("
            CASE 
                WHEN teves_payroll_employee_logs.log_type = 'RegularOT' 
                    THEN teves_payroll_employee_logs.regular_overtime_pay
                WHEN teves_payroll_employee_logs.log_type = 'RestDayOT' 
                    THEN teves_payroll_employee_logs.restday_overtime_pay
                ELSE 0
            END as overtime_pay
            "),
			'teves_payroll_employee_logs.night_differential_pay as night_differential_pay',
			'teves_payroll_employee_logs.regular_holiday_pay as regular_holiday_pay',
			'teves_payroll_employee_logs.special_holiday_pay as special_holiday_pay',
			'teves_branch_table.branch_name',
			'teves_payroll_department_table.department_name',
			'teves_payroll_employee_table.employee_number',
			DB::raw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,''), ' ') as employee_full_name"),
		]);
	
		return DataTables::of($regular_logs)
				->addIndexColumn()
                ->addColumn('attendance_source', function ($row) {
                    if (!\Illuminate\Support\Facades\Schema::hasTable('attendance_import_rows')) return 'Manual / Legacy';
                    $imported = \Illuminate\Support\Facades\DB::table('attendance_import_rows')->where('saved_log_id', $row->employee_logs_id)->exists();
                    return $imported ? 'Imported' : 'Manual / Legacy';
                })
                ->addColumn('action', function($row){				
					$actionBtn = '
					<div class="dropdown dropdown-action">
						<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="edit_employee_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="delete_employee_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }

	/*Fetch Employee Restday Log List using Datatable*/
	public function getEmployeeRestDayLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->where('teves_payroll_employee_logs.log_type', 'Restday')
		//->where('teves_payroll_employee_logs.employee_logs_id', 1)
		->select([
            'teves_payroll_employee_logs.employee_logs_id',
			'teves_payroll_employee_logs.attendance_date as attendance_date',
			'teves_payroll_employee_logs.total_restday_hours as total_restday_hours',
			'teves_payroll_employee_logs.total_night_differential_hours as total_night_differential_hours',
			'teves_payroll_employee_logs.log_in as log_in',
			'teves_payroll_employee_logs.log_out as log_out',
			'teves_payroll_employee_logs.breaktime_start as breaktime_start',
			'teves_payroll_employee_logs.breaktime_end as breaktime_end',
			'teves_payroll_employee_logs.restday_pay as restday_pay',
			'teves_payroll_employee_logs.night_differential_pay as night_differential_pay',
			'teves_payroll_employee_logs.regular_holiday_pay as regular_holiday_pay',
			'teves_payroll_employee_logs.special_holiday_pay as special_holiday_pay',
			'teves_branch_table.branch_name as branch_name',
			'teves_payroll_department_table.department_name as department_name',
			'teves_payroll_employee_table.employee_number',
			DB::raw("CONCAT(teves_payroll_employee_table.employee_last_name, ', ', teves_payroll_employee_table.employee_first_name, ' ', IFNULL(teves_payroll_employee_table.employee_middle_name,''), ' ', IFNULL(teves_payroll_employee_table.employee_extension_name,''), ' ') as employee_full_name"),
		]);
	
		return DataTables::of($regular_logs)
				->addIndexColumn()
                ->addColumn('attendance_source', function ($row) {
                    if (!\Illuminate\Support\Facades\Schema::hasTable('attendance_import_rows')) return 'Manual / Legacy';
                    $imported = \Illuminate\Support\Facades\DB::table('attendance_import_rows')->where('saved_log_id', $row->employee_logs_id)->exists();
                    return $imported ? 'Imported' : 'Manual / Legacy';
                })
                ->addColumn('action', function($row){				
					$actionBtn = '
					<div class="dropdown dropdown-action">
						<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="edit_employee_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->employee_logs_id.'" id="delete_employee_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
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
					->where('teves_payroll_employee_logs.employee_logs_id', $employeelogsID)
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

	public function submit_employee_regular_logs_information(Request $request)
    {
        return app(\App\Services\Attendance\AttendanceLogWriter::class)->save($request);
    }

}
