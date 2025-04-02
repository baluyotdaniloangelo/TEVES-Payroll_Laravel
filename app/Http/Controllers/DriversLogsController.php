<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\EmployeeModel;
use App\Models\DriversLogsModel;
use App\Models\HolidayModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DriversLogsController extends Controller
{
	


	/*Fetch Employee Regular Log List using Datatable*/
	public function getDriversLogsList(Request $request)
    {

		if ($request->ajax()) {

		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$regular_logs = DriversLogsModel::query()
		->join('teves_payroll_employee_table', 'teves_payroll_drivers_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
		->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
		->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
		->select([
			'teves_payroll_drivers_logs.*',
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
								<a class="dropdown-item" href="#" data-id="'.$row->drivers_logs_id.'" id="edit_driver_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								<a class="dropdown-item" href="#" data-id="'.$row->drivers_logs_id.'" id="delete_driver_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							</div>
					</div>';
                    return $actionBtn;
					
                })
					
				->rawColumns(['action'])
                ->make(true);
		}
    }
	
	/*Fetch EmployeeDetails Information*/
	public function drivers_logs_info(Request $request){

		$DriverlogsID = $request->DriverlogsID;

		$data = DriversLogsModel::join('teves_payroll_employee_table', 'teves_payroll_drivers_logs.employee_idx', '=', 'teves_payroll_employee_table.employee_id')
					->join('teves_payroll_department_table', 'teves_payroll_department_table.department_id', '=', 'teves_payroll_employee_table.department_idx')
					->join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_payroll_employee_table.branch_idx')
					->where('teves_payroll_drivers_logs.drivers_logs_id', $DriverlogsID)
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
					'teves_payroll_drivers_logs.travel_date',
					'teves_payroll_drivers_logs.plate_number',
					'teves_payroll_drivers_logs.loading_terminal',
					'teves_payroll_drivers_logs.destination',
					'teves_payroll_drivers_logs.volume',
					'teves_payroll_drivers_logs.rate_per_liter',
					'teves_payroll_drivers_logs.gross_amount',
					'teves_payroll_drivers_logs.trip_pay'
					]);
		return response()->json($data);
		
	}

	/*Delete Employee Information*/
	public function delete_driver_log_confirmed(Request $request){

		$DriverlogsID = $request->DriverlogsID;
		DriversLogsModel::find($DriverlogsID)->delete();
		
		return 'Deleted';
		
	} 

	public function submit_drivers_logs_information (Request $request){
			
			$drivers_logs_id 		= $request->drivers_logs_id;
			
			$branch_idx				= $request->branch_idx;
			$employee_idx 			= $request->employee_idx;
			
			$travel_date 			= $request->travel_date;
			$plate_number 			= $request->plate_number;
			$loading_terminal 		= $request->loading_terminal;
			$destination 			= $request->destination;
			$volume 				= $request->volume;
			$rate_per_liter 		= $request->rate_per_liter;
			
			
			if($branch_idx!=0 && $employee_idx!=0){
			/*Query Employee Information*/
			$employee_data = EmployeeModel::where('teves_payroll_employee_table.employee_id', $employee_idx)
						->get([
						'teves_payroll_employee_table.branch_idx',
						'teves_payroll_employee_table.department_idx',
						'teves_payroll_employee_table.employee_rate'
						]);
			
			$branch_idx 				= $employee_data[0]->branch_idx;
			$department_idx 			= $employee_data[0]->department_idx;
			
			$driver_current_rate 		= $employee_data[0]->employee_rate;
			
			$gross_amount = $volume * $rate_per_liter;

			$trip_pay = $gross_amount * ($driver_current_rate/100);

			$request->validate([
				  'branch_idx'				=> ['required'],
				  'employee_idx'      		=> ['required',Rule::unique('teves_payroll_drivers_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('travel_date', $travel_date)
														->where(function ($q) use($drivers_logs_id) {
															if ($drivers_logs_id!=0) {
															   $q->where('teves_payroll_drivers_logs.drivers_logs_id', '<>', $drivers_logs_id);
															}
														})									
													)],
				  'travel_date'    		=> ['required',Rule::unique('teves_payroll_drivers_logs')->where( 
													fn ($query) =>$query
														->where('employee_idx', $employee_idx)
														->where('travel_date', $travel_date)
														->where(function ($q) use($drivers_logs_id) {
															if ($drivers_logs_id!=0) {
															   $q->where('teves_payroll_drivers_logs.drivers_logs_id', '<>', $drivers_logs_id);
															}
														})									
													)]
				]);
							
			if($drivers_logs_id==0){

				$DriversLogs = new DriversLogsModel();
			
				$DriversLogs->employee_idx 			= $employee_idx;
				$DriversLogs->branch_idx 			= $branch_idx;
				$DriversLogs->department_idx 		= $department_idx;
				$DriversLogs->current_rate			= $driver_current_rate;
				$DriversLogs->travel_date 			= $travel_date;
				$DriversLogs->plate_number			= $plate_number;
				$DriversLogs->loading_terminal		= $loading_terminal;
				$DriversLogs->destination			= $destination;
				$DriversLogs->volume				= $volume;
				$DriversLogs->rate_per_liter		= $rate_per_liter;
				$DriversLogs->gross_amount			= $gross_amount;
				$DriversLogs->trip_pay				= $trip_pay;
				
				$DriversLogs->created_by_user_idx 	= Session::get('loginID');
				
				$result = $DriversLogs->save();

				 if($result){
					 return response()->json(['success'=>'Created']);
				 }
				 else{
					 return response()->json(['success'=>'Error on Insert Drivers Logs Information']);
				 }
			
			}else{
				
				$DriversLogs = new DriversLogsModel();
				$DriversLogs = DriversLogsModel::find($drivers_logs_id);
				
				$DriversLogs->employee_idx 			= $employee_idx;
				$DriversLogs->branch_idx 			= $branch_idx;
				$DriversLogs->department_idx 		= $department_idx;
				$DriversLogs->current_rate			= $driver_current_rate;
				$DriversLogs->travel_date 			= $travel_date;
				$DriversLogs->plate_number			= $plate_number;
				$DriversLogs->loading_terminal		= $loading_terminal;
				$DriversLogs->destination			= $destination;
				$DriversLogs->volume				= $volume;
				$DriversLogs->rate_per_liter		= $rate_per_liter;
				$DriversLogs->gross_amount			= $gross_amount;
				$DriversLogs->trip_pay				= $trip_pay;
				
				$result = $DriversLogs->update();
				
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
				  'travel_date'    			=> 'required'
				]);
		
	}
	}

}

