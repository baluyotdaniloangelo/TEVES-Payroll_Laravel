<?php
namespace App\Http\Controllers;

use App\Models\DriversLogsModel;
use App\Models\EmployeeModel;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Session;

class DriversLogsController extends Controller
{
    public function drivers_attendance_logs()
    {
        abort_unless(Session::has('loginID'), 401);
        $title = 'Drivers Logs';
        $data = User::where('user_id', Session::get('loginID'))->first();
        $active_link = 'drivers_attendance_logs';
        $driverLogsConfig = [
            'csrfToken' => csrf_token(),
            'today' => now()->toDateString(),
            'list' => route('getDriversLogsList'),
            'comparison' => route('getDriversLogsComputationComparison'),
            'recalculate' => route('RecalculateDriverLog'),
            'branches' => route('getBranchList_for_selection'),
            'drivers' => route('getDriversList_for_item_selection'),
            'save' => route('submit_drivers_logs_information'),
            'info' => route('DriversLogsInformation'),
            'delete' => route('DeleteDriversLog'),
        ];
        return view('payroll.drivers_attendance_logs_main', compact('data', 'title', 'active_link', 'driverLogsConfig'));
    }

    public function getDriversLogsList(Request $request)
    {
        abort_unless(Session::has('loginID'), 401);
        if (!$request->ajax()) {
            abort(404);
        }

        $logs = DriversLogsModel::query()
            ->join('teves_payroll_employee_table as employee', 'employee.employee_id', '=', 'teves_payroll_drivers_logs.employee_idx')
            ->leftJoin('teves_branch_table as branch', 'branch.branch_id', '=', 'teves_payroll_drivers_logs.branch_idx')
            ->select([
                'teves_payroll_drivers_logs.*',
                'branch.branch_name',
                'employee.employee_number',
                DB::raw("CONCAT(TRIM(employee.employee_last_name), ', ', TRIM(employee.employee_first_name), IF(NULLIF(TRIM(employee.employee_middle_name), '') IS NULL, '', CONCAT(' ', TRIM(employee.employee_middle_name))), IF(NULLIF(TRIM(employee.employee_extension_name), '') IS NULL, '', CONCAT(' ', TRIM(employee.employee_extension_name)))) as employee_full_name"),
            ]);

        return DataTables::of($logs)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="si si-options-vertical"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <button type="button" class="dropdown-item js-edit-driver-log" data-id="' . (int) $row->drivers_logs_id . '"><i class="fa-solid fa-pencil m-r-5"></i> Edit</button>
                        <button type="button" class="dropdown-item js-delete-driver-log" data-id="' . (int) $row->drivers_logs_id . '"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</button>
                    </div></div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDriversLogsComputationComparison(Request $request)
    {
        abort_unless(Session::has('loginID'), 401);
        abort_unless($request->ajax(), 404);

        $logs = DriversLogsModel::query()
            ->join('teves_payroll_employee_table as employee', 'employee.employee_id', '=', 'teves_payroll_drivers_logs.employee_idx')
            ->select([
                'teves_payroll_drivers_logs.drivers_logs_id',
                'teves_payroll_drivers_logs.travel_date',
                'teves_payroll_drivers_logs.gross_amount',
                'teves_payroll_drivers_logs.current_rate as saved_rate',
                'teves_payroll_drivers_logs.trip_pay as saved_trip_pay',
                'employee.employee_number',
                'employee.employee_rate as current_rate',
                DB::raw("CONCAT(TRIM(employee.employee_last_name), ', ', TRIM(employee.employee_first_name), IF(NULLIF(TRIM(employee.employee_middle_name), '') IS NULL, '', CONCAT(' ', TRIM(employee.employee_middle_name))), IF(NULLIF(TRIM(employee.employee_extension_name), '') IS NULL, '', CONCAT(' ', TRIM(employee.employee_extension_name)))) as employee_full_name"),
                DB::raw('(teves_payroll_drivers_logs.gross_amount * employee.employee_rate / 100) as recalculated_trip_pay'),
                DB::raw('(SELECT COUNT(*) FROM driver_payroll_items WHERE driver_payroll_items.drivers_logs_id = teves_payroll_drivers_logs.drivers_logs_id) as payroll_locked'),
            ]);

        return DataTables::of($logs)
            ->addIndexColumn()
            ->addColumn('difference', fn ($row) => (float) $row->recalculated_trip_pay - (float) $row->saved_trip_pay)
            ->addColumn('action', function ($row) {
                if ((int) $row->payroll_locked > 0) {
                    return '<span class="text-muted">Included in saved payroll</span>';
                }
                return '<button type="button" class="btn btn-sm btn-outline-primary js-recalculate-driver-log" data-id="' . (int) $row->drivers_logs_id . '">Use current rate</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function recalculateDriverLog(Request $request)
    {
        $validated = $request->validate(['drivers_logs_id' => ['required', 'integer', 'min:1']]);
        $log = DriversLogsModel::findOrFail($validated['drivers_logs_id']);
        if (DB::table('driver_payroll_items')->where('drivers_logs_id', $log->drivers_logs_id)->exists()) {
            return response()->json(['message' => 'Kasama na sa saved payroll ang trip na ito.'], 409);
        }

        $driver = EmployeeModel::where('employee_id', $log->employee_idx)
            ->where('employment_type', 'Others')->firstOrFail();
        $log->current_rate = (float) $driver->employee_rate;
        $log->trip_pay = (float) $log->gross_amount * ((float) $driver->employee_rate / 100);
        $log->updated_by_user_idx = Session::get('loginID');
        $log->save();

        return response()->json(['success' => true, 'message' => 'Na-update ang saved rate at trip pay gamit ang kasalukuyang driver rate.']);
    }

    public function drivers_logs_info(Request $request)
    {
        $validated = $request->validate(['DriverlogsID' => ['required', 'integer', 'min:1']]);
        $log = DriversLogsModel::query()
            ->join('teves_payroll_employee_table as employee', 'employee.employee_id', '=', 'teves_payroll_drivers_logs.employee_idx')
            ->leftJoin('teves_branch_table as branch', 'branch.branch_id', '=', 'teves_payroll_drivers_logs.branch_idx')
            ->where('teves_payroll_drivers_logs.drivers_logs_id', $validated['DriverlogsID'])
            ->first([
                'teves_payroll_drivers_logs.*', 'branch.branch_id as selected_branch_id', 'branch.branch_code', 'branch.branch_name',
                'employee.employee_number', 'employee.employee_last_name', 'employee.employee_first_name',
                'employee.employee_middle_name', 'employee.employee_extension_name',
            ]);
        abort_unless($log, 404);
        $log->employee_full_name = $this->formatEmployeeName($log);
        return response()->json($log);
    }

    public function delete_driver_log_confirmed(Request $request)
    {
        $validated = $request->validate(['DriverlogsID' => ['required', 'integer', 'min:1']]);
        $log = DriversLogsModel::findOrFail($validated['DriverlogsID']);
        if (DB::table('driver_payroll_items')->where('drivers_logs_id', $log->drivers_logs_id)->exists()) {
            return response()->json(['message' => 'Hindi puwedeng burahin ang trip na kasama na sa saved driver payroll.'], 409);
        }
        $log->delete();
        return response()->json(['success' => true, 'message' => 'Na-delete ang driver trip.']);
    }

    public function submit_drivers_logs_information(Request $request)
    {
        $validated = $request->validate([
            'drivers_logs_id' => ['required', 'integer', 'min:0'],
            'branch_idx' => ['required', 'integer', 'exists:teves_branch_table,branch_id'],
            'employee_idx' => ['required', 'integer', Rule::exists('teves_payroll_employee_table', 'employee_id')->where(fn ($query) => $query->where('employment_type', 'Others'))],
            'travel_date' => [
                'required', 'date_format:Y-m-d',
                Rule::unique('teves_payroll_drivers_logs', 'travel_date')
                    ->where(fn ($query) => $query->where('employee_idx', $request->input('employee_idx')))
                    ->ignore((int) $request->input('drivers_logs_id'), 'drivers_logs_id'),
            ],
            'plate_number' => ['required', 'string', 'max:255'],
            'loading_terminal' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'volume' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'rate_per_liter' => ['required', 'numeric', 'gt:0', 'max:999999999'],
        ], [
            'drivers_logs_id.required' => 'Hindi makita ang driver trip na ise-save. I-refresh ang page at subukan ulit.',
            'branch_idx.required' => 'Pumili ng branch.',
            'branch_idx.integer' => 'Pumili ng branch mula sa listahan.',
            'branch_idx.exists' => 'Hindi valid ang napiling branch.',
            'employee_idx.required' => 'Pumili ng driver mula sa listahan.',
            'employee_idx.integer' => 'Pumili ng driver mula sa listahan.',
            'employee_idx.exists' => 'Pumili ng valid na driver mula sa listahan.',
            'travel_date.required' => 'Ilagay ang travel date.',
            'travel_date.date_format' => 'Pumili ng valid na travel date.',
            'travel_date.unique' => 'May trip log na para sa driver sa petsang ito.',
            'plate_number.required' => 'Ilagay ang plate number.',
            'plate_number.max' => 'Hanggang 255 characters lamang ang plate number.',
            'loading_terminal.required' => 'Ilagay ang loading terminal.',
            'loading_terminal.max' => 'Hanggang 255 characters lamang ang loading terminal.',
            'destination.required' => 'Ilagay ang destination.',
            'destination.max' => 'Hanggang 255 characters lamang ang destination.',
            'volume.required' => 'Ilagay ang volume.',
            'volume.numeric' => 'Numero lamang ang puwedeng ilagay sa volume.',
            'volume.gt' => 'Dapat mas mataas sa zero ang volume.',
            'rate_per_liter.required' => 'Ilagay ang rate per liter.',
            'rate_per_liter.numeric' => 'Numero lamang ang puwedeng ilagay sa rate per liter.',
            'rate_per_liter.gt' => 'Dapat mas mataas sa zero ang rate per liter.',
        ]);

        $driver = EmployeeModel::where('employee_id', $validated['employee_idx'])
            ->where('employment_type', 'Others')->firstOrFail();
        if ((int) $driver->branch_idx !== (int) $validated['branch_idx']) {
            throw ValidationException::withMessages(['employee_idx' => 'Hindi naka-assign ang driver sa napiling branch.']);
        }

        $logId = (int) $validated['drivers_logs_id'];
        $log = $logId ? DriversLogsModel::findOrFail($logId) : new DriversLogsModel();
        if ($logId && DB::table('driver_payroll_items')->where('drivers_logs_id', $logId)->exists()) {
            throw ValidationException::withMessages(['drivers_logs_id' => 'Kasama na ang trip na ito sa saved payroll kaya hindi na ito puwedeng baguhin.']);
        }

        $currentRate = (float) $driver->employee_rate;
        $grossAmount = (float) $validated['volume'] * (float) $validated['rate_per_liter'];
        $log->employee_idx = $driver->employee_id;
        $log->branch_idx = $driver->branch_idx;
        $log->department_idx = $driver->department_idx;
        $log->current_rate = $currentRate;
        $log->travel_date = $validated['travel_date'];
        $log->plate_number = trim($validated['plate_number']);
        $log->loading_terminal = trim($validated['loading_terminal']);
        $log->destination = trim($validated['destination']);
        $log->volume = $validated['volume'];
        $log->rate_per_liter = $validated['rate_per_liter'];
        $log->gross_amount = $grossAmount;
        $log->trip_pay = $grossAmount * ($currentRate / 100);
        if (!$logId) {
            $log->created_by_user_idx = Session::get('loginID');
        } else {
            $log->updated_by_user_idx = Session::get('loginID');
        }
        $log->save();

        return response()->json([
            'success' => true,
            'message' => $logId ? 'Na-update ang driver trip details.' : 'Na-save ang driver trip details.',
        ]);
    }

    private function formatEmployeeName($employee): string
    {
        $last = trim((string) ($employee->employee_last_name ?? ''));
        $given = collect([
            $employee->employee_first_name ?? null,
            $employee->employee_middle_name ?? null,
            $employee->employee_extension_name ?? null,
        ])->map(fn ($part) => trim((string) $part))->filter()->implode(' ');
        return trim($last . ($last !== '' && $given !== '' ? ', ' : '') . $given);
    }
}
