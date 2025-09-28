<?php

namespace App\Helpers;

use App\Models\EmployeeLogsModel;
use Illuminate\Support\Facades\Session;

class EmployeeLogsHelper
{
    public static function saveEmployeeRegularLogs(array $data)
    {
        $EmployeeRegularLogs = new EmployeeLogsModel();

        $EmployeeRegularLogs->employee_idx                   = $data['employee_idx'];
        $EmployeeRegularLogs->branch_idx                     = $data['branch_idx'];
        $EmployeeRegularLogs->department_idx                 = $data['department_idx'];
        $EmployeeRegularLogs->current_rate                   = $data['employee_current_rate'];
        $EmployeeRegularLogs->attendance_date                = $data['attendance_date'];
        $EmployeeRegularLogs->override_shift                 = $data['override_default_shift'];
        $EmployeeRegularLogs->log_in                         = $data['log_in'];
        $EmployeeRegularLogs->breaktime_start                = $data['breaktime_start'];
        $EmployeeRegularLogs->breaktime_end                  = $data['breaktime_end'];
        $EmployeeRegularLogs->log_out                        = $data['log_out'];

        $EmployeeRegularLogs->total_hours                    = number_format((float)$data['total_hours_from_log_in_and_out'], 2, '.', '');
        $EmployeeRegularLogs->total_regular_hours            = number_format((float)$data['total_regular_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_regular_overtime_hours   = number_format((float)$data['total_regular_overtime_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_restday_hours            = number_format((float)$data['total_restday_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_restday_overtime_hours   = number_format((float)$data['total_restday_overtime_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_breaktime_hours          = number_format((float)$data['total_breaktime_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_tardiness_hours          = number_format((float)$data['total_tardiness_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_undertime_hours          = number_format((float)$data['total_undertime_hours'], 2, '.', '');
        $EmployeeRegularLogs->total_night_differential_hours = number_format((float)$data['total_covered_night_diff_hrs'], 2, '.', '');

        $EmployeeRegularLogs->regular_pay                    = number_format((float)$data['regular_pay'], 2, '.', '');
        $EmployeeRegularLogs->regular_overtime_pay           = number_format((float)$data['regular_overtime_pay'], 2, '.', '');
        $EmployeeRegularLogs->restday_pay                    = number_format((float)$data['restday_pay'], 2, '.', '');
        $EmployeeRegularLogs->restday_overtime_pay           = number_format((float)$data['restday_overtime_pay'], 2, '.', '');
        /*
        if ($data['log_type'] == 'Regular') {
            
        } elseif ($data['log_type'] == 'RegularOT') {
            
        } elseif ($data['log_type'] == 'RestDayOT') {
            
        } else {
            
        }
        */
        $EmployeeRegularLogs->night_differential_pay = number_format((float)$data['night_differential_pay'], 2, '.', '');
        $EmployeeRegularLogs->regular_holiday_pay    = number_format((float)$data['regular_holiday_pay'], 2, '.', '');
        $EmployeeRegularLogs->special_holiday_pay    = number_format((float)$data['special_holiday_pay'], 2, '.', '');
        $EmployeeRegularLogs->log_type               = $data['log_type'];

        $EmployeeRegularLogs->created_by_user_idx    = Session::get('loginID');

        return $EmployeeRegularLogs->save();
    }
}
