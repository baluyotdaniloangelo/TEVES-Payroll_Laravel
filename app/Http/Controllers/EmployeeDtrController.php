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
        return view('payroll.my_dtr', compact('user', 'employee', 'items'));
    }

    public function submit(Request $request, int $row, AttendanceImportService $attendance)
    {
        $user = User::where('user_id', session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string) $user->user_type), 'Employee') === 0 && $user->employee_idx, 403);
        $values = $request->validate([
            'version' => ['required','integer','min:1'],
            'proposed.log_in' => ['required','date_format:Y-m-d\\TH:i'],
            'proposed.log_out' => ['required','date_format:Y-m-d\\TH:i'],
            'proposed.break_policy' => ['required',Rule::in(['recorded','none'])],
            'proposed.breaktime_start' => ['nullable','required_if:proposed.break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'proposed.breaktime_end' => ['nullable','required_if:proposed.break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'note' => ['nullable','string','max:1000'],
            'acknowledged' => ['accepted'],
        ]);
        $attendance->submitEmployeeDtr((int) $user->employee_idx, $row, $values);
        return redirect()->route('my-dtr.index')->with('success', 'DTR submitted to payroll for review.');
    }
}
