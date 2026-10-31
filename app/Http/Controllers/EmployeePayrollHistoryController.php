<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeePayrollHistoryController extends Controller
{
    public function index()
    {
        $user = User::where('user_id', session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string) $user->user_type), 'Employee') === 0 && $user->employee_idx, 403);

        $employeeId = (int) $user->employee_idx;
        $regular = DB::table('teves_payroll_employee_salary_per_cut_off as salary')
            ->join('teves_payroll_cutoff_table as cutoff', 'cutoff.cutoff_id', '=', 'salary.cutoff_idx')
            ->leftJoin('teves_branch_table as branch', 'branch.branch_id', '=', 'salary.branch_idx')
            ->where('salary.employee_idx', $employeeId)
            ->select('salary.*', 'cutoff.cut_off_period_start as period_start',
                'cutoff.cut_off_period_end as period_end', 'branch.branch_code')
            ->orderByDesc('cutoff.cut_off_period_start')->get();

        $drivers = Schema::hasTable('driver_payrolls')
            && Schema::hasColumns('driver_payrolls', ['cutoff_start', 'cutoff_end'])
            ? DB::table('driver_payrolls as payroll')
                ->leftJoin('teves_branch_table as branch', 'branch.branch_id', '=', 'payroll.branch_idx')
                ->where('payroll.employee_idx', $employeeId)
                ->select('payroll.*', 'branch.branch_code')
                ->orderByDesc('payroll.cutoff_start')->get()
            : collect();
        $driverItems = $drivers->isNotEmpty() && Schema::hasTable('driver_payroll_items')
            ? DB::table('driver_payroll_items')
                ->whereIn('driver_payroll_id', $drivers->pluck('driver_payroll_id'))
                ->orderBy('travel_date')->get()->groupBy('driver_payroll_id')
            : collect();

        $records = $regular->map(fn ($payroll) => [
            'type' => 'regular',
            'id' => $payroll->employee_salary_id,
            'start' => $payroll->period_start,
            'end' => $payroll->period_end,
            'branch_code' => $payroll->branch_code,
            'reference' => 'Cutoff #'.$payroll->cutoff_idx,
            'gross' => (float) $payroll->gross_salary,
            'deductions' => (float) $payroll->deduction_amount_total,
            'allowances' => (float) $payroll->allowance_amount_total,
            'net' => (float) $payroll->net_salary,
            'detail' => $payroll,
            'items' => collect(),
        ])->concat($drivers->map(fn ($payroll) => [
            'type' => 'driver',
            'id' => $payroll->driver_payroll_id,
            'start' => $payroll->cutoff_start,
            'end' => $payroll->cutoff_end,
            'branch_code' => $payroll->branch_code,
            'reference' => $payroll->payroll_no ?: 'Driver payroll #'.$payroll->driver_payroll_id,
            'gross' => (float) $payroll->total_trip_pay,
            'deductions' => null,
            'allowances' => null,
            'net' => (float) $payroll->total_trip_pay,
            'detail' => $payroll,
            'items' => $driverItems->get($payroll->driver_payroll_id, collect()),
        ]))->sortByDesc('start')->values();

        $title = 'My Payroll';
        $active_link = 'my_payroll';
        return view('payroll.my_payroll', compact('user', 'records', 'title', 'active_link'));
    }
}
