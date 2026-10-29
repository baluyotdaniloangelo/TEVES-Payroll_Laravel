<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CAMRAlreadyLoggedIn
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
		if(Session()->has('loginID') && (url('/')==$request->url()))
		{
			$userType = DB::table('teves_payroll_user_table')->where('user_id', session('loginID'))->value('user_type');
			if (strcasecmp(trim((string) $userType), 'Employee') === 0) return redirect()->route('my-dtr.index');
			//return redirect('login')->with('fail', "You Have to Login First");
			return back();
		}
        return $next($request);
    }
}
