<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\BranchModel;
use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\HolidayModel;
use App\Models\EmployeeDeductionLogsModel;
use App\Models\EmployeeAllowanceLogsModel;
use App\Models\EmployeeLeaveLogsModel;
use App\Models\CutOffModel;
use App\Models\EmployeesSalaryPerCutOffModel;
use App\Models\CompanyDetailsModel;

use Session;
use Validator;
//use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/*PDF*/
use PDF;
use DataTables;

class CreatePayrollController extends Controller
{
	/*Create Payroll*/
	public function create_payroll(){
		
		if(Session::has('loginID')){
				
			if(Session::has('loginID')){
				
				$title = 'Create Payroll';
				$data = array();
				
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
				
				$teves_branch = BranchModel::all();
				
				return view("payroll.create_payroll", compact('data','title','teves_branch'));
				
			}

		}
	}  	

	public function generate_review_payroll(Request $request){
		
		$request->validate([
		  'branchID'      		=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'branchID.required' 	=> 'Please select a Branch',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);

		
		$branchID	  = $request->branchID;
		$start_date	  = $request->start_date;
		$end_date	  = $request->end_date;
		
		$employee_data = EmployeeModel::join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_table.branch_idx', $branchID)
					->where('teves_payroll_employee_table.employment_type', '!=', 'Others')
              		->get([
					'teves_payroll_employee_table.employee_id',
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_full_name',
					'teves_payroll_employee_table.employee_rate',
					'teves_payroll_employee_table.employment_type'
					]);
					
			$result = array();	
			
			foreach ($employee_data as $employee_data_cols){
					
					$employee_id 		= $employee_data_cols->employee_id;
					$employee_number 	= $employee_data_cols->employee_number;
					$employee_full_name = $employee_data_cols->employee_full_name;
					$employee_rate = $employee_data_cols->employee_rate;
					$employment_type = $employee_data_cols->employment_type;
					
					$daily_rate = $employee_rate * 8;
					
					/*Get Attendance Logs based on the Selected Date Range*/
					/*Regular*/
					$regular_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'Regular')
					->selectRaw('ifnull(sum(regular_pay),0) as regular_pay, count(*) as days_count_regular')
					->get();
					
					$regular_logs_count = $regular_logs[0]['days_count_regular'];
					$regular_pay_total = $regular_logs[0]['regular_pay'];
					
					/*Regular Overtime*/
					$regular_overtime_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RegularOT')
					->selectRaw('ifnull(sum(regular_overtime_pay),0) as regular_overtime_pay')
					->get();
					
					$regular_overtime_pay_total = $regular_overtime_logs[0]['regular_overtime_pay'];
					
					/*Restday Pay*/
					$day_off_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RestDay')
					->selectRaw('ifnull(sum(restday_pay),0) as restday_pay, count(*) as days_count_day_off')
					->get();
					
					$day_off_logs_count = $day_off_logs[0]['days_count_day_off'];
					$restday_pay_total = $day_off_logs[0]['restday_pay'];

                    /*Restday Overtime*/
					$regular_overtime_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RestDayOT')
					->selectRaw('ifnull(sum(restday_overtime_pay),0) as restday_overtime_pay')
					->get();
					
					$restday_overtime_pay_total = $regular_overtime_logs[0]['restday_overtime_pay'];

					/*Night Diff Pay*/
					$night_differential_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(night_differential_pay),0) as night_differential_pay')
					->get();
					
					$night_differential_pay_total = $night_differential_logs[0]['night_differential_pay'];
					
					/*Regular Holiday Pay*/
					$period = CarbonPeriod::create("$start_date 00:00", '1 Day', "$end_date 00:00");		
					
					$regular_holiday_pay_total = 0;
					
					foreach ($period as $key => $date) {
						
						$date_only 	= $date->format('Y-m-d');
						
						$regular_holiday_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
						->where('attendance_date', $date_only)
						->selectRaw('ifnull(sum(regular_holiday_pay),0) as regular_holiday_pay, count(*) as logs_count')
						->get();
						
						$logs_count = $night_differential_logs[0]['logs_count'];
						
						if($logs_count!=0){
							
							$_regular_holiday_pay_total = $night_differential_logs[0]['regular_holiday_pay'];
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}else{
						
							/*Check Regular Holiday*/
							$check_date_reg_holiday =  HolidayModel::where('holiday_date', $date_only)
							->selectRaw('count(*) as regular_holiday_count')
							->get();
							
							$regular_holiday_count = $check_date_reg_holiday[0]['regular_holiday_count'];
						
							$_regular_holiday_pay_total =  ($employee_rate * 8)* $regular_holiday_count;
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}
					
					}
					
					/*Special Holiday Pay*/
					$special_holiday_pay_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(special_holiday_pay),0) as special_holiday_pay')
					->get();
					
