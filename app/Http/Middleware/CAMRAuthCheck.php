<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        return $next($request);
    }
}
