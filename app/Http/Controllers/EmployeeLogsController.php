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
		]);
	
		return DataTables::of($regular_logs)
				->addIndexColumn()
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
	
	/*Fetch Employee Regular Overtime Log List using Datatable*/
	public function getEmployeeRegularOTLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->where('teves_payroll_employee_logs.log_type', 'RegularOT')
		->select([
			'teves_payroll_employee_logs.*',
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
	public function getEmployeeRestDayOTLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = EmployeeLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_employee_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->where('teves_payroll_employee_logs.log_type', 'RestDayOT')
		//->where('teves_payroll_employee_logs.employee_logs_id', 1)
		->select([
			'teves_payroll_employee_logs.*',
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

	public function submit_employee_regular_logs_information(Request $request){
			
			$branch_idx						= $request->branch_idx;
			$employee_idx 					= $request->employee_idx;
			$employee_logs_id 				= $request->employee_logs_id;
			$attendance_date 				= $request->attendance_date;
			$log_in 						= $request->log_in;
			$breaktime_start 				= $request->breaktime_start;
			$breaktime_end 					= $request->breaktime_end;
			$log_out 						= $request->log_out;
			$override_default_shift 		= $request->override_default_shift;
			$overtime_status				= $request->overtime_status;
			
			
			if($branch_idx!=0 && $employee_idx!=0){
			/*Query Employee Information*/
			/*jj*/
			$employee_data = EmployeeModel::where('teves_payroll_employee_table.employee_id', $employee_idx)
						->get([
						'teves_payroll_employee_table.branch_idx',
						'teves_payroll_employee_table.department_idx',
						'teves_payroll_employee_table.employee_rate',
						'teves_payroll_employee_table.employee_night_diff_pay',
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
			
			$employee_night_diff_pay 					= $employee_data[0]->employee_night_diff_pay;
			
			$restday_monday 					= $employee_data[0]->restday_monday;
			$restday_tuesday 					= $employee_data[0]->restday_tuesday;
			$restday_wednesday 					= $employee_data[0]->restday_wednesday;
			$restday_thursday 					= $employee_data[0]->restday_thursday;
			$restday_friday 					= $employee_data[0]->restday_friday;
			$restday_saturday 					= $employee_data[0]->restday_saturday;
			$restday_sunday 					= $employee_data[0]->restday_sunday;
			
			$employee_default_time_in 			= $employee_data[0]->time_in;
			$employee_default_time_out 			= $employee_data[0]->time_out;
			$employee_default_breaktime_in 		= $employee_data[0]->break_time_in;
			$employee_default_breaktime_out 	= $employee_data[0]->break_time_out;
			
			
			$total_default_shift_hours			= $employee_data[0]->total_shift_hours;
			$total_default_breaktime_hours 		= $employee_data[0]->total_breaktime_hours;
			
			$employee_current_rate = $employee_data[0]->employee_rate;
			
			$total_default_working_hours		= $total_default_shift_hours - $total_default_breaktime_hours;			
			
				$attendance_date_Number = (date('N', strtotime($attendance_date)));
				
				if($attendance_date_Number==0){
					$check_employee_rest_day = $restday_sunday;
				}
				else if($attendance_date_Number==1){
					$check_employee_rest_day = $restday_monday;
				}
				else if($attendance_date_Number==2){
					$check_employee_rest_day = $restday_tuesday;
				}
				else if($attendance_date_Number==3){
					$check_employee_rest_day = $restday_wednesday;
				}
				else if($attendance_date_Number==4){
					$check_employee_rest_day = $restday_thursday;
				}
				else if($attendance_date_Number==5){
					$check_employee_rest_day = $restday_friday;
				}
				else{
					$check_employee_rest_day = $restday_saturday;
				}
				
				if($check_employee_rest_day == 1){
					
					$log_type = 'RestDayOT';
					
				}else{
					
					if($overtime_status=='Yes'){
						
						$log_type = 'RegularOT';
						
					}else{
						
						$log_type = 'Regular';
						
					}
					
					
				}
				/*jj*/
			
			if($overtime_status=='Yes'){
				
				$request->validate([
				  'branch_idx'				=> ['required'],
				  'employee_idx'      		=> ['required'],
				  'attendance_date'    		=> ['required',Rule::unique('teves_payroll_employee_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('attendance_date', $attendance_date)
														->where('log_type',  $log_type )
														->where(function ($q) use($employee_logs_id) {
															if ($employee_logs_id!=0) {
															   $q->where('teves_payroll_employee_logs.employee_logs_id', '<>', $employee_logs_id);
															}
														})											
													)],
				  'log_in'    				=> 'required',
				  'breaktime_start'  		=> 'required',
				  'breaktime_end' 			=> 'required',
				  'log_out'  				=> 'required'
				]);
				
			}else{
				
				$request->validate([
				  'branch_idx'				=> ['required'],
				  'employee_idx'      		=> ['required'],
				  'attendance_date'    		=> ['required',Rule::unique('teves_payroll_employee_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('attendance_date', $attendance_date)
														->where('log_type',   $log_type )
														->where(function ($q) use($employee_logs_id) {
															if ($employee_logs_id!=0) {
															   $q->where('teves_payroll_employee_logs.employee_logs_id', '<>', $employee_logs_id);
															}
														})								
													)],
				  'log_in'    	 			=> 'required',
				  'breaktime_start'   		=> 'required',
				  'breaktime_end'  			=> 'required',
				  'log_out'  				=> 'required',
				  'branch_idx'				=> 'required'
				]);
							
			}
		
			/*1st 4 Hours - morning shift*/
			$time_in_string_to_time = strtotime($log_in);
			$break_time_in_string_to_time = strtotime($breaktime_start);

			/*2nd 4 Hours - late afternoon*/
			$time_out_string_to_time = strtotime($log_out);
			$break_time_out_string_to_time = strtotime($breaktime_end);

			$morning_shift_total_hours = ($break_time_in_string_to_time - $time_in_string_to_time) / 3600;
			$late_afternoon_total_hours = ($break_time_out_string_to_time - $time_out_string_to_time) / 3600;

			/*Gawing sakto ang na compute na oras. halimbawa nag in sya ng 7:45am pero and dapat na oras nya ay 8am dapat hindi kasama ang 15 minutes sa total hrs*/
			/*Ma compute din neto and tardiness*/

			/*Compute Total Breaktime Hours*/
			$total_breaktime_hours = ($break_time_out_string_to_time - $break_time_in_string_to_time) / 3600;

			/*Convert Default Employee Shift to String to Time*/
			$employee_default_time_in_strtotime = strtotime("$attendance_date $employee_default_time_in");
			$employee_default_time_out_strtotime = strtotime("$attendance_date $employee_default_time_out");
			$employee_default_breaktime_out_strtotime = strtotime("$attendance_date $employee_default_breaktime_out");

			/*Compute Tardiness*/
			 $tardiness_morning_shift = ($employee_default_time_in_strtotime - $time_in_string_to_time) / 3600;
			//echo "[$tardiness_morning_shift]";
			 $tardiness_late_afternoon = ($employee_default_breaktime_out_strtotime - $break_time_out_string_to_time) / 3600;
			/*Tardiness for Morning*/
			if($tardiness_morning_shift<0){
				/*Negative Means Late*/
				$default_timein_to_timein_log_excess = 0;
			}else{
				/*Positive Means Not Late or Arrived Earlier than the Default Time In, Less the Excess*/
				$default_timein_to_timein_log_excess = $tardiness_morning_shift;/*To Less on the Total Working Hours*/
			}
			/*Tardiness for Afternoon*/
			if($tardiness_late_afternoon<0){
				/*Negative Means Late*/
				$default_breaktimeout_to_breaktimeout_log_excess = 0;
			}else{
				/*Positive Means Not Late or Arrived Earlier than the Default Time In, Less the Excess*/
				$default_breaktimeout_to_breaktimeout_log_excess = $tardiness_late_afternoon;/*To Less on the Total Working Hours*/
			}
	
			/*Get Total Hours for Employee In and Out Logs*/	
			$total_hours_from_log_in_and_out = number_format(($time_out_string_to_time - $time_in_string_to_time) / 3600+0, 2, '.', '');

			if($override_default_shift=='No'){
				$total_tardiness = abs($tardiness_morning_shift) + abs($tardiness_late_afternoon);
				$total_excess_hours = number_format($default_timein_to_timein_log_excess + $default_breaktimeout_to_breaktimeout_log_excess+0, 2, '.', '');/*Yung Sobra sa 8hrs*/
				/*Compute the Total Undertime*/
				 $undertime_hours = ($time_out_string_to_time - $employee_default_time_out_strtotime) / 3600;
			}
			else{
				$total_tardiness = 0;
				$total_excess_hours = 0;/*Yung Sobra sa 8hrs*/
				/*Compute the Total Undertime*/
				$undertime_hours = 0;
			}
		
			if($undertime_hours<0){
				/*Negative Means Undertime*/
				$total_undertime_hours = abs($undertime_hours);
				//$excess_hours_after_shift = 0;
			}else{
				
				$total_undertime_hours =0;
				//$excess_hours_after_shift = abs($undertime_hours);
			}

			/*Start to Compute night shift hrs*/
			/*Compute The Covered Night Shift Hours*/
			/*Source*/
			/*https://stackoverflow.com/questions/10532687/time-night-differential-computation-in-php/10534110#10534110*/
			/*Set Limit for Night Differential*/
						
						
						if($employee_night_diff_pay=='Yes'){
						
						define('START_NIGHT_HOUR','22');
						define('START_NIGHT_MINUTE','00');
						define('START_NIGHT_SECOND','00');
						define('END_NIGHT_HOUR','06');
						define('END_NIGHT_MINUTE','00');
						define('END_NIGHT_SECOND','00');

						$night_start_work = strtotime($log_in);
						$night_end_work = strtotime($log_out);						
							
							$default_start_of_night_diff = mktime(START_NIGHT_HOUR,START_NIGHT_MINUTE,START_NIGHT_SECOND,date('m',$night_start_work),date('d',$night_start_work),date('Y',$night_start_work));
							
							/*Comparison of Day*/
							$day1 = date('d',$night_start_work);
							$day2 = date('d',$night_end_work);
							
							if($day1!=$day2){
								//echo "startDAY<br>";
								$default_end_of_night_diff   = mktime(END_NIGHT_HOUR,END_NIGHT_MINUTE,END_NIGHT_SECOND,date('m',$night_start_work),date('d',$night_start_work) + 1,date('Y',$night_start_work));
							}
							else{
								//echo "endDAY<br>";
								$default_end_of_night_diff   = mktime(END_NIGHT_HOUR,END_NIGHT_MINUTE,END_NIGHT_SECOND,date('m',$night_end_work),date('d',$night_end_work) ,date('Y',$night_end_work));
							}
						
							if($night_start_work >= $default_start_of_night_diff && $night_start_work <= $default_end_of_night_diff)
							{
								if($night_end_work >= $default_end_of_night_diff) 
								{
									//echo "NS7<br>";
									$night_shft_hrs = ($default_end_of_night_diff - $night_start_work) / 3600;
									//echo "NS6<br>";
									$night_shift_breaktime = 0;
								}
								else
								{
									//echo "NS5<br>";
									$night_shft_hrs = ($night_end_work - $night_start_work) / 3600;
									$night_shift_breaktime = 0;
								}
							}
							elseif($night_end_work >= $default_start_of_night_diff && $night_end_work <= $default_end_of_night_diff)
							{
								if($night_start_work <= $default_start_of_night_diff)
								{
									$night_shft_hrs = ($night_end_work - $default_start_of_night_diff) / 3600;
									$night_shift_breaktime = $total_breaktime_hours;
									//echo "NS4<br>";
								}
								else
								{
									$night_shft_hrs = ($night_end_work - $night_start_work) / 3600;
									//echo "NS3<br>";
									$night_shift_breaktime = 0;
								}
							}
							else
							{
								if($night_start_work < $default_start_of_night_diff && $night_end_work > $default_end_of_night_diff)
								{
									
									if(($default_end_of_night_diff - $night_start_work) / 3600 < 0 ){
										//echo "NS1.1<br>";
										 $night_shft_hrs = 0;
										 $night_shift_breaktime = 0;
									}else{
										//echo "NS2.1<br>";
										$night_shft_hrs = ($default_end_of_night_diff - $night_start_work) / 3600;
										$night_shift_breaktime = $total_breaktime_hours;
									}
									
								}
								else
								{	
									if( (($night_start_work - $default_end_of_night_diff) / 3600) < 0 ){
										//echo "NS1.2<br>";
										 $night_shft_hrs = ($night_end_work - $night_start_work - 0) / 3600;
										 $night_shift_breaktime = $total_breaktime_hours;
									}else{
										//echo "NS2.2<br>";
										$night_shft_hrs = ($night_start_work - $default_end_of_night_diff) / 3600;
										$night_shift_breaktime = 0;
									}
									
								}
							}
							
			$covered_night_diff_hrs = number_format(($night_shft_hrs)+0, 1, '.', '');
			$total_covered_night_diff_hrs = number_format(($night_shft_hrs - $night_shift_breaktime)+0, 2, '.', '');
			
			$night_differential_pay = $total_covered_night_diff_hrs * $employee_current_rate * (10/100);
			/*End to Compute night shift hrs*/
						}else{
							
							$covered_night_diff_hrs = 0;
							$total_covered_night_diff_hrs = 0;
							
							$night_differential_pay = 0;
							
						}
			
			if($overtime_status=='No'){

           // echo $total_hours_from_log_in_and_out;

           if($total_hours_from_log_in_and_out - ($total_excess_hours + $total_breaktime_hours + $excess_hours_after_shift + $total_undertime_hours)>=8){
                echo $excess_hours_after_shift = ($total_hours_from_log_in_and_out - ($total_excess_hours + $total_breaktime_hours + $excess_hours_after_shift + $total_undertime_hours) )-8;
            }
            else{
                 $excess_hours_after_shift = 0;
            }

			$total_regular_hours = $total_hours_from_log_in_and_out - ($total_excess_hours + $total_breaktime_hours + $excess_hours_after_shift + $total_undertime_hours);
			$total_tardiness_hours = $total_tardiness;
				
				if($check_employee_rest_day == 1){
					
					$Regular_pay = 0;
					$RegularOT_pay = 0;
					$RestDayOT_pay = ($employee_current_rate * $total_regular_hours) * 1.3;
					
				}else{
					
					/*Compute by Rate*/
					/*Computed pay = total regular hours by rate*/
					$Regular_pay = $employee_current_rate * $total_regular_hours;
					$RegularOT_pay = 0;
					$RestDayOT_pay = 0;
					
				}
				
			}else{
			$total_tardiness_hours = 0;

            

			$total_regular_hours = $total_hours_from_log_in_and_out - (0);

					/*Soon to have a table for Rate on Overtime - Hard Coded pa to to 0.25*/
					$Regular_pay = 0;
					$RegularOT_pay = $employee_current_rate * 1.25 * $total_regular_hours;
					$RestDayOT_pay = 0;
					
			}
			
			/*Query Holiday*/
			/*Holiday Computation from Logs*/
			$holiday_data_for_regular = HolidayModel::where('holiday_date', $attendance_date)
						->where('holiday_type', 'Regular Holiday')
						->get([
						'holiday_id',
						'holiday_description',
						'holiday_date',
						'holiday_type'
						]);
			
			$holiday_data_for_special_non_working = HolidayModel::where('holiday_date', $attendance_date)
						->where('holiday_type', 'Special Non-Working Holiday')
						->get([
						'holiday_id',
						'holiday_description',
						'holiday_date',
						'holiday_type'
						]);
						
			$regular_holiday_pay = 0;
			$special_holiday_pay = 0;
			
			foreach ($holiday_data_for_regular as $holiday_data_for_regular_cols){
				
				$holiday_id 			= $holiday_data_for_regular_cols->holiday_id;
				$holiday_description 	= $holiday_data_for_regular_cols->holiday_description;
				$holiday_date 			= $holiday_data_for_regular_cols->holiday_date;
				$holiday_type 			= $holiday_data_for_regular_cols->holiday_type;
				
				/*Regular Holiday and Regular Working Days*/
				if($holiday_type=='Regular Holiday' && $log_type=='Regular'){                                          
					$regular_holiday_pay += $employee_current_rate * $total_default_working_hours;
				}
				/*Regular Holiday and Regular Working Days Overtime*/
				else if($holiday_type=='Regular Holiday' && $log_type=='RegularOT'){
					$regular_holiday_pay += ((($employee_current_rate * 2) * 1.30) * $total_regular_hours ) - $RegularOT_pay;
				}
				/*Regular Holiday and Restday Overtime*/
				else{
					//2600 = 1000 X 200% + (1000 x 200% )X30%(*0.3)
					$regular_holiday_pay += 1.3 * ( $employee_current_rate * $total_regular_hours ) ;

				}
				
			}
			
			foreach ($holiday_data_for_special_non_working as $holiday_data_for_special_non_working_cols){
	   
				$holiday_id 			= $holiday_data_for_special_non_working_cols->holiday_id;
				$holiday_description 	= $holiday_data_for_special_non_working_cols->holiday_description;
				$holiday_date 			= $holiday_data_for_special_non_working_cols->holiday_date;
				$holiday_type 			= $holiday_data_for_special_non_working_cols->holiday_type;
				
				/*Special Non-Working Holiday and Regular Working Days Overtime*/
				if($holiday_type=='Special Non-Working Holiday' && $log_type=='Regular'){
					
					$special_holiday_pay = (($employee_current_rate * 0.3)) * ($total_regular_hours);
					
				}
				/*Special Non-Working Holiday and Regular Working Days Overtime*/
				else if($holiday_type=='Special Non-Working Holiday' && $log_type=='RegularOT'){
					
					$special_holiday_pay = (($employee_current_rate * 0.3 * 0.3)) * ($total_regular_hours);
					
				}
				/*Special Non-Working Holiday and Restdays Days Overtime*/
				else{
					
						if(($total_regular_hours)>8){
						
							$special_holiday_pay = (($employee_current_rate * 0.5) * 0.3) * ($total_regular_hours);
		
						}else{
						
							$special_holiday_pay = (($employee_current_rate * 0.5)) * ($total_regular_hours);
						
						}

				}
				
			
			}
			
			if($employee_logs_id==0){

				$EmployeeRegularLogs = new EmployeeLogsModel();
			
				$EmployeeRegularLogs->employee_idx 						= $employee_idx;
				$EmployeeRegularLogs->branch_idx 						= $branch_idx;
				$EmployeeRegularLogs->department_idx 					= $department_idx;
				$EmployeeRegularLogs->current_rate						= $employee_current_rate;
				$EmployeeRegularLogs->attendance_date 					= $attendance_date;
				$EmployeeRegularLogs->override_shift					= $override_default_shift;
				$EmployeeRegularLogs->log_in 							= $log_in;
				$EmployeeRegularLogs->breaktime_start 					= $breaktime_start;
				$EmployeeRegularLogs->breaktime_end 					= $breaktime_end;
				$EmployeeRegularLogs->log_out 							= $log_out;
				
				$EmployeeRegularLogs->total_hours 						= number_format((float)$total_hours_from_log_in_and_out, 2, '.', '');
				$EmployeeRegularLogs->total_regular_hours 				= number_format((float)$total_regular_hours, 2, '.', '');
				$EmployeeRegularLogs->total_breaktime_hours 			= number_format((float)$total_breaktime_hours, 2, '.', '');
				$EmployeeRegularLogs->total_tardiness_hours 			= number_format((float)$total_tardiness_hours, 2, '.', '');
				$EmployeeRegularLogs->total_undertime_hours 			= number_format((float)$total_undertime_hours,2);
				$EmployeeRegularLogs->total_night_differential_hours 	= number_format((float)$total_covered_night_diff_hrs, 2, '.', '');

				if($log_type=='Regular'){
					$EmployeeRegularLogs->basic_pay						= number_format((float)$Regular_pay, 2, '.', '');
				}else if($log_type=='RegularOT'){
					$EmployeeRegularLogs->overtime_pay					= number_format((float)$RegularOT_pay, 2, '.', '');
				}else{
					$EmployeeRegularLogs->day_off_pay					= number_format((float)$RestDayOT_pay , 2, '.', '');
				}
				
				$EmployeeRegularLogs->night_differential_pay			= number_format((float)$night_differential_pay, 2, '.', '');
				$EmployeeRegularLogs->regular_holiday_pay				= number_format((float)$regular_holiday_pay, 2, '.', '');
				$EmployeeRegularLogs->special_holiday_pay				= number_format((float)$special_holiday_pay, 2, '.', '');
				$EmployeeRegularLogs->log_type						 	= $log_type;
				
				$EmployeeRegularLogs->created_by_user_idx 		= Session::get('loginID');
				
				$result = $EmployeeRegularLogs->save();

				 if($result){
					 return response()->json(['success'=>'Created']);
				 }
				 else{
					 return response()->json(['success'=>'Error on Insert Employee Information']);
				 }
			
			}else{
			
				$EmployeeRegularLogs = new EmployeeLogsModel();
				$EmployeeRegularLogs = EmployeeLogsModel::find($employee_logs_id);
				
				$EmployeeRegularLogs->branch_idx 						= $branch_idx;
				$EmployeeRegularLogs->department_idx 					= $department_idx;
				$EmployeeRegularLogs->current_rate						= $employee_current_rate;
				$EmployeeRegularLogs->attendance_date 					= $attendance_date;
				$EmployeeRegularLogs->override_shift					= $override_default_shift;
				$EmployeeRegularLogs->log_in 							= $log_in;
				$EmployeeRegularLogs->breaktime_start 					= $breaktime_start;
				$EmployeeRegularLogs->breaktime_end 					= $breaktime_end;
				$EmployeeRegularLogs->log_out 							= $log_out;
				
				$EmployeeRegularLogs->total_hours 						= number_format((float)$total_hours_from_log_in_and_out, 2, '.', '');
				$EmployeeRegularLogs->total_regular_hours 				= number_format((float)$total_regular_hours, 2, '.', '');
				$EmployeeRegularLogs->total_breaktime_hours 			= number_format((float)$total_breaktime_hours, 2, '.', '');
				$EmployeeRegularLogs->total_tardiness_hours 			= number_format((float)$total_tardiness_hours, 2, '.', '');
				$EmployeeRegularLogs->total_undertime_hours 			= number_format((float)$total_undertime_hours,2);
				$EmployeeRegularLogs->total_night_differential_hours 	= number_format((float)$total_covered_night_diff_hrs, 2, '.', '');

				if($log_type=='Regular'){
					$EmployeeRegularLogs->basic_pay						= number_format((float)$Regular_pay, 2, '.', '');
				}else if($log_type=='RegularOT'){
					$EmployeeRegularLogs->overtime_pay					= number_format((float)$RegularOT_pay, 2, '.', '');
				}else{
					$EmployeeRegularLogs->day_off_pay					= number_format((float)$RestDayOT_pay , 2, '.', '');
				}
				
				$EmployeeRegularLogs->night_differential_pay			= number_format((float)$night_differential_pay, 2, '.', '');
				$EmployeeRegularLogs->regular_holiday_pay				= number_format((float)$regular_holiday_pay, 2, '.', '');
				$EmployeeRegularLogs->special_holiday_pay				= number_format((float)$special_holiday_pay, 2, '.', '');
				$EmployeeRegularLogs->log_type						 	= $log_type;
				
				$EmployeeRegularLogs->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $EmployeeRegularLogs->update();
				
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
				  'attendance_date'    		=> 'required',
				  'log_in'    				=> 'required',
				  'breaktime_start'  		=> 'required',
				  'breaktime_end' 			=> 'required',
				  'log_out'  				=> 'required'
				]);
		
	}
	}

}