					$special_holiday_pay_total = $special_holiday_pay_logs[0]['special_holiday_pay'];
					
					/*Leave*/
					$leave_logs =  EmployeeLeaveLogsModel::where('employee_idx', $employee_id)
					->whereBetween('date_of_leave', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(leave_amount),0) as leave_amount, count(*) as leave_logs_count')
					->get();
					
					$leave_logs_count = $leave_logs[0]['leave_logs_count'];
					$leave_amount_pay_total = $leave_logs[0]['leave_amount'];
					
					
					/*Count Days of Work*/
					$count_days = $regular_logs_count + $day_off_logs_count;
					
					
					/*Deductions*/
					$deduction_amount =  EmployeeDeductionLogsModel::where('employee_idx', $employee_id)
					->whereBetween('deduction_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(deduction_amount),0) as deduction_amount')
					->get();
					
					$deduction_amount_total = $deduction_amount[0]['deduction_amount'];
					
					/*Allowance*/
					$allowance_amount =  EmployeeAllowanceLogsModel::where('employee_idx', $employee_id)
					->whereBetween('allowance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(allowance_amount),0) as allowance_amount')
					->get();
					
					$allowance_amount_total = $allowance_amount[0]['allowance_amount'];
					
					$basic_pay_total = $regular_pay_total + $leave_amount_pay_total;
					
					$gross_salary = $basic_pay_total+$regular_overtime_pay_total+$restday_pay_total+$restday_overtime_pay_total+$night_differential_pay_total+$regular_holiday_pay_total+$special_holiday_pay_total;
					$net_salary = ($gross_salary + $allowance_amount_total) - $deduction_amount_total; 
					
