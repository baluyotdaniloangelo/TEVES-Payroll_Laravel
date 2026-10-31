<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CAMRAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
		if(!Session()->has('loginID'))
		{
			return redirect('/')->with('fail', "You Have to Login First");
		}

		$account = DB::table('teves_payroll_user_table')->where('user_id', session('loginID'))->first(['user_type','employee_idx']);
		if ($account && strcasecmp(trim((string) $account->user_type), 'Employee') === 0) {
			abort_unless($account->employee_idx && $request->routeIs('my-dtr.*', 'my-payroll.*', 'my-profile.*', 'my-updates.*', 'logout-user'), 403, 'Employee accounts can access only their own portal.');
		}
        return $next($request);
    }
}
