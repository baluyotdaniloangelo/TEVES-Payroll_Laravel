<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Attendance\AttendanceImportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeDtrController extends Controller
{
    public function index(AttendanceImportService $attendance)
    {
        $user = User::where('user_id', session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string) $user->user_type), 'Employee') === 0 && $user->employee_idx, 403);
        $employee = \App\Models\EmployeeModel::where('employee_id', $user->employee_idx)->firstOrFail();
        $items = $attendance->employeeItems((int) $employee->employee_id);
        $title = 'My DTR';
        return view('payroll.my_dtr', compact('user', 'employee', 'items', 'title'));
    }

    public function submit(Request $request, int $row, AttendanceImportService $attendance)
    {
        $user = User::where('user_id', session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string) $user->user_type), 'Employee') === 0 && $user->employee_idx, 403);
        $proposed = $request->input('proposed', []);
        if (is_array($proposed)) {
            foreach (['breaktime_start', 'breaktime_end'] as $field) {
                if ($request->filled('break_times.'.$field)) {
                    $proposed[$field] = $request->input('break_dates.'.$field).'T'.$request->input('break_times.'.$field);
                }
            }
            $request->merge(['proposed' => $proposed]);
        }
        $values = $request->validate([
            'version' => ['required','integer','min:1'],
            'proposed.log_in' => ['required','date_format:Y-m-d\\TH:i'],
            'proposed.log_out' => ['required','date_format:Y-m-d\\TH:i'],
            'proposed.break_policy' => ['required',Rule::in(['recorded','none'])],
            'proposed.breaktime_start' => ['nullable','required_if:proposed.break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'proposed.breaktime_end' => ['nullable','required_if:proposed.break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'break_dates.breaktime_start' => ['nullable','date_format:Y-m-d'],
            'break_dates.breaktime_end' => ['nullable','date_format:Y-m-d'],
            'break_times.breaktime_start' => ['nullable','date_format:H:i'],
            'break_times.breaktime_end' => ['nullable','date_format:H:i'],
            'note' => ['nullable','string','max:1000'],
            'acknowledged' => ['accepted'],
        ]);
        $attendance->submitEmployeeDtr((int) $user->employee_idx, $row, $values);
        return redirect()->route('my-dtr.index')->with('success', 'DTR submitted to payroll for review.');
    }
}
