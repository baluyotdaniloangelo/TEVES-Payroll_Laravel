<?php
namespace App\Http\Controllers;

use App\Models\DriversLogsModel;
use App\Models\BranchModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Session;

class DriversPayrollController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Session::has('loginID'), 401);
        $data = User::where('user_id', Session::get('loginID'))->first();
        $title = 'Generate Driver Payroll';
        $active_link = 'drivers_payroll';
        // The employee form stores this choice as "Others" (label: Others/Outsource/Drivers).
        $drivers = DB::table('teves_payroll_employee_table as e')
            ->join('teves_branch_table as b', 'b.branch_id', '=', 'e.branch_idx')
            ->where('e.employment_type', 'Others')->orderBy('e.employee_full_name')
            ->get(['e.employee_id', 'e.employee_number', 'e.employee_full_name', 'e.branch_idx', 'b.branch_name', 'b.branch_code']);
        $employee = null;
        $trips = collect();
        $saved = null;
        $savedTrips = collect();
        $cutoffPayrolls = collect();
        $cutoffTotal = 0.0;
        $hasPeriod = $request->filled(['employee_id', 'cutoff_start', 'cutoff_end']);

        if ($hasPeriod) {
            $request->validate([
                'employee_id' => ['required', 'integer', 'exists:teves_payroll_employee_table,employee_id'],
                'cutoff_start' => ['required', 'date_format:Y-m-d'],
                'cutoff_end' => ['required', 'date_format:Y-m-d', 'after_or_equal:cutoff_start'],
            ]);
            $days = Carbon::parse($request->cutoff_start)->diffInDays(Carbon::parse($request->cutoff_end));
            if ($days > 366) {
                throw ValidationException::withMessages(['cutoff_end' => 'Ang cutoff period ay hindi puwedeng lumampas ng isang taon.']);
            }
            $employee = $drivers->firstWhere('employee_id', (int) $request->employee_id);
            abort_unless($employee, 404);
            $saved = DB::table('driver_payrolls')->where('employee_idx', $employee->employee_id)
                ->where('cutoff_start', $request->cutoff_start)->where('cutoff_end', $request->cutoff_end)->first();

            if ($saved) {
                $savedTrips = DB::table('driver_payroll_items')->where('driver_payroll_id', $saved->driver_payroll_id)->orderBy('travel_date')->get();
            } else {
                $trips = $this->availableTrips($employee->employee_id, $employee->branch_idx, $request->cutoff_start, $request->cutoff_end);
            }

            $cutoffPayrolls = DB::table('driver_payrolls as p')
                ->join('teves_payroll_employee_table as e', 'e.employee_id', '=', 'p.employee_idx')
                ->where('p.branch_idx', $employee->branch_idx)
                ->where('p.cutoff_start', $request->cutoff_start)->where('p.cutoff_end', $request->cutoff_end)
                ->orderBy('e.employee_full_name')->select('p.*', 'e.employee_number', 'e.employee_full_name')
                ->selectSub(DB::table('driver_payroll_items')->selectRaw('COUNT(*)')->whereColumn('driver_payroll_items.driver_payroll_id', 'p.driver_payroll_id'), 'total_trip_count')
                ->get();
            $cutoffTotal = (float) $cutoffPayrolls->sum('total_trip_pay');
        }

        return view('payroll.drivers_payroll', compact('data', 'title', 'active_link', 'drivers', 'employee', 'trips', 'saved', 'savedTrips', 'cutoffPayrolls', 'cutoffTotal', 'hasPeriod'));
    }

    private function availableTrips(int $employeeId, int $branchId, string $start, string $end)
    {
        return DriversLogsModel::query()->where('employee_idx', $employeeId)->where('branch_idx', $branchId)
            ->whereBetween('travel_date', [$start, $end])->whereNull('cutoff_idx')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('driver_payroll_items')
                    ->whereColumn('driver_payroll_items.drivers_logs_id', 'teves_payroll_drivers_logs.drivers_logs_id');
            })->orderBy('travel_date')->get();
    }

    public function save(Request $request, int $employeeId)
    {
        $request->validate([
            'cutoff_start' => ['required', 'date_format:Y-m-d'],
            'cutoff_end' => ['required', 'date_format:Y-m-d', 'after_or_equal:cutoff_start'],
            'trip_reference' => ['required', 'string', 'max:100'],
            'reviewed' => ['accepted'],
        ]);
        if (Carbon::parse($request->cutoff_start)->diffInDays(Carbon::parse($request->cutoff_end)) > 366) {
            throw ValidationException::withMessages(['cutoff_end' => 'Ang cutoff period ay hindi puwedeng lumampas ng isang taon.']);
        }

        DB::transaction(function () use ($request, $employeeId) {
            $employee = DB::table('teves_payroll_employee_table')->where('employee_id', $employeeId)
                ->where('employment_type', 'Others')->lockForUpdate()->first();
            abort_unless($employee, 404);
            if (DB::table('driver_payrolls')->where('employee_idx', $employeeId)
                ->where('cutoff_start', $request->cutoff_start)->where('cutoff_end', $request->cutoff_end)->exists()) {
                throw ValidationException::withMessages(['payroll' => 'May saved payroll na para sa driver at cutoff period na ito.']);
            }

            $logs = $this->availableTrips($employeeId, $employee->branch_idx, $request->cutoff_start, $request->cutoff_end)->all();
            if (!$logs) {
                throw ValidationException::withMessages(['payroll' => 'Walang available trip logs ang driver sa cutoff na ito.']);
            }
            $rates = collect($logs)->pluck('current_rate')->filter(fn ($rate) => $rate !== null)->unique();
            $payrollId = DB::table('driver_payrolls')->insertGetId([
                'cutoff_idx' => null, 'cutoff_start' => $request->cutoff_start, 'cutoff_end' => $request->cutoff_end,
                'employee_idx' => $employeeId, 'branch_idx' => $employee->branch_idx, 'payroll_no' => null,
                'trip_reference' => trim($request->input('trip_reference')), 'payroll_date' => Carbon::today()->toDateString(),
                'rate_per_trip' => $rates->count() === 1 ? (float) $rates->first() : null,
                'total_volume' => collect($logs)->sum('volume'), 'total_gross_amount' => collect($logs)->sum('gross_amount'),
                'total_trip_pay' => collect($logs)->sum('trip_pay'), 'reviewed_by_user_idx' => Session::get('loginID'),
                'reviewed_at' => now(), 'created_by_user_idx' => Session::get('loginID'), 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('driver_payrolls')->where('driver_payroll_id', $payrollId)
                ->update(['payroll_no' => 'DP-' . Carbon::today()->format('Y') . '-' . str_pad((string) $payrollId, 4, '0', STR_PAD_LEFT)]);
            foreach ($logs as $log) {
                DB::table('driver_payroll_items')->insert([
                    'driver_payroll_id' => $payrollId, 'drivers_logs_id' => $log->drivers_logs_id,
                    'travel_date' => $log->travel_date, 'current_rate' => $log->current_rate,
                    'plate_number' => $log->plate_number, 'loading_terminal' => $log->loading_terminal,
                    'destination' => $log->destination, 'volume' => $log->volume, 'rate_per_liter' => $log->rate_per_liter,
                    'gross_amount' => $log->gross_amount, 'trip_pay' => $log->trip_pay, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('drivers_payroll.index', [
            'employee_id' => $employeeId, 'cutoff_start' => $request->cutoff_start, 'cutoff_end' => $request->cutoff_end, 'tab' => 'saved',
        ])->with('success', 'Na-save ang driver payroll para sa cutoff period.');
    }

    public function print(Request $request, int $employeeId)
    {
        abort_unless(Session::has('loginID'), 401);
        $request->validate(['cutoff_start' => ['required', 'date_format:Y-m-d'], 'cutoff_end' => ['required', 'date_format:Y-m-d', 'after_or_equal:cutoff_start']]);
        $payroll = DB::table('driver_payrolls as p')
            ->join('teves_payroll_employee_table as e', 'e.employee_id', '=', 'p.employee_idx')
            ->join('teves_branch_table as b', 'b.branch_id', '=', 'p.branch_idx')
            ->where('p.employee_idx', $employeeId)->where('p.cutoff_start', $request->cutoff_start)->where('p.cutoff_end', $request->cutoff_end)
            ->first(['p.*', 'e.employee_number', 'e.employee_full_name', 'b.branch_name', 'b.branch_code']);
        abort_unless($payroll, 404);
        $branchInformation = BranchModel::find($payroll->branch_idx, [
            'branch_name', 'branch_code', 'branch_tin', 'branch_address', 'branch_owner', 'branch_owner_title', 'branch_logo',
        ]);
        $logoFile = $branchInformation && $branchInformation->branch_logo
            ? public_path('client_logo/' . $branchInformation->branch_logo)
            : null;
        $logoPath = $logoFile && is_file($logoFile) ? $logoFile : null;
        $items = DB::table('driver_payroll_items')->where('driver_payroll_id', $payroll->driver_payroll_id)->orderBy('travel_date')->get();
        $rates = $items->pluck('current_rate')->filter(fn ($rate) => $rate !== null)->unique();
        $rateLabel = $rates->count() > 1 ? 'Mixed rates' : number_format((float) ($rates->first() ?? 0), 2) . '%';
        $pdf = \PDF::loadView('printables.driver_payroll', compact('payroll', 'items', 'rateLabel', 'branchInformation', 'logoPath'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream($payroll->payroll_no . '.pdf');
    }
}
