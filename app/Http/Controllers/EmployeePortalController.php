<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Attendance\AttendanceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EmployeePortalController extends Controller
{
    private function account(): User
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string)$user->user_type),'Employee')===0 && $user->employee_idx,403);
        return $user;
    }

    public function profile()
    {
        $user=$this->account();
        $employee=DB::table('teves_payroll_employee_table as e')
            ->leftJoin('teves_branch_table as b','b.branch_id','=','e.branch_idx')
            ->where('e.employee_id',$user->employee_idx)
            ->select('e.*','b.branch_code','b.branch_name')->first();
        abort_unless($employee,404);
        $title='My Profile';$active_link='my_profile';
        return view('payroll.my_profile',compact('user','employee','title','active_link'));
    }

    public function changePassword(Request $request)
    {
        $user=$this->account();
        $values=$request->validate([
            'current_password'=>['required','string'],
            'password'=>['required','string','min:8','max:72','confirmed','different:current_password'],
        ]);
        if(!Hash::check($values['current_password'],$user->user_password)){
            return back()->withErrors(['current_password'=>'The current password is incorrect.']);
        }
        DB::table('teves_payroll_user_table')->where('user_id',$user->user_id)
            ->update(['user_password'=>Hash::make($values['password'])]);
        return redirect()->route('my-profile.index')->with('success','Password updated.');
    }

    public function notifications(AttendanceImportService $attendance)
    {
        $user=$this->account();
        $items=$attendance->employeeItems((int)$user->employee_idx);
        $recentRegular=Schema::hasTable('teves_payroll_employee_salary_per_cut_off')
            ?DB::table('teves_payroll_employee_salary_per_cut_off as s')
                ->join('teves_payroll_cutoff_table as c','c.cutoff_id','=','s.cutoff_idx')
                ->where('s.employee_idx',$user->employee_idx)
                ->orderByDesc('c.cut_off_period_end')->limit(5)
                ->get(['c.cut_off_period_start as start','c.cut_off_period_end as end','s.net_salary as amount'])
                ->map(fn($entry)=>['start'=>$entry->start,'end'=>$entry->end,'amount'=>$entry->amount,'type'=>'Employee payroll'])
            :collect();
        $recentDrivers=Schema::hasTable('driver_payrolls')
            ?DB::table('driver_payrolls')->where('employee_idx',$user->employee_idx)
                ->orderByDesc('cutoff_end')->limit(5)
                ->get(['cutoff_start as start','cutoff_end as end','total_trip_pay as amount'])
                ->map(fn($entry)=>['start'=>$entry->start,'end'=>$entry->end,'amount'=>$entry->amount,'type'=>'Driver trips'])
            :collect();
        $recentPayroll=$recentRegular->concat($recentDrivers)->sortByDesc('end')->take(5);
        $title='My Updates';$active_link='my_updates';
        return view('payroll.my_updates',compact('user','items','recentPayroll','title','active_link'));
    }
}
