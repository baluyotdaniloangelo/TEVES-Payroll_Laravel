<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BranchModel;
use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\HolidayModel;
use App\Models\EmployeeDeductionLogsModel;
use App\Models\EmployeeAllowanceLogsModel;
use App\Models\EmployeeLeaveLogsModel;

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

				return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
				
	}


}