					$result[] = array(
					 'employee_number' 					=> $employee_number,
					 'employee_full_name'				=> $employee_full_name,
					 'daily_rate'						=> $daily_rate,
					 'employment_type'					=> $employment_type,
					 'regular_pay'					    => $regular_pay_total,
					 'regular_overtime_pay'		        => $regular_overtime_pay_total,
					 'restday_pay'				        => $restday_pay_total,
					 'restday_overtime_pay'				=> $restday_overtime_pay_total,
					 'night_differential_pay_total'		=> $night_differential_pay_total,
					 'regular_holiday_pay_total' 		=> $regular_holiday_pay_total,
					 'special_holiday_pay_total'		=> $special_holiday_pay_total,
					 'count_days'						=> $count_days,
					 'deduction_amount_total'			=> $deduction_amount_total,
					 'allowance_amount_total'			=> $allowance_amount_total,
					 'leave_logs_count'					=> $leave_logs_count,					
					 'leave_amount_pay_total'			=> $leave_amount_pay_total,
					 'gross_salary'						=> $gross_salary,
					 'net_salary'						=> $net_salary
					 );
					
			}	

				return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
				
	}

	public function save_generated_payroll(Request $request){
	
		$branchID	  = $request->branch_idx;
		$start_date	  = $request->cut_off_period_start;
		$end_date	  = $request->cut_off_period_end;
		
		$request->validate([
		  'branch_idx'      		=> ['required'],
		  'cut_off_period_start'   	=> ['required',
			function ($a, $start_date, $fail) use ($request) {
				$exists = CutOffModel::where('branch_idx', $request->branch_idx)
					->where('cut_off_period_start','>=',  $start_date)
					->where('cut_off_period_start', '<=', $request->cut_off_period_end)
					->exists();
				if ($exists) {
					$fail('The combination of start date and end date must be unique.');
				}
			}],
		  'cut_off_period_end'     	=> ['required',
			function ($b, $end_date, $fail) use ($request) {
				$exists = CutOffModel::where('branch_idx', $request->branch_idx)
					->where('cut_off_period_end','>=',  $request->cut_off_period_start)
					->where('cut_off_period_end', '<=', $end_date)
					->exists();
				if ($exists) {
					$fail('The combination of start date and end date must be unique.');
				}
			}]
        ], 
        [
			'branch_idx.required' 	=> 'Please select a Branch',
			'cut_off_period_start.required' 	=> 'Please select a Start Date',
			'cut_off_period_end.required' 	=> 'Please select a End Date'
        ]
		);

		$_payroll_start_date=date_create("$start_date");
        $payroll_month = strtoupper(date_format($_payroll_start_date,"F"));

		/*Saved Cut-off*/
		$SaveCutOFF = new CutOffModel();
		$SaveCutOFF->branch_idx 			= $branchID;
        $SaveCutOFF->cut_off_month       	= $payroll_month;
		$SaveCutOFF->cut_off_period_start 	= $start_date;
		$SaveCutOFF->cut_off_period_end 	= $end_date;
		$SaveCutOFF->created_by_user_idx 	= Session::get('loginID');
		$result_cut_off_save 				= $SaveCutOFF->save();
		$cutoff_idx = $SaveCutOFF->cutoff_id;
		
		$employee_data = EmployeeModel::join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_table.branch_idx', $branchID)
					->where('teves_payroll_employee_table.employment_type', '!=', 'Others')
              		->get([
					'teves_payroll_employee_table.employee_id',
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_full_name',
					'teves_payroll_employee_table.employee_rate',
					'teves_payroll_employee_table.employment_type'
					]);
					
			$result = array();

            $cut_off_gross_salary 					= 0;
			$cut_off_net_salary 					= 0;
			
			foreach ($employee_data as $employee_data_cols){
					
					$employee_id 		= $employee_data_cols->employee_id;
					$employee_number 	= $employee_data_cols->employee_number;
					$employee_full_name = $employee_data_cols->employee_full_name;
					$employee_rate = $employee_data_cols->employee_rate;
					$employment_type = $employee_data_cols->employment_type;
					
					$daily_rate = $employee_rate * 8;
					
					/*Get Attendance Logs based on the Selected Date Range*/
					/*Regular*/
					$regular_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'Regular')
					->selectRaw('ifnull(sum(regular_pay),0) as regular_pay, count(*) as days_count_regular')
					->get();
					
					$regular_logs_count = $regular_logs[0]['days_count_regular'];
					$regular_pay_total = $regular_logs[0]['regular_pay'];
					
					/*Regular Overtime*/
					$regular_overtime_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RegularOT')
					->selectRaw('ifnull(sum(regular_overtime_pay),0) as regular_overtime_pay')
					->get();
					
					$regular_overtime_pay_total = $regular_overtime_logs[0]['regular_overtime_pay'];
					
					/*Restday Pay*/
					$day_off_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RestDay')
					->selectRaw('ifnull(sum(restday_pay),0) as restday_pay, count(*) as days_count_day_off')
					->get();
					
					$day_off_logs_count = $day_off_logs[0]['days_count_day_off'];
					$restday_pay_total = $day_off_logs[0]['restday_pay'];

                    /*Restday Overtime*/
					$regular_overtime_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RestDayOT')
					->selectRaw('ifnull(sum(restday_overtime_pay),0) as restday_overtime_pay')
					->get();
					
					$restday_overtime_pay_total = $regular_overtime_logs[0]['restday_overtime_pay'];

					/*Night Diff Pay*/
					$night_differential_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(night_differential_pay),0) as night_differential_pay')
					->get();
					
					$night_differential_pay_total = $night_differential_logs[0]['night_differential_pay'];
					
					/*Regular Holiday Pay*/
					$period = CarbonPeriod::create("$start_date 00:00", '1 Day', "$end_date 00:00");		
					
					$regular_holiday_pay_total = 0;
					
					foreach ($period as $key => $date) {
						
						$date_only 	= $date->format('Y-m-d');
						
						$regular_holiday_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
						->where('attendance_date', $date_only)
						->selectRaw('ifnull(sum(regular_holiday_pay),0) as regular_holiday_pay, count(*) as logs_count')
						->get();
						
						$logs_count = $night_differential_logs[0]['logs_count'];
						
						if($logs_count!=0){
							
							$_regular_holiday_pay_total = $night_differential_logs[0]['regular_holiday_pay'];
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}else{
						
							/*Check Regular Holiday*/
							$check_date_reg_holiday =  HolidayModel::where('holiday_date', $date_only)
							->selectRaw('count(*) as regular_holiday_count')
							->get();
							
							$regular_holiday_count = $check_date_reg_holiday[0]['regular_holiday_count'];
						
							$_regular_holiday_pay_total =  ($employee_rate * 8)* $regular_holiday_count;
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}
					
					}
					
					/*Special Holiday Pay*/
					$special_holiday_pay_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(special_holiday_pay),0) as special_holiday_pay')
					->get();
					
					$special_holiday_pay_total = $special_holiday_pay_logs[0]['special_holiday_pay'];
					
					/*Leave*/
					$leave_logs =  EmployeeLeaveLogsModel::where('employee_idx', $employee_id)
					->whereBetween('date_of_leave', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(leave_amount),0) as leave_amount, count(*) as leave_logs_count')
					->get();
					
					$leave_logs_count = $leave_logs[0]['leave_logs_count'];
					$leave_amount_pay_total = $leave_logs[0]['leave_amount'];
					
					
					/*Count Days of Work*/
					$count_days = $regular_logs_count + $day_off_logs_count;
					
					
					/*Deductions*/
					$deduction_amount =  EmployeeDeductionLogsModel::where('employee_idx', $employee_id)
					->whereBetween('deduction_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(deduction_amount),0) as deduction_amount')
					->get();
					
					$deduction_amount_total = $deduction_amount[0]['deduction_amount'];
					
					/*Allowance*/
					$allowance_amount =  EmployeeAllowanceLogsModel::where('employee_idx', $employee_id)
					->whereBetween('allowance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(allowance_amount),0) as allowance_amount')
					->get();
					
					$allowance_amount_total = $allowance_amount[0]['allowance_amount'];
					
					$basic_pay_total = $regular_pay_total + $leave_amount_pay_total;
					
					$gross_salary = $basic_pay_total+$regular_overtime_pay_total+$restday_pay_total+$restday_overtime_pay_total+$night_differential_pay_total+$regular_holiday_pay_total+$special_holiday_pay_total;
					$net_salary = ($gross_salary + $allowance_amount_total) - $deduction_amount_total; 
					
					$result[] = array(
					 'employee_number' 					=> $employee_number,
					 'employee_full_name'				=> $employee_full_name,
					 'daily_rate'						=> $daily_rate,
					 'employment_type'					=> $employment_type,
					 'regular_pay'					    => $regular_pay_total,
					 'regular_overtime_pay'		        => $regular_overtime_pay_total,
					 'restday_pay'				        => $restday_pay_total,
					 'restday_overtime_pay'				=> $restday_overtime_pay_total,
					 'night_differential_pay_total'		=> $night_differential_pay_total,
					 'regular_holiday_pay_total' 		=> $regular_holiday_pay_total,
					 'special_holiday_pay_total'		=> $special_holiday_pay_total,
					 'count_days'						=> $count_days,
					 'deduction_amount_total'			=> $deduction_amount_total,
					 'allowance_amount_total'			=> $allowance_amount_total,
					 'leave_logs_count'					=> $leave_logs_count,					
					 'leave_amount_pay_total'			=> $leave_amount_pay_total,
					 'gross_salary'						=> $gross_salary,
					 'net_salary'						=> $net_salary
					 );
					 
					 /*Save Employees Salary based on the Selected Cut Off*/
					$SaveEmployeesSalaryPerCutOFF = new EmployeesSalaryPerCutOffModel();
					$SaveEmployeesSalaryPerCutOFF->employee_idx 					= $employee_id;
					$SaveEmployeesSalaryPerCutOFF->branch_idx 						= $branchID;
					$SaveEmployeesSalaryPerCutOFF->cutoff_idx 						= $cutoff_idx;
					$SaveEmployeesSalaryPerCutOFF->hourly_rate 						= $employee_rate;
					$SaveEmployeesSalaryPerCutOFF->daily_rate 						= $daily_rate;
					$SaveEmployeesSalaryPerCutOFF->employment_type 					= $employment_type;
					$SaveEmployeesSalaryPerCutOFF->regular_pay_total 			    = $regular_pay_total;
					$SaveEmployeesSalaryPerCutOFF->regular_overtime_pay_total 		= $regular_overtime_pay_total;
					$SaveEmployeesSalaryPerCutOFF->restday_pay_total 				= $restday_pay_total;
					$SaveEmployeesSalaryPerCutOFF->restday_overtime_pay_total 	    = $restday_overtime_pay_total;
					$SaveEmployeesSalaryPerCutOFF->night_differential_pay_total 	= $night_differential_pay_total;
					$SaveEmployeesSalaryPerCutOFF->regular_holiday_pay_total 		= $regular_holiday_pay_total;
					$SaveEmployeesSalaryPerCutOFF->special_holiday_pay_total 		= $special_holiday_pay_total;
					$SaveEmployeesSalaryPerCutOFF->count_days 						= $count_days;
					$SaveEmployeesSalaryPerCutOFF->deduction_amount_total 			= $deduction_amount_total;
					$SaveEmployeesSalaryPerCutOFF->allowance_amount_total 			= $allowance_amount_total;
					$SaveEmployeesSalaryPerCutOFF->leave_logs_count 				= $leave_logs_count;
					$SaveEmployeesSalaryPerCutOFF->leave_amount_pay_total 			= $leave_amount_pay_total;
					$SaveEmployeesSalaryPerCutOFF->gross_salary 					= $gross_salary;
					$SaveEmployeesSalaryPerCutOFF->net_salary 						= $net_salary;
					$SaveEmployeesSalaryPerCutOFF->created_by_user_idx 				= Session::get('loginID');
					$result_save_salary = $SaveEmployeesSalaryPerCutOFF->save();

                    $cut_off_gross_salary 					+= $gross_salary;
			        $cut_off_net_salary 					+= $net_salary;

					$RegularLogsLock = EmployeeLogsModel::where('employee_idx', $employee_id)
					->where('log_type', 'Regular')
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);
						
					$RegularOTLogsLock = EmployeeLogsModel::where('employee_idx', $employee_id)
					->where('log_type', 'RegularOT')
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);
						
					$RestOTLogsLock = EmployeeLogsModel::where('employee_idx', $employee_id)
					->where('log_type', 'RegularOT')
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);
					
					/*Leave*/					
					$LeaveLogsLock = EmployeeLeaveLogsModel::where('employee_idx', $employee_id)
					->whereBetween('date_of_leave', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);
						
					/*Deduction*/	
					$DeductionLogsLock = EmployeeDeductionLogsModel::where('employee_idx', $employee_id)
					->whereBetween('deduction_date', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);
					
					/*Allowance*/	
					$AllowanceLogsLock = EmployeeAllowanceLogsModel::where('employee_idx', $employee_id)
					->whereBetween('allowance_date', ["$start_date", "$end_date"])
					->update(
						[
						'cutoff_idx' => $cutoff_idx
						],
						);	
						
			}
                /*Update Cut-off Gross and Net Salary*/
		        $UpdateCutOFF = new CutOffModel();
                $UpdateCutOFF = CutOffModel::find($cutoff_idx);
		        $UpdateCutOFF->cut_off_gross_salary 		= $cut_off_gross_salary;
		        $UpdateCutOFF->cut_off_net_salary 			= $cut_off_net_salary;
		        $UpdateCutOFF->update();

				$datatable_data =  DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
				return response()->json(array('data' => $datatable_data, 'cutoff_idx' => @$cutoff_idx), 200);
				
	}


	/*Drafts*/
	public function generate_employees_payroll_draft_pdf(Request $request){
		
		$request->validate([
		  'branchID'      		=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'branchID.required' 	=> 'Please select a Branch',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);

		
		$branchID	  = $request->branchID;
		$start_date	  = $request->start_date;
		$end_date	  = $request->end_date;
		
		$employee_data = EmployeeModel::join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_employee_table.branch_idx', $branchID)
					->where('teves_payroll_employee_table.employment_type', '!=', 'Others')
              		->get([
					'teves_payroll_employee_table.employee_id',
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_full_name',
					'teves_payroll_employee_table.employee_rate',
					'teves_payroll_employee_table.employment_type'
					]);
					
			$result = array();	
			
			foreach ($employee_data as $employee_data_cols){
					
					$employee_id 		= $employee_data_cols->employee_id;
					$employee_number 	= $employee_data_cols->employee_number;
					$employee_full_name = $employee_data_cols->employee_full_name;
					$employee_rate = $employee_data_cols->employee_rate;
					$employment_type = $employee_data_cols->employment_type;
					
					$daily_rate = $employee_rate * 8;
					
					/*Get Attendance Logs based on the Selected Date Range*/
					/*Regular*/
					$regular_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'Regular')
					->selectRaw('ifnull(sum(basic_pay),0) as basic_pay, count(*) as days_count_regular')
					->get();
					
					$regular_logs_count = $regular_logs[0]['days_count_regular'];
					$regular_pay_total = $regular_logs[0]['basic_pay'];
					
					/*Regular Overtime*/
					$regular_overtime_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RegularOT')
					->selectRaw('ifnull(sum(overtime_pay),0) as overtime_pay')
					->get();
					
					$regular_overtime_pay_total = $regular_overtime_logs[0]['overtime_pay'];
					
					/*Restday Overtime Pay*/
					$day_off_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->where('log_type', 'RestDayOT')
					->selectRaw('ifnull(sum(day_off_pay),0) as day_off_pay, count(*) as days_count_day_off')
					->get();
					
					$day_off_logs_count = $day_off_logs[0]['days_count_day_off'];
					$day_off_pay_total = $day_off_logs[0]['day_off_pay'];
					
					/*Night Diff Pay*/
					$night_differential_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(night_differential_pay),0) as night_differential_pay')
					->get();
					
					$night_differential_pay_total = $night_differential_logs[0]['night_differential_pay'];
					
					/*Regular Holiday Pay*/
					$period = CarbonPeriod::create("$start_date 00:00", '1 Day', "$end_date 00:00");		
					
					$regular_holiday_pay_total = 0;
					
					foreach ($period as $key => $date) {
						
						$date_only 	= $date->format('Y-m-d');
						
						$regular_holiday_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
						->where('attendance_date', $date_only)
						->selectRaw('ifnull(sum(regular_holiday_pay),0) as regular_holiday_pay, count(*) as logs_count')
						->get();
						
						$logs_count = $night_differential_logs[0]['logs_count'];
						
						if($logs_count!=0){
							
							$_regular_holiday_pay_total = $night_differential_logs[0]['regular_holiday_pay'];
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}else{
						
							/*Check Regular Holiday*/
							$check_date_reg_holiday =  HolidayModel::where('holiday_date', $date_only)
							->selectRaw('count(*) as regular_holiday_count')
							->get();
							
							$regular_holiday_count = $check_date_reg_holiday[0]['regular_holiday_count'];
						
							$_regular_holiday_pay_total =  ($employee_rate * 8)* $regular_holiday_count;
							$regular_holiday_pay_total += $_regular_holiday_pay_total;
							
						}
					
					}
					
					/*Special Holiday Pay*/
					$special_holiday_pay_logs =  EmployeeLogsModel::where('employee_idx', $employee_id)
					->whereBetween('attendance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(special_holiday_pay),0) as special_holiday_pay')
					->get();
					
					$special_holiday_pay_total = $special_holiday_pay_logs[0]['special_holiday_pay'];
					
					/*Leave*/
					$leave_logs =  EmployeeLeaveLogsModel::where('employee_idx', $employee_id)
					->whereBetween('date_of_leave', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(leave_amount),0) as leave_amount, count(*) as leave_logs_count')
					->get();
					
					$leave_logs_count = $leave_logs[0]['leave_logs_count'];
					$leave_amount_pay_total = $leave_logs[0]['leave_amount'];
					
					
					/*Count Days of Work*/
					$count_days = $regular_logs_count + $day_off_logs_count;
					
					
					/*Deductions*/
					$deduction_amount =  EmployeeDeductionLogsModel::where('employee_idx', $employee_id)
					->whereBetween('deduction_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(deduction_amount),0) as deduction_amount')
					->get();
					
					$deduction_amount_total = $deduction_amount[0]['deduction_amount'];
					
					/*Allowance*/
					$allowance_amount =  EmployeeAllowanceLogsModel::where('employee_idx', $employee_id)
					->whereBetween('allowance_date', ["$start_date", "$end_date"])
					->selectRaw('ifnull(sum(allowance_amount),0) as allowance_amount')
					->get();
					
					$allowance_amount_total = $allowance_amount[0]['allowance_amount'];
					
					$basic_pay_total = $regular_pay_total + $leave_amount_pay_total;
					
					$gross_salary = $basic_pay_total+$regular_overtime_pay_total+$day_off_pay_total+$night_differential_pay_total+$regular_holiday_pay_total+$special_holiday_pay_total;
					$net_salary = ($gross_salary + $allowance_amount_total) - $deduction_amount_total; 
					
					$result[] = array(
					 'employee_number' 					=> $employee_number,
					 'employee_full_name'				=> $employee_full_name,
					 'daily_rate'						=> $daily_rate,
					 'employment_type'					=> $employment_type,
					 'basic_pay_total'					=> $basic_pay_total,
					 'regular_overtime_pay_total'		=> $regular_overtime_pay_total,
					 'day_off_pay_total'				=> $day_off_pay_total,
					 'night_differential_pay_total'		=> $night_differential_pay_total,
					 'regular_holiday_pay_total' 		=> $regular_holiday_pay_total,
					 'special_holiday_pay_total'		=> $special_holiday_pay_total,
					 'count_days'						=> $count_days,
					 'deduction_amount_total'			=> $deduction_amount_total,
					 'allowance_amount_total'			=> $allowance_amount_total,
					 'leave_logs_count'					=> $leave_logs_count,					
					 'leave_amount_pay_total'			=> $leave_amount_pay_total,
					 'gross_salary'						=> $gross_salary,
					 'net_salary'						=> $net_salary
					 );
					
			}	

		$branch_information = BranchModel::find($branchID, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();
		
		$title = 'Payroll - Drafts';
        $pdf = PDF::loadView('printables.employees_payroll_drafts_pdf', compact('title', 'result', 'user_data', 'branch_information','start_date','end_date'));
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		$pdf->setPaper('legal', 'landscape');/*Set to Landscape*/
		return $pdf->stream($branch_information['branch_code']."Payroll-Drafts.pdf");	
	}	



	/*Saved Cut OFF*/
	public function generate_employees_saved_payroll_pdf(Request $request){
		
		$cutoff_idx	  = $request->cutoff_idx;
		
		$result = EmployeesSalaryPerCutOffModel::join('teves_payroll_employee_table', 'teves_payroll_employee_salary_per_cut_off.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					->where('teves_payroll_employee_salary_per_cut_off.cutoff_idx', $cutoff_idx)
              		->get([
					'teves_payroll_employee_table.employee_id',
					'teves_payroll_employee_table.employee_number',
					'teves_payroll_employee_table.employee_full_name',
					'teves_payroll_employee_salary_per_cut_off.hourly_rate',
					'teves_payroll_employee_salary_per_cut_off.daily_rate',
					'teves_payroll_employee_salary_per_cut_off.employment_type',
					'teves_payroll_employee_salary_per_cut_off.regular_pay_total',
					'teves_payroll_employee_salary_per_cut_off.regular_overtime_pay_total',
					'teves_payroll_employee_salary_per_cut_off.restday_pay_total',
					'teves_payroll_employee_salary_per_cut_off.restday_overtime_pay_total',
					'teves_payroll_employee_salary_per_cut_off.night_differential_pay_total',
					'teves_payroll_employee_salary_per_cut_off.regular_holiday_pay_total',
					'teves_payroll_employee_salary_per_cut_off.special_holiday_pay_total',
					'teves_payroll_employee_salary_per_cut_off.count_days',
					'teves_payroll_employee_salary_per_cut_off.deduction_amount_total',
					'teves_payroll_employee_salary_per_cut_off.allowance_amount_total',
					'teves_payroll_employee_salary_per_cut_off.leave_logs_count',
					'teves_payroll_employee_salary_per_cut_off.leave_amount_pay_total',
					'teves_payroll_employee_salary_per_cut_off.gross_salary',
					'teves_payroll_employee_salary_per_cut_off.net_salary'
					]);
					
        $cut_off_information = CutOffModel::query()
            ->leftJoin('teves_payroll_user_table as prepared_by', 'teves_payroll_cutoff_table.created_by_user_idx', '=', 'prepared_by.user_id')
            ->leftJoin('teves_payroll_user_table as reviewed_by', 'teves_payroll_cutoff_table.reviewed_by_user_idx', '=', 'reviewed_by.user_id')
            ->leftJoin('teves_payroll_user_table as approved_by', 'teves_payroll_cutoff_table.approved_by_user_idx', '=', 'approved_by.user_id')
            ->leftJoin('teves_branch_table as branch_details', 'teves_payroll_cutoff_table.branch_idx', '=', 'branch_details.branch_id')
            ->where('teves_payroll_cutoff_table.cutoff_id', $cutoff_idx)
            ->select(
                'teves_payroll_cutoff_table.*',
                'prepared_by.user_real_name as prepared_by_name',
                'prepared_by.user_job_title as prepared_by_position',
                'reviewed_by.user_real_name as reviewed_by_name',
                'reviewed_by.user_job_title as reviewed_by_position',
                'approved_by.user_real_name as approved_by_name',
                'approved_by.user_job_title as approved_by_position',
                'branch_details.branch_code as branch_code'
            )
           ->get();

        $branchID	= $cut_off_information[0]->branch_idx;
		$start_date	= $cut_off_information[0]->cut_off_period_start;
		$end_date	= $cut_off_information[0]->cut_off_period_end;

		$prepared_by_name	    = $cut_off_information[0]->prepared_by_name;
        $prepared_by_position	= $cut_off_information[0]->prepared_by_position;
		$reviewed_by_name	    = $cut_off_information[0]->reviewed_by_name;
		$reviewed_by_position	= $cut_off_information[0]->reviewed_by_position;
		$approved_by_name	    = $cut_off_information[0]->approved_by_name;
		$approved_by_position	= $cut_off_information[0]->approved_by_position;
		$branch_code	        = $cut_off_information[0]->branch_code;
		
		$company_information = CompanyDetailsModel::find(1, ['sss_number','pagibig_number','philhealth']);
		$branch_information = BranchModel::find($branchID, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$title = "Employee's Weekly Payroll";
        $pdf = PDF::loadView('printables.employees_saved_payroll_pdf', compact('title', 'result', 'user_data', 'branch_information','start_date','end_date','prepared_by_name','prepared_by_position','reviewed_by_name','reviewed_by_position','approved_by_name','approved_by_position', 'company_information', 'cutoff_idx'));
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		$pdf->setPaper('legal', 'landscape');/*Set to Landscape*/
		return $pdf->stream($branch_information['branch_code']."Payroll.pdf");	
		
	}	

}
