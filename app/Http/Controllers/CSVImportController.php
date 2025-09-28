<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\HolidayModel;

use App\Helpers\EmployeeLogsHelper;

use DataTables;
class CSVImportController extends Controller
{
    // Show the CSV upload form
    public function showImportForm()
    {
        return view('payroll.employee_logs_import');
    }

    // Handle CSV import
    public function importCSV(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file format or size'], 400);
        }

        // Get the uploaded file
        $file = $request->file('csv_file');

        // Parse the CSV file using the League CSV package
        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0); // First row is the header

            $no = 1;

            $data = [];
foreach ($csv as $row) {
    // CSV Data
    $employee_number  = $row['employee_no'];
    $attendance_date  = $row['log_date'];
    $log_in           = strtotime($row['time_in']);
    $break_start      = strtotime($row['breaktime_start']);
    $break_end        = strtotime($row['breaktime_end']);
    $log_out          = strtotime($row['time_out']);
    $time_in_ot       = !empty($row['time_in_ot']) ? strtotime($row['time_in_ot']) : null;
    $time_out_ot      = !empty($row['time_out_ot']) ? strtotime($row['time_out_ot']) : null;

    // Employee Info
    $employee = EmployeeModel::where('teves_payroll_employee_table.employee_number', $employee_number)->first();

    $employee_idx           = $employee->employee_id;
    $branch_idx 			= $employee->branch_idx;
	$department_idx 		= $employee->department_idx;

    $attendance_date_fmt = date('Y-m-d', strtotime($attendance_date)); 

    $employee_full_name     = $employee->employee_full_name;
    $default_in  = strtotime($attendance_date_fmt . " " . $employee->time_in);
    $default_out = strtotime($attendance_date_fmt . " " . $employee->time_out);
    $break_in     = strtotime($employee->break_time_in);
    $break_out    = strtotime($employee->break_time_out);
    $default_hours = $employee->total_shift_hours - $employee->total_breaktime_hours;

    // Hourly rate (assuming daily rate / 8 hours)
    $hourly_rate = $employee->employee_rate;
    $daily_rate  = $hourly_rate * $default_hours;
    $nd_rate     = $hourly_rate * 0.10; // 10% night diff

    // Compute tardiness & undertime
    $tardiness = max(0, ($log_in - $default_in) / 60); // minutes late
    $undertime = max(0, ($default_out - $log_out) / 60); // minutes early out

    // Compute regular worked hours (excluding breaks)
    $total_hours_from_log_in_and_out = (($log_out - $log_in)) / 3600;
    $worked_hours = (($log_out - $log_in) - ($break_end - $break_start)) / 3600;
    $regular_hours = min($worked_hours, $default_hours);

    // Compute overtime
    $regular_overtime_hours = 0;
    if ($time_in_ot && $time_out_ot) {
        $regular_overtime_hours = ($time_out_ot - $time_in_ot) / 3600;
    }

    // Check restday
    $day_num = (int) date('N', strtotime($attendance_date));
    switch ($day_num) {
        case 1: $restday = $employee->restday_monday; break;
        case 2: $restday = $employee->restday_tuesday; break;
        case 3: $restday = $employee->restday_wednesday; break;
        case 4: $restday = $employee->restday_thursday; break;
        case 5: $restday = $employee->restday_friday; break;
        case 6: $restday = $employee->restday_saturday; break;
        case 7: $restday = $employee->restday_sunday; break;
        default: $restday = 0;
    }

    $log_type = $restday ? 'RestDay' : 'Regular';

    // Night differential window
    $night_start = strtotime(date('Y-m-d 22:00:00', strtotime($attendance_date)));
    $night_end   = strtotime(date('Y-m-d 06:00:00', strtotime($attendance_date . ' +1 day')));

    $night_diff_hours = 0;
    // Regular shift overlap
    $night_diff_hours += max(0, (min($log_out, $night_end) - max($log_in, $night_start)) / 3600);
    // Overtime overlap
    if ($time_in_ot && $time_out_ot) {
        $night_diff_hours += max(0, (min($time_out_ot, $night_end) - max($time_in_ot, $night_start)) / 3600);
    }

    // === PAY COMPUTATION ===
    

    if ($restday) {

        // Example: Rest day premium 130%
        /*Hours*/
        $restday_hours = $regular_hours;
        $restday_overtime_hours = $regular_overtime_hours;

        /*Pay*/
        /*Regular*/
        $regular_pay = 0;
        $regular_overtime_pay = 0;
        /*Restday*/
        $restday_pay = $restday_hours * $hourly_rate * 1.30;
        $restday_overtime_pay = $restday_overtime_hours * $hourly_rate * 1.69; // rest day OT rate (130% * 1.3 OT)

    } else {

        /*Hours*/
        $restday_hours = 0;
        $restday_overtime_hours = 0;

        /*Pay*/
        /*Regular*/
        $regular_pay = $regular_hours * $hourly_rate;
        $regular_overtime_pay = $regular_overtime_hours * $hourly_rate * 1.25; // regular OT
        /*Restday*/
        $restday_pay = 0;
        $restday_overtime_pay = 0; // rest day OT rate (130% * 1.3 OT)

    }

    $night_diff_pay = $night_diff_hours * $nd_rate;


	/*Query Holiday*/
	// Handle Holidays
    $Regular_holiday_pay   = 0;
    $Special_non_working_holiday_pay   = 0;

    $attendance_date_holiday  = date('Y-m-d', strtotime($row['log_date']));

    $holidays = HolidayModel::where('holiday_date', $attendance_date_holiday)->get();
    $holiday_type = null;
    $holiday_multiplier = 1;

    if ($holidays->count() > 0) {
        foreach ($holidays as $holiday) {
            if ($holiday->holiday_type == 'Regular Holiday') {
                if ($holiday_type == 'Regular Holiday') {
                    // 2 regular holidays same day
                    $holiday_multiplier = 3; 
                } else {
                    $holiday_multiplier = 2;
                    $holiday_type = 'Regular Holiday';
                }
            } elseif ($holiday->holiday_type == 'Special Non-Working Holiday') {
                if ($holiday_type != 'Regular Holiday') {
                    $holiday_multiplier = 0.3;
                    $holiday_type = 'Special Non-Working Holiday';
                }
            }
        }

        if ($holiday_type == 'Regular Holiday') {
            if ($regular_hours == 0 && $regular_overtime_hours == 0) {
                // No work, still paid daily rate (200% if double regular holiday)
                $Regular_holiday_pay = $daily_rate * ($holiday_multiplier == 3 ? 2 : 1);
                $Regular_holiday_pay_ot = 0;
            } else {
                $Regular_holiday_pay = $regular_hours * $hourly_rate * $holiday_multiplier;
                $Regular_holiday_pay_ot = 0;
                if ($regular_overtime_hours > 0) {
                    $Regular_holiday_pay += 0;
                    $Regular_holiday_pay_ot = $regular_overtime_hours * $hourly_rate * ($holiday_multiplier + 0.6);
                }
            }
        } elseif ($holiday_type == 'Special Non-Working Holiday') {
            if ($regular_hours > 0) {
                $Special_non_working_holiday_pay = $regular_hours * $hourly_rate * 0.3;
                $Special_non_working_holiday_pay_ot = 0;
                if ($regular_overtime_hours > 0) {
                    $Special_non_working_holiday_pay += 0;
                    $Special_non_working_holiday_pay_ot = $regular_overtime_hours * $hourly_rate * 0.69;
                }
            }
        }
    }


    // Save into $data
    $data[] = [
        'employee_full_name'                =>$employee_full_name,
        'employee_no'                       => $employee_number,
        'log_date'                          => $attendance_date,
        'log_type'                          => $log_type,
        'time_in'                           => $row['time_in'],
        'time_out'                          => $row['time_out'],
        'breaktime_start'                   => $row['breaktime_start'],
        'breaktime_end'                     => $row['breaktime_end'],
        'time_in_ot'                        => $row['time_in_ot'],
        'time_out_ot'                       => $row['time_out_ot'],
        'tardiness_mins'                    => round($tardiness, 2),
        'undertime_mins'                    => round($undertime, 2),
        'regular_hours'                     => round($regular_hours, 2),
        'regular_overtime_hours'            => round($regular_overtime_hours, 2),
        'restday_hours'                     => round($restday_hours, 2),
        'restday_overtime_hours'            => round($restday_overtime_hours, 2),
        'night_diff_hours'                  => round($night_diff_hours, 2),
        'regular_pay'                       => round($regular_pay, 2),
        'regular_overtime_pay'              => round($regular_overtime_pay, 2),
        'restday_pay'                       => round($restday_pay, 2),
        'restday_overtime_pay'              => round($restday_overtime_pay, 2),
        'night_diff_pay'                    => round($night_diff_pay, 2),
        'regular_holiday_pay'               => round($Regular_holiday_pay, 2),
        'special_non_working_holiday_pay'   => round($Special_non_working_holiday_pay, 2),
    ];


            if($log_type=='Regular'){

                /*Import Log Regular Working Hours*/
                    EmployeeLogsHelper::saveEmployeeRegularLogs([
                        'employee_idx'                      => $employee_idx,
                        'branch_idx'                        => $branch_idx,
                        'department_idx'                    => $department_idx,
                        'employee_current_rate'             => $hourly_rate,
                        'attendance_date'                   => date('Y-m-d', strtotime($attendance_date)),
                        'override_default_shift'            => 'Yes',
                        'log_in'                            => date('Y-m-d H:i:s', strtotime($row['time_in'])),
                        'breaktime_start'                   => date('Y-m-d H:i:s', strtotime($row['breaktime_start'])),
                        'breaktime_end'                     => date('Y-m-d H:i:s', strtotime($row['breaktime_end'])),
                        'log_out'                           => date('Y-m-d H:i:s', strtotime($row['time_out'])),
                        'total_hours_from_log_in_and_out'   => $total_hours_from_log_in_and_out,
                        'total_regular_hours'               => round($regular_hours, 2),
                        'total_regular_overtime_hours'      => 0,
                        'total_restday_hours'               => 0,
                        'total_restday_overtime_hours'      => 0,
                        'total_breaktime_hours'             => 0,
                        'total_tardiness_hours'             => round($tardiness),
                        'total_undertime_hours'             => round($undertime),
                        'total_covered_night_diff_hrs'      => round($night_diff_hours, 2),
                        'regular_pay'                       => round($regular_pay, 2),
                        'regular_overtime_pay'              => 0,
                        'restday_pay'                       => 0,
                        'restday_overtime_pay'              => 0,
                        'night_differential_pay'            => round($night_diff_pay, 2),
                        'regular_holiday_pay'               => round($Regular_holiday_pay, 2),
                        'special_holiday_pay'               => round($Special_non_working_holiday_pay, 2),
                        'log_type'                          => 'Regular',
                    ]);

                if($regular_overtime_hours > 0){
                    EmployeeLogsHelper::saveEmployeeRegularLogs([
                        'employee_idx'                      => $employee_idx,
                        'branch_idx'                        => $branch_idx,
                        'department_idx'                    => $department_idx,
                        'employee_current_rate'             => $hourly_rate,
                        'attendance_date'                   => date('Y-m-d', strtotime($attendance_date)),
                        'override_default_shift'            => 'Yes',
                        'log_in'                            => date('Y-m-d H:i:s', strtotime($row['time_in'])),
                        'breaktime_start'                   => date('Y-m-d H:i:s', strtotime($row['breaktime_start'])),
                        'breaktime_end'                     => date('Y-m-d H:i:s', strtotime($row['breaktime_end'])),
                        'log_out'                           => date('Y-m-d H:i:s', strtotime($row['time_out'])),
                        'total_hours_from_log_in_and_out'   => round($regular_overtime_hours, 2),
                        'total_regular_hours'               => 0,
                        'total_regular_overtime_hours'      => round($regular_overtime_hours, 2),
                        'total_restday_hours'               => 0,
                        'total_restday_overtime_hours'      => 0,
                        'total_breaktime_hours'             => 0,
                        'total_tardiness_hours'             => 0,
                        'total_undertime_hours'             => 0,
                        'total_covered_night_diff_hrs'      => round($night_diff_hours, 2),
                        'regular_pay'                       => 0,
                        'regular_overtime_pay'              => round($regular_overtime_pay, 2),
                        'restday_pay'                       => 0,
                        'restday_overtime_pay'              => 0,
                        'night_differential_pay'            => round($night_diff_pay, 2),
                        'regular_holiday_pay'               => round($Regular_holiday_pay, 2),
                        'special_holiday_pay'               => round($Special_non_working_holiday_pay, 2),
                        'log_type'                          => 'RegularOT',
                    ]);
                }

            }


            else{

            EmployeeLogsHelper::saveEmployeeRegularLogs([
                        'employee_idx'                      => $employee_idx,
                        'branch_idx'                        => $branch_idx,
                        'department_idx'                    => $department_idx,
                        'employee_current_rate'             => $hourly_rate,
                        'attendance_date'                   => date('Y-m-d', strtotime($attendance_date)),
                        'override_default_shift'            => 'Yes',
                        'log_in'                            => date('Y-m-d H:i:s', strtotime($row['time_in'])),
                        'breaktime_start'                   => date('Y-m-d H:i:s', strtotime($row['breaktime_start'])),
                        'breaktime_end'                     => date('Y-m-d H:i:s', strtotime($row['breaktime_end'])),
                        'log_out'                           => date('Y-m-d H:i:s', strtotime($row['time_out'])),
                        'total_hours_from_log_in_and_out'   => $total_hours_from_log_in_and_out,
                        'total_regular_hours'               => 0,
                        'total_regular_overtime_hours'      => 0,
                        'total_restday_hours'               => round($restday_hours, 2),
                        'total_restday_overtime_hours'      => 0,
                        'total_breaktime_hours'             => 0,
                        'total_tardiness_hours'             => round($tardiness),
                        'total_undertime_hours'             => round($undertime),
                        'total_covered_night_diff_hrs'      => round($night_diff_hours, 2),
                        'regular_pay'                       => 0,
                        'regular_overtime_pay'              => 0,
                        'restday_pay'                       => round($restday_pay, 2),
                        'restday_overtime_pay'              => 0,
                        'night_differential_pay'            => round($night_diff_pay, 2),
                        'regular_holiday_pay'               => round($Regular_holiday_pay, 2),
                        'special_holiday_pay'               => round($Special_non_working_holiday_pay, 2),
                        'log_type'                          => 'Restday',
                    ]);

            if($regular_overtime_hours > 0){
                    EmployeeLogsHelper::saveEmployeeRegularLogs([
                        'employee_idx'                      => $employee_idx,
                        'branch_idx'                        => $branch_idx,
                        'department_idx'                    => $department_idx,
                        'employee_current_rate'             => $hourly_rate,
                        'attendance_date'                   => date('Y-m-d', strtotime($attendance_date)),
                        'override_default_shift'            => 'Yes',
                        'log_in'                            => date('Y-m-d H:i:s', strtotime($row['time_in'])),
                        'breaktime_start'                   => date('Y-m-d H:i:s', strtotime($row['breaktime_start'])),
                        'breaktime_end'                     => date('Y-m-d H:i:s', strtotime($row['breaktime_end'])),
                        'log_out'                           => date('Y-m-d H:i:s', strtotime($row['time_out'])),
                        'total_hours_from_log_in_and_out'   => round($regular_overtime_hours, 2),
                        'total_regular_hours'               => 0,
                        'total_regular_overtime_hours'      => 0,
                        'total_restday_hours'               => 0,
                        'total_restday_overtime_hours'      => round($restday_overtime_hours, 2),
                        'total_breaktime_hours'             => 0,
                        'total_tardiness_hours'             => 0,
                        'total_undertime_hours'             => 0,
                        'total_covered_night_diff_hrs'      => round($night_diff_hours, 2),
                        'regular_pay'                       => 0,
                        'regular_overtime_pay'              => 0,
                        'restday_pay'                       => 0,
                        'restday_overtime_pay'              => round($restday_overtime_pay, 2),
                        'night_differential_pay'            => round($night_diff_pay, 2),
                        'regular_holiday_pay'               => round($Regular_holiday_pay, 2),
                        'special_holiday_pay'               => round($Special_non_working_holiday_pay, 2),
                        'log_type'                          => 'RestDayOT',
                    ]);

            }
            }


    $no++;
            }

            return response()->json([
                'message' => 'CSV imported successfully',
                'data' => DataTables::of($data)
                            ->addIndexColumn()
                            ->toArray() // Convert DataTables result to array
            ]);

            // Return the parsed data to populate the table
            //return response()->json(['message' => 'CSV imported successfully', 'data' => $data]);


        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing CSV file: ' . $e->getMessage()], 500);
        }
    }


    public function viewCSV(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file format or size'], 400);
        }

        // Get the uploaded file
        $file = $request->file('csv_file');

        // Parse the CSV file using the League CSV package
        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0); // First row is the header

            $no = 1;

            $data = [];
           foreach ($csv as $row) {

    // CSV Data
    $employee_number  = $row['employee_no'];
    $attendance_date  = $row['log_date'];
    $log_in           = strtotime($row['time_in']);
    $break_start      = strtotime($row['breaktime_start']);
    $break_end        = strtotime($row['breaktime_end']);
    $log_out          = strtotime($row['time_out']);
    $time_in_ot       = !empty($row['time_in_ot']) ? strtotime($row['time_in_ot']) : null;
    $time_out_ot      = !empty($row['time_out_ot']) ? strtotime($row['time_out_ot']) : null;

    // Employee Info
    $employee = EmployeeModel::where('teves_payroll_employee_table.employee_number', $employee_number)->first();

    $default_in   = strtotime($employee->time_in);
    $default_out  = strtotime($employee->time_out);
    $break_in     = strtotime($employee->break_time_in);
    $break_out    = strtotime($employee->break_time_out);
    $default_hours = $employee->total_shift_hours - $employee->total_breaktime_hours;

    // Compute tardiness & undertime
    $tardiness = max(0, ($log_in - $default_in) / 60); // minutes late
    $undertime = max(0, ($default_out - $log_out) / 60); // minutes early out

    // Compute regular worked hours (excluding breaks)
    $worked_hours = (($log_out - $log_in) - ($break_end - $break_start)) / 3600;
    $regular_hours = min($worked_hours, $default_hours);

    // Compute overtime
    $regular_overtime_hours = 0;
    if ($time_in_ot && $time_out_ot) {
        $regular_overtime_hours = ($time_out_ot - $time_in_ot) / 3600;
    }

    // Check restday
    $day_num = date('N', strtotime($attendance_date));
    $restday = match($day_num) {
        1 => $employee->restday_monday,
        2 => $employee->restday_tuesday,
        3 => $employee->restday_wednesday,
        4 => $employee->restday_thursday,
        5 => $employee->restday_friday,
        6 => $employee->restday_saturday,
        7 => $employee->restday_sunday,
    };

    $log_type = $restday ? 'RestDay' : 'Regular';

    // Compute night differential (10PM–6AM)
    $night_start = strtotime(date('Y-m-d 22:00:00', strtotime($attendance_date)));
    $night_end   = strtotime(date('Y-m-d 06:00:00', strtotime($attendance_date . ' +1 day')));

    $night_diff_hours = 0;

    // Regular shift overlap
    $night_diff_hours += max(0, (min($log_out, $night_end) - max($log_in, $night_start)) / 3600);

    // Overtime overlap
    if ($time_in_ot && $time_out_ot) {
        $night_diff_hours += max(0, (min($time_out_ot, $night_end) - max($time_in_ot, $night_start)) / 3600);
    }

    // Save into $data
    $data[] = [
        'employee_no'   => $employee_number,
        'date'          => $attendance_date,
        'log_type'      => $log_type,
        'tardiness_mins'=> round($tardiness),
        'undertime_mins'=> round($undertime),
        'regular_hours' => round($regular_hours, 2),
        'overtime_hours'=> round($regular_overtime_hours, 2),
        'night_diff'    => round($night_diff_hours, 2),
    ];

    $no++;
            }

            return response()->json([
                'message' => 'CSV imported successfully',
                'data' => DataTables::of($data)
                            ->addIndexColumn()
                            ->toArray() // Convert DataTables result to array
            ]);

            // Return the parsed data to populate the table
            //return response()->json(['message' => 'CSV imported successfully', 'data' => $data]);


        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing CSV file: ' . $e->getMessage()], 500);
        }
    }
}
